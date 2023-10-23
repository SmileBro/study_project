<?php

include 'config.php';
session_start();
$admin_id = $_SESSION['admin_id'];
if (!isset($admin_id)) {
    header('location:login.php');
};

if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    deleteBook($conn, $delete_id, $dest);
    header('location:admin_search_page.php');
}
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
    <link rel="stylesheet" href="css/admin_style.css">
</head>
<body>

<?php include 'admin_header.php'; ?>

<div class="heading">
    <h3>страница поиска</h3>
    <p><a href="admin_page.php">главная</a> / поиск </p>
</div>
<section class="search-form">
    <form action="" method="post">
        <input type="text" name="search" placeholder="начать поиск..."
               class="box">
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
                    ?>
                    <div class="box">
                        <a href="admin_detail.php?id=<?= $fetch_books['BOOK_ID'] ?>">
                            <img class="image"
                                 src="uploaded_img/<?= $fetch_books['BOOK_IMG'] . '?t=' . time() ?>"
                                 width=100%
                                 alt=""></a>
                        <div class="name"><?= $fetch_books['BOOK_NAME'] ?></div>
                        <div class="qty">
                            Количество: <?= $fetch_books['BOOK_AMOUNT'] ?></div>
                        <a href="admin_search_page.php?delete=<?= $fetch_books['BOOK_ID'] ?>"
                           class="delete-btn"
                           onclick="return confirm('Удалить эту книгу?');">Удалить</a>
                    </div>
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
<script src="js/admin_script.js"></script>
</body>
</html>