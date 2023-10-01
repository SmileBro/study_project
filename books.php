<?php

include 'config.php';
include 'get_function.php';
session_start();

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:login.php');
}

if (isset($_POST['add_to_cart'])) {
    $book_id = $_POST['book_id'];
    $book_amount = $_POST['book_amount'];
    $book_quantity = 1;

    if ($book_quantity > $book_amount) {
        $message[] = 'Книги нет в наличии!';
    } else {
        $is_book_in_the_cart = mysqli_query($conn, "SELECT * FROM `cart` WHERE USER_ID = '$user_id' AND BOOK_ID = '$book_id'") or die('query failed');
        if (mysqli_num_rows($is_book_in_the_cart) > 0){
            $message[] = 'Книга уже в корзине!';
        }else{
            mysqli_query($conn, "INSERT INTO `cart`(USER_ID, BOOK_ID, BOOK_AMOUNT) VALUES('$user_id', '$book_id', '$book_quantity')") or die('query failed');
            $message[] = 'Книга добавлена в корзину!';
        }
   }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Книги</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'header.php'; ?>

<div class="heading">
   <h3>Наши книги</h3>
   <p> <a href="home.php">главная</a> / книги </p>
</div>

<section class="books">

   <h1 class="title">новинки</h1>

   <div class="box-container">

      <?php  
         $select_books = mysqli_query($conn, "SELECT * FROM `books`") or die('query failed');
         if(mysqli_num_rows($select_books) > 0){
            while($fetch_books = mysqli_fetch_assoc($select_books)){
      ?>
     <form action="" method="post" class="box">
      <img class="image" src="uploaded_img/<?php echo $fetch_books['BOOK_IMG']; ?>" width="100%" alt="">
      <div class="name"><?php echo $fetch_books['BOOK_NAME']; ?></div>
      <div class="name"><?php $author = GetAuthorById($conn, $fetch_books['AUTH_ID']);
      echo $author['AUTH_NAME']; ?></div>

      <div class="qty">Кол-во: <?php echo $fetch_books['BOOK_AMOUNT']; ?></div>
      
      <input type="hidden" name="book_id" value="<?php echo $fetch_books['BOOK_ID']; ?>">
      <input type="hidden" name="book_amount" value="<?php echo $fetch_books['BOOK_AMOUNT']; ?>">
      <input type="submit" value="В корзину" name="add_to_cart" class="btn">
     </form>
      <?php
         }
      }else{
         echo '<p class="empty">Еще нет ни одной книги!</p>';
      }
      ?>
   </div>

</section>








<?php include 'footer.php'; ?>

<!-- custom js file link  -->
<script src="js/script.js"></script>

</body>
</html>