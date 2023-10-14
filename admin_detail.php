<?php

include 'config.php';
include 'get_function.php';
session_start();
$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:login.php');
}

if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    deleteBook($conn, $delete_id, $dest);
    header('location:admin_detail.php');
}

if (isset($_POST['update_book'])) {
    $upd_book_id = $_POST['upd_book_id'];
    $upd_book_release_year = $_POST['upd_book_release_year'];
    $upd_book_amount = $_POST['upd_book_amount'];

    $upd_book_name = $_POST['upd_book_name'];
    $upd_rating = $_POST['update_rating'];
    $upd_auth_name = $_POST['upd_auth_name'];
    $upd_pub_name = $_POST['upd_pub_name'];

    $message[] = updateBook($conn, $upd_book_id, $upd_book_name,
        $upd_book_amount, $upd_book_release_year, $upd_rating, $upd_auth_name,
        $upd_pub_name, $_FILES["update_image"], $dest,
        $_POST['update_old_image']);
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
    <link rel="stylesheet" href="css/admin_style.css">
</head>
<body>

<?php include 'admin_header.php'; ?>

<div class="heading">
    <h3>Описание книги</h3>
    <p><a href="admin_page.php">главная</a> / описание </p>
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
                    <input type="hidden" name="upd_book_id"
                           value="<?= $book_by_id['BOOK_ID'] ?>">
                    <input type="hidden" name="update_old_image"
                           value="<?= $book_by_id['BOOK_IMG'] ?>">
                    <input type="text" name="upd_book_name"
                           value="<?= $book_by_id['BOOK_NAME'] ?>"
                           class="box"
                           required placeholder="Введите новое название">
                    <input type="number" name="upd_book_amount"
                           value="<?= $book_by_id['BOOK_AMOUNT'] ?>" min="0"
                           class="box" required
                           placeholder="Введите новое количество">
                    <input type="text" name="upd_pub_name"
                           value="<?= $pub_by_id['PUB_NAME'] ?>" class="box"
                           required
                           placeholder="Введите название издательства">
                    <input type="text" name="upd_auth_name"
                           value="<?= $author_by_id['AUTH_NAME'] ?>"
                           class="box"
                           required placeholder="Введите автора">
                    <input type="number" name="upd_book_release_year"
                           value="<?= $book_by_id['RELEASE_YEAR'] ?>"
                           min="0"
                           class="box" required
                           placeholder="Введите год издания">
                    <input type="text" name="update_rating"
                           value="<?= $book_by_id['RATING'] ?>" class="box"
                           placeholder="Введите возрастное ограничение">
                    <input type="file" class="box" name="update_image"
                           accept="image/jpg, image/jpeg, image/png">
                    <input type="submit" value="Изменить" name="update_book"
                           class="btn">
                    <a href="admin_detail.php?delete=<?= $book_by_id['BOOK_ID'] ?>"
                       class="delete-btn"
                       onclick="return confirm('Удалить эту книгу?');">Удалить книгу</a>
                </form>
            </div>
            <div class="product-configuration">
                <div class="cable-config">
                    <span>Метки</span>
                    <div class="tags">
                        <button>Tag</button>
                    </div>
                </div>
            </div>
        </div>
    <?php }
    else {
        ?><a href="admin_books.php"><p class="empty">На главную</p></a><?php
    }
    ?>
</section>
<script src="js/admin_script.js"></script>
</body>
</html>