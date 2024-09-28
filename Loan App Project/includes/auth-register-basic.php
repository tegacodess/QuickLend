<?php
session_start();
include 'db_connect.php'; // Ensure this points to your database connection file

$error = ''; // Initialize error variable
$success = ''; // Initialize success variable

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        // Check if user already exists
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username OR email = :email");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $error = "Username or email already exists.";
            echo $error;
        } else {
            // Insert new user into database
            $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, 'user')");
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $password);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                // Get the newly inserted user ID
                $user_id = $conn->lastInsertId();

                // Set session variables
                $_SESSION['user_id'] = $user_id;
                $_SESSION['role'] = 'user';

                $success = "Sign up successful! You can now log in.";
                echo $success;

                // Redirect to login page or dashboard
                ob_end_clean(); // Clean output buffer
                header("Location: ../template/html/auth-login-basic.html"); // Adjust as needed
                exit();
            } else {
                $error = "Error: Unable to insert data into database.";
                echo $error;
            }
        }
    } catch (PDOException $e) {
        $error = "Error: " . $e->getMessage();
        echo $error;
    }
}
ob_end_flush(); // End output buffering
?>