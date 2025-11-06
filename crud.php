<?php
// crud.php - Single-file PHP MySQL CRUD (prepared statements + simple CSRF)
// Put this file in your XAMPP htdocs folder and open: http://localhost/php_basics/crud.php

session_start();

// ---------- DB CONFIG ----------
$db_host = 'localhost';
$db_user = 'root';
$db_pass = ''; // default XAMPP password is empty
$db_name = 'php_learning';

// Create connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    die("DB Connection failed: " . $conn->connect_error);
}

// ---------- CSRF token ----------
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
}
$csrf = $_SESSION['csrf_token'];

// ---------- Helpers ----------
function e($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

function redirect($url) {
    header("Location: $url");
    exit;
}

$action = $_GET['action'] ?? 'list';

// ---------- CREATE ----------
if ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF check
    if (!isset($_POST['csrf']) || $_POST['csrf'] !== $_SESSION['csrf_token']) {
        die("Invalid CSRF token");
    }

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');

    $errors = [];
    if ($name === '') $errors[] = "Name is required";
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required";

    if (empty($errors)) {
        $sql = "INSERT INTO users (name, email) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ss', $name, $email);

        if ($stmt->execute()) {
            $_SESSION['flash'] = "User created successfully!";
            $stmt->close();
            redirect('crud.php');
        } else {
            // handle duplicate email gracefully
            if ($conn->errno === 1062) $errors[] = "Email already exists.";
            else $errors[] = "DB error: " . $conn->error;
            $stmt->close();
        }
    }
}

// ---------- UPDATE ----------
if ($action === 'edit') {
    $id = intval($_GET['id'] ?? 0);
    // Show form (GET) or process update (POST)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['csrf']) || $_POST['csrf'] !== $_SESSION['csrf_token']) {
            die("Invalid CSRF token");
        }

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');

        $errors = [];
        if ($name === '') $errors[] = "Name is required";
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required";

        if (empty($errors)) {
            $sql = "UPDATE users SET name = ?, email = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ssi', $name, $email, $id);

            if ($stmt->execute()) {
                $_SESSION['flash'] = "User updated successfully!";
                $stmt->close();
                redirect('crud.php');
            } else {
                if ($conn->errno === 1062) $errors[] = "Email already exists.";
                else $errors[] = "DB error: " . $conn->error;
                $stmt->close();
            }
        }
    } else {
        // GET: load record for form
        $stmt = $conn->prepare("SELECT id, name, email FROM users WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        if (!$user) {
            $_SESSION['flash'] = "User not found.";
            redirect('crud.php');
        }
    }
}

// ---------- DELETE ----------
if ($action === 'delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf']) || $_POST['csrf'] !== $_SESSION['csrf_token']) {
        die("Invalid CSRF token");
    }
    $id = intval($_POST['id'] ?? 0);
    if ($id > 0) {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param('i', $id);
        if ($stmt->execute()) {
            $_SESSION['flash'] = "User deleted successfully!";
        } else {
            $_SESSION['flash'] = "Error deleting user: " . $conn->error;
        }
        $stmt->close();
    }
    redirect('crud.php');
}

// ---------- READ (list) ----------
if ($action === 'list') {
    $res = $conn->query("SELECT id, name, email, created_at FROM users ORDER BY id DESC");
}

