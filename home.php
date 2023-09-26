<?php
include 'config.php';
session_start();
$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location:login.php');
}

if (isset($_POST['add_to_cart'])) {
    $book_name = $_POST['book_name'];
    $book_amount = $_POST['book_amount'];
    $book_img = $_POST['book_img'];
    $product_quantity = $_POST['product_quantity'];

    $check_cart_numbers = mysqli_query($conn, "SELECT * FROM `cart` WHERE name = '$book_name' AND user_id = '$user_id'") or die('query failed');

    if (mysqli_num_rows($check_cart_numbers) > 0) {
        $message[] = 'already added to cart!';
    } else {
        mysqli_query($conn, "INSERT INTO `cart`(user_id, name, price, quantity, image) VALUES('$user_id', '$book_name', '$book_amount', '$product_quantity', '$book_img')") or die('query failed');
        $message[] = 'product added to cart!';
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
        <h3>Hand Picked Book to your door.</h3>
        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Excepturi, quod? Reiciendis ut porro iste totam.</p>
        <a href="about.php" class="white-btn">discover more</a>
    </div>
</section>

<section class="books">
    <h1 class="title">latest books</h1>
    <div class="box-container">
        <?php
        $select_books = mysqli_query($conn, "SELECT * FROM `books` LIMIT 6") or die('query failed');
        if (mysqli_num_rows($select_books) > 0) {
            while ($fetch_books = mysqli_fetch_assoc($select_books)) {
                ?>
                <form action="" method="post" class="box">
                    <img class="book_img" src="uploaded_img/<?php echo $fetch_books['BOOK_IMG']; ?>" width="100%"
                         height="100%" alt="">
                    <div class="name"><?php echo $fetch_books['BOOK_NAME']; ?></div>
                    <div class="amount"><?php echo $fetch_books['BOOK_AMOUNT']; ?></div>
                    <input type="number" min="1" name="product_quantity" value="1" class="qty">
                    <input type="hidden" name="book_name" value="<?php echo $fetch_books['BOOK_NAME']; ?>">
                    <input type="hidden" name="book_amount" value="<?php echo $fetch_books['BOOK_AMOUNT']; ?>">
                    <input type="hidden" name="book_img" value="<?php echo $fetch_books['BOOK_IMG']; ?>">
                    <input type="submit" value="add to cart" name="add_to_cart" class="btn">
                </form>
                <?php
            }
        } else {
            echo '<p class="empty">Еще нет ни одной книги!</p>';
        }
        ?>
    </div>
    <div class="load-more" style="margin-top: 2rem; text-align:center">
        <a href="shop.php" class="option-btn">load more</a>
    </div>
</section>

<section class="about">
    <div class="flex">
        <div class="image">
            <img src="images/about-img.jpg" alt="">
        </div>
        <div class="content">
            <h3>about us</h3>
            <p>Lorem ipsum dolor, sit amet consectetur adipisicing elit. Impedit quos enim minima ipsa dicta officia
                corporis ratione saepe sed adipisci?</p>
            <a href="about.php" class="btn">read more</a>
        </div>
    </div>
</section>

<section class="home-contact">
    <div class="content">
        <h3>have any questions?</h3>
        <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Atque cumque exercitationem repellendus, amet ullam
            voluptatibus?</p>
        <a href="contact.php" class="white-btn">contact us</a>
    </div>
</section>

<?php include 'footer.php'; ?>

<!-- custom js file link  -->
<script src="js/script.js"></script>

</body>
</html>