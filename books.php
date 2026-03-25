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

$sql = "SELECT books_id, title, author, image, category, book_rent FROM books";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $category = $row['category'];
        $books[$category][] = $row;
    }
} else {
    echo "No books found.";
}
$category = isset($_GET['category']) ? $_GET['category'] : 'Fiction';

// Query to get books based on category
$search = isset($_GET['search']) ? trim($_GET['search']) : '';


$filteredBooks = [];

if (!empty($search)) {
    foreach ($books[$category] as $book) {
        if (
            stripos($book['title'], $search) !== false ||
            stripos($book['author'], $search) !== false
        ) {
            $filteredBooks[] = $book;
        }
    }
} else {
    $filteredBooks = $books[$category];
}

$books_count = isset($_SESSION['borrowed_count']) ? $_SESSION['borrowed_count'] : 0;

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>BookNest | Browse Books</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Outfit', sans-serif; background-color: #f8fafc; }
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
      <a href="books.php" class="nav-link text-orange-600">Books</a>
      <a href="cart.php" class="nav-link hover:text-orange-600 transition relative">
        Cart
        <?php if ($books_count > 0): ?>
          <span class="absolute -top-3 -right-4 bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full shadow-md">
            <?php echo $books_count; ?>
          </span>
        <?php endif; ?>
      </a>
      <a href="bought_books.php" class="nav-link hover:text-orange-600 transition">Borrowed</a>
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

<div class="flex-grow w-full max-w-7xl mx-auto mt-32 relative z-10 px-4">

<br>
<br>
  <!-- Page Title & Search Section -->
  <div class="relative z-10">
  <section class="flex justify-center items-center">
    <div class="relative">
      <!-- Dropdown Button -->
      <button onclick="toggleDropdown()" class="bg-white/90 rounded-xl px-6 py-4 shadow-md w-full">
        <h2 class="text-amber-700 text-4xl font-semibold underline text-center hover:text-amber-500 transition duration-300 ease-in-out uppercase"
            style="--tw-shadow-glow: 0 0 8px #FBE084; text-shadow: var(--tw-shadow-glow);">
          <?php echo htmlspecialchars(ucwords($category)); ?>
        </h2>
        
      </button>

      <!-- Dropdown Menu -->
      <div id="dropdownMenu" class="absolute left-0 mt-2 w-full bg-white rounded-lg shadow-lg hidden z-10">
        <ul class="py-2 text-center text-lg text-gray-700">
          <li><a href="books.php?category=Fiction" class="block px-4 py-2 hover:bg-amber-100">Fiction</a></li>
          <li><a href="books.php?category=Science" class="block px-4 py-2 hover:bg-amber-100">Science</a></li>
          <li><a href="books.php?category=Biography" class="block px-4 py-2 hover:bg-amber-100">Biography</a></li>
          <li><a href="books.php?category=Technology" class="block px-4 py-2 hover:bg-amber-100">Technology</a></li>
          <li><a href="books.php?category=Self-Help" class="block px-4 py-2 hover:bg-amber-100">Self-Help</a></li>
          <li><a href="books.php?category=Children" class="block px-4 py-2 hover:bg-amber-100">Children</a></li>
        </ul>
      </div>
    </div>
  </section>
<
<br>



  <form method="GET" action="books.php" class="flex flex-col sm:flex-row items-center justify-center gap-4">
  <input
    type="text"
    name="search"
    placeholder="Search books..."
    class="border px-4 py-2 rounded w-full sm:w-1/2 focus:outline-none focus:ring-2 focus:ring-amber-400"
    value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>"
  />
  <input type="hidden" name="category" value="<?php echo htmlspecialchars($category); ?>">
  <button type="submit" class="bg-amber-600 text-white px-4 py-2 rounded hover:bg-amber-700">Search</button>
  <?php if (isset($_GET['search']) && trim($_GET['search']) !== ''): ?>
    <a href="books.php?category=<?php echo urlencode($category); ?>" class="bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400">Clear</a>
  <?php endif; ?>
</form>

<br>

