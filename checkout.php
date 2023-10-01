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
   
   $email = mysqli_real_escape_string($conn, $_POST['email']);
   $placed_on = date('d-M-Y');
   $due_date = date('d-M-Y', strtotime($placed_on. ' + 14 days'));
   $worker_id = 0;

   $cart_total = 0;
   

   $cart_query = mysqli_query($conn, "SELECT * FROM `cart` WHERE USER_ID = '$user_id'") or die('query failed');
   if(mysqli_num_rows($cart_query) > 0){
      while($cart_item = mysqli_fetch_assoc($cart_query)){
         $book_id = $cart_item['BOOK_ID'];
         $book = GetBookById($conn, $book_id);
         
         //Проверка на наличие книги
         $book_amount = $book['BOOK_AMOUNT'];
         if ($book_amount < 1){
            $message[] = 'В данный момент книга отсутствует.';
         }else{
         //Запись данных о выдаче в БД
         mysqli_query($conn, "INSERT INTO `leases`(USER_ID, BOOK_ID, WORKER_ID, LEASE_START, LEASE_DUE, LEASE_STATUS) VALUES('$user_id', '$book_id', '$worker_id', '$placed_on', '$due_date', 'Active')") or die('query failed');
         

         //Обновление количества книг в БД при заказе
         $new_amount = $book_amount-1;
         $update_lease_query = mysqli_query($conn, "UPDATE `books` SET BOOK_AMOUNT = '$new_amount' WHERE BOOK_ID = '$book_id'") or die('query failed');
         
         //Удаление предметов из корзины пользователя
         mysqli_query($conn, "DELETE FROM `cart` WHERE user_id = '$user_id'") or die('query failed');

         
      }
   }
   $message[] = 'Заказ успешно сформирован!';
   }else{
      $message[] = 'Ваша корзина пуста!';
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
    echo $book['BOOK_NAME']; ?> </p>
   <?php
      }
   }else{
      echo '<p class="empty">your cart is empty</p>';
   }
   ?>
   <div class="grand-total"> Всего книг : <span><?php echo $grand_total; ?></span> </div>

</section>

<section class="checkout">
   <?php
      $user = GetUserById($conn, $_SESSION['user_id']);
   ?>
   <form action="" method="post">
      <h3>оформить заказ</h3>
      <div class="flex">
         <div class="inputBox">
            <span>Ваше имя :</span>
            <input type="text" name="name" value = "<?php echo $user['USER_NAME']?>"
            required placeholder="Введите ваше имя">
         </div>
         <div class="inputBox">
            <span>Ваш номер :</span>
            <input type="number" name="number" value = "<?php echo $user['USER_PHONE']?>"
            required placeholder="Введите ваш номер">
         </div>
         <div class="inputBox">
            <span>Ваш email :</span>
            <input type="email" name="email" value = "<?php echo $user['USER_MAIL']?>"
            required placeholder="Введите ваш email">
         </div>
         </div>
      </div>
      <input type="submit" value="заказать" class="btn" name="order_btn">
   </form>

</section>

<?php include 'footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>