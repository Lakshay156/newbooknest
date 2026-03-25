<?php
session_start();

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
  header("Location: login.html");
  exit();
}

require 'db_connection.php'; // This file connects to your MySQL database

// Fetch all users
$users = mysqli_query($conn, "SELECT * FROM users ORDER BY created_at DESC");

// Fetch borrowed books with user details
$borrowed_books = mysqli_query($conn, "
  SELECT b.title, b.author, u.name, u.email, br.bought_on, b.book_rent
  FROM bought_books br
  JOIN books b ON br.books_id = b.books_id
  JOIN users u ON br.user_id = u.user_id
  ORDER BY br.bought_on DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>BookNest | Admin Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Outfit', sans-serif; background-color: #f1f5f9; }
    .fade-in { animation: fadeIn 0.4s ease-out both; }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body class="text-slate-800 min-h-screen flex flex-col">

<!-- Header -->
<header class="bg-slate-900 border-b border-slate-800 shadow-xl sticky top-0 z-50">
  <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
    <div class="flex items-center gap-3">
      <div class="bg-orange-500 rounded-lg p-2 shadow-inner">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
      </div>
      <h1 class="text-2xl font-extrabold tracking-wide text-white">BookNest <span class="text-orange-500 font-light">Admin Portal</span></h1>
    </div>
    <a href="logout.php" class="bg-slate-800 border border-slate-700 text-slate-300 hover:text-white hover:bg-red-500 hover:border-red-600 transition font-bold px-6 py-2 rounded-full shadow text-sm">Sign out</a>
  </div>
</header>

<main class="max-w-7xl mx-auto w-full p-6 sm:p-10 space-y-16 flex-grow fade-in mt-6">

  <!-- Users Section -->
  <section>
    <div class="flex items-center gap-4 mb-8">
      <div class="p-3 bg-white shadow-sm border border-slate-200 rounded-xl text-slate-800">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
      </div>
      <div>
        <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Registered Users</h2>
        <p class="text-slate-500 font-medium">Manage and view all registered users in your system.</p>
      </div>
    </div>
    
    <div class="overflow-x-auto bg-white rounded-[2rem] shadow-xl border border-slate-200">
      <table class="min-w-full table-auto text-sm text-left">
        <thead class="bg-slate-50 text-slate-500 font-bold tracking-wide uppercase text-xs border-b border-slate-200">
          <tr>
            <th class="px-8 py-5">User ID</th>
            <th class="px-8 py-5">Full Name</th>
            <th class="px-8 py-5">Email</th>
            <th class="px-8 py-5">Phone</th>
            <th class="px-8 py-5">Registered On</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <?php while ($user = mysqli_fetch_assoc($users)): ?>
          <tr class="hover:bg-slate-50/80 transition-colors">
            <td class="px-8 py-5 font-medium text-slate-900">#<?php echo $user['user_id']; ?></td>
            <td class="px-8 py-5 font-bold text-slate-800"><?php echo htmlspecialchars($user['name']); ?></td>
            <td class="px-8 py-5 text-slate-600"><?php echo htmlspecialchars($user['email']); ?></td>
            <td class="px-8 py-5 text-slate-600"><?php echo htmlspecialchars($user['mobile']); ?></td>
            <td class="px-8 py-5 text-slate-500"><?php echo date("M j, Y, g:i A", strtotime($user['created_at'])); ?></td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </section>

  <!-- Borrowed Books Section -->
  <section class="pb-10">
    <div class="flex items-center gap-4 mb-8">
      <div class="p-3 bg-white shadow-sm border border-slate-200 rounded-xl text-orange-600">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
      </div>
      <div>
        <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Active Borrowings</h2>
        <p class="text-slate-500 font-medium">Tracking books currently rented out by users.</p>
      </div>
    </div>
    
    <div class="overflow-x-auto bg-white rounded-[2rem] shadow-xl border border-slate-200">
      <table class="min-w-full table-auto text-sm text-left">
        <thead class="bg-slate-50 text-slate-500 font-bold tracking-wide uppercase text-xs border-b border-slate-200">
          <tr>
            <th class="px-8 py-5">Book Title</th>
            <th class="px-8 py-5">Author</th>
            <th class="px-8 py-5">Borrowed By</th>
            <th class="px-8 py-5">Rent</th>
            <th class="px-8 py-5">User Email</th>
            <th class="px-8 py-5">Borrow Date</th>
            <th class="px-8 py-5">Return Due</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <?php while ($borrow = mysqli_fetch_assoc($borrowed_books)): ?>
          <tr class="hover:bg-slate-50/80 transition-colors">
            <td class="px-8 py-5 font-bold text-slate-800"><?php echo htmlspecialchars($borrow['title']); ?></td>
            <td class="px-8 py-5 text-slate-600"><?php echo htmlspecialchars($borrow['author']); ?></td>
            <td class="px-8 py-5 font-semibold text-slate-700"><?php echo htmlspecialchars($borrow['name']); ?></td>
            <td class="px-8 py-5 font-bold text-orange-600">₹<?php echo htmlspecialchars($borrow['book_rent']); ?></td>
            <td class="px-8 py-5 text-slate-600"><?php echo htmlspecialchars($borrow['email']); ?></td>
            <td class="px-8 py-5 text-slate-500"><?php echo date("M j, Y", strtotime($borrow['bought_on'])); ?></td>
            <td class="px-8 py-5 font-bold text-slate-900">
              <?php
                $borrowedDate = new DateTime($borrow['bought_on']);
                $borrowedDate->modify('+1 month');
                echo $borrowedDate->format("M j, Y");
              ?>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </section>

</main>

<!-- Footer -->
<footer class="bg-slate-950 text-slate-400 text-center py-8 border-t border-slate-900 mt-auto">
  &copy; 2025 BookNest Premium | Admin Portal
</footer>

</body>
</html>
