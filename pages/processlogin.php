<?php
error_reporting(E_ALL);

ini_set('display_errors', 1);
// Start session at the beginning to handle sessions correctly
session_start();

// Include the config to access the database
require_once('../includes/config.php');  // Ensure this path is correct

// Check if the form was submitted and the request is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Authenticate the user
    if (authenticateUser($username, $password)) {
        // Redirect to the dashboard or another appropriate page on success
        header('Location: dashboard.php');
        exit();
    } else {
        // Authentication failed
        echo "Login failed: Invalid email or password.";
    }
} else {
    // Not a POST request
    echo "Invalid request method.";
}

// Function to authenticate user
function authenticateUser($username, $password) {
    // Get the database connection
    $mysqli = getConnection();

    // Prepare SQL statement to prevent SQL injection
    $stmt = $mysqli->prepare("SELECT id, username, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    if ($user = $result->fetch_assoc()) {
        // Verify the password
        if (password_verify($password, $user['password'])) {
            // Password is correct, set up session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            return true;
        } else {
            // Password is not correct
            return false;
        }
    } else {
        // No user found with that email address
        return false;
    }
}
?>
