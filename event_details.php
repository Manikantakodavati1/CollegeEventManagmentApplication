<?php
session_start();
include("supabase.php");

// Get the event ID from the URL
$event_id = $_GET['event_id'] ?? null;

if (!$event_id) {
    die("Invalid request: No event ID provided.");
}

// Fetch the specific event from Supabase
$query = "event_id=eq.$event_id";
$eventData = supabase_select('events', $query);

if (!$eventData || !isset($eventData[0])) {
    die("Event not found.");
}

$event = $eventData[0];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($event['title']) ?> - Event Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
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

    <!-- Event Details -->
    <div class="container mt-5">
        <div class="card">
            <img src="<?= !empty($event['image_url']) ? htmlspecialchars($event['image_url']) : 'default.jpeg' ?>"
                class="card-img-top" alt="<?= htmlspecialchars($event['title']) ?>"
                style="height: 300px; object-fit: cover;">

            <div class="card-body">
                <div class="row">
                    <!-- Left Side: Description -->
                    <div class="col-md-8">
                        <h2 class="card-title mb-3"><?= htmlspecialchars($event['title']) ?></h2>
                        <p><strong>Category:</strong> <?= htmlspecialchars($event['category'] ?? 'General') ?></p>
                        <p><strong>Date:</strong> <?= htmlspecialchars($event['date']) ?></p>
                        <p><strong>Location:</strong> <?= htmlspecialchars($event['location']) ?></p>
                        <p><strong>Description:</strong><br><?= nl2br(htmlspecialchars($event['description'])) ?></p>
                        <?php if (!empty($event['prerequisites'])): ?>
                            <p><strong>Prerequisites:</strong><br><?= nl2br(htmlspecialchars($event['prerequisites'])) ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- Right Side: Registration Info -->
                    <div class="col-md-4">
                        <div class="border p-3 rounded bg-light">
                            <h5 class="text-primary">Registration Details</h5>
                            <p><strong>Max Participants:</strong> <?= htmlspecialchars($event['max_participants'] ?? 'Unlimited') ?></p>
                            <p><strong>Registration Deadline:</strong> <?= htmlspecialchars($event['registration_deadline'] ?? 'No deadline') ?></p>
                            <p><strong>Registration Fee:</strong> <?= isset($event['registration_fee']) ? '₹' . htmlspecialchars($event['registration_fee']) : 'Free' ?></p>
                            <p><strong>Contact Email:</strong><br><a href="mailto:<?= htmlspecialchars($event['contact_email']) ?>"><?= htmlspecialchars($event['contact_email']) ?></a></p>

                            <a href="event_register.php?event_id=<?= urlencode($event['event_id']) ?>" class="btn btn-success w-100 mt-3">Register Now</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Feedback Modal -->
    <?php if (isset($_GET['status']) && $_GET['status'] === 'success'): ?>
        <div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-success">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" id="statusModalLabel">Success</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        🎉 Registration successful!
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <script>
            const modal = new bootstrap.Modal(document.getElementById('statusModal'));
            window.addEventListener('DOMContentLoaded', () => modal.show());
        </script>
    <?php elseif (isset($_GET['message'])): ?>
        <div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-warning">
                    <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title" id="statusModalLabel">Notice</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <?= htmlspecialchars(urldecode($_GET['message'])) ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <script>
            const modal = new bootstrap.Modal(document.getElementById('statusModal'));
            window.addEventListener('DOMContentLoaded', () => modal.show());
        </script>
    <?php endif; ?>
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
