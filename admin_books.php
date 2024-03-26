<?php

include 'config.php';
include 'get_function.php';
session_start();
$admin_id = $_SESSION['admin_id'];
if (!isset($admin_id)) {
    header('location:login.php');
}
error_reporting(E_ALL);
ini_set('display_errors', 'On');

// Function to import books from XML
function importBooksFromXML($xmlFilePath, $conn, $dest) {
    // Load XML file
    $xml = simplexml_load_file($xmlFilePath);
    
    // Check if XML is loaded successfully
    if ($xml === false) {
        return false;
    }
    // Iterate over each book in the XML
    foreach ($xml->book as $book) {
        $name = mysqli_real_escape_string($conn, (string)$book->name);
        $author = mysqli_real_escape_string($conn, (string)$book->author);
        $publisher = mysqli_real_escape_string($conn, (string)$book->publisher);
        $amount = (int)$book->amount;
        $year = (int)$book->year;
        
        // Generate image
        $image = generateBookImage($name, $author, $publisher, $amount, $year);
        $select_book_name = mysqli_query($conn,
        "SELECT BOOK_NAME FROM `books` WHERE BOOK_NAME = '$name'") or die('query failed');
        if (mysqli_num_rows($select_book_name) > 0) {
                $existing_books = mysqli_query($conn,
                    "SELECT BOOK_AMOUNT FROM `books` WHERE BOOK_NAME = '$name'") or die('query failed');
                $upd_amount = $amount + $existing_books->fetch_array()[0];
                mysqli_query($conn,
                    "UPDATE `books` SET `BOOK_AMOUNT`='$upd_amount' WHERE BOOK_NAME = '$name'") or die('query failed');
        } else {
        $pub_id = insertIfNeeded($conn, 'PUB_ID', 'publishers', 'PUB_NAME', $publisher);
        $auth_id = insertIfNeeded($conn, 'AUTH_ID', 'authors', 'AUTH_NAME', $author);
        $add_book_query = mysqli_query($conn,
            "INSERT INTO `books`(BOOK_NAME, BOOK_AMOUNT,PUB_ID, AUTH_ID, RELEASE_YEAR, BOOK_IMG) VALUES('$name','$amount','$pub_id', '$auth_id','$year', '')") or die('query failed');
        if ($add_book_query) {
            $inserted_book_id = mysqli_insert_id($conn);
            
            // Move the generated image to the 'uploaded_img' folder
            $destinationFolder = getcwd().'\\uploaded_img';
            if (!file_exists($destinationFolder)) {
                mkdir($destinationFolder); // Create the folder if it doesn't exist
            }
            
            $newImagePath = $destinationFolder . '\\' . $inserted_book_id. '.png';
            
            if (rename($image, $newImagePath)) {
            $image = $inserted_book_id. '.png';
            
            $add_book_query = mysqli_query($conn,
                "UPDATE `books` SET BOOK_IMG = '$image' WHERE BOOK_ID = '$inserted_book_id'") or die('query failed');
            }
            
        }
    }
}
    return true;
}

// Function to generate book image
function generateBookImage($name, $author, $publisher, $amount, $year) {
    // Create a new image with dimensions 400x300
    $image = imagecreatetruecolor(400, 300);
    
    // Set background color to white
    $bgColor = imagecolorallocate($image, 255, 255, 255);
    imagefill($image, 0, 0, $bgColor);
    
    // Set text color to black
    $textColor = imagecolorallocate($image, 0, 0, 0);
    
    // Write book information on the image
    $text = "Name: $name\nAuthor: $author\nPublisher: $publisher\n\nYear: $year";
    $font =  'arial.TTF'; // Path to a font file
    imagettftext($image, 12, 0, 10, 30, $textColor, $font, $text);
    
    // Save image to a temporary file
    $tempImageFile = tempnam(sys_get_temp_dir(), 'book_image_');
    $tempImagePath = $tempImageFile; // Change extension to PNG

    imagepng($image, $tempImageFile);
    
    // Free up memory
    imagedestroy($image);

    return $tempImagePath;
}
function uniquePost($posted) {
    // Define an array of form fields that you want to include in the description
    $formFields = ['name', 'amount', 'year', 'publisher', 'author'];

    // Initialize an array to store the values of the form fields
    $formValues = [];

    // Collect the form field values into the $formValues array
    foreach ($formFields as $field) {
        if (isset($posted[$field])) {
            $formValues[] = $posted[$field];
        }
    }

    // Combine the form field values into a single string
    $description = implode('', $formValues);

    // check if session hash matches current form hash
    if (isset($_SESSION['form_hash']) && $_SESSION['form_hash'] == md5($description)) {
        // form was re-submitted return false
        return FALSE;
    }
    // set the session value to prevent re-submit
    $_SESSION['form_hash'] = md5($description);
    return TRUE;
}

