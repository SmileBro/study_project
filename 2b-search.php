<?php
$dbhost = '127.0.0.1';
$dbname = 'library_bd'; 
$dbchar = "utf8mb4";
$dbuser = "root";
$dbpass = "";
$db_port = '3306';
$pdo = new PDO(
    "mysql:host=$dbhost;dbname=$dbname;charset=$dbchar",
    $dbuser, $dbpass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
  ]);
  
  // (B) DO SEARCH
  $data = [];
  $stmt = $pdo->prepare("SELECT `BOOK_NAME` FROM `books` WHERE `BOOK_NAME` LIKE ?");
  $stmt->execute(["%".$_POST["search"]."%"]);
  while ($r = $stmt->fetch()) { $data[] = $r["BOOK_NAME"]; }
  echo count($data)==0 ? "null" : json_encode($data) ;