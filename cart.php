<?php

include 'config.php';
include 'get_function.php';
session_start();

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:login.php');
}

//Т.к. выдаем по одному экземпляру книги уже не нужна функция изменения
//количества книг в заказе
/*if(isset($_POST['update_cart'])){
   $cart_id = $_POST['cart_id'];
   $cart_quantity = $_POST['cart_quantity'];
   mysqli_query($conn, "UPDATE `cart` SET quantity = '$cart_quantity' WHERE id = '$cart_id'") or die('query failed');
   $message[] = 'cart quantity updated!';
}*/

if(isset($_GET['delete'])){
   $delete_id = $_GET['delete'];
   mysqli_query($conn, "DELETE FROM `cart` WHERE CART_ID = '$delete_id'") or die('query failed');
   header('location:cart.php');
}

if(isset($_GET['delete_all'])){
   mysqli_query($conn, "DELETE FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
   header('location:cart.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>cart</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'header.php'; ?>

<div class="heading">
   <h3>shopping cart</h3>
   <p> <a href="home.php">home</a> / cart </p>
</div>

<section class="shopping-cart">

   <h1 class="title">products added</h1>

   <div class="box-container">
      <?php
         $grand_total = 0;
         $select_cart = mysqli_query($conn, "SELECT * FROM `cart` WHERE USER_ID = '$user_id'") or die('query failed');
         if(mysqli_num_rows($select_cart) > 0){
            while($fetch_cart = mysqli_fetch_assoc($select_cart)){
                $book = GetBookById($conn, $fetch_cart['BOOK_ID']);
      ?>
      <div class="box">
         <a href="cart.php?delete=<?php echo $fetch_cart['CART_ID']; ?>" class="fas fa-times" onclick="return confirm('delete this from cart?');"></a>
         <img src="uploaded_img/<?php echo $book['BOOK_IMG']; ?>" alt="">
         <div class="name"><?php echo $book['BOOK_NAME']; ?></div>
         
      </div>
      <?php
      $sub_total = ($fetch_cart['BOOK_AMOUNT']);
      $grand_total += $sub_total;
         }
      }else{
         echo '<p class="empty">Ваша корзина пуста</p>';
      }
      ?>
   </div>

   <div style="margin-top: 2rem; text-align:center;">
      <a href="cart.php?delete_all" class="delete-btn <?php echo ($grand_total > 1)?'':'disabled'; ?>" onclick="return confirm('Удалить все предметы из корзины?');">Удалить все</a>
   </div>

   <div class="cart-total">
      <p>Всего книг : <span><?php echo $grand_total; ?></span></p>
      <div class="flex">
         <a href="shop.php" class="option-btn">Продолжить поиск</a>
         <a href="checkout.php" class="btn <?php echo ($grand_total > 0)?'':'disabled'; ?>">Заказать</a>
      </div>
   </div>

</section>








<?php include 'footer.php'; ?>

<!-- custom js file link  -->
<script src="js/script.js"></script>

</body>
</html>