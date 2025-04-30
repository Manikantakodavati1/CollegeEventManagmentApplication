<?php
session_start();
include('supabase.php');

// Redirect if the user is not logged in
if (!isset($_SESSION['email'])) {
    header('Location: login.php');
    exit();
}

// Get logged-in user info
$user_email = $_SESSION['email'];
$user_email_encoded = urlencode($user_email);

// Fetch events created by the current user
$events = supabase_request("/rest/v1/events?created_by=eq.$user_email_encoded", 'GET');
if (!is_array($events)) {
    echo "Error fetching events. Please try again later.";
    exit();
}

// Count stats
$total_events = count($events);
$total_attendees = 0; // Optional: You can query an "attendees" table based on event ID
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        .floating-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 1000;
        }

        .profile-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .profile-info img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        .card-stat {
            border-left: 5px solid #007bff;
            background-color: #f8f9fa;
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
                        <a href="browse_events.php" class="btn btn-outline-info me-2">Browse Events</a>
                        <div class="profile-info">
                            <a href="profile.php" class="text-decoration-none text-dark d-flex align-items-center">
                                <img src="profile-pic.avif" alt="Profile Picture" width="40" height="40"
                                    class="rounded-circle">
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
    <hr>

    <div class="container my-4">
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card card-stat shadow-sm p-3">
                    <h5>Total Events Created</h5>
                    <h2><?= $total_events ?></h2>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card card-stat shadow-sm p-3">
                    <h5>Total Attendees (Coming Soon)</h5>
                    <h2><?= $total_attendees ?></h2>
                </div>
            </div>
        </div>

        <h4>Your Events</h4>

        <?php if (empty($events)): ?>
            <div class="alert alert-info mt-3" role="alert">
                You have not created any events yet. Click "Create New Event" to get started.
            </div>
        <?php else: ?>
            <div class="container">
                <div class="row">
                    <?php foreach ($events as $event): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card shadow h-100">

                                <!-- Event Image -->
                                <img src="<?= !empty($event['image_url']) ? htmlspecialchars($event['image_url']) : 'default.jpeg' ?>"
                                    class="card-img-top" alt="<?= htmlspecialchars($event['title'] ?? 'Event Image') ?>"
                                    style="height: 200px; object-fit: cover;">

                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title"><?= htmlspecialchars($event['title'] ?? 'Untitled Event') ?></h5>
                                    <p class="card-text">
                                        <?= htmlspecialchars($event['description'] ?? 'No description available.') ?></p>
                                    <p><strong>Date:</strong> <?= htmlspecialchars($event['date'] ?? 'TBD') ?></p>

                                    <!-- Event Settings Button -->
                                    <a href="event_settings.php?event_id=<?= urlencode($event['event_id'] ?? '') ?>"
                                        class="btn btn-outline-secondary mt-auto w-100">
                                        Event Settings
                                    </a>
                                </div>

                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Floating Add Event Button -->
    <a href="create_event.php" class="btn btn-success floating-btn">+ Add Event</a>
      <!-- Footer -->
  <footer class="bg-dark text-white mt-5 p-4">
    <div class="container">
      <div class="row">
        <div class="col-md-3">
          <h5>Support</h5>
          <ul class="list-unstyled">
            <li><a href="#" class="text-white text-decoration-none">FAQs</a></li>
            <li><a href="#" class="text-white text-decoration-none">Help Center</a></li>
            <li><a href="#" class="text-white text-decoration-none">Terms of Service</a></li>
          </ul>
        </div>
        <div class="col-md-3">
          <h5>Quick Links</h5>
          <ul class="list-unstyled">
            <li><a href="index.php" class="text-white text-decoration-none">Home</a></li>
            <li><a href="browse_events.php" class="text-white text-decoration-none">Browse Events</a></li>
            <li><a href="profile.php" class="text-white text-decoration-none">Dashboard</a></li>
          </ul>
        </div>
        <div class="col-md-3">
          <h5>Contact Us</h5>
          <p>Email: support@campusevents.com</p>
          <p>Phone: +91 9652474696</p>
        </div>
        <div class="col-md-3">
          <h5>Follow Us</h5>
          <a href="#" class="text-white me-2"><i class="bi bi-facebook"></i></a>
          <a href="#" class="text-white me-2"><i class="bi bi-twitter"></i></a>
          <a href="#" class="text-white"><i class="bi bi-instagram"></i></a>
        </div>
      </div>
      <hr class="my-3">
      <p class="text-center mb-0">&copy; <?= date('Y') ?> CampusEvents. All rights reserved.</p>
    </div>
  </footer>
</body>

</html>