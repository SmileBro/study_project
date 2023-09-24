<?php

include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:login.php');
}


if(isset($message)){
   foreach($message as $message){
      echo '
      <div class="message">
         <span>'.$message.'</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
   }
}

function uniquePost($posted) {
    // take some form values
    $description = $_POST['update_u_id'].$_POST['update_name'].$_POST['update_email'].$_POST['update_status'];
    
    // check if session hash matches current form hash
    if (isset($_SESSION['form_hash']) && $_SESSION['form_hash'] == md5($description) ) {
       // form was re-submitted return false
       return false;
    }
    // set the session value to prevent re-submit
    $_SESSION['form_hash'] = md5($description);
    return true;
 }

if(isset($_POST['update_user']) && uniquePost($_POST)){

    $update_u_id = $_POST['update_u_id'];
    $update_name = $_POST['update_name'];
    $update_email = $_POST['update_email'];
    $update_status = $_POST['update_status'];

    if ($admin_id == 3){
        mysqli_query($conn, "UPDATE `users` SET USER_NAME = '$update_name', USER_MAIL = '$update_email',USER_STATUS = '$update_status' WHERE USER_ID = '$update_u_id'") or die('query failed');
 
    }else{
        if ($update_status < 3){
            mysqli_query($conn, "UPDATE `users` SET USER_NAME = '$update_name', USER_MAIL = '$update_email' WHERE USER_ID = '$update_u_id'") or die('query failed');
        }else{
            $message[] = 'У вас нет прав редактироавть данные этого пользователя!';
        }
        
    }
 
    header('location:admin_users.php');
 
 }

if(isset($_GET['delete'])){
   $delete_id = $_GET['delete'];
   mysqli_query($conn, "DELETE FROM `users` WHERE USER_ID = '$delete_id'") or die('query failed');
   header('location:admin_users.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>users</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom admin css file link  -->
   <link rel="stylesheet" href="css/admin_style.css">

</head>
<body>
   
<?php include 'admin_header.php'; ?>

<section class="users">

   <h1 class="title"> user accounts </h1>

   <div class="box-container">
      <?php
         $select_users = mysqli_query($conn, "SELECT * FROM `users`") or die('query failed');
         while($fetch_users = mysqli_fetch_assoc($select_users)){
      ?>
      <div class="box">
         <p> Номер : <span><?php echo $fetch_users['USER_ID']; ?></span> </p>
         <p> Имя : <span><?php echo $fetch_users['USER_NAME']; ?></span> </p>
         <p> Email : <span><?php echo $fetch_users['USER_MAIL']; ?></span> </p>
         <p> Уровень : <span style="color:<?php if($fetch_users['USER_STATUS'] == 3){ echo 'var(--orange)'; } ?>">
         <?php 
         $status = $fetch_users['USER_STATUS']; 
         if ($status == 1){
            echo "Пользователь";
         }else if ($status == 2){
            echo "Работник";
         }else{
            echo "Администратор";
         }
         
         ?></span> </p>
         <a href="admin_users.php?delete=<?php echo $fetch_users['USER_ID']; ?>" onclick="return confirm('delete this user?');" class="delete-btn">delete user</a>
         <a href="admin_users.php?update=<?php echo $fetch_users['USER_ID']; ?>" class="option-btn">update</a>
      </div>
      <?php
         };
      ?>
   </div>

</section>


<section class="edit-form">

   <?php
      if(isset($_GET['update'])){
         $update_id = $_GET['update'];
         $update_query = mysqli_query($conn, "SELECT * FROM `users` WHERE USER_ID = '$update_id'") or die('query failed');
         if(mysqli_num_rows($update_query) > 0){
            while($fetch_update = mysqli_fetch_assoc($update_query)){
   ?>
   <form action="" method="post" enctype="multipart/form-data">
      <input type="hidden" name="update_u_id" value="<?php echo $fetch_update['USER_ID']; ?>">
      <input type="text" name="update_name" value="<?php echo $fetch_update['USER_NAME']; ?>" class="box" required placeholder="enter name">
      <input type="text" name="update_email" value="<?php echo $fetch_update['USER_MAIL']; ?>" min="0" class="box" required placeholder="enter email">
      <input type="text" name="update_status" value="<?php echo $fetch_update['USER_STATUS']; ?>" min="0" class="box" required placeholder="enter status">
      <input type="submit" value="update" name="update_user" class="btn">
      <input type="reset" value="cancel" id="close-update" class="option-btn">
   </form>
   <?php
         }
      }
      }else{
         echo '<script>document.querySelector(".edit-form").style.display = "none";</script>';
      }
   ?>

</section>






<!-- custom admin js file link  -->
<script src="js/admin_script.js"></script>

</body>
</html>