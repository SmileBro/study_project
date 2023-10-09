<?php

include 'config.php';

function insertIfNeeded($conn, $table, $column, $value) {
    $select_query = mysqli_query($conn,
        "SELECT {$table}_ID FROM `$table` WHERE $column = '$value'");
    if (mysqli_num_rows($select_query) > 0) {
        return $select_query->fetch_array()[0];
    }
    else {
        mysqli_query($conn, "INSERT INTO `$table`($column) VALUES('$value')");
        return mysqli_insert_id($conn);
    }
}

function deleteBook($conn, $delete_id, $dest) {
    $delete_image_query = mysqli_query($conn,
        "SELECT BOOK_IMG FROM `books` WHERE BOOK_ID = '$delete_id'") or die('query failed');
    $fetch_delete_image = mysqli_fetch_assoc($delete_image_query);
    unlink($dest . $fetch_delete_image['BOOK_IMG']);
    mysqli_query($conn,
        "DELETE FROM `books` WHERE BOOK_ID = '$delete_id'") or die('query failed');
}

function deleteLease($conn, $delete_id) {
    $lease = getColFromTable($conn, 'leases', 'LEASE_ID', $delete_id);
    $book = getColFromTable($conn, 'books', 'BOOK_ID', $lease['BOOK_ID']);
    mysqli_query($conn,
        "DELETE FROM `leases` WHERE LEASE_ID = '$delete_id'") or die('query failed');
    $new_amount = $book['BOOK_AMOUNT'] + 1;
    $book_id = $book['BOOK_ID'];
    mysqli_query($conn,
        "UPDATE `books` SET BOOK_AMOUNT = '$new_amount' WHERE BOOK_ID = '$book_id'") or die('query failed');
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

function updateBook(
    $conn,
    $book_id,
    $book_name,
    $book_amount,
    $release_year,
    $rating,
    $auth_name,
    $pub_name,
    $file,
    $dest,
    $old_image
) {
    $upd_book_name = mysqli_real_escape_string($conn, $book_name);
    $upd_rating = mysqli_real_escape_string($conn, $rating);
    $upd_auth_by_name = insertIfNeeded($conn, 'authors', 'AUTH_NAME', $auth_name);
    $upd_pub_by_name = insertIfNeeded($conn, 'publishers', 'PUB_NAME', $pub_name);
    $query = "UPDATE `books` SET 
        BOOK_NAME = '$upd_book_name', 
        BOOK_AMOUNT = '$book_amount', 
        PUB_ID = '$upd_pub_by_name', 
        AUTH_ID = '$upd_auth_by_name', 
        RELEASE_YEAR = '$release_year', 
        RATING = '$upd_rating' 
        WHERE BOOK_ID = '$book_id'";
    mysqli_query($conn, $query) or die('query failed');
    if (isset($_FILES["update_image"])) {
        $temp = explode(".", $file["name"]);
        $image_size = $file['size'];
        move_uploaded_file($file["tmp_name"], $dest . $old_image);
        return 'Книга успешно изменена!';
    }
    else {
        return 'Изменение не удалось.';
    }
}

function getColFromTable($conn, $table, $column, $value) {
    $query = "SELECT * FROM `$table` WHERE $column = '$value'";
    $result = mysqli_query($conn, $query);
    return $result ? mysqli_fetch_assoc($result) : null;
}