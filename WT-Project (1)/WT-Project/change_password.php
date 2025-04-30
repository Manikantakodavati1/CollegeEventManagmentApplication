<?php
include('supabase.php');
session_start();

if (!isset($_SESSION['email'])) {
  // User must be logged in to change password
  header('Location: login.php');
  exit();
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $current_password = $_POST['current_password'];
  $new_password = $_POST['new_password'];
  $confirm_password = $_POST['confirm_password'];

  if ($new_password !== $confirm_password) {
    $message = "New passwords do not match!";
  } else {
    // Fetch user details
    $email = $_SESSION['email'];
    $user = supabase_request("/rest/v1/users?email=eq.$email", 'GET');

    if (!empty($user)) {
      $user = $user[0];
      if (password_verify($current_password, $user['password'])) {
        // Update password
        $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update_data = ['password' => $hashed_new_password];
        $update = supabase_request("/rest/v1/users?id=eq." . $user['id'], 'PATCH', $update_data);

        if ($update) {
          $message = "Password updated successfully!";
        } else {
          $message = "Failed to update password.";
        }
      } else {
        $message = "Current password is incorrect!";
      }
    } else {
      $message = "User not found!";
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Change Password</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
    }
    .change-pass-card {
      max-width: 500px;
      margin: 50px auto;
      border: none;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    .change-header {
      background: linear-gradient(to right, #ef4444, #f59e0b);
      color: white;
      border-top-left-radius: 10px;
      border-top-right-radius: 10px;
      padding: 20px 0;
      text-align: center;
    }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light p-3">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="index.php">
      <img src="logo.jpg" alt="Logo" width="30" height="30" class="d-inline-block align-text-top">
      CampusEvents
    </a>
    <div class="ms-auto">
      <?php if (!isset($_SESSION['email'])): ?>
        <a href="login.php" class="btn btn-outline-primary me-2">Login</a>
      <?php else: ?>
        <a href="profile.php" class="btn btn-outline-secondary me-2">Profile</a>
        <a href="logout.php" class="btn btn-outline-danger">Logout</a>
      <?php endif; ?>
    </div>
  </div>
</nav>

<div class="card change-pass-card">
  <div class="change-header">
    <h2>Change Password</h2>
    <p class="small mb-0">Update your account security</p>
  </div>

  <div class="card-body p-4">
    <?php if (!empty($message)): ?>
      <div class="alert alert-info text-center"><?php echo $message; ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="mb-3">
        <label class="form-label">Current Password</label>
        <input type="password" name="current_password" class="form-control" placeholder="Enter current password" required>
      </div>
      <div class="mb-3">
        <label class="form-label">New Password</label>
        <input type="password" name="new_password" class="form-control" placeholder="Enter new password" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Confirm New Password</label>
        <input type="password" name="confirm_password" class="form-control" placeholder="Confirm new password" required>
      </div>
      <div class="d-grid">
        <button type="submit" class="btn btn-warning">Update Password</button>
      </div>
    </form>

    <div class="text-center mt-3">
      <a href="profile.php" class="btn btn-link">Back to Profile</a>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
