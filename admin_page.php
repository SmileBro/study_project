<?php

include 'config.php';
include 'get_function.php';
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
            $total_processing = getCountByStatus($conn, 'leases', 'LEASE_STATUS', 'processing');
            ?>
            <span style="color: var(--orange)"><?= $total_processing ?></span>
            <p>В обработке</p>
        </div>
        <div class="box">
            <?php
            $total_active = getCountByStatus($conn, 'leases', 'LEASE_STATUS', 'active');
            ?>
            <span style="color: var(--green)"><?= $total_active ?></span>
            <p>Активно</p>
        </div>
        <div class="box">
            <?php
            $total_overdue = getCountByStatus($conn, 'leases', 'LEASE_STATUS', 'overdue');
            ?>
            <span style="color: var(--red)"><?= $total_overdue ?></span>
            <p>Просроченных книг</p>
        </div>
        <div class="box">
            <?php
            $total_closed = getCountByStatus($conn, 'leases', 'LEASE_STATUS', 'closed');
            ?>
            <span><?= $total_closed ?></span>
            <p>Вернули всего</p>
        </div>
        <div class="box">
            <?php
            $total_leases = getCountByStatus($conn, 'leases', 'LEASE_STATUS', NULL);
            ?>
            <span><?= $total_leases ?></span>
            <p>Всего выдали</p>
        </div>
        <div class="box">
            <?php
            //$number_of_books = 0;
            $select_books = mysqli_query($conn,
                "SELECT SUM(BOOK_AMOUNT) as total_books FROM `books`") or die('query failed');
            $total_books = mysqli_fetch_assoc($select_books)['total_books'];
            ?>
            <span><?= $total_books ?></span>
            <p>Всего книг</p>
        </div>
        <div class="box">
            <?php
            $total_users = getCountByStatus($conn, 'users', 'USER_STATUS', 1);
            ?>
            <span><?= $total_users ?></span>
            <p>Пользователи</p>
        </div>
        <div class="box">
            <?php
            $total_admins = getCountByStatus($conn, 'users', 'USER_STATUS', 3);
            ?>
            <span><?= $total_admins ?></span>
            <p>Администраторы</p>
        </div>
        <div class="box">
            <?php
            $total_accounts = getCountByStatus($conn, 'users', 'USER_STATUS', NULL);
            ?>
            <span><?= $total_accounts ?></span>
            <p>Всего аккаунтов</p>
        </div>
        <div class="box">
            <?php
            $select_messages = mysqli_query($conn,
                "SELECT COUNT(*) as total_messages FROM `message` WHERE TO_USER = $admin_id OR TO_USER = 'null'") or die('query failed');
            $total_messages = mysqli_fetch_assoc($select_messages)['total_messages'];
            ?>
            <span><?= $total_messages ?></span>
            <p>Новых сообщений</p>
        </div>
    </div>
</section>
<script src="./js/admin_script.js"></script>
</body>
</html>