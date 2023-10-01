<?php
include 'config.php';
include 'get_function.php';
session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:login.php');
}

if (isset($_POST['send_lease'])) {
    $user_login = mysqli_real_escape_string($conn, $_POST['user_login']);
    $book_name = mysqli_real_escape_string($conn, $_POST['book_name']);
    $worker_name = mysqli_real_escape_string($conn, $_POST['worker_name']);
    $lease_start = $_POST['lease_start'];
    $lease_due = $_POST['lease_due'];
    $lease_status = mysqli_real_escape_string($conn, $_POST['lease_status']);
    
    // Проверка, существуют ли пользователь, книга и работник с такими данными
    $user_query = mysqli_query($conn, "SELECT * FROM `users` WHERE USER_LOGIN = '$user_login'");
    $book_query = mysqli_query($conn, "SELECT * FROM `books` WHERE BOOK_NAME = '$book_name'");
    $worker_query = mysqli_query($conn, "SELECT * FROM `users` WHERE USER_LOGIN = '$worker_name'");

    if ($user_query && $book_query && $worker_query) {
        $fetch_user = mysqli_fetch_assoc($user_query);
        $fetch_book = mysqli_fetch_assoc($book_query);
        $fetch_worker = mysqli_fetch_assoc($worker_query);

        //Проверка книги на наличие
        $book_amount = $fetch_book['BOOK_AMOUNT'];
        if ($book_amount < 1){
            $message[] = 'В данный момент книга отсутствует.';
        }else{

        $user = $fetch_user['USER_ID'];
        $book_id = $fetch_book['BOOK_ID'];
        $worker = $fetch_worker['USER_ID'];

        // Вставка записи в таблицу leases
        $add_lease_query = mysqli_query($conn, "INSERT INTO `leases`(USER_ID, BOOK_ID, WORKER_ID, LEASE_START, LEASE_DUE, LEASE_STATUS) VALUES('$user','$book_id', $worker, '$lease_start','$lease_due','$lease_status')");

        if ($add_lease_query) {
            // Уменьшение количества книг
            $new_amount = $book_amount-1;
            $update_lease_query = mysqli_query($conn, "UPDATE `books` SET BOOK_AMOUNT = '$new_amount' WHERE BOOK_ID = '$book_id'") or die('query failed');
            $message[] = 'Запись добавлена!';
            header('location:admin_leases.php');
        } else {
            $message[] = 'Не удалось добавить запись в базу данных.';
        }
        }
    } else {
        $message[] = 'Вы ввели неправильные данные!';
    }
}
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $lease = GetLeaseById($conn, $delete_id);
    $book = GetBookById($conn, $lease['BOOK_ID']);
    mysqli_query($conn, "DELETE FROM `leases` WHERE LEASE_ID = '$delete_id'") or die('query failed');
    $new_amount = $book['BOOK_AMOUNT']+1;
    $book_id = $book['BOOK_ID'];
    $update_lease_query = mysqli_query($conn, "UPDATE `books` SET BOOK_AMOUNT = '$new_amount' WHERE BOOK_ID = '$book_id'") or die('query failed');
    header('location:admin_leases.php');
}