if (isset($_POST['add_from_xml'])) {
    

     // Check if a file is uploaded
     if (isset($_FILES['xmlFile'])) {
        $xmlFile = $_FILES['xmlFile'];

        // Check if the file is XML
        $fileType = pathinfo($xmlFile['name'], PATHINFO_EXTENSION);
        if ($fileType === 'xml') {
            // Move the uploaded file to a temporary location
            $tempFilePath = $xmlFile['tmp_name'];

            // Import books from the uploaded XML file
            if (importBooksFromXML($tempFilePath, $conn, $dest)) {
                $message[] = 'Книги успешно добавлены!';
            } else {
                $message[] = "Ошибка при добавлении книг!";
            }
        } else {
            $message[] = "Пожалуйста загрузите XML фаил";
        }
    }
    
}

if (isset($_POST['add_book']) && uniquePost($_POST)) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $amount = intval($_POST['amount']);
    $year = intval($_POST['year']);
    $publisher = mysqli_real_escape_string($conn, $_POST['publisher']);
    $author = mysqli_real_escape_string($conn, $_POST['author']);

    $temp = explode(".", $_FILES["image"]["name"]);

    $image_size = $_FILES['image']['size'];

    $select_book_name = mysqli_query($conn,
        "SELECT BOOK_NAME FROM `books` WHERE BOOK_NAME = '$name'") or die('query failed');

    if ($image_size > 2000000) {
        $message[] = 'Размер файла слишком большой!';
    }
    else {
        if (mysqli_num_rows($select_book_name) > 0) {
            $existing_books = mysqli_query($conn,
                "SELECT BOOK_AMOUNT FROM `books` WHERE BOOK_NAME = '$name'") or die('query failed');
            $upd_amount = $amount + $existing_books->fetch_array()[0];
            mysqli_query($conn,
                "UPDATE `books` SET `BOOK_AMOUNT`='$upd_amount' WHERE BOOK_NAME = '$name'") or die('query failed');
            $message[] = 'Количество книг обновлено!';
        }
        else {
            $pub_id = insertIfNeeded($conn, 'PUB_ID', 'publishers', 'PUB_NAME', $publisher);
            $auth_id = insertIfNeeded($conn, 'AUTH_ID', 'authors', 'AUTH_NAME', $author);
            $add_book_query = mysqli_query($conn,
                "INSERT INTO `books`(BOOK_NAME, BOOK_AMOUNT,PUB_ID, AUTH_ID, RELEASE_YEAR, BOOK_IMG) VALUES('$name','$amount','$pub_id', '$auth_id','$year', '')") or die('query failed');
            if ($add_book_query) {
                $inserted_book_id = mysqli_insert_id($conn);
                $newfilename = $inserted_book_id . '.' . end($temp);
                $add_book_query = mysqli_query($conn,
                    "UPDATE `books` SET BOOK_IMG = '$newfilename' WHERE BOOK_ID = '$inserted_book_id'") or die('query failed');
                move_uploaded_file($_FILES["image"]["tmp_name"], $dest . $newfilename);
                $message[] = 'Книга успешно добавлена!';
            }
        }
    }
}

if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    deleteBook($conn, $delete_id, $dest);
    header('location:admin_books.php');
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Книги</title>
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin_style.css">
</head>
<body>

<?php include 'admin_header.php'; ?>

<section class="add-books">
    <h1 class="title">Добавить книги</h1>
    <form action="" method="post" enctype="multipart/form-data">
        <input type="text" name="name" class="box"
               placeholder="Введите название книги" required>
        <input type="text" name="author" class="box"
               placeholder="Введите автора" required>
        <input type="text" name="publisher" class="box"
               placeholder="Введите издательство книги" required>
        <input type="number" min="0" name="amount" class="box"
               placeholder="Введите количество" required>
        <input type="number" min="0" name="year" class="box"
               placeholder="Введите год" required>
        <input type="file" name="image"
               accept="image/jpg, image/jpeg, image/png" class="box" required>
        <input type="submit" value="Добавить" name="add_book" class="btn">
    </form>
    
    <form action="" method="post" enctype="multipart/form-data">
        <h1>Загрузить книги из XML</h1>
        <p></p>
        <input type="file" name="xmlFile" accept=".xml">
        <button type="submit" name="add_from_xml">Загрузить</button>
    </form>
</section>
<section class="books">
    <div class="box-container">
        <?php
        $select_books = mysqli_query($conn,
            "SELECT * FROM `books`") or die('query failed');
        if (mysqli_num_rows($select_books) > 0) {
            while ($fetch_books = mysqli_fetch_assoc($select_books)) {
                ?>
                <div class="box">
                    <a href="admin_detail.php?id=<?= $fetch_books['BOOK_ID'] ?>">
                        <img class="image"
                             src="uploaded_img/<?= $fetch_books['BOOK_IMG'] . '?t=' . time() ?>"
                             width=100%
                             alt=""></a>
                    <div class="desc">
                        <div class="name"><?= $fetch_books['BOOK_NAME'] ?></div>
                        <div class="qty">Количество: <?= $fetch_books['BOOK_AMOUNT'] ?></div>
                        <a href="admin_books.php?delete=<?= $fetch_books['BOOK_ID'] ?>"
                           class="delete-btn"
                           onclick="return confirm('Удалить эту книгу?');">Удалить</a>
                    </div>
                </div>
                <?php
            }
        }
        else {
            echo '<p class="empty">Еще нет ни одной книги!</p>';
        }
        ?>
    </div>
</section>
<script src="js/admin_script.js"></script>
</body>
</html>