<?php
session_start();
include("supabase.php");

// Handle search, filter, and sort
$search = $_GET['search'] ?? '';
$location = $_GET['location'] ?? '';
$sort = $_GET['sort'] ?? 'date.asc';

$queryParts = [];

if ($search) {
    $searchEncoded = urlencode("%$search%");
    $queryParts[] = "or=(title.ilike.$searchEncoded,description.ilike.$searchEncoded)";
}

if ($location) {
    $queryParts[] = "location=eq." . urlencode($location);
}

$query = implode("&", $queryParts);
$query .= "&order=" . urlencode($sort);

$events = supabase_select('events', $query);

// Fetch distinct locations for filter dropdown
$allEvents = supabase_select('events');
$locations = array_unique(array_column($allEvents, 'location'));
sort($locations);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Browse Events</title>
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
    <div class="container mt-5">
        <h1 class="mb-4 text-center">Browse Events</h1>

        <form method="get" class="row g-3 mb-4">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Search events..."
                    value="<?= htmlspecialchars($search) ?>">
            </div>

            <div class="col-md-3">
                <select name="location" class="form-select">
                    <option value="">All Locations</option>
                    <?php foreach ($locations as $loc): ?>
                        <option value="<?= htmlspecialchars($loc) ?>" <?= $loc === $location ? 'selected' : '' ?>>
                            <?= htmlspecialchars($loc) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3">
                <select name="sort" class="form-select">
                    <option value="date.asc" <?= $sort === 'date.asc' ? 'selected' : '' ?>>Date ↑</option>
                    <option value="date.desc" <?= $sort === 'date.desc' ? 'selected' : '' ?>>Date ↓</option>
                </select>
            </div>

            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Apply</button>
            </div>
        </form>

        <div class="row">
            <?php if ($events && is_array($events)): ?>
                <?php foreach ($events as $event): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card shadow-sm h-100">

                            <!-- Event Image -->
                            <img src="<?= !empty($event['image_url']) ? htmlspecialchars($event['image_url']) : 'default.jpeg' ?>"
                                class="card-img-top" alt="<?= htmlspecialchars($event['title'] ?? 'Event Image') ?>"
                                style="height: 200px; object-fit: cover;">

                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?= htmlspecialchars($event['title']) ?></h5>
                                <p class="card-text"><?= nl2br(htmlspecialchars($event['description'] ?? 'No description.')) ?>
                                </p>
                                <p class="card-text"><strong>Date:</strong> <?= htmlspecialchars($event['date'] ?? 'N/A') ?></p>
                                <p class="card-text"><strong>Location:</strong>
                                    <?= htmlspecialchars($event['location'] ?? 'N/A') ?></p>

                                <!-- Register Now Button -->
                                <a href="event_details.php?event_id=<?= urlencode($event['event_id']) ?>"
                                    class="btn btn-success mt-auto w-100">View Details</a>
                            </div>
                        </div>

                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-warning text-center" role="alert">
                        No matching events found.
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

</body>

</html>