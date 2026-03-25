<?php
session_start();
$books_count = $_SESSION['borrowed_count'] ?? 0;

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>BookNest | Contact</title>
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
<section class="relative flex-grow min-h-screen pt-32 pb-16">
<div class="fixed inset-0 bg-[url('https://images.pexels.com/photos/245240/pexels-photo-245240.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1')] bg-cover bg-center blur-sm brightness-50 z-[-1]"></div>

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
        <?php if ($books_count > 0): ?>
          <span class="absolute -top-3 -right-4 bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full shadow-md">
            <?php echo $books_count; ?>
          </span>
        <?php endif; ?>
      </a>
      <a href="bought_books.php" class="nav-link hover:text-orange-600 transition">Borrowed</a>
      <a href="contact.php" class="nav-link text-orange-600 transition">Contact</a>
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


  <section class="max-w-xl mx-auto py-0 px-4">

  <!-- Contact Section -->
  <section class="max-w-xl mx-auto py-16 px-4 ">
  <section class="flex justify-center items-center ">
  <h1 class="text-4xl font-bold text-center mb-4 bg-white/90 rounded-lg inline-block px-20 py-4 transform transition duration-300 hover:scale-110 hover:shadow-xl cursor-default">
        Contact Us
  </h1>
</section>
<br>
    <p class="text-center text-lg text-white mb-8">We'd love to hear from you. Please fill out the form below and we'll get back to you as soon as possible.</p>
    <form class="space-y-6" action="contact-confirmation.html">
    <div>
        <label class="block text-white mb-1 font-medium" for="name"> Name</label>
        <input type="text" id="name" placeholder="Enter Your Name" class="w-full border border-gray-300 px-3 py-2 rounded focus:outline-none focus:ring-2 focus:ring-indigo-400" required />
      </div>
      <div>
        <label class="block text-white mb-1 font-medium" for="email">Email</label>
        <input type="email" id="email" placeholder="Enter Your Email" class="w-full border border-gray-300 px-3 py-2 rounded focus:outline-none focus:ring-2 focus:ring-indigo-400" required />
      </div>
      <div>
        <label class="block text-white mb-1 font-medium" for="name">Contact Number</label>
        <input type="tel" name="mobile" pattern="[0-9]{10}" maxlength="10" class="w-full border rounded-r px-3 py-2 transition duration-200 ease-in-out hover:shadow-md" placeholder="Enter your mobile number" required />
        </div>
      <div>
        <label class="block text-white text-whiten mb-1 font-medium" for="message">Message</label>
        <textarea id="message" placeholder="Your Message......" class="w-full border border-gray-300 px-3 py-2 rounded h-32 focus:outline-none focus:ring-2 focus:ring-indigo-400" required></textarea>
      </div>
      <button type="submit" class="w-full bg-orange-600 text-white px-4 py-2 rounded hover:bg-amber-800 transition">Send Message</button>
    </form>
  </section>


</section>

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

  <!-- Mobile Navigation Toggle Script -->
  <script>
    const menuToggle = document.getElementById('menu-toggle');
    const mobileMenu = document.getElementById('mobile-menu');
    menuToggle.addEventListener('click', () => {
      mobileMenu.classList.toggle('hidden');
    });
  </script>

</body>
</html>