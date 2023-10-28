<?php

include 'config.php';
include 'get_function.php';
session_start();
$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location:login.php');
};

if (isset($_POST['add_to_cart'])) {
    $book_id = $_POST['book_id'];
    $book_amount = $_POST['book_amount'];
    $book_quantity = 1;
    if ($book_quantity > $book_amount) {
        $message[] = 'Невозможно забронировать такое количество книг!';
    }
    else {
        mysqli_query($conn,
            "INSERT INTO `cart`(USER_ID, BOOK_ID, BOOK_AMOUNT) VALUES('$user_id', '$book_id', '$book_quantity')") or die('query failed');
        $message[] = 'Книга добавлена в корзину!';
    }
};
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>search page</title>
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <script src="js/search.js"></script>
    
</head>
<body>

<?php include 'header.php'; ?>

<div class="heading">
    <h3>страница поиска</h3>
    <p><a href="home.php">главная</a> / поиск </p>
</div>
<section class="search-form">
    <form action="" method="post">
        <input type="text" name="search" id="dName" placeholder="начать поиск..."
               class="box" style="width: 100%" >
            <script>
            ac.attach({
            target: document.getElementById("dName"),
            data: "2b-search.php"
            }); 
            </script>
        <input type="submit" name="submit" value="поиск" class="btn">
    </form>
</section>
<section class="books" style="padding-top: 0;">
    <div class="box-container">
        <?php
        if (isset($_POST['submit'])) {
            $search_item = $_POST['search'];
            $select_books = mysqli_query($conn,
                "SELECT * FROM `books` WHERE BOOK_NAME LIKE '%{$search_item}%'") or die('query failed');
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
                            <div class="amount"><?= $amount ?> шт.</div>
                            <div class="name"><?= $author_by_id['AUTH_NAME'] ?></div>
                            <input type="hidden" name="book_id"
                                   value="<?= $fetch_books['BOOK_ID'] ?>">
                            <input type="hidden" name="book_name"
                                   value="<?= $fetch_books['BOOK_NAME'] ?>">
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
                echo '<p class="empty">Ничего не нашлось!</p>';
            }
        }
        else {
            echo '<p class="empty">Поищите что-нибудь!</p>';
        }
        ?>
    </div>
</section>

<?php include 'footer.php'; ?>
<script src="js/script.js"></script>
</body>
</html>