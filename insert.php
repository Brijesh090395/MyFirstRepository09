<!DOCTYPE html>
<html>
<head>
<title>PHP CRUD</title>
<style>
table, th, td {
  border: 1px solid black;
  border-collapse: collapse;
  padding: 8px;
}
th {
  background-color: #f2f2f2;
}
input[type=text], input[type=email] {
  padding: 5px;
  width: 200px;
}
button {
  padding: 5px 10px;
  cursor: pointer;
}
</style>
</head>
<body>

<h2>User Management</h2>

<?php
include "db.php";

$name = "";
$email = "";
$edit_id = "";

// ✅ DELETE
if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    $conn->query("DELETE FROM users WHERE id=$id");
    echo "<p style='color:red;'>User deleted ✅</p>";
}

// ✅ EDIT - Fetch data to edit
if(isset($_GET['edit'])){
    $edit_id = $_GET['edit'];
    $result = $conn->query("SELECT * FROM users WHERE id=$edit_id");
    $row = $result->fetch_assoc();
    $name = $row['name'];
    $email = $row['email'];
}

// ✅ INSERT / UPDATE
if(isset($_POST['save'])){
    $name = $_POST['name'];
    $email = $_POST['email'];

    // Update user
    if($_POST['edit_id'] != ""){
        $id = $_POST['edit_id'];
        $conn->query("UPDATE users SET name='$name', email='$email' WHERE id=$id");
        echo "<p style='color:green;'>User updated ✅</p>";
    } else {
        // Insert user
        $conn->query("INSERT INTO users (name, email) VALUES('$name', '$email')");
        echo "<p style='color:green;'>User added ✅</p>";
    }

    $name = "";
    $email = "";
}
?>

<!-- ✅ Form -->
<form method="post">
  <input type="hidden" name="edit_id" value="<?php echo $edit_id; ?>">

  Name: <input type="text" name="name" value="<?php echo $name; ?>" required><br><br>
  Email: <input type="email" name="email" value="<?php echo $email; ?>" required><br><br>

  <button type="submit" name="save">
    <?php echo ($edit_id != "") ? "Update" : "Save"; ?>
  </button>
</form>

<br><br>

<!-- ✅ Display Table -->
<table>
<tr>
  <th>ID</th>
  <th>Name</th>
  <th>Email</th>
  <th>Actions</th>
</tr>

<?php
$result = $conn->query("SELECT * FROM users");
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "<tr>
            <td>".$row['id']."</td>
            <td>".$row['name']."</td>
            <td>".$row['email']."</td>
            <td>
              <a href='insert.php?edit=".$row['id']."'><button>Edit</button></a>
              <a href='insert.php?delete=".$row['id']."' onclick=\"return confirm('Are you sure?');\">
                <button style='background:red;color:white;'>Delete</button>
              </a>
            </td>
        </tr>";
    }
} else {
    echo "<tr><td colspan='4'>No data found</td></tr>";
}
?>
</table>

</body>
</html>
