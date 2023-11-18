<?php

include 'config.php';
include 'get_function.php';
session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location:login.php');
}

if (isset($message)) {
    foreach ($message as $msg) {
        echo '
      <div class="message">
         <span>' . $msg . '</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
    }
}

if (isset($_POST['update_user'])) {
    $update_u_id = $_POST['update_u_id'];
    $update_u_login = $_POST['update_u_login'];
    $update_login = mysqli_real_escape_string($conn, $_POST['update_login']);
    $update_name = mysqli_real_escape_string($conn, $_POST['update_name']);
    $update_email = mysqli_real_escape_string($conn, $_POST['update_email']);
    $update_phone = mysqli_real_escape_string($conn, $_POST['update_phone']);
    $update_address = mysqli_real_escape_string($conn, $_POST['update_address']);

    $select_users = mysqli_query($conn,
        "SELECT * FROM `users` WHERE USER_LOGIN = '$update_login'") or die('query failed');
    if ($update_login == $update_u_login) {
        mysqli_query($conn,
            "UPDATE `users` SET USER_LOGIN = '$update_login',USER_NAME = '$update_name', USER_MAIL = '$update_email', USER_PHONE = '$update_phone', USER_ADRESS = '$update_address' WHERE USER_ID = '$update_u_id'") or die('query failed');
    }
    elseif (mysqli_num_rows($select_users) == 0 && !ctype_space($update_login)) {
        mysqli_query($conn,
            "UPDATE `users` SET USER_LOGIN = '$update_login',USER_NAME = '$update_name', USER_MAIL = '$update_email', USER_PHONE = '$update_phone', USER_ADRESS = '$update_address' WHERE USER_ID = '$update_u_id'") or die('query failed');
    }
    else {
        $message[] = 'Пользователь уже существует!';
    }
    header('location:user_cabinet.php');
}

if (isset($_GET['delete'])) {
    $delete_id = (int) $_GET['delete'];

    if ($delete_id > 0) {
        $delete_query_cart = "DELETE FROM `cart` WHERE USER_ID = '$delete_id'";
        $select_leases = "SELECT * FROM `leases` WHERE USER_ID = '$delete_id' AND LEASE_STATUS != 'Completed'";
        $fetch_leases = mysqli_query($conn, $select_leases);
        if (mysqli_num_rows($fetch_leases) > 0) {
            $message[] = 'Верните все книги перед удалением' . mysqli_error($conn);
        }
        else {
            $delete_query = "DELETE FROM `users` WHERE USER_ID = $delete_id";
            $result = mysqli_query($conn, $delete_query);
            if ($result) {
                header('location:login.php');
            }
            else {
                $message[] = 'Ошибка при удалении пользователя: ' . mysqli_error($conn);
            }
        }
    }
    else {
        $message[] = 'Неверный идентификатор пользователя.';
    }
}