<!-- Display Books -->
  <div class="max-w-7xl mx-auto px-4 pb-16">
  <section class="flex justify-center items-center ">
<p class="text-gray-800 text-bold text-center mb-8 bg-white/80 rounded-lg inline-block px-6 py-4">Find books by genre, author, or title</p>
</section>

<br>

<!-- Cards Section -->
<section class="px-4 pb-16">
  <div class="grid gap-x-10 gap-y-8 grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
    <?php foreach ($filteredBooks as $book): ?>
      <div class="w-full h-[350px] bg-white/80 rounded-2xl shadow-lg transform transition duration-300 hover:scale-105 hover:shadow-xl hover:bg-blue-100 overflow-hidden">
        <img src="<?php echo $book['image']; ?>" alt="<?php echo $book['title']; ?>" class="h-[180px] w-full object-cover rounded-t-2xl">
        <div class="p-4">
          <h3 class="font-semibold text-lg truncate"><?php echo $book['title']; ?></h3>
          <p class="text-gray-600 text-sm mb-2 truncate">by <?php echo $book['author']; ?></p>
          <h4 class="font-semibold text-red-600 text-lg truncate">Book Rent : ₹<?php echo $book['book_rent']; ?></h4>
          <form method="POST" action="add_to_cart.php" onsubmit="handleFormSubmit(event, this)">
          <input type="hidden" name="books_id" value="<?php echo $book['books_id']; ?>">
            <input type="hidden" name="title" value="<?php echo $book['title']; ?>">
            <input type="hidden" name="author" value="<?php echo $book['author']; ?>">
            <input type="hidden" name="image" value="<?php echo $book['image']; ?>">
            <input type="hidden" name="user_id" value="<?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : ''; ?>">
            <input type="hidden" name="book_rent" value="<?php echo $book['book_rent']; ?>">
            <div class="flex gap-2">
              <button type="submit" class="mt-2 text-sm bg-amber-600 text-white px-3 py-2 rounded hover:bg-amber-700">
                ADD TO CART
              </button>
              <a href="book-details.php?id=<?php echo $book['books_id']; ?>" class="mt-2 text-sm bg-gray-600 text-white px-3 py-2 rounded hover:bg-gray-700 text-center items-center flex">
                View Details
              </a>
            </div>
          </form>
        </div>
      </div>
    <?php endforeach; ?>
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

  <div id="toast-success" class="fixed bottom-5 left-1/2 transform -translate-x-1/2 z-50 hidden p-4 w-80 bg-green-100 border border-green-400 text-green-700 rounded-lg shadow-lg transition-opacity duration-300">
  <div class="flex items-center justify-between">
    <span><strong class="font-semibold">Success!</strong> Book added to cart successfully.</span>
    <button onclick="hideToast()" class="ml-4 text-green-700 hover:text-green-900">&times;</button>
  </div>
</div>

  <script>
    
  function handleFormSubmit(event, form) {
    event.preventDefault(); // temporarily stop form submission
    showToast(); // show success message

    setTimeout(() => {
      form.submit(); // submit form after delay
    }, 1000); // delay in milliseconds
  }

  function showToast() {
    const toast = document.getElementById('toast-success');
    toast.classList.remove('hidden');
    setTimeout(() => hideToast(), 3000);
  }

  function hideToast() {
    const toast = document.getElementById('toast-success');
    toast.classList.add('hidden');
  }

  const menuToggle = document.getElementById('menu-toggle');
  const mobileMenu = document.getElementById('mobile-menu');
  menuToggle.addEventListener('click', () => {
    mobileMenu.classList.toggle('hidden');
  });
</script>
<script>
  function toggleDropdown() {
    const menu = document.getElementById('dropdownMenu');
    menu.classList.toggle('hidden');
  }

  // Optional: close dropdown if user clicks outside
  window.addEventListener('click', function(e) {
    const button = document.querySelector('button[onclick="toggleDropdown()"]');
    const menu = document.getElementById('dropdownMenu');
    if (!button.contains(e.target) && !menu.contains(e.target)) {
      menu.classList.add('hidden');
    }
  });
</script>

</body>
</html>
