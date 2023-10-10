<?php

include 'config.php';
session_start();
$admin_id = $_SESSION['admin_id'];
if (!isset($admin_id)) {
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
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin_style.css">
</head>
<body>

<?php include 'admin_header.php'; ?>

<section class="dashboard">
    <h1 class="title">Статистика</h1>
    <div class="box-container">
        <div class="box">
            <?php
            //$total_overdue = 0;
            $select_overdue = mysqli_query($conn,
                "SELECT COUNT(*) as total_overdue FROM `leases` WHERE LEASE_STATUS = 'overdue'") or die('query failed');
            $total_overdue = mysqli_fetch_assoc($select_overdue)['total_overdue'];
            ?>
            <h3><?= $total_overdue ?></h3>
            <p>Просроченных книг</p>
        </div>
        <div class="box">
            <?php
            //$total_closed = 0;
            $select_closed = mysqli_query($conn,
                "SELECT COUNT(*) as total_closed FROM `leases` WHERE LEASE_STATUS = 'closed'") or die('query failed');
            $total_closed = mysqli_fetch_assoc($select_closed)['total_closed'];
            ?>
            <h3><?= $total_closed ?></h3>
            <p>Вернули всего</p>
        </div>
        <div class="box">
            <?php
            $select_leases = mysqli_query($conn,
                "SELECT COUNT(*) as total_leases FROM `leases`") or die('query failed');
            $total_leases = mysqli_fetch_assoc($select_leases)['total_leases'];
            ?>
            <h3><?= $total_leases ?></h3>
            <p>Всего выдали</p>
        </div>
        <div class="box">
            <?php
            //$number_of_books = 0;
            $select_books = mysqli_query($conn,
                "SELECT SUM(BOOK_AMOUNT) as total_books FROM `books`") or die('query failed');
            $total_books = mysqli_fetch_assoc($select_books)['total_books'];
            ?>
            <h3><?= $total_books ?></h3>
            <p>Всего книг</p>
        </div>
        <div class="box">
            <?php
            $select_users = mysqli_query($conn,
                "SELECT COUNT(*) as total_users FROM `users` WHERE USER_STATUS = 1") or die('query failed');
            $total_users = mysqli_fetch_assoc($select_users)['total_users'];
            ?>
            <h3><?= $total_users ?></h3>
            <p>Пользователи</p>
        </div>
        <div class="box">
            <?php
            $select_admins = mysqli_query($conn,
                "SELECT COUNT(*) as total_admins FROM `users` WHERE USER_STATUS = 3") or die('query failed');
            $total_admins = mysqli_fetch_assoc($select_admins)['total_admins'];
            ?>
            <h3><?= $total_admins ?></h3>
            <p>Администраторы</p>
        </div>
        <div class="box">
            <?php
            $select_accounts = mysqli_query($conn,
                "SELECT COUNT(*) as total_accounts FROM `users`") or die('query failed');
            $total_accounts = mysqli_fetch_assoc($select_accounts)['total_accounts'];
            ?>
            <h3><?= $total_accounts ?></h3>
            <p>Всего аккаунтов</p>
        </div>
        <div class="box">
            <?php
            $select_messages = mysqli_query($conn,
                "SELECT COUNT(*) as total_messages FROM `message` WHERE TO_USER = $admin_id OR TO_USER = 'null'") or die('query failed');
            $total_messages = mysqli_fetch_assoc($select_messages)['total_messages'];
            ?>
            <h3><?= $total_messages ?></h3>
            <p>Новых сообщений</p>
        </div>
    </div>
</section>
<script src="./js/admin_script.js"></script>
</body>
</html>