if (isset($_GET['delete_msg'])) {
    $delete_id = $_GET['delete_msg'];
    mysqli_query($conn,
        "DELETE FROM `message` WHERE MESSAGE_ID = '$delete_id'") or die('query failed');
    header('location:user_cabinet.php');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Кабинет</title>
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'header.php'; ?>
<div class="heading">
    <h3> Ваш кабинет </h3>
    <p><a href="home.php">главная</a> / кабинет </p>
</div>
<section class="users">
    <div class="box-container">
        <?php
        $select_users = mysqli_query($conn,
            "SELECT * FROM `users` WHERE USER_ID = '$user_id'") or die('query failed');
        while ($fetch_users = mysqli_fetch_assoc($select_users)) {
            ?>
            <div class="box">
                <p> Логин : <span><?= $fetch_users['USER_LOGIN'] ?></span></p>
                <p> Имя : <span><?= $fetch_users['USER_NAME'] ?></span></p>
                <p> Email : <span><?= $fetch_users['USER_MAIL'] ?></span></p>
                <p> Номер : <span><?= $fetch_users['USER_PHONE'] ?></span></p>
                <p> Адрес : <span><?= $fetch_users['USER_ADRESS'] ?></span></p>
                <a href="user_cabinet.php?delete=<?= $fetch_users['USER_ID'] ?>"
                   onclick="return confirm('Удалить аккаунт?');"
                   class="delete-btn">удалить аккаунт</a>
                <a href="user_cabinet.php?update=<?= $fetch_users['USER_ID'] ?>"
                   class="option-btn">изменить</a>
            </div>
            <?php
        }
        ?>
    </div>
</section>
<section class="messages">
    <h1 class="title"> Сообщения </h1>
    <div class="box-container">
        <?php
        $select_message = mysqli_query($conn,
            "SELECT * FROM `message` WHERE TO_USER = $user_id") or die('query failed');
        if (mysqli_num_rows($select_message) > 0) {
            while ($fetch_message = mysqli_fetch_assoc($select_message)) {
                $user_by_id = getColFromTable($conn, 'users', 'USER_ID', $fetch_message['FROM_USER']);
                ?>
                <div class="box">
                    <div class="sector">
                        <p> Логин : <span><?= $user_by_id['USER_LOGIN'] ?></span></p>
                        <p> Имя : <span><?= $user_by_id['USER_NAME'] ?></span></p>
                        <p> Номер тел. : <span><?= $user_by_id['USER_PHONE'] ?></span></p>
                        <p> Email : <span><?= $user_by_id['USER_MAIL'] ?></span></p>
                    </div>
                    <p> Сообщение : <span><?= $fetch_message['MESSAGE'] ?></span></p>
                    <a href="user_cabinet.php?delete_msg=<?= $fetch_message['MESSAGE_ID'] ?>"
                       onclick="return confirm('Удалить это сообщение?');"
                       class="delete-btn">удалить</a>
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
<section class="edit-form">
    <?php
    if (isset($_GET['update']) && $_GET['update'] == $user_id) {
        $update_id = $_GET['update'];
        $update_query = mysqli_query($conn,
            "SELECT * FROM `users` WHERE USER_ID = '$update_id'") or die('query failed');
        if (mysqli_num_rows($update_query) > 0) {
            while ($fetch_update = mysqli_fetch_assoc($update_query)) {
                ?>
                <form action="" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="update_u_id"
                           value="<?= $fetch_update['USER_ID'] ?>">
                    <input type="hidden" name="update_u_login"
                           value="<?= $fetch_update['USER_LOGIN'] ?>">
                    <h1>Логин</h1>
                    <input type="text" name="update_login"
                           value="<?= $fetch_update['USER_LOGIN'] ?>"
                           class="box"
                           placeholder="Введите login"
                           required>
                    <h1>ФИО</h1>
                    <input type="text" name="update_name"
                           value="<?= $fetch_update['USER_NAME'] ?>"
                           class="box"
                           placeholder="Введите имя"
                           required>
                    <h1>Email</h1>
                    <input type="text" name="update_email"
                           value="<?= $fetch_update['USER_MAIL'] ?>"
                           class="box"
                           placeholder="Введите email"
                           required>
                    <h1>Номер</h1>
                    <input type="text" name="update_phone"
                           value="<?= $fetch_update['USER_PHONE'] ?>"
                           class="box"
                           placeholder="Введите номер телефона"
                           required>
                    <h1>Адрес</h1>
                    <input type="text" name="update_address"
                           value="<?= $fetch_update['USER_ADRESS'] ?>"
                           class="box"
                           placeholder="Введите адрес"
                           required>
                    <input type="submit" value="Изменить"
                           name="update_user"
                           class="btn">
                    <input type="reset" value="Отменить"
                           id="close-update"
                           class="option-btn">
                </form>
                <?php
            }
        }
    }
    else {
        echo '<script>document.querySelector(".edit-form").style.display = "none";</script>';
    }
    ?>
</section>

<script src="js/script.js"></script>
</body>
</html>