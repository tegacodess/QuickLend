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

require_once 'db_connect.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data from your updated form
    $username = isset($_POST['username']) ? $_POST['username'] : null;
    $email = isset($_POST['email']) ? $_POST['email'] : null;
    $password = isset($_POST['password']) ? $_POST['password'] : null;
    $terms = isset($_POST['terms']) ? $_POST['terms'] : null;

    /* Debugging to ensure data is received correctly
    echo "Form data received!<br>";
    echo "Username: $username <br>";
    echo "Email: $email <br>";
    echo "Password: $password <br>";
    */
    
    // Make sure terms are checked
    if (!$terms) {
        // Save error message to session
        $_SESSION['error'] = "Please accept the terms and conditions.";
        
        // Redirect back to the registration page
        header("Location: ../template/html/auth-register-basic.php");
        exit();
    }

    // Check if email already exists using PDO
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$email]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
       // echo "Email already registered.<br>";
        $error = "Email already registered. You can log in instead.";
        $_SESSION['error'] = $error;
        // Redirect back to the registration page
        header("Location: ../template/html/auth-login-basic.php");
        exit();
    } else {
        echo "Email not registered, proceeding with registration.<br>";
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    echo "Password hashed successfully.<br>";

    // Insert into the database using PDO
    $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    if ($stmt->execute([$username, $email, $hashed_password])) {
        echo "Registration successful!<br>";
        header("Location: ../template/html/auth-login-basic.php");
        exit();
    } else {
        echo "Error executing INSERT statement.<br>";
    }

    // Close the connection (PDO does not need explicit closing)
}
?>
