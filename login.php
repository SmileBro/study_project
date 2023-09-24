<?php

include 'config.php';
session_start();

if(isset($_POST['submit'])){

   $login = mysqli_real_escape_string($conn, $_POST['login']);
   $pass = mysqli_real_escape_string($conn, md5($_POST['password']));

   $select_users = mysqli_query($conn, "SELECT * FROM `users` WHERE USER_LOGIN = '$login' AND USER_PASSWORD = '$pass'") or die('query failed');

   if(mysqli_num_rows($select_users) > 0){

      $row = mysqli_fetch_assoc($select_users);

      if($row['USER_STATUS'] == 3 || $row['USER_STATUS'] == 2){

         $_SESSION['admin_name'] = $row['USER_LOGIN'];
         $_SESSION['admin_email'] = $row['USER_MAIL'];
         $_SESSION['admin_id'] = $row['USER_ID'];
         header('location:admin_page.php');

      }elseif($row['USER_STATUS'] == 1){

         $_SESSION['user_name'] = $row['USER_LOGIN'];
         $_SESSION['user_email'] = $row['USER_MAIL'];
         $_SESSION['user_id'] = $row['USER_ID'];
         header('location:home.php');

      }

   }else{
      $message[] = 'Неправильный логин или пароль!';
   }

}

?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Вход</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">

</head>
<body>

<?php
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
?>
   
<div class="form-container">

   <form action="" method="post">
      <h3>Войти</h3>
      <input type="text" name="login" placeholder="Логин" required class="box">
      <input type="password" name="password" placeholder="Пароль" required class="box">
      <input type="submit" name="submit" value="Войти" class="btn">
      <p>Нет аккаунта? <a href="register.php">Зарегистрироваться</a></p>
   </form>

</div>

</body>
</html>