if (isset($_POST['send_update_lease'])) {
    $user_login = mysqli_real_escape_string($conn, $_POST['user_login']);
    $book_name = mysqli_real_escape_string($conn, $_POST['book_name']);
    $worker_name = mysqli_real_escape_string($conn, $_POST['worker_name']);
    $lease_start = $_POST['lease_start'];
    $lease_due = $_POST['lease_due'];
    $lease_status = mysqli_real_escape_string($conn, $_POST['lease_status']);
    $lease_id = $_POST['lease_id'];
    // Проверка, существуют ли пользователь, книга и работник с такими данными
    $user_query = mysqli_query($conn, "SELECT USER_ID FROM `users` WHERE USER_LOGIN = '$user_login'");
    $book_query = mysqli_query($conn, "SELECT BOOK_ID FROM `books` WHERE BOOK_NAME = '$book_name'");
    $worker_query = mysqli_query($conn, "SELECT USER_ID FROM `users` WHERE USER_LOGIN = '$worker_name'");

    if ($user_query && $book_query && $worker_query) {
        $fetch_user = mysqli_fetch_assoc($user_query);
        $fetch_book = mysqli_fetch_assoc($book_query);
        $fetch_worker = mysqli_fetch_assoc($worker_query);

        $user = $fetch_user['USER_ID'];
        $book = $fetch_book['BOOK_ID'];
        $worker = $fetch_worker['USER_ID'];

        
        // Обновление записи в таблице leases
        $update_lease_query = mysqli_query($conn, "UPDATE `leases` SET USER_ID = '$user', BOOK_ID = '$book', WORKER_ID = '$worker', LEASE_START = '$lease_start', LEASE_DUE = '$lease_due', LEASE_STATUS = '$lease_status' WHERE LEASE_ID = '$lease_id'") or die('query failed');

        if ($update_lease_query) {
            $message[] = 'Запись добавлена!';
            header('location:admin_leases.php');
        } else {
            $message[] = 'Не удалось добавить запись в базу данных.';
        }
    } else {
        $message[] = 'Вы ввели неправильные данные!';
    }
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>leases</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- custom admin css file link  -->
    <link rel="stylesheet" href="css/admin_style.css">

</head>
<body>

<?php include 'admin_header.php'; ?>

<section class="leases">
    <h1 class="title">выданные книги</h1>
    <div class="box-container">
        <div class="box">
        <a href="admin_leases.php?add_lease" class="option-btn">Добавить</a>
        </div>
    </div>
    <div class="box-container">
        <?php
        $select_leases = mysqli_query($conn, "SELECT * FROM `leases`") or die('query failed');
        if (mysqli_num_rows($select_leases) > 0) {
            while ($fetch_leases = mysqli_fetch_assoc($select_leases)) {
                $user_id = $fetch_leases['USER_ID'];
                $book_id = $fetch_leases['BOOK_ID'];
                $fetch_user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM `users` WHERE USER_ID = $user_id"));
                $fetch_book = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM `books` WHERE BOOK_ID = $book_id"));
                ?>
                <div class="box">
                    <p> № выдачи : <span><?= $fetch_leases['LEASE_ID'] ?></span></p>
                    <p> № пользователя : <span><?= $fetch_leases['USER_ID'] ?></span></p>
                    <p> Выдача размещена : <span><?= $fetch_leases['LEASE_START'] ?></span></p>
                    <p> Логин пользователя : <span><?= $fetch_user['USER_LOGIN'] ?></span></p>
                    <p> Номер пользователя : <span><?= $fetch_user['USER_PHONE'] ?></span></p>
                    <p> Email пользователя : <span><?= $fetch_user['USER_MAIL'] ?></span></p>
                    <p> Статус выдачи : <span><?= $fetch_leases['LEASE_STATUS'] ?></span></p>
                            
                    <form action="" method="post">
                        <input type="hidden" name="lease_id" value="<?= $fetch_leases['LEASE_ID'] ?>">
                        
                        <a href="admin_leases.php?update_lease=<?= $fetch_leases['LEASE_ID'] ?>" class="option-btn">Изменить</a>
                        
                        <a href="admin_leases.php?delete=<?= $fetch_leases['LEASE_ID'] ?>"
                           onclick="return confirm('Удалить этот заказ?');" class="delete-btn">Удалить</a>
                    </form>
                </div>
                <?php
            }
        } else {
            echo '<p class="empty">no leases placed yet!</p>';
        }
        ?>
    </div>
</section>

<section class="edit-form">
    <?php
    if (isset($_GET['add_lease'])) {
        ?>
        <form action="" method="post" enctype="multipart/form-data">
            <h1 class="title">Добавление записи</h1>

            <input type="text" name="user_login" class="box" required placeholder="Введите логин заказчика">
            <input type="text" name="user_name" class="box" required placeholder="Введите имя заказчика">
            <input type="text" name="book_name" class="box" required placeholder="Введите название книги">
            <input type="text" name="worker_name" class="box" value="<?= $_SESSION['admin_name'] ?>" required
                   placeholder="Введите логин работника">
            <input type="date" name="lease_start" class="box" required placeholder="Введите дату начала выдачи">
            <input type="date" name="lease_due" class="box" required placeholder="Введите дату конца выдачи">

            <select name="lease_status" class="box">
                <option value="active">Активна</option>
                <option value="closed">Закрыта</option>
                <option value="pending">Просрочена</option>
            </select>

            <input type="submit" value="Добавить" name="send_lease" class="btn">
            <input type="reset" value="Отменить" id="close-update" class="option-btn">
        </form>
        <?php
    } else if (isset($_GET['update_lease'])){

        $update_id = $_GET['update_lease'];
        $update_query = mysqli_query($conn, "SELECT * FROM `leases` WHERE LEASE_ID = '$update_id'") or die('query failed');
        if (mysqli_num_rows($update_query) > 0) {
            while ($fetch_leases = mysqli_fetch_assoc($update_query)) {
             
                $user_id = $fetch_leases['USER_ID'];
                $book_id = $fetch_leases['BOOK_ID'];
                $fetch_user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM `users` WHERE USER_ID = $user_id"));
                $fetch_book = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM `books` WHERE BOOK_ID = $book_id"));
                ?>
                <form action="" method="post" enctype="multipart/form-data">
                <div class="box">
                    <p>Логин пользователя</p>
                    <input type="text" name="user_login" class="box"
                    value = "<?= $fetch_user['USER_LOGIN'] ?>"  required placeholder="Введите логин заказчика">

                    <p>Название книги</p>
                    <input type="text" name="book_name" class="box" 
                    value = "<?= $fetch_book['BOOK_NAME'] ?>" required placeholder="Введите название книги">

                    <p>Логин работника</p>
                    <input type="text" name="worker_name" class="box" value="<?= $_SESSION['admin_name'] ?>" required
                    placeholder="Введите логин работника">
                    
                    <p>Дата заказа</p>
                    <input type="date" name="lease_start" class="box"  value = "<?= $fetch_leases['LEASE_START'] ?>" required placeholder="Введите дату начала выдачи">
                   
                    <p>Дата конца выдачи</p>
                    <input type="date" name="lease_due" class="box"
                    value = "<?= $fetch_leases['LEASE_DUE'] ?>"
                    required placeholder="Введите дату конца выдачи">
                    
                    <p>Статус выдачи</p>
                    <select name="lease_status" class="box">
                        <option value="active">Активна</option>
                        <option value="closed">Закрыта</option>
                        <option value="pending">Просрочена</option>
                    </select>
                    
                    <input type="hidden" name="lease_id" value="<?= $fetch_leases['LEASE_ID'] ?>">
                    <input type="hidden" name="user_id" value="<?= $fetch_leases['USER_ID'] ?>">
                    
                    <input type="submit" value="Изменить" name="send_update_lease" class="option-btn">
                    <input type="reset" value="Отменить" id="close-update" class="option-btn">
                    
                </div>
                </form>
                <?php
            }
        }
    }else {
        echo '<script>document.querySelector(".edit-form").style.display = "none";</script>';
    }
    ?>
</section>


<!-- custom admin js file link  -->
<script src="js/admin_script.js"></script>
</body>
</html>