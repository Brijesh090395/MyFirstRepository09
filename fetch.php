<?php
include "db.php";

$sql = "SELECT * FROM users";
$result = $conn->query($sql);

echo "<h2>User List</h2>";

if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
    echo "ID: " . $row["id"]. " | Name: " . $row["name"]. " | Email: " . $row["email"] . "<br>";
  }
} else {
  echo "No records found";
}
?>
