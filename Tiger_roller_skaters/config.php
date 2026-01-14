<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "tiger_roller_skaters";

// Create database connection
$db = new mysqli($host, $user, $pass, $dbname);
// $db = new mysqli('localhost','root','','tiger_roller_skaters');
$db = new mysqli('sql211.infinityfree.com','if0_40714832','kevin542007','if0_40714832_tiger_roller_skaters');
$db->set_charset('utf8mb4');

// Check connection
if ($db->connect_error) {
    die("Database Connection Failed: " . $db->connect_error);
}
if ($db->connect_error) die('DB connect error: '.$db->connect_error);
?>
