<?php
session_start();
unset($_SESSION['user_id']);
header('Location:login.php');
require_once 'config.php';
mysqli_close($conn);