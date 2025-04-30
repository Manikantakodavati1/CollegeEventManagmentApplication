<?php
session_start();

// Include the Supabase functions
include('supabase.php');

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header('Location: login.php');
    exit();
}

// Function to get user by email
function get_user_by_email($email) {
    // Ensure email is sanitized and safe to use in query
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);

    // Query to fetch the user by email
    $query = "email=eq.$email"; // This is the query format for Supabase
    $result = supabase_select('users', $query);

    // Return the result (it should return an array of users)
    return $result;
}

// Function to get events by user ID
function get_user_events($user_id) {
  // Sanitize the user ID to avoid SQL injection issues
  $user_id = filter_var($user_id, FILTER_SANITIZE_NUMBER_INT);

  // Query to fetch the events associated with the user ID
  $query = "user_id=eq.$user_id"; // Assuming your table has a 'user_id' column
  $result = supabase_select('events', $query); // 'events' is the table name

  // Return the result (an array of events)
  return $result;
}

// Fetch user data based on email
$user_email = $_SESSION['email'];
$user_data = get_user_by_email($user_email);

// Check if user exists
if (empty($user_data)) {
    echo "User not found!";
    exit();
}

// Get user details
$user = $user_data[0];  // Assuming single user response
$user_id = $user['user_id'];

// Fetch events the user is registered for
$events_data = get_user_events($user_id);
$events = $events_data ?? [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Profile | CampusEvents</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
    }
    .gradient-header {
      background: linear-gradient(90deg, #4f46e5, #6366f1);
      border-top-left-radius: 16px;
      border-top-right-radius: 16px;
      padding: 40px 20px;
      color: white;
    }
    .profile-pic-lg {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      border: 4px solid white;
      object-fit: cover;
    }
    .nav-tabs .nav-link.active {
      border-bottom: 3px solid #4f46e5;
      font-weight: bold;
    }
    .card-custom {
      border-radius: 16px;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
    }
    .edit-btn {
      position: absolute;
      top: 20px;
      right: 20px;
    }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold d-flex align-items-center" href="index.php">
      <img src="logo.jpg" alt="Logo" width="30" height="30" class="me-2">
      CampusEvents
    </a>
    <div class="ms-auto d-flex align-items-center">
      <a href="profile.php" class="text-decoration-none text-dark d-flex align-items-center">
        <img src="profile-pic.avif" alt="Profile Picture" width="40" height="40" class="rounded-circle me-2">
        <div>
          <strong><?= htmlspecialchars($user['name']) ?></strong><br>
          <small><?= htmlspecialchars($user['email']) ?></small>
        </div>
      </a>
    </div>
  </div>
</nav>

<!-- Profile Page -->
<div class="container my-5">
  <div class="card card-custom overflow-hidden">
    <div class="gradient-header text-center position-relative">
      <img src="profile-pic.avif" alt="Profile" class="profile-pic-lg mb-3">
      <h3 class="fw-bold mb-0"><?= htmlspecialchars($user['name']) ?></h3>
      <span class="badge bg-light text-dark mt-2"><?= strtoupper($user['role']) ?></span>
      <a href="edit_profile.php" class="btn btn-light btn-sm edit-btn">Edit Profile</a>
    </div>

    <ul class="nav nav-tabs px-4 pt-3" id="profileTabs" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile"
                type="button" role="tab">Profile</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="events-tab" data-bs-toggle="tab" data-bs-target="#events"
                type="button" role="tab">Events</button>
      </li>
    </ul>

    <div class="tab-content p-4" id="profileTabsContent">
      <!-- Profile Tab -->
      <div class="tab-pane fade show active" id="profile" role="tabpanel">
        <div class="row">
          <div class="col-md-6">
            <h5 class="mb-3">Personal Information</h5>
            <p><strong>Full Name:</strong> <?= htmlspecialchars($user['name']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
            <p><strong>Department:</strong> <?= htmlspecialchars($user['department']) ?></p>
            <p><strong>Year:</strong> <?= htmlspecialchars($user['academic_year']) ?></p>
            <p><strong>Interests:</strong> <?= htmlspecialchars($user['interests']) ?></p>
            <p><strong>Bio:</strong> <?= htmlspecialchars($user['bio']) ?></p>
          </div>
          <div class="col-md-6">
            <h5 class="mb-3">Account</h5>
            <p><strong>Account Type:</strong> <?= htmlspecialchars($user['role']) ?></p>
            <p><strong>Member Since:</strong> <?= date("F Y", strtotime($user['created_at'])) ?></p>
            <p><strong>Events Participated:</strong> <?= count($events) ?></p>
          </div>
          <div class="text-center">
            <a href="logout.php" class="btn btn-outline-danger" style="width:100px;">Logout</a> 
          </div>
        </div>
      </div>

      <!-- Events Tab -->
      <div class="tab-pane fade" id="events" role="tabpanel">
        <?php if (count($events) > 0): ?>
          <ul class="list-group">
            <?php foreach ($events as $event): ?>
              <li class="list-group-item">
                <?= htmlspecialchars($event['event_name']) ?>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php else: ?>
          <p>No events listed yet.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
