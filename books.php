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
    <title>Книги</title>
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'header.php'; ?>

<div class="heading">
    <h3>Наши книги</h3>
    <p><a href="home.php">главная</a> / книги </p>
</div>

<section class="books">
    <h1 class="title">новинки</h1>
    <div class="box-container">
        <?php
        $select_books = mysqli_query($conn,
            "SELECT * FROM `books`") or die('query failed');
        if (mysqli_num_rows($select_books) > 0) {
            while ($fetch_books = mysqli_fetch_assoc($select_books)) {
                $author_by_id = getColFromTable($conn, 'authors', 'AUTH_ID', $fetch_books['AUTH_ID']);
                $amount = $fetch_books['BOOK_AMOUNT'];
                ?>
                <form action="" method="post" class="box">
                    <a href="details.php?id=<?= $fetch_books['BOOK_ID'] ?>">
                        <img class="image"
                             src="uploaded_img/<?= $fetch_books['BOOK_IMG'] ?>"
                             width="100%" alt=""></a>
                    <div class="desc">
                        <div class="name"><?= $fetch_books['BOOK_NAME'] ?></div>
                        <div class="amount">Новинка</div>
                        <div class="name"><?= $author_by_id['AUTH_NAME'] ?></div>
                        <div class="qty"><?= ($amount != 0) ? "В наличии: $amount шт." : 'Нет в наличии' ?> </div>
                        <input type="hidden" name="book_id"
                               value="<?= $fetch_books['BOOK_ID'] ?>">
                        <input type="hidden" name="book_amount"
                               value="<?= $fetch_books['BOOK_AMOUNT'] ?>">
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
</section>
<?php include 'footer.php'; ?>
<script src="js/script.js"></script>
</body>
</html>