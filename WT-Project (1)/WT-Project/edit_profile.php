<?php
session_start();
include('supabase.php');

// Function to get user by email
function get_user_by_email($email) {
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    $query = "email=eq.$email";
    return supabase_select('users', $query);
}

// Function to update user data
function update_user_profile($user_id, $data) {
    $matchQuery = "user_id=eq.$user_id";
    $response = supabase_update('users', $matchQuery, $data);
    
    // Log the response to check for errors
    if (!$response || isset($response['error'])) {
        // If there's an error, log the response and return false
        error_log("Error updating profile: " . json_encode($response));
        return false;
    }
    return true;
}

// Check if user is logged in (you can use session or other methods)
if (!isset($_SESSION['email'])) {
    die("You must be logged in to edit your profile.");
}

$email = $_SESSION['email']; // Get the logged-in user's email

// Fetch current user data from the database
$user = get_user_by_email($email);

if ($user) {
    $user = $user[0]; // Assuming you only get one user with the matching email
    $user_id = $user['user_id'];
    $current_name = $user['name'];
    $current_email = $user['email'];
    $current_bio = $user['bio'];
    $current_academic_year = $user['academic_year'];
    $current_department = $user['department'];
    $current_interests = $user['interests'];
    $events_participated = $user['events_participated'];
} else {
    die("User not found.");
}

// Handle the form submission (if the user updates their profile)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_name = $_POST['name'];
    $new_bio = $_POST['bio'];
    $new_academic_year = $_POST['academic_year'];
    $new_department = $_POST['department'];
    $new_interests = $_POST['interests'];

    // Prepare data to update in the database
    $update_data = [
        'name' => $new_name,
        'bio' => $new_bio,
        'academic_year' => $new_academic_year,
        'department' => $new_department,
        'interests' => $new_interests
    ];

    // Update user data in Supabase
    $update_result = update_user_profile($user_id, $update_data);

    // Check the result of the update operation
    if ($update_result) {
        echo "<script>alert('Profile updated successfully!'); window.location.href = 'profile_page.php';</script>";
    } else {
        echo "<script>alert('Error updating profile. Please try again later.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7f6;
            color: #333;
            padding: 20px;
        }
        h2 {
            color: #2c3e50;
            text-align: center;
        }
        .form-container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 50%;
            margin: 0 auto;
        }
        label {
            font-size: 16px;
            font-weight: bold;
            display: block;
            margin-bottom: 8px;
        }
        input[type="text"],
        input[type="email"],
        textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }
        input[type="email"]:disabled {
            background-color: #e9ecef;
        }
        button {
            background-color: #3498db;
            color: white;
            font-size: 16px;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }
        button:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>

    <h2>Edit Profile</h2>

    <!-- Display the current profile data in the form -->
    <div class="form-container">
        <form method="POST" action="">
            <label for="name">Name:</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($current_name); ?>" required>

            <label for="email">Email:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($current_email); ?>" required disabled>

            <label for="bio">Bio:</label>
            <textarea name="bio"><?php echo htmlspecialchars($current_bio); ?></textarea>

            <label for="academic_year">Academic Year:</label>
            <input type="text" name="academic_year" value="<?php echo htmlspecialchars($current_academic_year); ?>">

            <label for="department">Department:</label>
            <input type="text" name="department" value="<?php echo htmlspecialchars($current_department); ?>">

            <label for="interests">Interests:</label>
            <input type="text" name="interests" value="<?php echo htmlspecialchars($current_interests); ?>">

            <button type="submit">Update Profile</button>
        </form>
    </div>

</body>
</html>
