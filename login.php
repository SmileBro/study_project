<?php
include 'config.php';
session_start();

$select_leases = mysqli_query($conn,
    "SELECT LEASE_ID, LEASE_DUE, LEASE_STATUS, BOOK_ID FROM `leases`") or die('query failed');
if (mysqli_num_rows($select_leases) > 0) {
    try {
        while ($fetch_leases = mysqli_fetch_assoc($select_leases)) {
            if ($fetch_leases['LEASE_DUE'] < date("Y-m-d")) {
                $lease_id = $fetch_leases['LEASE_ID'];
                if ($fetch_leases['LEASE_STATUS'] == 'active') {
                    mysqli_query($conn,
                        "UPDATE `leases` SET LEASE_STATUS = 'pending' WHERE LEASE_ID = '$lease_id'") or die('query failed');
                }
                elseif ($fetch_leases['LEASE_STATUS'] == 'processing') {
                    mysqli_query($conn,
                        "UPDATE `leases` SET LEASE_STATUS = 'closed' WHERE LEASE_ID = '$lease_id'") or die('query failed');
                    $book = getColFromTable($conn, 'books', 'BOOK_ID', $fetch_leases['BOOK_ID']);
                    $new_amount = $book['BOOK_AMOUNT'] + 1;
                    $book_id = $book['BOOK_ID'];
                    mysqli_query($conn,
                        "UPDATE `books` SET BOOK_AMOUNT = '$new_amount' WHERE BOOK_ID = '$book_id'") or die('query failed');
                }
            }
        }
    }
    catch (Exception $e) {
        $message = 'Something went wrong: ' .  $e->getMessage();
    }
}

if (isset($_POST['submit'])) {
    $login = mysqli_real_escape_string($conn, $_POST['login']);
    $pass = mysqli_real_escape_string($conn, md5($_POST['password']));

    $select_users = mysqli_query($conn, "SELECT * FROM `users` WHERE USER_LOGIN = '$login' AND USER_PASSWORD = '$pass'") or die('query failed');

    if (mysqli_num_rows($select_users) > 0) {
        $row = mysqli_fetch_assoc($select_users);
        $userStatus = $row['USER_STATUS'];

        if ($userStatus == 3 || $userStatus == 2) {
            $sessionKey = 'admin';
            $redirectPage = 'admin_page.php';
        } elseif ($userStatus == 1) {
            $sessionKey = 'user';
            $redirectPage = 'home.php';
        } else {
            $message[] = 'Неправильный логин или пароль!';
            // Дополнительная обработка для других статусов пользователя, если необходимо
        }

        if (isset($sessionKey) && isset($redirectPage)) {
            $_SESSION[$sessionKey . '_name'] = $row['USER_LOGIN'];
            $_SESSION[$sessionKey . '_email'] = $row['USER_MAIL'];
            $_SESSION[$sessionKey . '_id'] = $row['USER_ID'];
            header('location:' . $redirectPage);
        }
    } else {
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
        <h3>Войти</h3>
        <input type="text" name="login" placeholder="Логин" required class="box">
        <input type="password" name="password" placeholder="Пароль" required class="box">
        <input type="submit" name="submit" value="Войти" class="btn">
        <p>Нет аккаунта? <a href="register.php">Зарегистрироваться</a></p>
    </form>
</div>
</body>
</html>