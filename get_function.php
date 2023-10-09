<?php

include 'config.php';

function GetUserByLogin($conn, $login) {
    $user_query = mysqli_query($conn,
        "SELECT * FROM `users` WHERE USER_LOGIN = '$login'") or die('query failed');
    return $fetch_user = mysqli_fetch_assoc($user_query);
}

function GetUserById($conn, $id) {
    $user_query = mysqli_query($conn,
        "SELECT * FROM `users` WHERE USER_ID = '$id'") or die('query failed');
    return $fetch_user = mysqli_fetch_assoc($user_query);
}

function GetBookByName($conn, $name) {
    $book_query = mysqli_query($conn,
        "SELECT * FROM `books` WHERE BOOK_NAME = '$name'") or die('query failed');
    return $fetch_book = mysqli_fetch_assoc($book_query);
}

function GetBookById($conn, $id) {
    $book_query = mysqli_query($conn,
        "SELECT * FROM `books` WHERE BOOK_ID = '$id'") or die('query failed');
    return $fetch_book = mysqli_fetch_assoc($book_query);
}

function GetLeaseById($conn, $id) {
    $lease_query = mysqli_query($conn,
        "SELECT * FROM `leases` WHERE LEASE_ID = '$id'") or die('query failed');
    return $fetch_lease = mysqli_fetch_assoc($lease_query);
}

function GetAuthorById($conn, $id) {
    $author_query = mysqli_query($conn,
        "SELECT * FROM `authors` WHERE AUTH_ID = '$id'") or die('query failed');
    return $fetch_lease = mysqli_fetch_assoc($author_query);
}

function GetAuthorByName($conn, $name) {
    $author_query = mysqli_query($conn,
        "SELECT * FROM `authors` WHERE AUTH_NAME = '$name'") or die('query failed');
    return $fetch_lease = mysqli_fetch_assoc($author_query);
}

function GetPublisherById($conn, $id) {
    $query = mysqli_query($conn,
        "SELECT * FROM `publishers` WHERE PUB_ID = '$id'") or die('query failed');
    return $fetch = mysqli_fetch_assoc($query);
}

function GetPublisherByName($conn, $name) {
    $query = mysqli_query($conn,
        "SELECT * FROM `publishers` WHERE PUB_NAME = '$name'") or die('query failed');
    return $fetch = mysqli_fetch_assoc($query);
}

function insertPublisherIfNeeded($conn, $publisher) {
    $select_pub = mysqli_query($conn,
        "SELECT PUB_ID FROM `publishers` WHERE PUB_NAME = '$publisher'");
    if (mysqli_num_rows($select_pub) > 0) {
        return $select_pub->fetch_array()[0];
    }
    else {
        mysqli_query($conn,
            "INSERT INTO `publishers`(PUB_NAME) VALUES('$publisher')");
        return mysqli_insert_id($conn);
    }
}

function insertAuthorIfNeeded($conn, $author) {
    $select_auth = mysqli_query($conn,
        "SELECT AUTH_ID FROM `authors` WHERE AUTH_NAME = '$author'");
    if (mysqli_num_rows($select_auth) > 0) {
        return $select_auth->fetch_array()[0];
    }
    else {
        mysqli_query($conn,
            "INSERT INTO `authors`(AUTH_NAME) VALUES('$author')");
        return mysqli_insert_id($conn);
    }
}

function deleteBook($conn, $delete_id, $dest) {
    // Получаем имя файла изображения
    $delete_image_query = mysqli_query($conn,
        "SELECT BOOK_IMG FROM `books` WHERE BOOK_ID = '$delete_id'") or die('query failed');
    $fetch_delete_image = mysqli_fetch_assoc($delete_image_query);

    // Удаляем изображение с сервера
    unlink($dest . $fetch_delete_image['BOOK_IMG']);

    // Удаляем запись о книге из базы данных
    mysqli_query($conn,
        "DELETE FROM `books` WHERE BOOK_ID = '$delete_id'") or die('query failed');

    // Перенаправляем на страницу admin_books.php
    header('location:admin_books.php');
}

function addToCart($conn, $user_id, $book_id, $book_quantity, $book_amount) {
    if ($book_quantity > $book_amount) {
        return 'Книги нет в наличии!';
    } else {
        $is_book_in_the_cart = mysqli_query($conn, "SELECT * FROM `cart` WHERE USER_ID = '$user_id' AND BOOK_ID = '$book_id'") or die('query failed');
        if (mysqli_num_rows($is_book_in_the_cart) > 0) {
            return 'Книга уже в корзине!';
        } else {
            mysqli_query($conn, "INSERT INTO `cart`(USER_ID, BOOK_ID, BOOK_AMOUNT) VALUES('$user_id', '$book_id', '$book_quantity')") or die('query failed');
            return 'Книга добавлена в корзину!';
        }
    }
}

function uploadImage($file, $destination, $newfilename) {
    $temp = explode(".", $file["name"]);
    $image_size = $file['size'];
    move_uploaded_file($file["tmp_name"], $destination . $newfilename);
    return 'Книга успешно изменена!';
}

function fetchRecord($conn, $table, $column, $value) {
    $query = "SELECT * FROM `$table` WHERE $column = '$value'";
    $result = mysqli_query($conn, $query);
    return $result ? mysqli_fetch_assoc($result) : null;
}