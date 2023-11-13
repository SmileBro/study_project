<?php
include 'config.php';
session_start();
$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
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
function uniquePost($posted)
{
    // take some form values
    $description = $_POST['update_u_id'] . $_POST['update_name'] . $_POST['update_email'] . $_POST['update_status'];

    // check if session hash matches current form hash
    if (isset($_SESSION['form_hash']) && $_SESSION['form_hash'] == md5($description)) {
        // form was re-submitted return false
        return false;
    }
    // set the session value to prevent re-submit
    $_SESSION['form_hash'] = md5($description);
    return true;
}

if (isset($_POST['update_user']) && uniquePost($_POST)) {
    $update_u_id = $_POST['update_u_id'];
    $update_name = $_POST['update_name'];
    $update_email = $_POST['update_email'];
    $update_status = $_POST['update_status'];

    if ($admin_id == 3) {
        mysqli_query($conn, "UPDATE `users` SET USER_NAME = '$update_name', USER_MAIL = '$update_email',USER_STATUS = '$update_status' WHERE USER_ID = '$update_u_id'") or die('query failed');
    } elseif ($update_status < 3) {
        mysqli_query($conn, "UPDATE `users` SET USER_NAME = '$update_name', USER_MAIL = '$update_email' WHERE USER_ID = '$update_u_id'") or die('query failed');
    } else {
        $message[] = 'У вас нет прав редактироавть данные этого пользователя!';
    }
    header('location:admin_users.php');
}

if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']); // Преобразовываем значение в целое число, чтобы избежать SQL-инъекций

    if ($delete_id > 0) {
        $delete_query = "DELETE FROM `users` WHERE USER_ID = $delete_id";
        $result = mysqli_query($conn, $delete_query);

        if ($result) {
            header('location:admin_users.php');
        } else {
            $message[] = 'Ошибка при удалении пользователя: ' . mysqli_error($conn);
        }
    } else {
        $message[] = 'Неверный идентификатор пользователя.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>users</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- custom admin css file link  -->
    <link rel="stylesheet" href="css/admin_style.css">

</head>
<body>

<?php include 'admin_header.php'; ?>

<section class="users">
    <h1 class="title"> Аккаунты пользователей </h1>
    <div class="box-container">
        <?php
        $select_users = mysqli_query($conn, "SELECT * FROM `users`") or die('query failed');
        while ($fetch_users = mysqli_fetch_assoc($select_users)) {
            ?>
            <div class="box">
                <div class="sector">
                    <p> ID : <span><?= $fetch_users['USER_ID']; ?></span></p>
                </div>
                <div class="sector">
                    <p> Имя : <span><?= $fetch_users['USER_NAME']; ?></span></p>
                    <p> Номер : <span><?= $fetch_users['USER_PHONE']; ?></span></p>
                    <p> Email : <span><?= $fetch_users['USER_MAIL']; ?></span></p>
                </div>
                <p> Уровень : <span style="color:<?php if ($fetch_users['USER_STATUS'] == 3) {
                        echo 'var(--orange)';
                    } ?>">
         <?php
         $status = $fetch_users['USER_STATUS'];
         $userType = ($status == 1) ? "Пользователь" : (($status == 2) ? "Работник" : "Администратор");
         echo $userType;
         ?></span></p>
                <a href="admin_users.php?delete=<?= $fetch_users['USER_ID']; ?>" onclick="return confirm('Удалить этого пользователя?');" class="delete-btn">Удалить</a>
                <a href="admin_users.php?update=<?= $fetch_users['USER_ID']; ?>" class="option-btn">Изменить</a>
            </div>
            <?php
        };
        ?>
    </div>
</section>
<section class="edit-form">
    <?php
    if (isset($_GET['update'])) {
        $update_id = $_GET['update'];
        $update_query = mysqli_query($conn, "SELECT * FROM `users` WHERE USER_ID = '$update_id'") or die('query failed');
        if (mysqli_num_rows($update_query) > 0) {
            while ($fetch_update = mysqli_fetch_assoc($update_query)) {
                ?>
                <form action="" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="update_u_id" value="<?= $fetch_update['USER_ID'] ?>">
                    <input type="text" name="update_name" value="<?= $fetch_update['USER_NAME'] ?>" class="box"
                           required placeholder="Введите имя">
                    <input type="text" name="update_email" value="<?= $fetch_update['USER_MAIL'] ?>" min="0"
                           class="box" required placeholder="Введите email">
                    <input type="text" name="update_status" value="<?= $fetch_update['USER_STATUS'] ?>" min="0"
                           class="box" required placeholder="Введите уровень доступа">
                    <input type="submit" value="Изменить" name="update_user" class="btn">
                    <input type="reset" value="Отменить" id="close-update" class="option-btn">
                </form>
                <?php
            }
        }
    } else {
        echo '<script>document.querySelector(".edit-form").style.display = "none";</script>';
    }
    ?>
</section>

<!-- custom admin js file link  -->
<script src="js/admin_script.js"></script>
</body>
</html>