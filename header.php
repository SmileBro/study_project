<?php if (isset($message)): ?>
    <?php foreach ($message as $msg): ?>
        <div class="message">
            <span><?= $msg ?></span>
            <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<header class="header">
    <div class="header-1">
        <div class="flex">
            <div class="share">
            <a href="https://vk.com/yar_l"><i class="fab fa-vk"></i></a>
            <a href="https://vk.com/azat_ku"> <i class="fab fa-vk"></i></a>
            <a href="https://t.me/yar_laz"> <i class="fab fa-telegram"></i></a>
            <a href="https://t.me/perfectfury"> <i class="fab fa-telegram"></i></a>
            </div>
            <a href="home.php" class="logo">БАЗА</a>
            <p><a href="login.php">Войти</a> | <a href="register.php">Регистрация</a></p>
        </div>
    </div>
    <div class="header-2">
        <div class="flex">
            <nav class="navbar">
                <a href="home.php">главная</a>
                <a href="about.php">о нас</a>
                <a href="books.php">книги</a>
                <a href="contact.php">связатьсья</a>
                <a href="orders.php">заказы</a>
                <a href="cart.php">корзина</a>
            </nav>
            <div class="icons">
                <div id="menu-btn" class="fas fa-bars"></div>
                <a href="search_page.php" class="fas fa-search"></a>
                <div id="user-btn" class="fas fa-user"></div>
            </div>
            <div class="user-box">
                <p>Логин : <span><?php echo $_SESSION['user_name']; ?></span></p>
                <p>email : <span><?php echo $_SESSION['user_email']; ?></span></p>
                <a href="logout.php" class="delete-btn">Выйти</a>
            </div>
        </div>
    </div>
</header>