<?php

include 'config.php';
include 'get_function.php';
session_start();

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:login.php');
};

if(isset($_POST['add_to_cart'])){

    $book_id = $_POST['book_id'];
    $book_amount = $_POST['book_amount'];
    $book_quantity = 1;

    if ($book_quantity > $book_amount) {
        $message[] = 'Невозможно забронировать такое количество книг!';
    } else {
        mysqli_query($conn, "INSERT INTO `cart`(USER_ID, BOOK_ID, BOOK_AMOUNT) VALUES('$user_id', '$book_id', '$book_quantity')") or die('query failed');
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

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'header.php'; ?>

<div class="heading">
   <h3>search page</h3>
   <p> <a href="home.php">home</a> / search </p>
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
            $fetch_author = GetAuthorById($conn, $fetch_books['AUTH_ID']);
   ?>
        <form action="" method="post" class="box">
            <a href="details.php?id=<?=$fetch_books['BOOK_ID']?>">
            <img class="image" src="uploaded_img/<?php echo $fetch_books['BOOK_IMG']; ?>" width="100%" alt=""></a>
            <div class="name"><?php echo $fetch_books['BOOK_NAME']; ?></div>
            <div class="amount"><?php echo $fetch_books['BOOK_AMOUNT']; ?></div>
            <div class="name"><?php echo $fetch_author['AUTH_NAME']; ?></div>
            <input type="hidden" name="book_id" value="<?php echo $fetch_books['BOOK_ID']; ?>">
            <input type="hidden" name="book_name" value="<?php echo $fetch_books['BOOK_NAME']; ?>">
            <input type="hidden" name="book_amount" value="<?php echo $fetch_books['BOOK_AMOUNT']; ?>">
            <input type="submit" value="В корзину" name="add_to_cart" class="btn">
        </form>
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









<?php include 'footer.php'; ?>

<!-- custom js file link  -->
<script src="js/script.js"></script>

</body>
</html>