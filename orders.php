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
                $book_by_id = getColFromTable($conn, 'books', 'BOOK_ID', $fetch_orders['BOOK_ID']);
                $color = 'var(--main)';
                $show_button = false;
                switch ($fetch_orders['LEASE_STATUS']) {
                    case 'processing':
                        $color = 'orange';
                        $show_button = true;
                        break;
                    case 'active':
                        $color = 'green';
                        break;
                    case 'closed':
                        $color = 'black';
                        break;
                    case 'overdue':
                        $color = 'red';
                        break;
                }
                ?>
                <div class="box">
                    <div class="sector">
                        <p> номер заказа : <span><?= $fetch_orders['LEASE_ID'] ?></span></p>
                    </div>
                    <div class="sector">
                        <p> дата заказа : <span><?= $fetch_orders['LEASE_START'] ?></span></p>
                        <p> выдача до : <span><?= $fetch_orders['LEASE_DUE'] ?></span></p>
                    </div>
                    <p> книга : <span><?= $book_by_id['BOOK_NAME']; ?></span></p>
                    <p> статус : <span style="color:<?= $color ?>;"><?= $fetch_orders['LEASE_STATUS'] ?></span></p>
                    <form action="" method="post">
                        <input type="hidden" name="lease_id"
                               value="<?= $fetch_orders['LEASE_ID'] ?>">
                        <?php
                        if ($show_button) {
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