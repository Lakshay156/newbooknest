<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'booknest';
$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get data from POST
$user_id = $_SESSION['user_id'];
$books_id = $_POST['books_id'];
$book_rent = $_POST['book_rent'];  // 👈 Used correctly here

// ✅ Insert into borrowings (make sure your table has these columns: user_id, books_id, book_rent, borrowed_on, status)
$sql = "INSERT INTO borrowings (user_id, books_id, book_rent, borrowed_on, status) VALUES (?, ?, ?, NOW(), 'borrowed')";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $user_id, $books_id, $book_rent);

if ($stmt->execute()) {
    header("Location: cart.php");
    exit;
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
