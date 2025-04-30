<?php
session_start();
include('supabase.php');

// Redirect if not logged in
if (!isset($_SESSION['email'])) {
    header('Location: login.php');
    exit();
}

// Get Event ID
$event_id = $_GET['event_id'] ?? null;
if (!$event_id) {
    echo "Event ID not provided.";
    exit();
}

// Fetch the event
$response = supabase_request("/rest/v1/events?event_id=eq.$event_id", 'GET');
$event = $response[0] ?? null;

if (!$event || $event['created_by'] !== $_SESSION['email']) {
    echo "Unauthorized access or event not found.";
    exit();
}

// Determine Back Page
$backPage = 'dashboard.php'; // default for faculty
if (isset($_SESSION['role']) && $_SESSION['role'] === 'student') {
    $backPage = 'my_events.php';
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update'])) {
        $data = [
            'title' => $_POST['title'],
            'description' => $_POST['description'],
            'date' => $_POST['date'],
            'location' => $_POST['location'],
            'category' => $_POST['category'],
            'max_participants' => (int) $_POST['max_participants'],
            'registration_deadline' => $_POST['registration_deadline'],
            'prerequisites' => $_POST['prerequisites'],
            'contact_email' => $_POST['contact_email'],
            'image_url' => $_POST['image_url'],
            'updated_at' => date('c')
        ];

        $update = supabase_request("/rest/v1/events?event_id=eq.$event_id", 'PATCH', $data);

        $message = "Event updated successfully.";
        $event = array_merge($event, $data);
    } elseif (isset($_POST['delete'])) {
        supabase_request("/rest/v1/events?event_id=eq.$event_id", 'DELETE');
        header("Location: $backPage");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Event</title>
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

    <!-- Main Content -->
    <div class="container mt-5">
        <h2>Edit Event</h2>

        <?php if ($message): ?>
            <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <form method="POST">
            <?php
            function input($name, $label, $type = 'text') {
                global $event;
                $value = htmlspecialchars($event[$name] ?? '');
                echo <<<HTML
                <div class="mb-3">
                    <label for="$name" class="form-label">$label</label>
                    <input type="$type" name="$name" id="$name" class="form-control" value="$value">
                </div>
                HTML;
            }

            input('title', 'Event Title');
            echo '<div class="mb-3"><label for="description" class="form-label">Event Description</label>';
            echo '<textarea name="description" id="description" rows="3" class="form-control">' . htmlspecialchars($event['description']) . '</textarea></div>';
            input('date', 'Event Date', 'date');
            input('location', 'Location');
            input('category', 'Category');
            input('max_participants', 'Max Participants', 'number');
            input('registration_deadline', 'Registration Deadline', 'date');
            input('prerequisites', 'Prerequisites');
            input('contact_email', 'Contact Email', 'email');
            input('image_url', 'Image URL', 'url');
            ?>

            <div class="d-flex gap-2">
                <button type="submit" name="update" class="btn btn-primary">Update</button>
                <button type="submit" name="delete" class="btn btn-danger"
                    onclick="return confirm('Are you sure you want to delete this event?')">Delete</button>
                <a href="<?= $backPage ?>" class="btn btn-secondary">Back</a>
            </div>
        </form>
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
