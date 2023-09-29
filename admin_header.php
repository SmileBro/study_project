<?php if (isset($message)): ?>
    <?php foreach ($message as $msg): ?>
        <div class="message">
            <span><?= $msg ?></span>
            <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<header class="header">
    <div class="flex">
        <a href="" class="logo">Панель администратора</a>
        <nav class="navbar">
            <a href="admin_page.php">Главная</a>
            <a href="admin_books.php">Книги</a>
            <a href="admin_leases.php">Выдачи</a>
            <a href="admin_users.php">Пользователи</a>
            <a href="admin_contacts.php">Сообщения</a>
        </nav>
        <div class="icons">
            <div id="menu-btn" class="fas fa-bars"></div>
            <div id="user-btn" class="fas fa-user"></div>
        </div>
        <div class="account-box">
            <p>username : <span><?= $_SESSION['admin_name'] ?></span></p>
            <p>email : <span><?= $_SESSION['admin_email'] ?></span></p>
            <a href="logout.php" class="delete-btn">Выйти</a>
            <div><a href="login.php">Войти</a> | <a href="register.php">Регистрация</a></div>
        </div>
    </div>
</header>