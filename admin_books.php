<?php

include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:login.php');
};

if(isset($_POST['add_book'])){

   $name = mysqli_real_escape_string($conn, $_POST['name']);
   $amount = $_POST['amount'];
   $publisher = mysqli_real_escape_string($conn, $_POST['publisher']);
   $author = mysqli_real_escape_string($conn, $_POST['author']);

   $filename = $_FILES["image"]["name"];
   $tempname = $_FILES["image"]["tmp_name"];
   $folder = "C:\\Users\\urere\\Desktop\\Рабочий стол\\Четвертый курс\\ПППР\\study_project\\uploaded _img\\" . $filename;
   $image_size = $_FILES['image']['size'];
   
   $select_book_name = mysqli_query($conn, "SELECT BOOK_NAME FROM `books` WHERE BOOK_NAME = '$name'") or die('query failed');

   if(mysqli_num_rows($select_book_name) > 0){
        $existing_books = mysqli_query($conn, "SELECT BOOK_AMOUNT FROM `books` WHERE BOOK_NAME = '$name'") or die('query failed');
        $upd_amount = $amount+$existing_books->fetch_array()[0];
        mysqli_query($conn, "UPDATE `books` SET `BOOK_AMOUNT`='$upd_amount' WHERE BOOK_NAME = '$name'") or die('query failed');
   }else{
        $select_pub = mysqli_query($conn, "SELECT PUB_ID FROM `publishers` WHERE PUB_NAME = '$publisher'") or die('query failed');
        if (mysqli_num_rows($select_pub) > 0){
            $pub_id = $select_pub->fetch_array()[0];
        }else{
            mysqli_query($conn, "INSERT INTO `publishers`(PUB_NAME) VALUES('$publisher')") or die('query failed');
            $pub_id = mysqli_insert_id($conn);
        }
        $select_auth = mysqli_query($conn, "SELECT AUTH_ID FROM `authors` WHERE AUTH_NAME = '$author'") or die('query failed');
        if (mysqli_num_rows($select_auth) > 0){
            $auth_id = $select_auth->fetch_array()[0];
        }else{
            mysqli_query($conn, "INSERT INTO `authors`(AUTH_NAME) VALUES('$author')") or die('query failed');
            $auth_id = mysqli_insert_id($conn);
        }
        if($image_size > 2000000){
         $message[] = 'Размер файла слишком большой!';
        }else{
         $add_book_query = mysqli_query($conn, "INSERT INTO `books`(BOOK_NAME, BOOK_AMOUNT,PUB_ID, AUTH_ID, BOOK_IMG) VALUES('$name', '$amount',$pub_id, $auth_id, '$filename')") or die('query failed');
         if($add_book_query){
            move_uploaded_file($tempname, $folder);
            $message[] = 'Книга успешно добавлена!';
         }
      }
   }
}

if(isset($_GET['delete'])){
   $delete_id = $_GET['delete'];
   $delete_image_query = mysqli_query($conn, "SELECT BOOK_IMG FROM `books` WHERE BOOK_ID = '$delete_id'") or die('query failed');
   $fetch_delete_image = mysqli_fetch_assoc($delete_image_query);
   unlink('./uploaded_img/'.$fetch_delete_image['BOOK_IMG']);
   mysqli_query($conn, "DELETE FROM `books` WHERE BOOK_ID = '$delete_id'") or die('query failed');
   header('location:admin_books.php');
}

