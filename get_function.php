<?php
include 'config.php';

function GetUserByLogin($conn, $login) {
    $user_query = mysqli_query($conn, "SELECT * FROM `users` WHERE USER_LOGIN = '$login'") or die('query failed');
    return $fetch_user = mysqli_fetch_assoc($user_query);
}
function GetUserById($conn, $id) {

    $user_query = mysqli_query($conn, "SELECT * FROM `users` WHERE USER_ID = '$id'") or die('query failed');
    return $fetch_user = mysqli_fetch_assoc($user_query);
}
function GetBookByName($conn, $name) {

    $book_query = mysqli_query($conn, "SELECT * FROM `books` WHERE BOOK_NAME = '$name'") or die('query failed');
    return $fetch_book = mysqli_fetch_assoc($book_query);
}
function GetBookById($conn, $id) {

    $book_query = mysqli_query($conn, "SELECT * FROM `books` WHERE BOOK_ID = '$id'") or die('query failed');
    return $fetch_book = mysqli_fetch_assoc($book_query);
}
function GetLeaseById($conn, $id) {

    $lease_query = mysqli_query($conn, "SELECT * FROM `leases` WHERE LEASE_ID = '$id'") or die('query failed');
    return $fetch_lease = mysqli_fetch_assoc($lease_query);
}

?>