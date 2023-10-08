<?php
include 'config.php';
include 'get_function.php';
session_start();
$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location:login.php');
}

if (isset($_POST['add_to_cart'])) {
    $book_id = $_POST['book_id'];
    $book_amount = $_POST['book_amount'];
    $book_quantity = mysqli_real_escape_string($conn, $_POST['product_quantity']);

    if ($book_quantity > $book_amount) {
        $message[] = 'Невозможно забронировать такое количество книг!';
    } else {
        mysqli_query($conn, "INSERT INTO `cart`(USER_ID, BOOK_ID, BOOK_AMOUNT) VALUES('$user_id', '$book_id', '$book_amount')") or die('query failed');
        $message[] = 'Книга добавлена в корзину!';
   }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>home</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/style.css">

</head>
<body>

<?php include 'header.php'; ?>

<section class="home">
    <div class="content">
        <h3>Забронируйте книгу не выходя из дома.</h3>
        <p>До 5 книг в одни руки из нашей библиотеки</p>
        <a href="books.php" class="white-btn">вперед</a>
    </div>
</section>

<section class="books">
    <h1 class="title">популярное</h1>
    <div class="box-container">
        <?php
        $select_books = mysqli_query($conn, "SELECT * FROM `books` LIMIT 6") or die('query failed');
        if (mysqli_num_rows($select_books) > 0) {
            while ($fetch_books = mysqli_fetch_assoc($select_books)) {
                ?>
                <form action="" method="post" class="box">
                    <a href="details.php?id=<?=$fetch_books['BOOK_ID']?>">
                    <img class="book_img" src="uploaded_img/<?php echo $fetch_books['BOOK_IMG']; ?>" height= "350rem" width=100%  alt=""></a>
                    <div class="name"><?php echo $fetch_books['BOOK_NAME']; ?></div>
                    <div class="amount">Новинка</div>
                    <div class="name"><?php 
                    $author = GetAuthorById($conn, $fetch_books['AUTH_ID']);
                    echo $author['AUTH_NAME']; ?></div>
                    <div class="qty">Кол-во: <?php echo $fetch_books['BOOK_AMOUNT']; ?></div>
                    <input type="hidden" name="book_id" value="<?php echo $fetch_books['BOOK_ID']; ?>">
                    <input type="hidden" name="book_name" value="<?php echo $fetch_books['BOOK_NAME']; ?>">
                    <input type="hidden" name="book_amount" value="<?php echo 1; ?>">
                    <input type="submit" value="В корзину" name="add_to_cart" class="btn">
                </form>
                <?php
            }
        } else {
            echo '<p class="empty">Еще нет ни одной книги!</p>';
        }
        ?>
    </div>
    <div class="load-more" style="margin-top: 2rem; text-align:center">
        <a href="books.php" class="option-btn">Еще</a>
    </div>
</section>

<section class="about">
    <div class="flex">
        <div class="image">
            <img src="images/about-img.jpg" alt="">
        </div>
        <div class="content">
            <h3>о нас</h3>
            <p>БАЗА - это удобное решение для библиотек для работы с клиентами и автоматизации книжного учета</p>
            <a href="about.php" class="btn">Еще</a>
        </div>
    </div>
</section>

<section class="home-contact">
    <div class="content">
        <h3>есть вопросы?</h3>
        <p>Вы можете оставить отзыв или задать вопрос по форме связи ниже</p>
        <a href="contact.php" class="white-btn">связаться</a>
    </div>
</section>

<?php include 'footer.php'; ?>

<!-- custom js file link  -->
<script src="js/script.js"></script>

</body>
</html>