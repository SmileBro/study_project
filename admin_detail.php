<?php

include 'get_function.php';
include 'config.php';

session_start();
$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:login.php');
};
function insertPublisherIfNeeded($conn, $publisher)
{
    $select_pub = mysqli_query($conn, "SELECT PUB_ID FROM `publishers` WHERE PUB_NAME = '$publisher'");
    if (mysqli_num_rows($select_pub) > 0) {
        return $select_pub->fetch_array()[0];
    } else {
        mysqli_query($conn, "INSERT INTO `publishers`(PUB_NAME) VALUES('$publisher')");
        return mysqli_insert_id($conn);
    }
}

function insertAuthorIfNeeded($conn, $author)
{
    $select_auth = mysqli_query($conn, "SELECT AUTH_ID FROM `authors` WHERE AUTH_NAME = '$author'");
    if (mysqli_num_rows($select_auth) > 0) {
        return $select_auth->fetch_array()[0];
    } else {
        mysqli_query($conn, "INSERT INTO `authors`(AUTH_NAME) VALUES('$author')");
        return mysqli_insert_id($conn);
    }
}

if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $delete_image_query = mysqli_query($conn, "SELECT BOOK_IMG FROM `books` WHERE BOOK_ID = '$delete_id'") or die('query failed');
    $fetch_delete_image = mysqli_fetch_assoc($delete_image_query);
    unlink($dest . $fetch_delete_image['BOOK_IMG']);
    mysqli_query($conn, "DELETE FROM `books` WHERE BOOK_ID = '$delete_id'") or die('query failed');
    header('location:admin_books.php');
}

if (isset($_POST['update_book'])) {
    $update_p_id = $_POST['update_p_id'];
    $update_name = mysqli_real_escape_string($conn, $_POST['update_name']);
    $update_amount = $_POST['update_amount'];
    $update_auth_name = mysqli_real_escape_string($conn, $_POST['update_auth']);
    $update_auth = insertAuthorIfNeeded($conn, $update_auth_name);
    $update_pub_name = mysqli_real_escape_string($conn, $_POST['update_pub']);
    $update_pub = insertPublisherIfNeeded($conn, $update_pub_name);
    $update_year = $_POST['update_year'];
    $update_rating = mysqli_real_escape_string($conn, $_POST['update_rating']);
    mysqli_query($conn, "UPDATE `books` SET BOOK_NAME = '$update_name', BOOK_AMOUNT = '$update_amount', PUB_ID = '$update_pub', AUTH_ID = '$update_auth', RELEASE_YEAR = '$update_year', RATING = '$update_rating' WHERE BOOK_ID = '$update_p_id'") or die('query failed');
    

    $temp = explode(".", $_FILES["update_image"]["name"]);
    $image_size = $_FILES['update_image']['size'];

    $newfilename = $_POST['update_old_image'];
    move_uploaded_file($_FILES["update_image"]["tmp_name"], $dest . $newfilename);
    $message[] = 'Книга успешно изменена!';

    header('location:admin_books.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Описание</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/admin_style.css">

</head>
<body>
<?php include 'admin_header.php'; ?>

<div class="heading">
   <h3>Описание книги</h3>
   <p> <a href="admin_page.php">главная</a> / описание </p>
</div>
<section class="description">
<?php
      $fetch_books = GetBookById($conn, $_GET['id']);
      if (isset($_GET['id']) && $fetch_books) {
        $fetch_pub = GetPublisherById($conn, $fetch_books['PUB_ID']);
        $fetch_author = GetAuthorById($conn, $fetch_books['AUTH_ID']);
   ?>     
  <!-- Left Column / Headphones Image -->
  <div class="left-column">
  <img class="img" src="uploaded_img/<?= $fetch_books['BOOK_IMG'].'?t='. time() ?>" height= "350rem" width=100%  alt=""></a>
  </div>
 
 
  <!-- Right Column -->
  <div class="right-column">
 
    <!-- Product Description -->
    <div class="product-description">
    <form action="" method="post" enctype="multipart/form-data">
    <input type="hidden" name="update_p_id" value="<?= $fetch_books['BOOK_ID'] ?>">
    <input type="hidden" name="update_old_image" value="<?= $fetch_books['BOOK_IMG'] ?>">

    <input type="text" name="update_name" value="<?= $fetch_books['BOOK_NAME'] ?>" class="box" required placeholder="Введите новое название">
    <input type="number" name="update_amount" value="<?= $fetch_books['BOOK_AMOUNT'] ?>" min="0" class="box" required placeholder="Введите новое количество">
    <input type="text" name="update_pub" value="<?= $fetch_pub['PUB_NAME'] ?>" class="box" required placeholder="Введите название издательства">
    <input type="text" name="update_auth" value="<?= $fetch_author['AUTH_NAME'] ?>" class="box" required placeholder="Введите автора">
    <input type="number" name="update_year" value="<?= $fetch_books['RELEASE_YEAR'] ?>" min="0" class="box" required placeholder="Введите год издания">
    <input type="text" name="update_rating" value="<?= $fetch_books['RATING'] ?>" class="box" placeholder="Введите возрастное ограничение">
    <input type="file" class="box" name="update_image" accept="image/jpg, image/jpeg, image/png">
    <input type="submit" value="Изменить" name="update_book" class="btn">
    <a href="admin_detail.php?delete=<?= $fetch_books['BOOK_ID'] ?>" class="delete-btn" onclick="return confirm('Удалить эту книгу?');">Удалить книгу</a>

    </form>
    </div>
 
    <!--Book description -->
    <div class="product-configuration">
 
      <!-- Tags -->
      <div class="cable-config">
        <span>Tags</span>
 
        <div class="tags">
          <button>Tag</button>
        </div>
      </div>
    </div>
  </div>
  <?php }else{

        ?><a href="admin_detail.php?id=<?=$fetch_books['BOOK_ID']?>">'<p class="empty">На главную</p>';</a><?php
      }?>
      
<!-- custom admin js file link  -->
<script src="js/admin_script.js"></script>
    </section>
    </body>