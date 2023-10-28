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
    $message[] = updateBook($conn, $_POST, $_FILES["update_image"], $dest);
}

if (isset($_POST['tag_input'])) {
    echo var_dump($_POST);
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
    <script src=
"https://code.jquery.com/jquery-3.6.0.min.js">
    </script>
    <script> 



// Function to create the cookie 
function createCookie(name, value, days) { 
	var expires; 
	
	if (days) { 
		var date = new Date(); 
		date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000)); 
		expires = "; expires=" + date.toGMTString(); 
	} 
	else { 
		expires = ""; 
	} 
	
	document.cookie = escape(name) + "=" + 
		escape(value) + expires + "; path=/"; 
} 

</script>
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
                    <form id="changeTagsForm" action="" method="post" enctype="multipart/form-data">
                    <div class="changeTags">
                    <div class="changeTagBtn">+</div>
                    <input type="text" placeholder="Тег" name="tag_input" id ="tag_input">
                    </div>
                    </form>
                    <script type="text/javascript">
                    //Shows Input Box When Focussed
                    $(".changeTagBtn").click(function() {
                      var neww = $(".changeTags input").css("width");
                      $(this).animate({
                        width: neww
                      }, 300, function() {
                        $(".changeTags input").fadeIn(300, function() {
                          $(".changeTagBtn").hide();
                        }).focus();
                      });
                    });

                    //Shows Button When Unfocussed
                    $(".changeTags input").blur(function() {
                        const XHR = new XMLHttpRequest();
                        XHR.setRequestHeader(
                        "Content-Type",
                        `multipart/form-data; boundary=${boundary}`,
                        );
                        XHR.open("POST", window.location.href+"?tag_input="+document.getElementById('tag_input').value);
                        XHR.send(document.getElementById('tag_input').value);
                      $(".changeTagBtn").css("width", "120px");
                      var neww = $(".changeTagBtn").css("width");
                      $(this).animate({
                        width: neww
                      }, 300, function() {
                        $(".changeTagBtn").show(0, function() {
                          $(".changeTags input").fadeOut(500, function() {
                            $(".changeTags input").css("width", "auto");
                          });
                        });
                      });
                    });
                    </script>
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