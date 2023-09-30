<?php

include 'config.php';
include 'get_function.php';

session_start();

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:login.php');
}
function uniquePost($posted)
{
    // Define an array of form fields that you want to include in the description
    $formFields = ['name', 'number', 'email'];

    // Initialize an array to store the values of the form fields
    $formValues = [];

    // Collect the form field values into the $formValues array
    foreach ($formFields as $field) {
        if (isset($_POST[$field])) {
            $formValues[] = $_POST[$field];
        }
    }

    // Combine the form field values into a single string
    $description = implode('', $formValues);
    $description = $description.strtotime("now");

    // check if session hash matches current form hash
    if (isset($_SESSION['form_hash']) && $_SESSION['form_hash'] == md5($description)) {
        // form was re-submitted return false
        return false;
    }
    // set the session value to prevent re-submit
    $_SESSION['form_hash'] = md5($description);
    return true;
}
//&& uniquePost($_POST)
if(isset($_POST['order_btn']) ){

   $name = mysqli_real_escape_string($conn, $_POST['name']);
   $number = $_POST['number'];
   $email = mysqli_real_escape_string($conn, $_POST['email']);
   $placed_on = date('d-M-Y');
   $due_date = date('d-M-Y', strtotime($placed_on. ' + 14 days'));
   $worker_id = 0;

   $cart_total = 0;
   $cart_products[] = '';

   $cart_query = mysqli_query($conn, "SELECT * FROM `cart` WHERE USER_ID = '$user_id'") or die('query failed');
   if(mysqli_num_rows($cart_query) > 0){
      while($cart_item = mysqli_fetch_assoc($cart_query)){
         $book_id = $cart_item['BOOK_ID'];
         $book = GetBookById($conn, $book_id);
         $cart_products[] = $book['BOOK_NAME'].' ('.$cart_item['BOOK_AMOUNT'].') ';

         #$sub_total = ($cart_item['price'] * $cart_item['quantity']);
         #$cart_total += $sub_total;

         mysqli_query($conn, "INSERT INTO `leases`(USER_ID, BOOK_ID, WORKER_ID, LEASE_START, LEASE_DUE, LEASE_STATUS) VALUES('$user_id', '$book_id', '$worker_id', '$placed_on', '$due_date', 'pending')") or die('query failed');
         $message[] = 'order placed successfully!';

         mysqli_query($conn, "DELETE FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
      }
   }else{
      $message[] = 'your cart is empty';
   }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>checkout</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'header.php'; ?>

<div class="heading">
   <h3>checkout</h3>
   <p> <a href="home.php">home</a> / checkout </p>
</div>

<section class="display-order">

   <?php  
      $grand_total = 0;
      $select_cart = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
      if(mysqli_num_rows($select_cart) > 0){
         while($fetch_cart = mysqli_fetch_assoc($select_cart)){
            $grand_total += $fetch_cart['BOOK_AMOUNT'];
   ?>
   <p> <?php $book = GetBookById($conn, $fetch_cart['BOOK_ID']);
    echo $book['BOOK_NAME']; ?> <span>(<?php echo $fetch_cart['BOOK_AMOUNT']; ?>)</span> </p>
   <?php
      }
   }else{
      echo '<p class="empty">your cart is empty</p>';
   }
   ?>
   <div class="grand-total"> grand total : <span><?php echo $grand_total; ?></span> </div>

</section>

<section class="checkout">

   <form action="" method="post">
      <h3>place your order</h3>
      <div class="flex">
         <div class="inputBox">
            <span>your name :</span>
            <input type="text" name="name" required placeholder="enter your name">
         </div>
         <div class="inputBox">
            <span>your number :</span>
            <input type="number" name="number" required placeholder="enter your number">
         </div>
         <div class="inputBox">
            <span>your email :</span>
            <input type="email" name="email" required placeholder="enter your email">
         </div>
         </div>
      </div>
      <input type="submit" value="order now" class="btn" name="order_btn">
   </form>

</section>

<?php include 'footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>