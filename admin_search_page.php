<?php

include 'config.php';
session_start();
$admin_id = $_SESSION['admin_id'];
if (!isset($admin_id)) {
    header('location:login.php');
};

if (isset($_GET['delete'])) {
   $delete_id = $_GET['delete'];
   $delete_image_query = mysqli_query($conn, "SELECT BOOK_IMG FROM `books` WHERE BOOK_ID = '$delete_id'") or die('query failed');
   $fetch_delete_image = mysqli_fetch_assoc($delete_image_query);
   unlink('$dest . $fetch_delete_image['BOOK_IMG']);
   mysqli_query($conn, "DELETE FROM `books` WHERE BOOK_ID = '$delete_id'") or die('query failed');
   header('location:admin_search_page.php');
}

if (isset($_POST['update_book'])) {
   $update_p_id = $_POST['update_p_id'];
   $update_name = $_POST['update_name'];
   $update_amount = $_POST['update_amount'];

   mysqli_query($conn, "UPDATE `books` SET BOOK_NAME = '$update_name', BOOK_AMOUNT = '$update_amount' WHERE BOOK_ID = '$update_p_id'") or die('query failed');

   $temp = explode(".", $_FILES["update_image"]["name"]);
   $image_size = $_FILES['update_image']['size'];

   $newfilename = $_POST['update_old_image'];
   move_uploaded_file($_FILES["update_image"]["tmp_name"], $dest . $newfilename);
   $message[] = 'Книга успешно изменена!';

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
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/admin_style.css">

</head>
<body>
   
<?php include 'admin_header.php'; ?>

<div class="heading">
   <h3>search page</h3>
   <p> <a href="admin_page.php">home</a> / search </p>
</div>

<section class="search-form">
   <form action="" method="post">
      <input type="text" name="search" placeholder="search books..." class="box">
      <input type="submit" name="submit" value="search" class="btn">
      
   </form>
</section>

<section class="books" style="padding-top: 0;">

   <div class="box-container">
   <?php
      if(isset($_POST['submit'])){
         $search_item = $_POST['search'];
         $select_books = mysqli_query($conn, "SELECT * FROM `books` WHERE BOOK_NAME LIKE '%{$search_item}%'") or die('query failed');
         if(mysqli_num_rows($select_books) > 0){
         while($fetch_books = mysqli_fetch_assoc($select_books)){
   ?>
        <div class="box">
                    <img class="book_img" src="uploaded_img/<?= $fetch_books['BOOK_IMG'].'?t='. time() ?>" height= "350rem" width=100% 
                         alt="">
                    <div class="name"><?= $fetch_books['BOOK_NAME'] ?></div>
                    <div class="amount">Количество: <?= $fetch_books['BOOK_AMOUNT'] ?></div>
                    <a href="admin_search_page.php?update=<?= $fetch_books['BOOK_ID'] ?>" class="option-btn">Изменить</a>
                    <a href="admin_search_page.php?delete=<?= $fetch_books['BOOK_ID'] ?>" class="delete-btn"
                       onclick="return confirm('delete this book?');">Удалить</a>
                </div>
   <?php
            }
         }else{
            echo '<p class="empty">no result found!</p>';
         }
      }else{
         echo '<p class="empty">search something!</p>';
      }
   ?>
   </div>
  

</section>


<section class="edit-form">
    <?php
    if (isset($_GET['update'])) {
        $update_id = $_GET['update'];
        $update_query = mysqli_query($conn, "SELECT * FROM `books` WHERE BOOK_ID = '$update_id'") or die('query failed');
        if (mysqli_num_rows($update_query) > 0) {
            while ($fetch_update = mysqli_fetch_assoc($update_query)) {
                ?>
                <form action="" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="update_p_id" value="<?= $fetch_update['BOOK_ID'] ?>">
                    <input type="hidden" name="update_old_image" value="<?= $fetch_update['BOOK_IMG'] ?>">
                    <img class="book_img" src="uploaded_img/<?= $fetch_update['BOOK_IMG'] ?>" alt="">
                    <input type="text" name="update_name" value="<?= $fetch_update['BOOK_NAME'] ?>" class="box"
                           required placeholder="Введите новое название">
                    <input type="number" name="update_amount" value="<?= $fetch_update['BOOK_AMOUNT'] ?>" min="0" class="box" required placeholder="Введите новое количество">
                    <input type="file" class="box" name="update_image" accept="image/jpg, image/jpeg, image/png">
                    
                    <input type="submit" value="Изменить" name="update_book" class="btn">
                    <p></p>
                    <input type="reset" value="Отменить" id="close-update" class="option-btn">
                </form>
                <?php
            }
        }
    } else {
        echo '<script>document.querySelector(".edit-form").style.display = "none";</script>';
    }
    ?>
    
</section>

<!-- custom admin js file link  -->
<script src="js/admin_script.js"></script>


</body>
</html>