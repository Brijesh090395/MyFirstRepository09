<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "php_learning";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

echo "Database Connected Successfully!";
?>
