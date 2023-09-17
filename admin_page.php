<?php

include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:login.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Панель администратора</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
   <link rel="stylesheet" href="css/admin_style.css">

</head>
<body>
   
<?php include 'admin_header.php'; ?>

<!-- admin dashboard -->

<section class="dashboard">

   <h1 class="title">Статистика</h1>

   <div class="box-container">

      <div class="box">
         <?php
            $total_pendings = 0;
            $select_pending = mysqli_query($conn, "SELECT * FROM `leases` WHERE LEASE_STATUS = 'Pending'") or die('query failed');
            $total_pendings = mysqli_num_rows($select_pending);
            ?>
         <h3><?php echo $total_pendings; ?></h3>
         <p>Просроченых книг</p>
         
      </div>
        
      <div class="box">
        <?php
            $total_completed = 0;
            $select_completed = mysqli_query($conn, "SELECT * FROM `leases` WHERE LEASE_STATUS = 'Closed'") or die('query failed');
            $total_completed = mysqli_num_rows($select_completed);
            ?>
         <h3><?php echo $total_completed;?></h3>
         <p>Вернули всего</p>
        
      </div>
    
      <div class="box">
        <?php 
            $select_leases = mysqli_query($conn, "SELECT * FROM `leases`") or die('query failed');
            $number_of_leases = mysqli_num_rows($select_leases);
         ?>
         <h3><?php echo $number_of_leases; ?></h3>
         <p>Всего выдали</p>
        
      </div>

      <div class="box">
         <?php 
            $select_books = mysqli_query($conn, "SELECT * FROM `books`") or die('query failed');
            $number_of_books = mysqli_num_rows($select_books);
         ?>
         <h3><?php echo $number_of_books; ?></h3>
         <p>Всего книг</p>
      </div>

      <!--<div class="box">
         <?php/* 
            $select_users = mysqli_query($conn, "SELECT * FROM `users` WHERE user_type = 'user'") or die('query failed');
            $number_of_users = mysqli_num_rows($select_users);
         */?>
         <h3><?php// echo $number_of_users; ?></h3>
         <p>normal users</p>
      </div>

      <div class="box">
         <?php 
            /*$select_admins = mysqli_query($conn, "SELECT * FROM `users` WHERE user_type = 'admin'") or die('query failed');
            $number_of_admins = mysqli_num_rows($select_admins);
         */?>
         <h3><?php// echo $number_of_admins; ?></h3>
         <p>admin users</p>
      </div>

      <div class="box">
         <?php 
            /*$select_account = mysqli_query($conn, "SELECT * FROM `users`") or die('query failed');
            $number_of_account = mysqli_num_rows($select_account);
         */?>
         <h3><?php// echo $number_of_account; ?></h3>
         <p>total accounts</p>
      </div>

      <div class="box">
         <?php 
            /*$select_messages = mysqli_query($conn, "SELECT * FROM `message`") or die('query failed');
            $number_of_messages = mysqli_num_rows($select_messages);
         */?>
         <h3><?php// echo $number_of_messages; ?></h3>
         <p>new messages</p>
      </div>-->

   </div>

</section>

<!-- admin dashboard section ends -->









<!-- custom admin js file link  -->
<script src="js/admin_script.js"></script>

</body>
</html>