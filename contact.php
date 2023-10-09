<?php

include 'config.php';
include 'get_function.php';
session_start();
$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location:login.php');
}

if (isset($_POST['send'])) {
    $user_login = mysqli_real_escape_string($conn, $_POST['user_login']);
    $user_id = $_POST['user_id'];
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $number = $_POST['number'];
    $msg = mysqli_real_escape_string($conn, $_POST['message']);
    if ($user_login == NULL) {
        mysqli_query($conn,
            "INSERT INTO `message`(TO_USER, FROM_USER ,MESSAGE) VALUES('null', '$user_id', '$msg')") or die('query failed');
        $message[] = 'Сообщение отправлено!';
    }
    else {
        //Проверка пользователя на существование
        $user_by_login = getColFromTable($conn, 'users', 'USER_LOGIN', $user_login);
        if ($user_by_login) {
            $to_user = $user_by_login['USER_ID'];
            mysqli_query($conn,
                "INSERT INTO `message`(TO_USER, FROM_USER,MESSAGE) VALUES('$to_user', '$user_id', '$msg')") or die('query failed');
            $message[] = 'Сообщение отправлено!';
        }
        else {
            $message[] = 'Пользователя с таким логином не существует!';
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
    <title>Сообщения</title>
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'header.php'; ?>

<div class="heading">
    <h3>Свяжитесь с нами</h3>
    <p><a href="home.php">главная</a> / связаться </p>
</div>
<section class="contact">
    <?php
    $user_by_id = getColFromTable($conn, 'users', 'USER_ID', $_SESSION['user_id']);
    ?>
    <form action="" method="post">
        <h3>скажите что-нибудь!</h3>
        <p>Оставьте поле ниже пустым чтобы отправить сообщение в библиотеку</p>
        <input type="hidden" name="user_id"
               value="<?= $_SESSION['user_id'] ?>" class="box">
        <input type="text" name="user_login"
               placeholder="Введите логин получателя" class="box">
        <input type="text" name="name" value="<?= $user_by_id['USER_NAME'] ?>"
               required placeholder="Введите ваше имя" class="box">
        <input type="email" name="email"
               value="<?= $user_by_id['USER_MAIL'] ?>"
               required placeholder="Введите ваш email" class="box">
        <input type="text" name="number"
               value="<?= $user_by_id['USER_PHONE'] ?>" required
               placeholder="Введите ваш номер" class="box">
        <textarea name="message" class="box" placeholder="Ваше сообщение" id=""
                  cols="30" rows="10"></textarea>
        <input type="submit" value="Отправить" name="send" class="btn">
    </form>
</section>

<?php include 'footer.php'; ?>
<script src="js/script.js"></script>
</body>
</html>