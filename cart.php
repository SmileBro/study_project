<?php

include 'config.php';
include 'get_function.php';
session_start();
$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location:login.php');
}

if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    mysqli_query($conn,
        "DELETE FROM `cart` WHERE CART_ID = '$delete_id'") or die('query failed');
    header('location:cart.php');
}

if (isset($_GET['delete_all'])) {
    mysqli_query($conn,
        "DELETE FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
    header('location:cart.php');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Корзина</title>
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'header.php'; ?>

<div class="heading">
    <h3>Корзина</h3>
    <p><a href="home.php">главная</a> / корзина </p>
</div>
<section class="shopping-cart">
    <h1 class="title">добавленные книги</h1>
    <div class="box-container">
        <?php
        $grand_total = 0;
        $select_cart = mysqli_query($conn,
            "SELECT * FROM `cart` WHERE USER_ID = '$user_id'") or die('query failed');
        if (mysqli_num_rows($select_cart) > 0) {
            while ($fetch_cart = mysqli_fetch_assoc($select_cart)) {
                $book_by_id = getColFromTable($conn, 'books', 'BOOK_ID', $fetch_cart['BOOK_ID']);
                ?>
                <div class="box">
                    <a href="cart.php?delete=<?= $fetch_cart['CART_ID'] ?>"
                       class="fas fa-times"
                       onclick="return confirm('Удалить эту книгу из корзины?');"></a>
                    <a href="details.php?id=<?= $fetch_cart['BOOK_ID'] ?>">
                        <img src="uploaded_img/<?= $book_by_id['BOOK_IMG'] ?>" alt=""></a>
                    <div class="name"><?= $book_by_id['BOOK_NAME'] ?></div>
                </div>
                <?php
                $sub_total = $fetch_cart['BOOK_AMOUNT'];
                $grand_total += $sub_total;
            }
        }
        else {
            echo '<p class="empty">Ваша корзина пуста</p>';
        }
        ?>
    </div>
    <div class="cart-clear">
        <a href="cart.php?delete_all"
           class="delete-btn <?= ($grand_total > 1) ? '' : 'disabled' ?>"
           onclick="return confirm('Удалить все предметы из корзины?');">Удалить все</a>
    </div>
    <div class="cart-total">
        <p>Всего книг : <span><?= $grand_total ?></span></p>
        <div class="flex">
            <a href="books.php" class="option-btn">Продолжить поиск</a>
            <a href="checkout.php"
               class="btn <?= ($grand_total > 0) ? '' : 'disabled' ?>">Заказать</a>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>
<script src="js/script.js"></script>
</body>
</html>