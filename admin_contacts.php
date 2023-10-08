<?php

include 'config.php';
include 'get_function.php';
session_start();
$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:login.php');
};

if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    mysqli_query($conn,
        "DELETE FROM `message` WHERE MESSAGE_ID = '$delete_id'") or die('query failed');
    header('location:admin_contacts.php');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Сообщения</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- custom admin css file link  -->
    <link rel="stylesheet" href="css/admin_style.css">

</head>
<body>

<?php include 'admin_header.php'; ?>

<section class="messages">
    <h1 class="title"> Сообщения </h1>
    <div class="box-container">
        <?php
        $select_message = mysqli_query($conn,
            "SELECT * FROM `message` WHERE TO_USER = $admin_id OR TO_USER = 'null'") or die('query failed');
        if (mysqli_num_rows($select_message) > 0) {
            while ($fetch_message = mysqli_fetch_assoc($select_message)) {
                $fetch_user = GetUserById($conn, $fetch_message['FROM_USER'])
                ?>
                <div class="box">
                    <p> ID : <span><?= $fetch_message['FROM_USER'] ?></span></p>
                    <p> Логин : <span><?= $fetch_user['USER_LOGIN'] ?></span>
                    </p>
                    <p> Имя : <span><?= $fetch_user['USER_NAME'] ?></span></p>
                    <p> Номер тел. :
                        <span><?= $fetch_user['USER_PHONE'] ?></span></p>
                    <p> Email : <span><?= $fetch_user['USER_MAIL'] ?></span></p>
                    <p> Сообщение :
                        <span><?= $fetch_message['MESSAGE'] ?></span></p>
                    <a href="admin_contacts.php?delete=<?= $fetch_message['MESSAGE_ID'] ?>"
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

<!-- custom admin js file link  -->
<script src="js/admin_script.js"></script>
</body>
</html>