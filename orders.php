<?php

include 'config.php';
include 'get_function.php';
session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location:login.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>orders</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
                $user = GetUserById($conn, $user_id);
                $book_id = $fetch_orders['BOOK_ID'];
                $book_query = mysqli_query($conn, "SELECT * FROM `books` WHERE BOOK_ID = '$book_id'");
                $book_details = mysqli_fetch_assoc($book_query);
                ?>
                <div class="box">
                    <p> дата заказа : <span><?= $fetch_orders['LEASE_START'] ?></span></p>
                    <p> книга : <span><?= $book_details['BOOK_NAME']; ?></span></p>
                    <p> номер : <span><?= $user['USER_PHONE'] ?></span></p>
                    <p> email : <span><?= $user['USER_MAIL'] ?></span></p>
                    <p> статус : <span style="color:<?php if ($fetch_orders['LEASE_STATUS'] == 'pending') {
                                echo 'red';
                            } else {
                                echo 'green';
                            } ?>;"><?= $fetch_orders['LEASE_STATUS'] ?></span></p>
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

<!-- custom js file link  -->
<script src="js/script.js"></script>

</body>
</html>