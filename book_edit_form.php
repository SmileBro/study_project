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
                    <input type="reset" value="Отменить" id="close-update" class="option-btn">
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