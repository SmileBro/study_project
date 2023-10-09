<?php

include 'config.php';
include 'get_function.php';
session_start();
$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location:login.php');
}

if (isset($_GET['cancel'])) {
    $cancel_lease_id = $_GET['cancel'];
    $lease_by_id = getColFromTable($conn, 'leases', 'LEASE_ID', $cancel_lease_id);
    if ($lease_by_id['LEASE_STATUS'] == 'processing') {
        deleteLease($conn, $cancel_lease_id);
    }
    header('location:orders.php');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>orders</title>
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'header.php'; ?>

<div class="heading">
    <h3>ваши заказы</h3>
    <p><a href="home.php">главная</a> / заказы </p>
</div>
<section class="placed-orders">
    <h1 class="title">список заказов</h1>
    <div class="box-container">
        <?php
        $order_query = mysqli_query($conn,"SELECT * FROM `leases` WHERE USER_ID = '$user_id'") or die('query failed');
        if (mysqli_num_rows($order_query) > 0) {
            while ($fetch_orders = mysqli_fetch_assoc($order_query)) {
                $user_by_id = getColFromTable($conn, 'users', 'USER_ID', $user_id);
                $book_by_id = getColFromTable($conn, 'books', 'BOOK_ID', $fetch_orders['BOOK_ID']);
                ?>
                <div class="box">
                    <p> дата заказа : <span><?= $fetch_orders['LEASE_START'] ?></span></p>
                    <p> книга : <span><?= $book_by_id['BOOK_NAME']; ?></span></p>
                    <p> номер : <span><?= $user_by_id['USER_PHONE'] ?></span></p>
                    <p> email : <span><?= $user_by_id['USER_MAIL'] ?></span></p>
                    <p> статус : <span style="color:<?php if ($fetch_orders['LEASE_STATUS'] == 'pending') {
                                echo 'red';
                            } else {
                                echo 'green';
                            } ?>;"><?= $fetch_orders['LEASE_STATUS'] ?></span></p>
                    <form action="" method="post">
                        <input type="hidden" name="lease_id"
                               value="<?= $fetch_orders['LEASE_ID'] ?>">
                        <?php
                        if ($fetch_orders['LEASE_STATUS'] == 'processing') {
                            ?>
                            <a href="orders.php?cancel=<?= $fetch_orders['LEASE_ID'] ?>"
                               onclick="return confirm('Отменить этот заказ?');"
                               class="delete-btn">Отменить заказ</a>
                            <?php
                        }
                        ?>
                    </form>
                </div>
                <?php
            }
        }
        else {
            echo '<p class="empty">У вас еще нет заказов!</p>';
        }
        ?>
    </div>
</section>

<?php include 'footer.php'; ?>
<script src="js/script.js"></script>
</body>
</html>