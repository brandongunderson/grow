<?php
// db.php
$host = "localhost";
$dbname = "grow";
$dbuser = "zaragenetics";
$dbpass = "Vxupwyyrt1!";  // Change as needed for your setup

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $dbuser, $dbpass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error connecting to database: " . $e->getMessage());
}
?>