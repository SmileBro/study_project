<?php

include 'config.php';
include 'get_function.php';
session_start();

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:login.php');
}

function insertPublisherIfNeeded($conn, $publisher)
{
    $select_pub = mysqli_query($conn, "SELECT PUB_ID FROM `publishers` WHERE PUB_NAME = '$publisher'");
    if (mysqli_num_rows($select_pub) > 0) {
        return $select_pub->fetch_array()[0];
    } else {
        mysqli_query($conn, "INSERT INTO `publishers`(PUB_NAME) VALUES('$publisher')");
        return mysqli_insert_id($conn);
    }
}

function insertAuthorIfNeeded($conn, $author)
{
    $select_auth = mysqli_query($conn, "SELECT AUTH_ID FROM `authors` WHERE AUTH_NAME = '$author'");
    if (mysqli_num_rows($select_auth) > 0) {
        return $select_auth->fetch_array()[0];
    } else {
        mysqli_query($conn, "INSERT INTO `authors`(AUTH_NAME) VALUES('$author')");
        return mysqli_insert_id($conn);
    }
}

if (isset($_POST['add_to_cart'])) {
    $book_id = $_POST['book_id'];
    $book_amount = $_POST['book_amount'];
    $book_quantity = 1;

    if ($book_quantity > $book_amount) {
        $message[] = 'Книги нет в наличии!';
    } else {
        $is_book_in_the_cart = mysqli_query($conn, "SELECT * FROM `cart` WHERE USER_ID = '$user_id' AND BOOK_ID = '$book_id'") or die('query failed');
        if (mysqli_num_rows($is_book_in_the_cart) > 0){
            $message[] = 'Книга уже в корзине!';
        }else{
            mysqli_query($conn, "INSERT INTO `cart`(USER_ID, BOOK_ID, BOOK_AMOUNT) VALUES('$user_id', '$book_id', '$book_quantity')") or die('query failed');
            $message[] = 'Книга добавлена в корзину!';
        }
   }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Описание</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
<?php include 'header.php'; ?>

<div class="heading">
   <h3>Описание книги</h3>
   <p> <a href="home.php">главная</a> / описание </p>
</div>
<section class="description">
<?php
      $fetch_books = GetBookById($conn, $_GET['id']);
      if (isset($_GET['id']) && $fetch_books) {
        $fetch_pub = GetPublisherById($conn, $fetch_books['PUB_ID']);
        $fetch_author = GetAuthorById($conn, $fetch_books['AUTH_ID']);
   ?>     
  <!-- Left Column / Headphones Image -->
  <div class="left-column">
  <img class="img" src="uploaded_img/<?= $fetch_books['BOOK_IMG'].'?t='. time() ?>" height= "350rem" width=100%  alt=""></a>
  </div>
 
 
  <!-- Right Column -->
  <div class="right-column">
 
    <!-- Product Description -->
    <div class="product-description">
    <form action="" method="post" enctype="multipart/form-data">
    <h1><?= $fetch_books['BOOK_NAME'] ?></h1>
    <span>Количество: </span><?= $fetch_books['BOOK_AMOUNT'] ?>
    <p></p>
    <span>Издательство: </span><?= $fetch_pub['PUB_NAME'] ?>
    <p></p>
    <span>Автор: </span><?= $fetch_author['AUTH_NAME'] ?>
    <p></p>
    <span>Год издания: </span><?= $fetch_books['RELEASE_YEAR'] ?>
    <p></p>
    <span>Возрастное ограничение: </span><?= $fetch_books['RATING'] ?>
    <p></p>
    
    <input type="hidden" name="book_id" value="<?php echo $fetch_books['BOOK_ID']; ?>">
    <input type="hidden" name="book_amount" value="<?php echo $fetch_books['BOOK_AMOUNT']; ?>">
    <input type="submit" value="В корзину" name="add_to_cart" class="btn">

    </form>
    </div>
 
    <!--Book description -->
    <div class="product-configuration">
 
      <!-- Tags -->
      <div class="tags-config">
        <span>Метки</span>
 
        <div class="tags">
          <button>Tag</button>
        </div>
      </div>
    </div>
  </div>
  <?php }else{

        ?><a href="details.php?id=<?=$fetch_books['BOOK_ID']?>">'<p class="empty">На главную</p>';</a><?php
      }?>
      
<!-- custom admin js file link  -->
<script src="js/script.js"></script>
    </section>
    </body>