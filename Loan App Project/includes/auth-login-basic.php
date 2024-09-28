<?php
session_start();
include 'db_connect.php'; // Ensure this points to your database connection file

// Initialize error and success variables
$error = ''; 
$success = ''; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['email-username'];
    $password = $_POST['password'];

    // Prepare the SQL statement to check for the user by username
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    // Fetch user data
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if user exists and verify password
    if ($user) {
        if (password_verify($password, $user['password'])) {
            // Successful login
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['success'] = "Login successful!"; // Store success message in session

            // Store redirect URL in session based on role
            if ($user['role'] === 'admin') {
                $_SESSION['redirect_url'] = '../template/html/admin-dashboard.php'; // Admin dashboard
            } else {
                $_SESSION['redirect_url'] = 'index.html'; // User dashboard
            }

            // Redirect back to the login page to show the success message
            header("Location: ../template/html/auth-login-basic.php");
            exit();
        } else {
            $_SESSION['error'] = "Incorrect password."; // Store error message in session
        }
    } else {
        $_SESSION['error'] = "Username not found."; // Store error message in session
    }

    // Redirect back to the login page if there's an error
    header("Location: ../template/html/auth-login-basic.php");
    exit();
}
?>
