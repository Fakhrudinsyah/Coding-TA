<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "toko_komputer";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
