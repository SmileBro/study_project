<?php

include 'config.php';
include 'get_function.php';
session_start();
$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location:login.php');
}

if (isset($_POST['order_btn'])) {
    $placed_on = date('Y-m-d h:i:sa');
    $due_date = date('Y-m-d h:i:sa', strtotime($placed_on . ' + 30 days'));
    $worker_id = 0; // system
    $total_leases = getCountByStatus($conn, 'leases', 'USER_ID', $user_id);
    $cart_total = getCountByStatus($conn, 'cart', 'USER_ID', $user_id);
    if ($cart_total + $total_leases > 5) {
        $message[] = "Можно взять ещё " . (5 - $total_leases) . " книг. У вас - $cart_total.";
    }
    else {
        $cart_query = mysqli_query($conn,
            "SELECT * FROM `cart` WHERE USER_ID = '$user_id'") or die('query failed');
        if (mysqli_num_rows($cart_query) > 0) {
            while ($cart_item = mysqli_fetch_assoc($cart_query)) {
                $book_id = $cart_item['BOOK_ID'];
                $book_by_id = getColFromTable($conn, 'books', 'BOOK_ID', $book_id);
                $leased_book = getColFromTable($conn, 'leases', 'BOOK_ID', $book_id);
                $is_leased = mysqli_query($conn,
                    "SELECT * FROM `leases` WHERE USER_ID = '$user_id' AND BOOK_ID = '$book_id'") or die('query failed');
                if ($leased_book && mysqli_num_rows($is_leased) > 0) {
                    $book_name = $book_by_id['BOOK_NAME'];
                    $message[] = "Книга \"$book_name\" уже была заказана.";
                }
                else {
                    //Проверка на наличие книги
                    $book_amount = $book_by_id['BOOK_AMOUNT'];
                    if ($book_amount < 1) {
                        $message[] = 'В данный момент книга отсутствует.';
                    }
                    else {
                        //Запись данных о выдаче в БД
                        mysqli_query($conn,
                            "INSERT INTO `leases`(USER_ID, BOOK_ID, WORKER_ID, LEASE_START, LEASE_DUE, LEASE_STATUS) VALUES('$user_id', '$book_id', '$worker_id', '$placed_on', '$due_date', 'processing')") or die('query failed');
                        //Обновление количества книг в БД при заказе
                        $new_amount = $book_amount - 1;
                        $update_lease_query = mysqli_query($conn,
                            "UPDATE `books` SET BOOK_AMOUNT = '$new_amount' WHERE BOOK_ID = '$book_id'") or die('query failed');

                        //Удаление предметов из корзины пользователя
                        mysqli_query($conn,
                            "DELETE FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
                        $message[] = 'Заказ успешно сформирован!';
                    }
                }
            }
        }
        else {
            $message[] = 'Ваша корзина пуста!';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>оформление</title>
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'header.php'; ?>

<div class="heading">
    <h3>оформление</h3>
    <p><a href="home.php">главная</a> / оформить заказ </p>
</div>
<section class="display-order">
    <?php
    $cart_total = getCountByStatus($conn, 'cart', 'USER_ID', $user_id);
    $select_cart = mysqli_query($conn,
        "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
    if (mysqli_num_rows($select_cart) > 0) {
        while ($fetch_cart = mysqli_fetch_assoc($select_cart)) {
            $book_by_id = getColFromTable($conn, 'books', 'BOOK_ID', $fetch_cart['BOOK_ID']);
            ?>
            <p><?= $book_by_id['BOOK_NAME'] ?></p>
            <?php
        }
    }
    else {
        echo '<p class="empty">Ваша корзина пуста</p>';
    }
    ?>
    <div class="grand-total"> Всего книг : <span><?= $cart_total ?></span>
    </div>
</section>

<section class="checkout">
    <?php $user_by_id = getColFromTable($conn, 'users', 'USER_ID', $_SESSION['user_id']); ?>
    <form action="" method="post">
        <h3>оформить заказ</h3>
        <div class="flex">
            <div class="inputBox">
                <span>Ваше имя :</span>
                <input type="text" name="name"
                       value="<?= $user_by_id['USER_NAME'] ?>"
                       required placeholder="Введите ваше имя">
            </div>
            <div class="inputBox">
                <span>Ваш номер :</span>
                <input type="text" name="number"
                       value="<?= $user_by_id['USER_PHONE'] ?>"
                       required placeholder="Введите ваш номер">
            </div>
            <div class="inputBox">
                <span>Ваш email :</span>
                <input type="email" name="email"
                       value="<?= $user_by_id['USER_MAIL'] ?>"
                       required placeholder="Введите ваш email">
            </div>
        </div>
        <input type="submit" value="заказать" class="btn" name="order_btn">
    </form>
</section>

<?php include 'footer.php'; ?>
<script src="js/script.js"></script>
</body>
</html>