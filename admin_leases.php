<?php

include 'config.php';
include 'get_function.php';
session_start();
$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:login.php');
}
if (isset($_POST['send_lease'])) {
    $message[] = processLeaseRequest($conn, $_POST);
    echo '<script>setTimeout(\'location="admin_leases.php"\', 500)</script>';
    // header('location:admin_leases.php');
}
if (isset($_GET['delete'])) {
    $delete_lease_id = $_GET['delete'];
    deleteLease($conn, $delete_lease_id);
    header('location:admin_leases.php');
}
if (isset($_POST['send_update_lease'])) {
    $lease_id = $_POST['lease_id'];
    $message[] = processLeaseRequest($conn, $_POST, true, $lease_id);
    echo '<script>setTimeout(\'location="admin_leases.php"\', 500)</script>';
    // header('location:admin_leases.php');
}

updateBookStatus($conn);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>leases</title>
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin_style.css">

</head>
<body>

<?php include 'admin_header.php'; ?>
<section class="add-leases">
    <h1 class="title">выданные книги</h1>
    <form action="" method="post" enctype="multipart/form-data">
        <input type="text" name="user_login" class="box" required
               placeholder="Введите логин заказчика">
        <input type="text" name="book_name" class="box" required
               placeholder="Введите название книги">
        <input type="text" name="worker_name" class="box"
               value="<?= $_SESSION['admin_name'] ?>" required
               placeholder="Введите логин работника">
        <input type="date" name="lease_start" class="box" required
               placeholder="Введите дату начала выдачи">
        <input type="date" name="lease_due" class="box" required
               placeholder="Введите дату конца выдачи">
        <select name="lease_status" class="box">
            <option value="processing">В обработке</option>
            <option value="active">Активна</option>
            <option value="closed">Закрыта</option>
            <option value="overdue">Просрочена</option>
        </select>

        <input type="submit" value="Добавить" name="send_lease" class="btn">
    </form>
</section>
<section class="leases">
    <div class="box-container">
        <?php
        $select_leases = mysqli_query($conn,
            "SELECT * FROM `leases`") or die('query failed');
        if (mysqli_num_rows($select_leases) > 0) {
            while ($fetch_leases = mysqli_fetch_assoc($select_leases)) {
                $fetch_user = getColFromTable($conn, 'users', 'USER_ID', $fetch_leases['USER_ID']);
                $fetch_book = getColFromTable($conn, 'books', 'BOOK_ID', $fetch_leases['BOOK_ID']);
                ?>
                <div class="box">
                    <div class="sector">
                        <p> № выдачи :
                            <span><?= $fetch_leases['LEASE_ID'] ?></span></p>
                    </div>
                    <div class="sector">
                        <p> Логин :
                            <span><?= $fetch_user['USER_LOGIN'] ?></span></p>
                        <p> Номер :
                            <span><?= $fetch_user['USER_PHONE'] ?></span></p>
                        <p> Email :
                            <span><?= $fetch_user['USER_MAIL'] ?></span></p>
                    </div>
                    <div class="sector">
                        <p> Дата выдачи :
                            <span><?= $fetch_leases['LEASE_START'] ?></span></p>
                        <p> Выдача до :
                            <span><?= $fetch_leases['LEASE_DUE'] ?></span></p>
                    </div>
                    <p> Книга :
                        <span><?= $fetch_book['BOOK_NAME'] ?></span></p>
                    <p> Статус выдачи :
                        <span><?= $fetch_leases['LEASE_STATUS'] ?></span></p>

                    <form action="" method="post">
                        <input type="hidden" name="lease_id"
                               value="<?= $fetch_leases['LEASE_ID'] ?>">
                        <a href="admin_leases.php?update_lease=<?= $fetch_leases['LEASE_ID'] ?>"
                           class="option-btn">Изменить</a>
                        <a href="admin_leases.php?delete=<?= $fetch_leases['LEASE_ID'] ?>"
                           onclick="return confirm('Удалить этот заказ?');"
                           class="delete-btn">Удалить</a>
                    </form>
                </div>
                <?php
            }
        }
        else {
            echo '<p class="empty">Выдач в данный момент нет!</p>';
        }
        ?>
    </div>
</section>

<section class="edit-form">
    <?php
    if (isset($_GET['update_lease'])) {
        $update_id = $_GET['update_lease'];
        $update_query = mysqli_query($conn,
            "SELECT * FROM `leases` WHERE LEASE_ID = '$update_id'") or die('query failed');
        if (mysqli_num_rows($update_query) > 0) {
            while ($fetch_leases = mysqli_fetch_assoc($update_query)) {
                $fetch_user = getColFromTable($conn, 'users', 'USER_ID', $fetch_leases['USER_ID']);
                $fetch_book = getColFromTable($conn, 'books', 'BOOK_ID', $fetch_leases['BOOK_ID']);
                ?>
                <form action="" method="post" enctype="multipart/form-data">
                    <p>Логин пользователя</p>
                    <input type="text" name="user_login" class="box"
                           value="<?= $fetch_user['USER_LOGIN'] ?>"
                           required
                           placeholder="Введите логин заказчика">
                    <p>Название книги</p>
                    <input type="text" name="book_name" class="box"
                           value="<?= $fetch_book['BOOK_NAME'] ?>"
                           required
                           placeholder="Введите название книги">
                    <p>Логин работника</p>
                    <input type="text" name="worker_name" class="box"
                           value="<?= $_SESSION['admin_name'] ?>"
                           required
                           placeholder="Введите логин работника">
                    <p>Дата заказа</p>
                    <input type="date" name="lease_start" class="box"
                           value="<?= $fetch_leases['LEASE_START'] ?>"
                           required
                           placeholder="Введите дату начала выдачи">
                    <p>Дата конца выдачи</p>
                    <input type="date" name="lease_due" class="box"
                           value="<?= $fetch_leases['LEASE_DUE'] ?>"
                           required
                           placeholder="Введите дату конца выдачи">
                    <p>Статус выдачи</p>
                    <select name="lease_status" class="box">
                        <option value="processing">В обработке</option>
                        <option value="active">Активна</option>
                        <option value="closed">Закрыта</option>
                        <option value="overdue">Просрочена</option>
                    </select>
                    <input type="hidden" name="lease_id"
                           value="<?= $fetch_leases['LEASE_ID'] ?>">
                    <input type="hidden" name="user_id"
                           value="<?= $fetch_leases['USER_ID'] ?>">
                    <input type="submit" value="Изменить"
                           name="send_update_lease" class="btn">
                    <input value="Отменить" id="close-update"
                           class="option-btn"
                           type="reset">
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
<script src="js/admin_script.js"></script>
</body>
</html>