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
    $message[] = addToCart($conn, $user_id, $book_id, $book_quantity,
        $book_amount);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Описание</title>
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'header.php'; ?>

<div class="heading">
    <h3>Описание книги</h3>
    <p><a href="home.php">главная</a> / описание </p>
</div>
<section class="description">
    <?php
    $book_by_id = getColFromTable($conn, 'books', 'BOOK_ID', $_GET['id']);
    if (isset($_GET['id']) && $book_by_id) {
        $pub_by_id = getColFromTable($conn, 'publishers', 'PUB_ID',
            $book_by_id['PUB_ID']);
        $author_by_id = getColFromTable($conn, 'authors', 'AUTH_ID',
            $book_by_id['AUTH_ID']);
        ?>
        <div class="left-column">
            <img class="img"
                 src="uploaded_img/<?= $book_by_id['BOOK_IMG'] . '?t=' . time() ?>"
                 height="350rem" width=100% alt="">
        </div>
        <div class="right-column">
            <div class="product-description">
                <form action="" method="post" enctype="multipart/form-data">
                    <h1><?= $book_by_id['BOOK_NAME'] ?></h1>
                    <span>Количество: </span><?= $book_by_id['BOOK_AMOUNT'] ?>
                    <p></p>
                    <span>Издательство: </span><?= $pub_by_id['PUB_NAME'] ?>
                    <p></p>
                    <span>Автор: </span><?= $author_by_id['AUTH_NAME'] ?>
                    <p></p>
                    <span>Год издания: </span><?= $book_by_id['RELEASE_YEAR'] ?>
                    <p></p>
                    <span>Возрастное ограничение: </span><?= $book_by_id['RATING'] ?>
                    <p></p>
                    <input type="hidden" name="book_id"
                           value="<?= $book_by_id['BOOK_ID'] ?>">
                    <input type="hidden" name="book_amount"
                           value="<?= $book_by_id['BOOK_AMOUNT'] ?>">
                    <input type="submit" value="В корзину" name="add_to_cart"
                           class="btn">
                </form>
            </div>
            <div class="product-configuration">
                <div class="tags-config">
                    <span>Метки</span>
                    <div class="tags">
                        <button>Tag</button>
                    </div>
                </div>
            </div>
        </div>
    <?php }
    else {
        ?><a href="books.php"><p class="empty">На главную</p></a><?php
    }
    ?>
</section>
<script src="js/script.js"></script>
</body>
</html>