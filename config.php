<?php
$db_host        = '127.0.0.1';
$db_user        = 'root';
$db_pass        = '';
$db_database    = 'library_bd'; 
$db_port        = '3306';
$conn = mysqli_connect($db_host,$db_user,$db_pass,$db_database,$db_port) or die('connection failed');
?>