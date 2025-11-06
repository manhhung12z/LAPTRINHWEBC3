<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "quanliphong";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

mysqli_set_charset($conn, "utf8");
?>
