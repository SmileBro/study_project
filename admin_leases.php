<?php

include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:login.php');
}

if(isset($_POST['send_lease'])){
   
   $user_login = mysqli_real_escape_string($conn,$_POST['user_login']);
   $user_query = mysqli_query($conn, "SELECT * FROM `users` WHERE USER_LOGIN = '$user_login'") or die('query failed');
   $book_name = mysqli_real_escape_string($conn,$_POST['book_name']);
   $book_query = mysqli_query($conn, "SELECT * FROM `books` WHERE BOOK_NAME = '$book_name'") or die('query failed');
   $worker_name = mysqli_real_escape_string($conn,$_POST['worker_name']);
   $worker_query = mysqli_query($conn, "SELECT * FROM `users` WHERE USER_LOGIN = '$worker_name'") or die('query failed');
   if ($user_query && $book_query && $worker_query){
      $fetch_user = mysqli_fetch_assoc($user_query);
      $fetch_book = mysqli_fetch_assoc($book_query);
      $fetch_worker = mysqli_fetch_assoc($worker_query);

      $user = $fetch_user['USER_ID'];
      $book = $fetch_book['BOOK_ID'];
      $worker = $fetch_worker['USER_ID'];

         
      $lease_start = $_POST['lease_start'];
      $lease_due = $_POST['lease_due'];
      $lease_status = mysqli_real_escape_string($conn,$_POST['lease_status']);

      $add_lease_query = mysqli_query($conn, "INSERT INTO `leases`(USER_ID, BOOK_ID, WORKER_ID, LEASE_START, LEASE_DUE, LEASE_STATUS) VALUES('$user','$book', $worker, $lease_start,'$lease_due','$lease_status')") or die('query failed');
      if($add_lease_query){
         $message[] = 'Запись добавлена!';
         header('location:admin_leases.php');
      }
      
   }else{
      $message[] = 'Вы ввели неправильные данные!'; 
   }
}

if(isset($_GET['delete'])){
   $delete_id = $_GET['delete'];
   mysqli_query($conn, "DELETE FROM `leases` WHERE LEASE_ID = '$delete_id'") or die('query failed');
   header('location:admin_leases.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>leases</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom admin css file link  -->
   <link rel="stylesheet" href="css/admin_style.css">

</head>
<body>
   
<?php include 'admin_header.php'; ?>

<section class="leases">

   <h1 class="title">выданные книги</h1>
   <div class="box-container">
   <div class="box">
   <form action="" method="post">
   <a href="admin_leases.php?add_lease" class="option-btn">Добавить</a>
   </div> 
   </div>     
   </form>
   <div class="box-container">
      <?php
      $select_leases = mysqli_query($conn, "SELECT * FROM `leases`") or die('query failed');
      if(mysqli_num_rows($select_leases) > 0){
         while($fetch_leases = mysqli_fetch_assoc($select_leases)){
            $user_id = $fetch_leases['USER_ID'];
            $book_id = $fetch_leases['BOOK_ID'];
            $fetch_user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM `users` WHERE USER_ID = $user_id"));
            $fetch_book = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM `books` WHERE BOOK_ID = $book_id") );
      ?>
      <div class="box">
         <p> user id : <span><?php echo $fetch_leases['USER_ID']; ?></span> </p>
         <p> placed on : <span><?php echo $fetch_leases['LEASE_START']; ?></span> </p>
         <p> name : <span><?php echo $fetch_user['USER_LOGIN']; ?></span> </p>
         <p> number : <span><?php echo $fetch_user['USER_PHONE']; ?></span> </p>
         <p> email : <span><?php echo $fetch_user['USER_MAIL']; ?></span> </p>
         
         <form action="" method="post">
            <input type="hidden" name="lease_id" value="<?php echo $fetch_leases['LEASE_ID']; ?>">
            <select name="update_status">
               <option value="" selected disabled><?php echo $fetch_leases['LEASE_STATUS']; ?></option>
               <option value="pending">pending</option>
               <option value="completed">completed</option>
            </select>
            <input type="submit" value="update" name="update_lease" class="option-btn">
            <a href="admin_leases.php?delete=<?php echo $fetch_leases['LEASE_ID']; ?>" onclick="return confirm('delete this order?');" class="delete-btn">delete</a>
         </form>
      </div>
      <?php
         }
      }else{
         echo '<p class="empty">no leases placed yet!</p>';
      }
      ?>
   </div>

</section>



<section class="edit-form">

   <?php
      if(isset($_GET['add_lease'])){
         
   ?>
   <form action="" method="post" enctype="multipart/form-data">
      
      <h1 class="title">Добавление записи</h1>

      <input type="text" name="user_login" class="box" required placeholder="Введите логин заказчика">
      <input type="text" name="user_name" class="box" required placeholder="Введите имя заказчика">
      <input type="text" name="book_name" class="box" required placeholder="Введите название книги">
      <input type="text" name="worker_name" class="box" value ="<?php echo $_SESSION['admin_name']; ?>" required placeholder="Введите логин работника">
      
      <input type="date" name="lease_start" class="box" required placeholder="Введите дату начала выдачи">

      <input type="date" name="lease_due" class="box" required placeholder="Введите дату конца выдачи">
      
      <select name = "lease_status" class="box">
      <option value="active">Активна</option>
      <option value="closed">Закрыта</option>
      <option value="pending">Просрочена</option>
      </select>
      
      <input type="submit" value="update" name="send_lease" class="btn">
      <input type="reset" value="cancel" id="close-update" class="option-btn">
   </form>
   <?php
         }else{
         echo '<script>document.querySelector(".edit-form").style.display = "none";</script>';
      }
   ?>

</section>






<!-- custom admin js file link  -->
<script src="js/admin_script.js"></script>

</body>
</html>