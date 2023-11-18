<?php

include 'config.php';
include 'get_function.php';
session_start();
$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:login.php');
};

if (isset($_GET['delete_msg'])) {
    $delete_id = $_GET['delete_msg'];
    mysqli_query($conn,
        "DELETE FROM `message` WHERE MESSAGE_ID = '$delete_id'") or die('query failed');
    header('location:admin_contacts.php');
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
    <link rel="stylesheet" href="css/admin_style.css">
</head>
<body>

<?php include 'admin_header.php'; ?>
<section class="contact">
    <?php
    $admin_by_id = getColFromTable($conn, 'users', 'USER_ID', $_SESSION['admin_id']);
    ?>
    <form action="" method="post">
        <input type="hidden" name="user_id"
               value="<?= $_SESSION['admin_id'] ?>"
               class="box">
        <input type="text" name="user_login"
               class="box"
               placeholder="Введите логин получателя"
               required>
        <input type="text" name="name"
               value="<?= $admin_by_id['USER_NAME'] ?>"
               class="box"
               placeholder="Введите ваше имя"
               required>
        <input type="email" name="email"
               value="<?= $admin_by_id['USER_MAIL'] ?>"
               class="box"
               placeholder="Введите ваш email"
               required>
        <input type="text" name="number"
               value="<?= $admin_by_id['USER_PHONE'] ?>"
               class="box"
               placeholder="Введите ваш номер"
               required>
        <textarea name="message" class="box" placeholder="Ваше сообщение" id=""
                  cols="30" rows="10"></textarea>
        <input type="submit" value="Отправить" name="send" class="btn">
    </form>
</section>
<section class="messages">
    <h1 class="title"> Сообщения </h1>
    <div class="box-container">
        <?php
        $select_message = mysqli_query($conn,
            "SELECT * FROM `message` WHERE TO_USER = $admin_id OR TO_USER = 'null'") or die('query failed');
        if (mysqli_num_rows($select_message) > 0) {
            while ($fetch_message = mysqli_fetch_assoc($select_message)) {
                $user_by_id = getColFromTable($conn, 'users', 'USER_ID', $fetch_message['FROM_USER']);
                ?>
                <div class="box">
                    <div class="sector">
                        <p> ID : <span><?= $fetch_message['FROM_USER'] ?></span></p>
                    </div>
                    <div class="sector">
                        <p> Логин : <span><?= $user_by_id['USER_LOGIN'] ?></span></p>
                        <p> Имя : <span><?= $user_by_id['USER_NAME'] ?></span></p>
                        <p> Номер тел. : <span><?= $user_by_id['USER_PHONE'] ?></span></p>
                        <p> Email : <span><?= $user_by_id['USER_MAIL'] ?></span></p>
                    </div>
                    <p> Сообщение : <span><?= $fetch_message['MESSAGE'] ?></span></p>
                    <a href="admin_contacts.php?delete_msg=<?= $fetch_message['MESSAGE_ID'] ?>"
                       onclick="return confirm('Удалить это сообщение?');"
                       class="delete-btn">удалить сообщение</a>
                </div>
                <?php
            }
        }
        else {
            echo '<p class="empty">У вас нет сообщений!</p>';
        }
        ?>
    </div>
</section>

<script src="js/admin_script.js"></script>
</body>
</html>