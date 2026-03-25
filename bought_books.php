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

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT b.title AS book_title, b.author AS book_author, b.image AS book_cover, bb.bought_on 
        FROM bought_books bb 
        JOIN books b ON bb.books_id = b.books_id 
        WHERE bb.user_id = ? 
        ORDER BY bb.bought_on DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$bought_books = [];
while ($row = $result->fetch_assoc()) {
    $bought_books[] = $row;
}
$borrowed_count = isset($_SESSION['borrowed_count']) ? $_SESSION['borrowed_count'] : 0;

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>BookNest | Purchase History</title>
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
    
    <button id="menu-toggle" class="sm:hidden text-3xl text-slate-700">&#9776;</button>
    
    <nav id="menu" class="hidden sm:flex space-x-8 items-center text-lg font-semibold text-slate-700">
      <a href="index.php" class="nav-link hover:text-orange-600 transition">Home</a>
      <a href="books.php" class="nav-link hover:text-orange-600 transition">Books</a>
      <a href="cart.php" class="nav-link hover:text-orange-600 transition relative">Cart
        <?php if ($borrowed_count > 0): ?>
          <span class="absolute -top-3 -right-4 bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full shadow-md">
            <?php echo $borrowed_count; ?>
          </span>
        <?php endif; ?>
      </a>
      <a href="bought_books.php" class="nav-link text-orange-600 transition">Borrowed</a>
      <a href="contact.php" class="nav-link hover:text-orange-600 transition">Contact</a>
      <div class="border-l-2 border-slate-200 pl-8 ml-4 flex items-center gap-4">
        <?php if (isset($_SESSION['user_id'])): ?>
          <span class="font-bold text-orange-600 flex items-center gap-2">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
            <?php echo htmlspecialchars($_SESSION['user_name']); ?>
          </span>
          <a href="logout.php" class="px-4 py-2 rounded-full border-2 border-slate-200 text-slate-600 hover:border-red-500 hover:text-red-500 transition-colors text-sm font-bold">Logout</a>
        <?php else: ?>
          <a href="login.html" class="btn-gradient text-white px-6 py-2 rounded-full font-bold shadow-md">Login</a>
        <?php endif; ?>
      </div>
    </nav>
  </div>
</header>

<section class="relative flex-grow overflow-hidden min-h-screen">
  <div class="absolute inset-0 bg-cover bg-center filter blur-sm z-0 brightness-50"
       style="background-image: url('https://iili.io/315mRaf.jpg'); background-attachment: fixed;">
  </div>

  <div class="max-w-7xl mx-auto px-4 pb-16 mt-32 w-full relative z-10 w-full">
    <section class="flex justify-center items-center">
      <div class="bg-white/80 rounded-xl px-6 py-4 shadow-md z-10">
        <h2 class="text-amber-700 text-4xl font-semibold underline text-center hover:text-amber-500 transition duration-300 ease-in-out uppercase"
            style="--tw-shadow-glow: 0 0 8px #FBE084; text-shadow: var(--tw-shadow-glow);">
          Your Borrowings
        </h2>
      </div>
    </section>

    <br><br>

    <section class="px-4 pb-16">
      <div class="grid gap-x-10 gap-y-8 grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
        <?php if (count($bought_books) > 0): ?>
          <?php foreach ($bought_books as $book): ?>
            <div class="w-full h-[400px] bg-white/80 rounded-2xl shadow-lg transform transition duration-300 hover:scale-105 hover:shadow-xl hover:bg-green-100 overflow-hidden">
  <img src="<?php echo $book['book_cover']; ?>" alt="Book Cover" class="w-full h-56 object-cover rounded-t-2xl">
  <div class="p-4">
    <h3 class="font-semibold text-lg truncate"><?php echo $book['book_title']; ?></h3>
    <p class="text-gray-600 text-sm truncate">by <?php echo $book['book_author']; ?></p>
    <br>
    <p class="text-gray-500 text-xs mt-1">Borrowed on: <?php echo date("F j, Y", strtotime($book['bought_on'])); ?></p>
  </div>
</div>

          <?php endforeach; ?>
        <?php else: ?>
          <div class="col-span-full z-10">
            <div class="flex justify-center items-center mt-10 z-10">
              <h1 class="text-2xl text-red-600 text-center bg-white/80 rounded-lg px-6 py-4 shadow">
                You haven't borrowed any books yet 😔
              </h1>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </section>
  </div>
</section>

<!-- Footer -->
<footer class="bg-slate-950 text-slate-400 py-12 border-t border-slate-800 w-full mt-auto relative z-10">
  <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center px-6 gap-6">
    <div class="flex items-center gap-3">
      <img src="https://i.ibb.co/Kc0pZmw6/logo-no-background.png" class="h-8 filter cursor-pointer grayscale opacity-50 hover:grayscale-0 hover:opacity-100 transition duration-300">
    </div>
    <div class="text-sm font-medium">
      &copy; 2025 BookNest Premium. Developed by Lakshay. All rights reserved.
    </div>
  </div>
</footer>

</body>
</html>
