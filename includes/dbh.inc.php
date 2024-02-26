<?php

$servername = "localhost";
$user = "root";
$password = "Martinka7";
$database = "atpu";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$database", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected successfully";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit(); // exit script if connection fails
}
?>
