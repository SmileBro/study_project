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

if (isset($_POST['update_book'])) {
    $upd_book_id = $_POST['upd_book_id'];
    $upd_book_name = $_POST['upd_book_name'];
    $upd_book_amount = $_POST['upd_book_amount'];
    $message[] = updateBook($conn, $upd_book_id, $upd_book_name,
        $upd_book_amount, NULL, NULL, NULL, NULL, $_FILES["update_image"],
        $dest, $_POST['update_old_image']);
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

    <!-- font awesome cdn link  -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- custom css file link  -->
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
                            <img class="book_img"
                                 src="uploaded_img/<?= $fetch_books['BOOK_IMG'] . '?t=' . time() ?>"
                                 height="350rem" width=100%
                                 alt=""></a>
                        <div class="name"><?= $fetch_books['BOOK_NAME'] ?></div>
                        <div class="amount">
                            Количество: <?= $fetch_books['BOOK_AMOUNT'] ?></div>
                        <a href="admin_search_page.php?update=<?= $fetch_books['BOOK_ID'] ?>"
                           class="option-btn">Изменить</a>
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


<section class="edit-form">
    <?php
    if (isset($_GET['update'])) {
        $update_id = $_GET['update'];
        $update_query = mysqli_query($conn,
            "SELECT * FROM `books` WHERE BOOK_ID = '$update_id'") or die('query failed');
        if (mysqli_num_rows($update_query) > 0) {
            while ($fetch_update = mysqli_fetch_assoc($update_query)) {
                ?>
                <form action="" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="upd_book_id"
                           value="<?= $fetch_update['BOOK_ID'] ?>">
                    <input type="hidden" name="update_old_image"
                           value="<?= $fetch_update['BOOK_IMG'] ?>">
                    <img class="book_img"
                         src="uploaded_img/<?= $fetch_update['BOOK_IMG'] ?>"
                         alt="">
                    <input type="text" name="upd_book_name"
                           value="<?= $fetch_update['BOOK_NAME'] ?>" class="box"
                           required placeholder="Введите новое название">
                    <input type="number" name="upd_book_amount"
                           value="<?= $fetch_update['BOOK_AMOUNT'] ?>" min="0"
                           class="box" required
                           placeholder="Введите новое количество">
                    <input type="file" class="box" name="update_image"
                           accept="image/jpg, image/jpeg, image/png">

                    <input type="submit" value="Изменить" name="update_book"
                           class="btn">
                    <p></p>

                    <a href="admin_search_page.php" id="close-update"
                       class="option-btn">Отменить</a>
                </form>
                <?php
            }
        }
    }
    else {
        echo '<script>document.querySelector(".edit-form").style.display = "none";</script>';
    }
    ?>

</section>

<!-- custom admin js file link  -->
<script src="js/admin_script.js"></script>


</body>
</html>