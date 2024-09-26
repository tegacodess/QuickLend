<?php
session_start();
include 'db_connect.php'; // Ensure this points to your database connection file

$error = ''; // Initialize error variable
$success = ''; // Initialize success variable

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
        // Here we directly compare the plain text password with the stored password
        if ($password === $user['password']) {
            // Successful login
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $success = "Login successful!"; // Indicate successful login
            echo $success;

            // Redirect based on user role
            if ($user['role'] === 'admin') {
                header("Location: ../template/html/index.html");
            } else {
                header("Location: ../template/html/pages-account-settings-account.html"); // Adjust as needed
            }
            exit();
        } else {
            $error = "Incorrect password."; // Password mismatch
            echo $error;
        }
    } else {
        $error = "Username not found."; // User not found
        echo $error;
    }
}
?>
