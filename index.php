<?php
include('supabase.php');
session_start();

// Fetch events
$events = supabase_request('/rest/v1/events?select=*', 'GET');

// Ensure $events is a valid array
if (!is_array($events)) {
    echo "<div style='color: red; padding: 20px; font-weight: bold;'>";
    echo "Error fetching events. Please check your Supabase configuration or try again later.";
    echo "</div>";
    die(); // Stop further execution
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>CampusEvents</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

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
  <!-- Heading -->
  <div class="container text-center my-4">
    <h1 class="display-5 fw-bold">CampusEvents</h1>
    <p class="lead">Explore and register for upcoming events</p>
  </div>
<!-- Carousel with Text Side-by-Side -->
<div class="container my-5">
  <div class="row justify-content-center">
    
    <!-- Carousel Full Width -->
    <div class="col-lg-8">
      <div id="eventCarousel" class="carousel slide mb-4" data-bs-ride="carousel" data-bs-interval="2500">
        <div class="carousel-inner rounded shadow">
          <div class="carousel-item active">
            <img src="https://images.unsplash.com/photo-1504384308090-c894fdcc538d?ixlib=rb-4.0.3&auto=format&fit=crop&w=1050&q=80" class="d-block w-100" alt="Event 1" style="height: 400px; object-fit: cover;">
          </div>
          <div class="carousel-item">
            <img src="https://images.unsplash.com/photo-1501281668745-f7f57925c3b4?ixlib=rb-4.0.3&auto=format&fit=crop&w=1050&q=80" class="d-block w-100" alt="Event 2" style="height: 400px; object-fit: cover;">
          </div>
          <div class="carousel-item">
            <img src="https://images.unsplash.com/photo-1633356122102-3fe601e05bd2?ixlib=rb-4.0.3&auto=format&fit=crop&w=1050&q=80" class="d-block w-100" alt="Event 3" style="height: 400px; object-fit: cover;">
          </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#eventCarousel" data-bs-slide="prev">
          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#eventCarousel" data-bs-slide="next">
          <span class="carousel-control-next-icon" aria-hidden="true"></span>
        </button>
      </div>
    </div>

    <!-- Text + CTA -->
    <div class="col-lg-8 text-center">
      <h2 class="fw-bold mb-3">Join Our Amazing Events</h2>
      <p class="mb-4">Register now and be a part of our growing community. Discover workshops, seminars, and fun experiences tailored just for you!</p>
      <div class="d-flex justify-content-center flex-wrap gap-3">
        <a href="register.php" class="btn btn-success btn-lg px-4">Sign Up Now</a>
        <a href="browse_events.php" class="btn btn-outline-primary btn-lg px-4">Browse Events</a>
      </div>
    </div>    
  </div>
</div>

  <h1 class="text-center mb-4">Upcoming Events</h1>
  <div class="text-center mb-4">
    <p>Join us for our exciting events and workshops. Click on the "Register Now" button to secure your spot!</p>
  <!-- Event Cards -->
  <div class="container">
    <div class="row">
      <?php foreach ($events as $event): ?>
        <div class="col-md-4 mb-4">
        <div class="card shadow-sm h-100">

<!-- Event Image -->
<img src="<?= !empty($event['image_url']) ? htmlspecialchars($event['image_url']) : 'default.jpeg' ?>" 
     class="card-img-top" 
     alt="<?= htmlspecialchars($event['title'] ?? 'Event Image') ?>" 
     style="height: 200px; object-fit: cover;">

<div class="card-body d-flex flex-column">
    <h5 class="card-title"><?= htmlspecialchars($event['title']) ?></h5>
    <p class="card-text"><?= nl2br(htmlspecialchars($event['description'] ?? 'No description.')) ?></p>
    <p class="card-text"><strong>Date:</strong> <?= htmlspecialchars($event['date'] ?? 'N/A') ?></p>
    <p class="card-text"><strong>Location:</strong> <?= htmlspecialchars($event['location'] ?? 'N/A') ?></p>
    
    <!-- Register Now Button -->
    <a href="event_details.php?event_id=<?= urlencode($event['event_id']) ?>" 
       class="btn btn-success mt-auto w-100">View Details</a>
  </div>
</div>

        </div>
      <?php endforeach; ?>
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

</body>
</html>