<?php
session_start();
include 'db_connection.php';

// Get user input
$email = $conn->real_escape_string($_POST['email']);
$password = $_POST['password'];

// Fetch user from database
$sql = "SELECT * FROM users WHERE email='$email'";
$result = $conn->query($sql);

// Check if user exists
if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    // Verify password
    if (password_verify($password, $user['password'])) {
        // Set session variables
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['user_name'] = $user['name'] ?? 'User';
        $_SESSION['email'] = $user['email'];
        $_SESSION['phone'] = $user['mobile'];
        $_SESSION['created'] = $user['created_at'];

        // Check if admin
        if ($user['email'] === 'nest_admin1@gmail.com') { 
            $_SESSION['is_admin'] = true;
            session_write_close();
            header("Location: admin_index.php");
        } else {
            $_SESSION['is_admin'] = false;
            session_write_close();
            header("Location: index.php");
        }
        exit;
    } else {
        echo "<script>alert('Incorrect password.'); window.location.href='login.html';</script>";
        exit;
    }
} else {
    echo "<script>alert('No account found with that email.'); window.location.href='login.html';</script>";
    exit;
}

?>
