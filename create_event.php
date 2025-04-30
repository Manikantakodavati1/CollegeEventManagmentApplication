<?php
session_start();
include('supabase.php');

if (!isset($_SESSION['email'])) {
    header('Location: login.php');
    exit();
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'title' => $_POST['title'],
        'description' => $_POST['description'],
        'date' => $_POST['date'],
        'location' => $_POST['location'],
        'category' => $_POST['category'],
        'max_participants' => (int)$_POST['max_participants'],
        'registration_fee' => (float)$_POST['registration_fee'],
        'registration_deadline' => $_POST['registration_deadline'],
        'prerequisites' => $_POST['prerequisites'],
        'contact_email' => $_POST['contact_email'],
        'image_url' => $_POST['image_url'],
        'created_by' => $_SESSION['email'],
        'created_at' => date('c'),
        'updated_at' => date('c')
    ];

    $response = supabase_request('/rest/v1/events', 'POST', $data);
    header("Location: my_events.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Create New Event | CampusEvents</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(to right, #f8f9fa, #e9ecef);
    }
    .card {
      border: none;
      border-radius: 16px;
      box-shadow: 0 8px 24px rgba(0,0,0,0.1);
      padding: 2rem;
    }
    .form-control {
      border-radius: 10px;
    }
    .btn {
      border-radius: 10px;
      padding: 10px 20px;
    }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm p-3">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold d-flex align-items-center" href="index.php">
      <img src="logo.jpg" alt="Logo" width="30" height="30" class="d-inline-block me-2">
      CampusEvents
    </a>
    <div class="ms-auto">
      <a href="profile.php" class="text-decoration-none text-dark d-flex align-items-center">
        <img src="profile-pic.avif" alt="Profile Picture" width="40" height="40" class="rounded-circle me-2">
        <div>
          <strong><?= htmlspecialchars($_SESSION['name']) ?></strong><br>
          <small><?= htmlspecialchars($_SESSION['email']) ?></small>
        </div>
      </a>
    </div>
  </div>
</nav>

<!-- Form Container -->
<div class="container d-flex justify-content-center align-items-center my-5">
  <div class="card w-100" style="max-width: 700px;">
    <h3 class="mb-4 text-center fw-bold text-primary">Create a New Event</h3>

    <?php if ($message): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="mb-3">
        <label for="title" class="form-label">Event Title</label>
        <input type="text" name="title" id="title" required class="form-control">
      </div>

      <div class="mb-3">
        <label for="description" class="form-label">Event Description</label>
        <textarea name="description" id="description" rows="3" class="form-control"></textarea>
      </div>

      <div class="row">
        <div class="col-md-6 mb-3">
          <label for="date" class="form-label">Event Date</label>
          <input type="date" name="date" id="date" required class="form-control">
        </div>
        <div class="col-md-6 mb-3">
          <label for="registration_deadline" class="form-label">Registration Deadline</label>
          <input type="date" name="registration_deadline" id="registration_deadline" class="form-control">
        </div>
      </div>

      <div class="mb-3">
        <label for="location" class="form-label">Location</label>
        <input type="text" name="location" id="location" class="form-control">
      </div>

      <div class="mb-3">
        <label for="category" class="form-label">Category</label>
        <input type="text" name="category" id="category" class="form-control">
      </div>

      <div class="row">
        <div class="col-md-6 mb-3">
          <label for="max_participants" class="form-label">Max Participants</label>
          <input type="number" name="max_participants" id="max_participants" class="form-control">
        </div>
        <div class="col-md-6 mb-3">
          <label for="registration_fee" class="form-label">Registration Fee (₹)</label>
          <input type="number" step="0.01" name="registration_fee" id="registration_fee" required class="form-control">
        </div>
      </div>

      <div class="mb-3">
        <label for="prerequisites" class="form-label">Prerequisites</label>
        <input type="text" name="prerequisites" id="prerequisites" class="form-control">
      </div>

      <div class="mb-3">
        <label for="contact_email" class="form-label">Contact Email</label>
        <input type="email" name="contact_email" id="contact_email" class="form-control">
      </div>

      <div class="mb-4">
        <label for="image_url" class="form-label">Image URL (optional)</label>
        <input type="url" name="image_url" id="image_url" class="form-control">
      </div>

      <div class="d-flex justify-content-between">
        <a href="dashboard.php" class="btn btn-secondary">← Cancel</a>
        <button type="submit" class="btn btn-success">Create Event</button>
      </div>
    </form>
  </div>
</div>

</body>
</html>