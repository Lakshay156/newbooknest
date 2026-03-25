<?php
session_start();
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'booknest';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$book_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$book = null;

if ($book_id > 0) {
    $stmt = $conn->prepare("SELECT * FROM books WHERE books_id = ?");
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $book = $result->fetch_assoc();
    }
}

if (!$book) {
    die("Book not found.");
}

$books_count = isset($_SESSION['borrowed_count']) ? $_SESSION['borrowed_count'] : 0;
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>BookNest | <?php echo htmlspecialchars($book['title']); ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Outfit', sans-serif; background-color: #f8fafc; overflow-x: hidden;}
    .glass-nav {
      background: rgba(255, 255, 255, 0.7);
      backdrop-filter: blur(16px);
      -webkit-backdrop-filter: blur(16px);
      border: 1px solid rgba(255, 255, 255, 0.3);
    }
    .btn-gradient {
      background: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%);
      transition: all 0.3s ease;
    }
    .btn-gradient:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 15px -3px rgba(234, 88, 12, 0.3);
    }
    .nav-link { position: relative; }
    .nav-link::after {
      content: ''; position: absolute; width: 0; height: 2px;
      bottom: -4px; left: 0; background-color: #ea580c; transition: width 0.3s ease;
    }
    .nav-link:hover::after { width: 100%; }
  </style>
</head>
<body class="text-slate-800 min-h-screen flex flex-col">

<!-- Modern Floating Navbar -->
<header class="fixed top-4 left-4 right-4 z-50 glass-nav shadow-lg rounded-full px-6 py-3 transition-all duration-300">
  <div class="max-w-7xl mx-auto flex justify-between items-center">
    <a href="index.php" class="flex items-center gap-2 transform transition hover:scale-105">
      <img src="https://i.ibb.co/Kc0pZmw6/logo-no-background.png" class="h-10 w-auto drop-shadow-md">
    </a>
    
    <nav class="hidden sm:flex space-x-8 items-center text-lg font-semibold text-slate-700">
      <a href="index.php" class="nav-link hover:text-orange-600 transition">Home</a>
      <a href="books.php" class="nav-link text-orange-600 transition">Books</a>
      <a href="cart.php" class="nav-link hover:text-orange-600 transition relative">Cart</a>
      <a href="bought_books.php" class="nav-link hover:text-orange-600 transition">Borrowed</a>
      <div class="border-l-2 border-slate-200 pl-8 ml-4 flex items-center gap-4">
        <?php if (isset($_SESSION['user_id'])): ?>
          <a href="logout.php" class="px-4 py-2 rounded-full border-2 border-slate-200 text-slate-600 hover:border-red-500 hover:text-red-500 transition-colors text-sm font-bold">Logout</a>
        <?php else: ?>
          <a href="login.html" class="btn-gradient text-white px-6 py-2 rounded-full font-bold shadow-md">Login</a>
        <?php endif; ?>
      </div>
    </nav>
  </div>
</header>

<div class="flex-grow w-full max-w-7xl mx-auto mt-32 relative z-10 px-4">
  <section class="max-w-5xl mx-auto py-12 px-4">
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden p-8">
      <div class="flex flex-col md:flex-row gap-10">
        <!-- Cover Image -->
        <div class="md:w-1/3 flex-shrink-0">
          <img src="<?php echo htmlspecialchars($book['image']); ?>" class="w-full h-auto object-cover rounded-xl shadow-md border" alt="Cover of <?php echo htmlspecialchars($book['title']); ?>">
        </div>
        
        <!-- Details -->
        <div class="md:w-2/3 flex flex-col justify-center">
          <h2 class="text-4xl font-extrabold text-gray-900 mb-2"><?php echo htmlspecialchars($book['title']); ?></h2>
          <p class="text-xl text-gray-600 font-semibold mb-4">By <?php echo htmlspecialchars($book['author']); ?></p>
          
          <div class="flex items-center gap-4 mb-6">
            <span class="bg-amber-100 text-amber-800 text-sm font-semibold px-3 py-1 rounded-full uppercase tracking-wide"><?php echo htmlspecialchars($book['category']); ?></span>
            <span class="text-red-600 text-2xl font-bold border-l-2 pl-4 border-gray-300">₹<?php echo number_format($book['book_rent'], 2); ?> / mo</span>
          </div>

          <div class="prose max-w-none text-gray-700 leading-relaxed mb-8">
            <h3 class="text-lg font-bold text-gray-800 mb-2">Description</h3>
            <p><?php echo nl2br(htmlspecialchars($book['description'] ?? 'No description available for this book.')); ?></p>
          </div>
          
          <form method="POST" action="add_to_cart.php" onsubmit="alert('Added to cart!');">
            <input type="hidden" name="books_id" value="<?php echo $book['books_id']; ?>">
            <input type="hidden" name="title" value="<?php echo htmlspecialchars($book['title']); ?>">
            <input type="hidden" name="author" value="<?php echo htmlspecialchars($book['author']); ?>">
            <input type="hidden" name="image" value="<?php echo htmlspecialchars($book['image']); ?>">
            <input type="hidden" name="user_id" value="<?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : ''; ?>">
            <input type="hidden" name="book_rent" value="<?php echo htmlspecialchars($book['book_rent']); ?>">
            <button type="submit" class="bg-amber-600 text-white px-8 py-4 rounded-xl text-lg font-bold hover:bg-amber-700 transition shadow-lg w-full sm:w-auto">
              Add to Cart to Borrow
            </button>
          </form>
        </div>
      </div>
    </div>
  </section>
  <!-- Footer -->
<footer class="bg-slate-950 text-slate-400 py-12 mt-20 border-t border-slate-800 w-full">
  <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center px-6 gap-6">
    <div class="flex items-center gap-3">
      <img src="https://i.ibb.co/Kc0pZmw6/logo-no-background.png" class="h-8 filter cursor-pointer grayscale opacity-50 hover:grayscale-0 hover:opacity-100 transition duration-300">
    </div>
    <div class="text-sm font-medium">
      &copy; 2025 BookNest Premium. Developed by Lakshay. All rights reserved.
    </div>
  </div>
</footer>
