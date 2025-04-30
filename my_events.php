<?php
include('supabase.php');
session_start();

// Ensure user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

// Fetch registrations for logged in user
$userEmail = urlencode($_SESSION['email']);
$registrations = supabase_request("/rest/v1/event_registrations?user_email=eq.$userEmail&select=event_id", 'GET');

// Initialize array
$registeredEvents = [];

// If user has registrations
if (is_array($registrations) && count($registrations) > 0) {
    // Extract event IDs
    $eventIds = array_column($registrations, 'event_id');

    // Build event_ids for IN query
    if (!empty($eventIds)) {
        $eventIdsStr = implode(',', $eventIds);
        $registeredEvents = supabase_request("/rest/v1/events?event_id=in.($eventIdsStr)&select=*", 'GET');
    }
}

// Fetch upcoming events (date greater than current date)
$upcomingEvents = supabase_request("/rest/v1/events?&select=*", 'GET');

// Ensure the fetched data is valid
if (!is_array($registeredEvents) || !is_array($upcomingEvents)) {
    echo "<div style='color: red; padding: 20px; font-weight: bold;'>";
    echo "Error fetching events. Please check your Supabase configuration or try again later.";
    echo "</div>";
    die(); // Stop further execution
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>My Events</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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

<!-- Main Content -->
<div class="container mt-5">

  <!-- Registered Events -->
  <h2 class="mb-4">📅 Registered Events</h2>
  <div class="row">
    <?php if (!empty($registeredEvents)): ?>
      <?php foreach ($registeredEvents as $event): ?>
        <div class="col-md-4 mb-4">
          <div class="card shadow h-100">
            <div class="card-body">
              <h5 class="card-title"><?= htmlspecialchars($event['title']) ?></h5>
              <p class="card-text"><?= htmlspecialchars($event['description']) ?></p>
              <p><strong>Date:</strong> <?= htmlspecialchars($event['date']) ?></p>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="col-12">
        <p>No registered events found. You can explore upcoming events and register!</p>
      </div>
    <?php endif; ?>
  </div>

  <!-- Upcoming Events -->
  <h1 class="text-center mt-5 mb-4">Upcoming Events</h1>
  <div class="text-center mb-4">
    <p>Join us for our exciting events and workshops. Click on "Event Settings" to manage your event registrations!</p>
  </div>

  <div class="row">
    <?php if (!empty($upcomingEvents)): ?>
      <?php foreach ($upcomingEvents as $event): ?>
        <div class="col-md-4 mb-4">
          <div class="card shadow h-100">
            <img src="<?= !empty($event['image_url']) ? htmlspecialchars($event['image_url']) : 'default.jpeg' ?>"
              class="card-img-top" alt="<?= htmlspecialchars($event['title'] ?? 'Event Image') ?>"
              style="height: 200px; object-fit: cover;">
            <div class="card-body d-flex flex-column">
              <h5 class="card-title"><?= htmlspecialchars($event['title'] ?? 'Untitled Event') ?></h5>
              <p class="card-text"><?= htmlspecialchars($event['description'] ?? 'No description available.') ?></p>
              <p><strong>Date:</strong> <?= htmlspecialchars($event['date'] ?? 'TBD') ?></p>
              <a href="event_details.php?event_id=<?= urlencode($event['event_id'] ?? '') ?>" class="btn btn-outline-primary mt-auto w-100">
                View Deatails
              </a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="col-12">
        <p>No upcoming events available at the moment. Stay tuned!</p>
      </div>
    <?php endif; ?>
  </div>

</div>
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
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
