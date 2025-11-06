<?php
include 'config.php';

$id = $_GET['id'];

$sql = "SELECT * FROM users WHERE id = $id";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);
?>

<form method="POST">
    <input type="text" name="name" value="<?php echo $user['name']; ?>" required>
    <input type="email" name="email" value="<?php echo $user['email']; ?>" required>
    <button type="submit" name="update">Update</button>
</form>

<?php
if (isset($_POST['update'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];

    $update = "UPDATE users SET name='$name', email='$email' WHERE id=$id";

    if (mysqli_query($conn, $update)) {
        echo "✅ User Updated Successfully!";
        header("Location: index.php");
    } else {
        echo "❌ Error updating user!";
    }
}
?>