// ---------- Minimal CSS ----------
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>PHP CRUD Single File</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<style>
  body { font-family: Arial, sans-serif; max-width:900px; margin:30px auto; padding:0 15px; }
  h1 { display:flex; justify-content:space-between; align-items:center; }
  table { width:100%; border-collapse:collapse; margin-top:15px; }
  table th, table td { border:1px solid #ddd; padding:8px; text-align:left; }
  table th { background:#f4f4f4; }
  form { margin-top:10px; }
  input[type=text], input[type=email] { padding:8px; width:100%; box-sizing:border-box; margin-bottom:8px; }
  .btn { display:inline-block; padding:6px 10px; text-decoration:none; border-radius:4px; border:1px solid #ccc; background:#fff; }
  .btn-primary { background:#0b74de; color:#fff; border-color:#0b74de; }
  .btn-danger { background:#e74c3c; color:#fff; border-color:#e74c3c; }
  .flash { padding:10px; background:#eaffea; border:1px solid #c6f6c6; margin:10px 0; }
  .errors { padding:10px; background:#ffecec; border:1px solid #f5c2c2; margin:10px 0; }
  .small { font-size:13px; color:#666; }
</style>
</head>
<body>

<h1>
  PHP CRUD (Single File)
  <a href="crud.php?action=create" class="btn btn-primary">+ Add User</a>
</h1>

<?php
if (!empty($_SESSION['flash'])) {
    echo '<div class="flash">' . e($_SESSION['flash']) . '</div>';
    unset($_SESSION['flash']);
}

// show errors if present (create/edit)
if (!empty($errors) && is_array($errors)) {
    echo '<div class="errors"><ul>';
    foreach ($errors as $err) echo '<li>' . e($err) . '</li>';
    echo '</ul></div>';
}
?>

<?php if ($action === 'create'): ?>
  <h2>Add New User</h2>
  <form method="post" action="crud.php?action=create">
    <input type="hidden" name="csrf" value="<?php echo e($csrf); ?>">
    <label>Name</label>
    <input type="text" name="name" value="<?php echo e($_POST['name'] ?? ''); ?>" required>
    <label>Email</label>
    <input type="email" name="email" value="<?php echo e($_POST['email'] ?? ''); ?>" required>
    <button class="btn btn-primary" type="submit">Save</button>
    <a class="btn" href="crud.php">Cancel</a>
  </form>

<?php elseif ($action === 'edit'): ?>
  <h2>Edit User</h2>
  <form method="post" action="crud.php?action=edit&id=<?php echo e($user['id']); ?>">
    <input type="hidden" name="csrf" value="<?php echo e($csrf); ?>">
    <label>Name</label>
    <input type="text" name="name" value="<?php echo e($_POST['name'] ?? $user['name']); ?>" required>
    <label>Email</label>
    <input type="email" name="email" value="<?php echo e($_POST['email'] ?? $user['email']); ?>" required>
    <button class="btn btn-primary" type="submit">Update</button>
    <a class="btn" href="crud.php">Cancel</a>
  </form>

<?php else: // list ?>
  <h2>User List</h2>
  <?php if ($res->num_rows === 0): ?>
    <p class="small">No users found. Click "Add User" to create one.</p>
  <?php else: ?>
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Name</th>
          <th>Email</th>
          <th>Created At</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
      <?php while ($row = $res->fetch_assoc()): ?>
        <tr>
          <td><?php echo e($row['id']); ?></td>
          <td><?php echo e($row['name']); ?></td>
          <td><?php echo e($row['email']); ?></td>
          <td><?php echo e($row['created_at']); ?></td>
          <td>
            <a class="btn" href="crud.php?action=edit&id=<?php echo e($row['id']); ?>">Edit</a>
            <!-- Delete via POST form to avoid accidental GET deletes -->
            <form method="post" action="crud.php?action=delete" style="display:inline" onsubmit="return confirm('Delete this user?');">
              <input type="hidden" name="csrf" value="<?php echo e($csrf); ?>">
              <input type="hidden" name="id" value="<?php echo e($row['id']); ?>">
              <button class="btn btn-danger" type="submit">Delete</button>
            </form>
          </td>
        </tr>
      <?php endwhile; ?>
      </tbody>
    </table>
  <?php endif; ?>
<?php endif; ?>

<footer style="margin-top:25px;" class="small">
  Built with ❤️ — single-file CRUD. Use this as learning code; for production separate concerns, add stronger validation & auth.
</footer>

</body>
</html>
