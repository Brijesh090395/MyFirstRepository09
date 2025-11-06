<!DOCTYPE html>
<html>
<body>

<h2>Login Form</h2>

<form method="post">
  Username: <input type="text" name="user"><br><br>
  Password: <input type="password" name="pass"><br><br>
  <input type="submit" value="Login">
</form>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $user = $_POST['user'];
  $pass = $_POST['pass'];

  if ($user == "admin" && $pass == "1234") {
    echo "<p style='color:green;'>Login Successful ✅</p>";
  } else {
    echo "<p style='color:red;'>Invalid Credentials ❌</p>";
  }
}
?>

</body>
</html>
