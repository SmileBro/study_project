<?php

include 'config.php';
include 'get_function.php';
session_start();
$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location:login.php');
}

if (isset($_POST['add_to_cart'])) {
    $book_id = $_POST['book_id'];
    $book_amount = $_POST['book_amount'];
    $book_quantity = 1;
    $message[] = addToCart($conn, $user_id, $book_id, $book_quantity, $book_amount);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>home</title>
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'header.php'; ?>

<section class="home heading">
    <div class="content">
        <h3>Забронируйте книгу не выходя из дома.</h3>
        <p>До 5 книг в одни руки из нашей библиотеки</p>
        <a href="books.php" class="white-btn">вперед</a>
    </div>
</section>
<section class="books">
    <h1 class="title">популярное</h1>
    <div class="box-container">
        <?php
        $select_popular_leases = mysqli_query($conn,
            "SELECT b.BOOK_ID, b.BOOK_NAME, b.BOOK_AMOUNT, b.BOOK_IMG, a.AUTH_NAME, COUNT(l.LEASE_ID) AS popular FROM leases l JOIN books b ON l.BOOK_ID = b.BOOK_ID JOIN authors a ON b.AUTH_ID = a.AUTH_ID GROUP BY b.BOOK_ID,b.BOOK_NAME, a.AUTH_NAME ORDER BY popular DESC LIMIT 3") or die('query failed');
        if (mysqli_num_rows($select_popular_leases) > 0) {
            while ($popular_book = mysqli_fetch_assoc($select_popular_leases)) {
                $amount = $popular_book['BOOK_AMOUNT'];
                ?>
                <form action="" method="post" class="box">
                    <a href="details.php?id=<?= $popular_book['BOOK_ID'] ?>">
                        <img class="image"
                             src="uploaded_img/<?= $popular_book['BOOK_IMG'] ?>"
                             width=100% alt=""></a>
                    <div class="desc">
                        <div class="name"><?= $popular_book['BOOK_NAME'] ?></div>
                        <div class="name"><?= $popular_book['AUTH_NAME'] ?></div>
                        <div class="qty"><?= ($amount != 0) ? "В наличии: $amount шт." : 'Нет в наличии' ?> </div>
                        <input type="hidden" name="book_id"
                               value="<?= $popular_book['BOOK_ID'] ?>">
                        <input type="hidden" name="book_name"
                               value="<?= $popular_book['BOOK_NAME'] ?>">
                        <input type="hidden" name="book_amount"
                               value="<?= $popular_book['BOOK_AMOUNT'] ?>">
                        <input type="submit" value="В корзину"
                               name="add_to_cart"
                               class="btn <?= ($amount != 0) ? '' : 'disabled' ?>">
                    </div>
                </form>
                <?php
            }
        }
        else {
            echo '<p class="empty">Еще нет ни одной книги!</p>';
        }
        ?>
    </div>
    <div class="load-more" style="margin-top: 2rem; text-align:center">
        <a href="books.php" class="option-btn">Еще</a>
    </div>
</section>
<section class="about">
    <div class="flex">
        <div class="image">
            <img src="images/about-img.jpg" alt="">
        </div>
        <div class="content">
            <h3>о нас</h3>
            <p>БАЗА - это удобное решение для библиотек для работы с клиентами и
                автоматизации книжного учета</p>
            <a href="about.php" class="btn">Еще</a>
        </div>
    </div>
</section>
<section class="home-contact">
    <div class="content">
        <h3>есть вопросы?</h3>
        <p>Вы можете оставить отзыв или задать вопрос по форме связи ниже</p>
        <a href="contact.php" class="white-btn">связаться</a>
    </div>
</section>

<?php include 'footer.php'; ?>
<script src="js/script.js"></script>
</body>
</html>