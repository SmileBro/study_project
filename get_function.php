<?php

include 'config.php';

function insertIfNeeded($conn, $column, $table, $condition, $value) {
    $select_query = mysqli_query($conn,
        "SELECT $column FROM `$table` WHERE $condition = '$value'");
    if (mysqli_num_rows($select_query) > 0) {
        // возвращает id существующей записи
        return $select_query->fetch_array()[0];
    }
    else {
        mysqli_query($conn,
            "INSERT INTO `$table`($condition) VALUES('$value')");
        // возвращает id только что добавленной записи
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

function processLeaseRequest(
    $conn,
    $posted,
    $isUpdate = FALSE,
    $lease_id = NULL
) {
    $lease_start = $posted['lease_start'];
    $lease_due = $posted['lease_due'];
    $lease_status = mysqli_real_escape_string($conn, $posted['lease_status']);
    $user_login = mysqli_real_escape_string($conn, $posted['user_login']);
    $book_name = mysqli_real_escape_string($conn, $posted['book_name']);
    $worker_name = mysqli_real_escape_string($conn, $posted['worker_name']);
    $fetch_user = getColFromTable($conn, 'users', 'USER_LOGIN', $user_login);
    $fetch_book = getColFromTable($conn, 'books', 'BOOK_NAME', $book_name);
    $fetch_worker = getColFromTable($conn, 'users', 'USER_LOGIN', $worker_name);

    if ($fetch_user && $fetch_book && $fetch_worker) {
        $user = $fetch_user['USER_ID'];
        $book = $fetch_book['BOOK_ID'];
        $worker = $fetch_worker['USER_ID'];
        if (!$isUpdate) {
            $book_amount = $fetch_book['BOOK_AMOUNT'];
            if ($book_amount < 1) {
                return 'В данный момент книга отсутствует.';
            }
            else {
                $add_lease_query = mysqli_query($conn,
                    "INSERT INTO `leases`(USER_ID, BOOK_ID, WORKER_ID, LEASE_START, LEASE_DUE, LEASE_STATUS) VALUES('$user','$book', $worker, '$lease_start','$lease_due','$lease_status')");
                if ($add_lease_query) {
                    $new_amount = $book_amount - 1;
                    mysqli_query($conn,
                        "UPDATE `books` SET BOOK_AMOUNT = '$new_amount' WHERE BOOK_ID = '$book'") or die('query failed');
                    return 'Запись добавлена!';
                }
                else {
                    return 'Не удалось добавить запись в базу данных.';
                }
            }
        }
        else {
            // Обновление записи в таблице leases
            $update_lease_query = mysqli_query($conn,
                "UPDATE `leases` SET USER_ID = '$user', BOOK_ID = '$book', WORKER_ID = '$worker', LEASE_START = '$lease_start', LEASE_DUE = '$lease_due', LEASE_STATUS = '$lease_status' WHERE LEASE_ID = '$lease_id'") or die('query failed');
            if ($update_lease_query) {
                return 'Запись обновлена!';
            }
            else {
                return 'Не удалось обновить запись в базе данных.';
            }
        }
    }
    else {
        return 'Вы ввели неправильные данные!';
    }
}

function addToCart($conn, $user_id, $book_id, $book_quantity, $book_amount) {
    if ($book_quantity > $book_amount) {
        return 'Книги нет в наличии!';
    }
    else {
        $is_book_in_the_cart = mysqli_query($conn,
            "SELECT * FROM `cart` WHERE USER_ID = '$user_id' AND BOOK_ID = '$book_id'") or die('query failed');
        if (mysqli_num_rows($is_book_in_the_cart) > 0) {
            return 'Книга уже в корзине!';
        }
        else {
            mysqli_query($conn,
                "INSERT INTO `cart`(USER_ID, BOOK_ID, BOOK_AMOUNT) VALUES('$user_id', '$book_id', '$book_quantity')") or die('query failed');
            return 'Книга добавлена в корзину!';
        }
    }
}

function updateBook(
    $conn,
    $posted,
    $file,
    $dest
) {
    $book_id = $posted['upd_book_id'];
    $book_name = mysqli_real_escape_string($conn, $posted['upd_book_name']);
    $rating = mysqli_real_escape_string($conn, $posted['update_rating']);
    $book_amount = $posted['upd_book_amount'];
    $release_year = $posted['upd_book_release_year'];
    $auth_name = $posted['upd_auth_name'];
    $pub_name = $posted['upd_pub_name'];
    $old_image = $posted['update_old_image'];

    $upd_auth_by_name = insertIfNeeded($conn, 'AUTH_ID', 'authors', 'AUTH_NAME', $auth_name);
    $upd_pub_by_name = insertIfNeeded($conn, 'PUB_ID', 'publishers', 'PUB_NAME', $pub_name);

    $query = "UPDATE `books` SET 
        BOOK_NAME = '$book_name', 
        BOOK_AMOUNT = '$book_amount', 
        PUB_ID = '$upd_pub_by_name', 
        AUTH_ID = '$upd_auth_by_name', 
        RELEASE_YEAR = '$release_year', 
        RATING = '$rating' 
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

function getColFromTable($conn, $table, $condition, $value) {
    $query = "SELECT * FROM `$table` WHERE $condition = '$value'";
    $result = mysqli_query($conn, $query);
    return $result ? mysqli_fetch_assoc($result) : NULL;
}

function getCountByStatus($conn, $table, $status, $value) {
    $query = "SELECT COUNT(*) as total FROM `$table` WHERE $status = '$value'";
    $result = mysqli_query($conn, $query);
    return $result ? mysqli_fetch_assoc($result)['total'] : NULL;
}