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

// Get the borrowings for the logged-in user
$user_id = $_SESSION['user_id'];
$sql = "SELECT borrowings.borrow_id, borrowings.borrowed_on, borrowings.book_rent, books.books_id, books.title, books.author, books.image 
        FROM borrowings 
        JOIN books ON borrowings.books_id = books.books_id 
        WHERE borrowings.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$borrowed_books = [];
while ($row = $result->fetch_assoc()) {
    $borrowed_books[] = $row;
}
$borrowed_count = count(value: $borrowed_books); // <- Count borrowed books
$_SESSION['borrowed_count'] = $borrowed_count;


$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>BookNest | Cart</title>
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
<section class="relative flex-grow overflow-hidden min-h-screen" >
  <!-- Blurred background image -->
  <div class="absolute inset-0 bg-cover bg-center filter blur-sm z-[-1] brightness-50"
       style="background-image: url('https://iili.io/315mRaf.jpg'); background-attachment: fixed;">
  </div>
  
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
      <a href="cart.php" class="nav-link text-orange-600 transition relative">Cart
        <?php if ($borrowed_count > 0): ?>
          <span class="absolute -top-3 -right-4 bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full shadow-md">
            <?php echo $borrowed_count; ?>
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
  
<br>

<div class="max-w-7xl mx-auto px-4 pb-16 mt-32 relative z-10 w-full">
  <!-- Category Title -->
  <section class="flex justify-center items-center">
    <div class="bg-white/80 rounded-xl px-6 py-4 shadow-md z-10">
      <h2 class="text-amber-700 text-4xl font-semibold underline text-center hover:text-amber-500 transition duration-300 ease-in-out uppercase"
          style="--tw-shadow-glow: 0 0 8px #FBE084; text-shadow: var(--tw-shadow-glow);">
        Your Cart
      </h2>
      <!-- Remove All Button -->
<div class="flex justify-center mt-6 z-10">
  <form method="POST" action="clear_cart.php" onsubmit="return confirm('Are you sure you want to remove all books from your cart?');">
    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-semibold px-6 py-2 rounded-lg shadow-md transition duration-300 ease-in-out">
      🗑️ Empty Cart
    </button>
  </form>
</div>
    </div>
  </section>



  <br>
  <br>
  <!-- Cards Section -->
  <section class="px-4 pb-16">

  <!-- Header with Add Books Button -->
  <div class="flex items-center justify-between mb-6 z-10">
    <a href="index.php#explore" class="inline-block bg-white/90  px-4 py-2 rounded hover:text-red-900 text-bold transition">
      ➕ Add Books
    </a>
  </div>

  <!-- Book Cards Grid -->
  <div class="grid gap-x-10 gap-y-8 grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
  <?php if (count($borrowed_books) > 0): ?>
    <?php foreach ($borrowed_books as $book): ?>
      <div class="w-full h-[350px] bg-white/80 rounded-2xl shadow-lg transform transition duration-300 hover:scale-105 hover:shadow-xl hover:bg-blue-100 overflow-hidden">
        <img src="<?php echo $book['image']; ?>" alt="<?php echo $book['title']; ?>" class="h-[180px] w-full object-cover rounded-t-2xl">
        <div class="p-4">
          <h3 class="font-semibold text-lg truncate"><?php echo $book['title']; ?></h3>
          <p class="text-gray-600 text-sm mb-2 truncate">by <?php echo $book['author']; ?></p>
          <p class="text-gray-500 text-xs">Added on: <?php echo date("F j, Y", strtotime($book['borrowed_on'])); ?></p>
          <h4 class="font-semibold text-red-600 text-lg truncate">Book Rent : ₹<?php echo $book['book_rent']; ?></h4>
          <div class="flex gap-2 mt-2">
            <!-- Remove from Cart Button -->
            <form method="POST" action="remove_from_cart.php" onsubmit="return confirm('Are you sure you want to remove this book from your cart?');" class="w-1/2">
              <input type="hidden" name="borrow_id" value="<?php echo $book['borrow_id']; ?>">
              <button type="submit" class="bg-amber-600 text-white px-4 py-2 rounded hover:bg-red-700 w-full">
                Remove 
              </button>
            </form>
            <!-- Borrow Button -->
            <button onclick="openModal(<?php echo $book['books_id']; ?>, '<?php echo addslashes($book['title']); ?>', <?php echo $book['book_rent']; ?>)" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-800 w-full">
  Borrow
</button>

          </div>
        </div>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <div class="col-span-full z-10">
      <div class="flex justify-center items-center mt-10 z-10">
        <h1 class="text-2xl text-red-600 text-center bg-white/80 rounded-lg px-6 py-4 shadow">
          Your Cart is Empty :(
        </h1>
      </div>
    </div>
  <?php endif; ?>
</div>
</div>
</section>
</section>


<!-- Modal Structure -->
<!-- Modal Structure -->
<div id="bookModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden z-10">
  <div class="bg-white rounded-lg p-6 w-80 text-center shadow-lg">
    <h2 class="text-2xl text-amber-500 font-semibold mb-4 underline">Confirm Borrow</h2>
    <p id="bookTitle" class="font-semibold text-lg mb-2"></p>

    <div>
      <!-- Dummy UPI QR Code -->
      <div class="flex justify-center mb-4">
        <img src="https://i.ibb.co/NdBWp9VH/Whats-App-Image-2025-04-20-at-20-36-16-b11bb293.jpg" alt="9466812417@ptsbi">
      </div>
      <p id="bookRent" class="text-red-600 text-md mb-4"></p>

      <!-- Hidden form to POST to buy_book.php -->
      <form id="paidForm" method="POST" action="buy_book.php">
        <input type="hidden" name="books_id" id="paidBookId">
      </form>

      <!-- Paid Button -->
      <button id="paidButton" class="bg-blue-600 text-white px-4 py-2 rounded mb-3 w-full cursor-not-allowed opacity-50" disabled>
        Please wait...
      </button>

      <!-- Close Button -->
            <form id="close" method="POST" action="cart.php">
      <button type="submit" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-700 w-full">
        Close
      </button>
        </form>
    </div>
  </div>
</div>



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

<script>
function openModal(bookId, bookTitle, bookRent) {
  document.getElementById('bookTitle').textContent = `Book Title: ${bookTitle}`;
  document.getElementById('bookRent').textContent = `Book Rent: ₹${bookRent}`;
  document.getElementById('paidBookId').value = bookId; // Set book ID in hidden form
  document.getElementById('bookModal').classList.remove('hidden');

  const paidBtn = document.getElementById('paidButton');
  paidBtn.disabled = true;
  paidBtn.classList.add('cursor-not-allowed', 'opacity-50');

  let seconds = 5;
  paidBtn.textContent = `Please wait ${seconds}s...`;

  const countdown = setInterval(() => {
    seconds--;
    paidBtn.textContent = `Please wait ${seconds}s...`;

    if (seconds === 0) {
      clearInterval(countdown);
      paidBtn.disabled = false;
      paidBtn.textContent = "Paid";
      paidBtn.classList.remove('cursor-not-allowed', 'opacity-50');

      paidBtn.onclick = () => {
        document.getElementById('paidForm').submit(); // Submit form with bookId
      };
    }
  }, 1000);
}


  </script>
</body>
</html>

