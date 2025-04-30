<?php
session_start();
include("supabase.php");

// Ensure user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php?message=" . urlencode("Please login to register for events."));
    exit;
}

$email = $_SESSION['email'];
$event_id = $_GET['event_id'] ?? null;

if (!$event_id) {
    header("Location: event_details.php?event_id=$event_id&message=" . urlencode("Invalid event ID."));
    exit;
}

// Check if already registered
$query = "event_id=eq." . urlencode($event_id) . "&user_email=eq." . urlencode($email);
$existing = supabase_select("event_registrations", $query);

if ($existing && count($existing) > 0) {
    $msg = urlencode("You have already registered for this event.");
    header("Location: event_details.php?event_id=$event_id&message=$msg");
    exit;
}

// Register the user
$data = [
    "event_id" => $event_id,
    "user_email" => $email,
    "registered_at" => date("c")  // ISO 8601 timestamp
];

$response = supabase_insert("event_registrations", $data);

header("Location: event_details.php?event_id=$event_id&status=success");
exit;
?>