if(isset($_POST['update_book'])){

   $update_p_id = $_POST['update_p_id'];
   $update_name = $_POST['update_name'];
   $update_price = $_POST['update_price'];

   mysqli_query($conn, "UPDATE `books` SET BOOK_NAME = '$update_name', BOOK_AMOUNT = '$update_price' WHERE BOOK_ID = '$update_p_id'") or die('query failed');

   $update_image = $_FILES['update_image']['name'];
   $update_image_tmp_name = $_FILES['update_image']['tmp_name'];
   $update_image_size = $_FILES['update_image']['size'];
   $update_folder = './uploaded_img/'.$update_image;
   $update_old_image = $_POST['update_old_image'];

   if(!empty($update_image)){
      if($update_image_size > 2000000){
         $message[] = 'Слишком большой размер файла!';
      }else{
         mysqli_query($conn, "UPDATE `books` SET BOOK_IMG = '$update_image' WHERE BOOK_ID = '$update_p_id'") or die('query failed');
         move_uploaded_file($update_image_tmp_name, $update_folder);
         unlink('./uploaded_img/'.$update_old_image);
      }
   }

   header('location:admin_books.php');

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Книги</title>
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
   <link rel="stylesheet" href="css/admin_style.css">

</head>
<body>
   
<?php include 'admin_header.php'; ?>

<!-- book CRUD section starts  -->

<section class="add-books">

   <h1 class="title">Добавить книги</h1>
   
   <form action="" method="post" enctype="multipart/form-data">

      <input type="text" name="name" class="box" placeholder="Введите название книги" required>
      <input type="text" name="author" class="box" placeholder="Введите автора" required>
      <input type="text" name="publisher" class="box" placeholder="Введите издательство книги" required>
      <input type="number" min="0" name="amount" class="box" placeholder="Введите количество" required>
      <input type="number" min="0" name="year" class="box" placeholder="Введите год" required>
      <input type="file" name="image" accept="image/jpg, image/jpeg, image/png" class="box" required>
      <input type="submit" value="Добавить" name="add_book" class="btn">
   </form>

</section>

<!-- book CRUD section ends -->

<!-- show books  -->

<section class="show-books">

   <div class="box-container">

      <?php
         $select_books = mysqli_query($conn, "SELECT * FROM `books`") or die('query failed');
         if(mysqli_num_rows($select_books) > 0){
            while($fetch_books = mysqli_fetch_assoc($select_books)){
      ?>
      <div class="box">
         <img src="C:\\Users\\urere\\Desktop\\Рабочий стол\\Четвертый курс\\ПППР\\study_project\\uploaded _img\\<?php echo $fetch_books['BOOK_IMG']; ?>" alt="">
         <div class="name"><?php echo $fetch_books['BOOK_NAME']; ?></div>
         <div class="amount">Количество: <?php echo $fetch_books['BOOK_AMOUNT']; ?></div>
         <a href="admin_books.php?update=<?php echo $fetch_books['BOOK_ID']; ?>" class="option-btn">update</a>
         <a href="admin_books.php?delete=<?php echo $fetch_books['BOOK_ID']; ?>" class="delete-btn" onclick="return confirm('delete this book?');">delete</a>
      </div>
      <?php
         }
      }else{
         echo '<p class="empty">Еще нет ни одной книги!</p>';
      }
      ?>
   </div>

</section>

<section class="edit-book-form">

   <?php
      if(isset($_GET['update'])){
         $update_id = $_GET['update'];
         $update_query = mysqli_query($conn, "SELECT * FROM `books` WHERE BOOK_ID = '$update_id'") or die('query failed');
         if(mysqli_num_rows($update_query) > 0){
            while($fetch_update = mysqli_fetch_assoc($update_query)){
   ?>
   <form action="" method="post" enctype="multipart/form-data">
      <input type="hidden" name="update_p_id" value="<?php echo $fetch_update['BOOK_ID']; ?>">
      <input type="hidden" name="update_old_image" value="<?php echo $fetch_update['BOOK_IMG']; ?>">
      <img src="uploaded_img/<?php echo $fetch_update['BOOK_IMG']; ?>" alt="">
      <input type="text" name="update_name" value="<?php echo $fetch_update['BOOK_NAME']; ?>" class="box" required placeholder="enter book name">
      <input type="number" name="update_price" value="<?php echo $fetch_update['BOOK_AMOUNT']; ?>" min="0" class="box" required placeholder="enter book amount">
      <input type="file" class="box" name="update_image" accept="image/jpg, image/jpeg, image/png">
      <input type="submit" value="update" name="update_book" class="btn">
      <input type="reset" value="cancel" id="close-update" class="option-btn">
   </form>
   <?php
         }
      }
      }else{
         echo '<script>document.querySelector(".edit-book-form").style.display = "none";</script>';
      }
   ?>

</section>


<!-- custom admin js file link  -->
<script src="js/admin_script.js"></script>

</body>
</html>