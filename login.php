<?php
include('supabase.php');
session_start();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = $_POST['email'];
  $password = $_POST['password'];

  $users = supabase_request("/rest/v1/users?email=eq.$email", 'GET');

  if (!empty($users) && password_verify($password, $users[0]['password'])) {
    $_SESSION['email'] = $email;
    $_SESSION['name'] = $users[0]['name'];
    $_SESSION['role'] = $users[0]['role'];  // Save the role in session

    if ($_SESSION['role'] === 'student') {
      header('Location: my_events.php');
    } elseif ($_SESSION['role'] === 'faculty') {
      header('Location: dashboard.php');
    } else {
      header('Location: my_events.php');
    }
    exit();
  } else {
    $message = "Invalid email or password!";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Campus Events | Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
    }
    .login-card {
      max-width: 400px;
      margin: 50px auto;
      border: none;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    .login-header {
      background: linear-gradient(to right, #3b82f6, #8b5cf6);
      color: white;
      border-top-left-radius: 10px;
      border-top-right-radius: 10px;
      padding: 20px 0;
      text-align: center;
    }
    .demo-btn {
      background-color: #f1f1f1;
      color: #333;
      font-weight: 600;
      border: none;
    }
    .demo-btn:hover {
      background-color: #e2e2e2;
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
                    <a href="register.php" class="btn btn-outline-success">Register</a>
                <?php else: ?>
                    <div class="container mt-2 d-flex justify-content-between align-items-center">
                        <div class="profile-info">
                            <a href="profile.php" class="text-decoration-none text-dark d-flex align-items-center">
                                <img src="profile-pic.avif" alt="Profile Picture" width="40" height="40" class="rounded-circle">
                                <div class="ms-2">
                                    <strong><?= htmlspecialchars($_SESSION['name']) ?></strong><br>
                                    <small><?= htmlspecialchars($_SESSION['email']) ?></small>
                                </div>
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </nav>

<div class="card login-card">
  <div class="login-header">
    <h2>Welcome Back!</h2>
    <p class="small mb-0">Sign in to your account to continue</p>
  </div>

  <div class="card-body p-4">
    <?php if (!empty($message)): ?>
      <div class="alert alert-danger text-center"><?php echo $message; ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="mb-3">
        <label class="form-label">Email Address</label>
        <input type="email" name="email" class="form-control" placeholder="your.email@example.com" required>
      </div>
      <div class="mb-2">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" placeholder="********" required>
      </div>
      <div class="d-grid">
        <button type="submit" class="btn btn-primary">Sign In</button>
      </div>
    </form>

    <div class="text-center mt-3">
      <p class="small">Don't have an account? <a href="register.php" >Register Now</a></p>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
