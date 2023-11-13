<?php

include 'config.php';
if (isset($_POST['submit'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = mysqli_real_escape_string($conn, md5($_POST['password']));
    $cpass = mysqli_real_escape_string($conn, md5($_POST['cpassword']));

    $select_users = mysqli_query($conn,
        "SELECT * FROM `users` WHERE USER_LOGIN = '$name'") or die('query failed');

    if (mysqli_num_rows($select_users) > 0) {
        $message[] = 'Пользователь уже существует!';
    }
    elseif ($pass != $cpass) {
        $message[] = 'Пароли не совпадают!';
    }
    else {
        mysqli_query($conn,
            "INSERT INTO `users`(USER_LOGIN, USER_PASSWORD, USER_MAIL) VALUES('$name', '$cpass','$email')") or die('query failed');
        $message[] = 'Регистрация успешна!';
        header('location:login.php');
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация</title>
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php if (isset($message)): ?>
    <?php foreach ($message as $msg): ?>
        <div class="message">
            <span><?= $msg ?></span>
            <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<div class="form-container">
    <form action="" method="post">
        <h3>Регистрация</h3>
        <input type="text" name="name" placeholder="Логин" required class="box">
        <input type="text" name="email" placeholder="Адрес электронной почты"
               required class="box">
        <input type="password" name="password" placeholder="Пароль" required
               class="box">
        <input type="password" name="cpassword"
               placeholder="Введите пароль еще раз" required class="box">
        <input type="submit" name="submit" value="Зарегистрироваться"
               class="btn">
        <p>Уже зарегистрированы?
        <p><a href="login.php">Войти</a></p>
    </form>
</div>
</body>
</html>