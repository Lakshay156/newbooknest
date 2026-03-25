<?php
session_start();
include 'db_connection.php';

$books_count = $_SESSION['borrowed_count'] ?? 0;

$query = "SELECT *, FLOOR(RAND() * 500) + 10 AS borrow_count FROM books ORDER BY RAND() LIMIT 10";
$result = mysqli_query($conn, $query);

$popularBooks = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $popularBooks[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>BookNest | Premium Library</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Outfit', sans-serif; background-color: #f8fafc; overflow-x: hidden; }
    
    @keyframes marquee {
      0% { transform: translateX(0); }
      100% { transform: translateX(-50%); }
    }
    .animate-marquee {
      animation: marquee 30s linear infinite;
      display: flex;
      width: max-content;
    }
    .animate-marquee:hover {
      animation-play-state: paused;
    }
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
    .nav-link {
      position: relative;
    }
    .nav-link::after {
      content: '';
      position: absolute;
      width: 0;
      height: 2px;
      bottom: -4px;
      left: 0;
      background-color: #ea580c;
      transition: width 0.3s ease;
    }
    .nav-link:hover::after {
      width: 100%;
    }
  </style>
</head>
<body class="text-slate-800">

<!-- Modern Floating Navbar -->
<header class="fixed top-4 left-4 right-4 z-50 glass-nav shadow-lg rounded-full px-6 py-3 transition-all duration-300">
  <div class="max-w-7xl mx-auto flex justify-between items-center">
    <a href="index.php" class="flex items-center gap-2 transform transition hover:scale-105">
      <img src="https://i.ibb.co/Kc0pZmw6/logo-no-background.png" class="h-10 w-auto drop-shadow-md">
    </a>
    
    <button id="menu-toggle" class="sm:hidden text-3xl text-slate-700">&#9776;</button>
    
    <nav id="menu" class="hidden sm:flex space-x-8 items-center text-lg font-semibold text-slate-700">
      <a href="index.php" class="nav-link text-orange-600">Home</a>
      <a href="books.php" class="nav-link hover:text-orange-600 transition">Books</a>
      <a href="cart.php" class="nav-link hover:text-orange-600 transition relative">
        Cart
        <?php if ($books_count > 0): ?>
          <span class="absolute -top-3 -right-4 bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full shadow-md animate-bounce">
            <?php echo $books_count; ?>
          </span>
        <?php endif; ?>
      </a>
      <a href="bought_books.php" class="nav-link hover:text-orange-600 transition">Borrowed</a>
      <a href="contact.php" class="nav-link hover:text-orange-600 transition">Contact</a>

      <!-- Profile / Login Section -->
      <div class="border-l-2 border-slate-200 pl-8 ml-4 flex flex-col sm:flex-row shadow-none sm:shadow-none bg-transparent items-center gap-4">
        <?php if (isset($_SESSION['user_id'])): ?>
          <span id="user-info-btn" class="cursor-pointer font-bold text-orange-600 hover:text-orange-700 flex items-center gap-2">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
            <?php echo htmlspecialchars($_SESSION['user_name']); ?>
          </span>
          <a href="logout.php" class="px-4 py-2 rounded-full border-2 border-slate-200 text-slate-600 hover:border-red-500 hover:text-red-500 transition-colors text-sm font-bold">Logout</a>
          
          <!-- User Profile Modal -->
          <div id="user-modal" class="hidden fixed inset-0 bg-slate-900/40 backdrop-blur-sm flex justify-center items-center z-50">
            <div class="bg-white p-8 rounded-3xl shadow-2xl w-full max-w-md relative transform scale-95 transition-transform">
              <button id="close-modal" class="absolute top-4 right-4 text-slate-400 hover:text-slate-800 text-3xl font-light">&times;</button>
              <div class="text-center mb-6">
                 <div class="w-20 h-20 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-3xl text-orange-600 font-bold"><?php echo substr($_SESSION['user_name'], 0, 1); ?></span>
                 </div>
                 <h2 class="text-2xl font-bold text-slate-800">Your Profile</h2>
              </div>
              <div class="space-y-4 text-slate-600 font-medium">
                <div class="flex justify-between border-b pb-2">
                  <span>Full Name</span>
                  <span class="font-bold text-slate-800"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                </div>
                <div class="flex justify-between border-b pb-2">
                  <span>Email</span>
                  <span class="font-bold text-slate-800"><?php echo htmlspecialchars($_SESSION['email']); ?></span>
                </div>
                <div class="flex justify-between border-b pb-2">
                  <span>Phone</span>
                  <span class="font-bold text-slate-800"><?php echo htmlspecialchars($_SESSION['phone']); ?></span>
                </div>
                <div class="flex justify-between">
                  <span>Joined</span>
                  <span class="font-bold text-slate-800"><?php echo date("F j, Y", strtotime($_SESSION['created'])); ?></span>
                </div>
              </div>
            </div>
          </div>
        <?php else: ?>
          <a href="login.html" class="btn-gradient text-white px-6 py-2 rounded-full font-bold shadow-md">Login</a>
        <?php endif; ?>
      </div>
    </nav>
  </div>
</header>

<!-- Hero Section -->
<section class="relative min-h-[90vh] mt-24 mx-4 md:mx-8 rounded-[2rem] overflow-hidden shadow-2xl flex items-center border border-slate-200/50">
  <img src="https://images.unsplash.com/photo-1507842217343-583bb7270b66?q=80&w=2000&auto=format&fit=crop" class="absolute inset-0 w-full h-full object-cover z-0" alt="Hero Background" />
  <div class="absolute inset-0 bg-gradient-to-r from-slate-900/90 via-slate-900/60 to-transparent z-10"></div>
  
  <div class="relative z-20 max-w-7xl mx-auto px-8 w-full">
    <div class="max-w-2xl text-left">
      <span class="inline-block py-1.5 px-4 rounded-full bg-orange-500/20 text-orange-400 font-bold tracking-wider text-sm mb-6 border border-orange-500/30 uppercase">Welcome to BookNest</span>
      <h2 class="text-5xl md:text-7xl font-extrabold text-white mb-6 leading-tight">Your Premium <br><span class="text-transparent bg-clip-text bg-gradient-to-r from-orange-400 to-amber-500">Digital Library</span></h2>
      <p class="text-xl md:text-2xl text-slate-300 mb-10 leading-relaxed font-light">Borrow, explore, and dive into thousands of books online. Curated collections organized beautifully for your reading pleasure.</p>
      <div class="flex flex-col sm:flex-row gap-4">
        <a href="books.php" class="btn-gradient px-8 py-4 rounded-full font-bold text-white text-lg inline-flex justify-center items-center gap-2">
          Start Exploring
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
        </a>
        <a href="#popular" class="px-8 py-4 rounded-full font-bold text-white text-lg border border-white/30 hover:bg-white/10 transition backdrop-blur-sm shadow-lg text-center">View Trending</a>
      </div>
    </div>
  </div>
</section>

<!-- Auto-Scrolling Categories -->
<section class="py-24 overflow-hidden relative">
  <div class="absolute inset-0 bg-gradient-to-b from-transparent to-slate-100/50 -z-10"></div>
  <div class="text-center mb-16 px-4">
    <h2 class="text-4xl md:text-5xl font-extrabold text-slate-900">Explore Categories</h2>
    <div class="w-24 h-1.5 bg-gradient-to-r from-orange-500 to-amber-400 mx-auto mt-6 rounded-full"></div>
  </div>

  <?php 
  $cats = [
    ['name' => 'Fiction', 'desc' => 'Explore a world of imagination and creativity.', 'img' => 'https://cdn.dribbble.com/userupload/26650664/file/original-5042a2da9685bf3543e23fe0dbc644d9.gif', 'color' => 'bg-amber-100'],
    ['name' => 'Science', 'desc' => 'Discover the wonders of the universe.', 'img' => 'https://edusmart-website.s3.amazonaws.com/images/homepage/home-1.gif', 'color' => 'bg-blue-100'],
    ['name' => 'Biography', 'desc' => 'Get inspired by the lives of famous personalities.', 'img' => 'https://files.askiitians.com/cdn1/images/2014106-1405299-1283-tumblr_mve89xajw21s9j08jo1_500.gif', 'color' => 'bg-green-100'],
    ['name' => 'Technology', 'desc' => 'Explore the advancements and innovations.', 'img' => 'https://media1.tenor.com/m/ueCTt_UQF2EAAAAd/innovation-future.gif', 'color' => 'bg-indigo-100'],
    ['name' => 'Self-Help', 'desc' => 'Find guidance for personal growth.', 'img' => 'https://cdn.prod.website-files.com/61cb94e5fee3d491ca9aa59c/61cb94e5fee3d4055c9aa6fb_self-care-during-busy-work-week.gif', 'color' => 'bg-pink-100'],
    ['name' => 'Children', 'desc' => 'Delight in stories for young minds.', 'img' => 'https://i.pinimg.com/originals/10/e0/93/10e0938774f51bc442180a6854454ac5.gif', 'color' => 'bg-teal-100']
  ];
  // duplicate for continuous marquee
  $display_cats = array_merge($cats, $cats);
  ?>
  
  <div class="w-full relative py-8">
    <div class="absolute inset-y-0 left-0 w-16 md:w-48 bg-gradient-to-r from-[#f8fafc] to-transparent z-10 pointer-events-none"></div>
    <div class="absolute inset-y-0 right-0 w-16 md:w-48 bg-gradient-to-l from-[#f8fafc] to-transparent z-10 pointer-events-none"></div>
    
    <div class="animate-marquee gap-8 md:gap-12 px-4">
      <?php foreach ($display_cats as $c): ?>
      <a href="books.php?category=<?php echo urlencode($c['name']); ?>" class="block w-[300px] md:w-[380px] flex-shrink-0 group">
        <div class="bg-white rounded-[2rem] shadow-xl overflow-hidden border border-slate-100 transition-all duration-500 group-hover:-translate-y-4 group-hover:shadow-2xl">
          <div class="h-56 md:h-64 overflow-hidden relative">
            <div class="absolute inset-0 bg-black/10 group-hover:bg-transparent transition-colors z-10"></div>
            <img class="w-full h-full object-cover transform transition duration-700 group-hover:scale-110" src="<?php echo $c['img']; ?>" alt="<?php echo $c['name']; ?>">
          </div>
          <div class="p-8 <?php echo $c['color']; ?>/20 border-t border-slate-50">
            <h3 class="text-3xl font-extrabold text-slate-800 mb-3"><?php echo $c['name']; ?></h3>
            <p class="text-slate-600 font-medium leading-relaxed"><?php echo $c['desc']; ?></p>
          </div>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Popular Books -->
<section id="popular" class="py-24 px-4 bg-white relative">
  <div class="max-w-7xl mx-auto">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-16 gap-6">
      <div>
        <h2 class="text-4xl md:text-5xl font-extrabold text-slate-900 mb-4">Trending Now</h2>
        <p class="text-slate-500 text-xl">The most borrowed books by our community this week</p>
      </div>
      <a href="books.php" class="inline-flex items-center gap-2 text-orange-600 font-bold hover:text-orange-700 transition bg-orange-50 px-6 py-3 rounded-full hover:bg-orange-100">
        Browse All Books <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
      </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-8">
      <?php foreach ($popularBooks as $book): ?>
        <div class="bg-white rounded-3xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden border border-slate-100 group flex flex-col h-full hover:-translate-y-2">
          <div class="relative h-72 overflow-hidden">
            <img src="<?php echo $book['image']; ?>" alt="<?php echo htmlspecialchars($book['title']); ?>" class="w-full h-full object-cover transform group-hover:scale-110 transition duration-500">
            <div class="absolute top-4 right-4 bg-white/95 backdrop-blur-md px-4 py-1.5 rounded-full text-xs font-black text-orange-600 shadow-md">
              🔥 <?php echo $book['borrow_count']; ?>
            </div>
          </div>
          <div class="p-6 flex-grow flex flex-col bg-slate-50 border-t border-slate-100">
            <h3 class="font-bold text-lg text-slate-900 mb-1 line-clamp-1" title="<?php echo htmlspecialchars($book['title']); ?>"><?php echo htmlspecialchars($book['title']); ?></h3>
            <p class="text-slate-500 text-sm mb-6 font-medium">by <?php echo htmlspecialchars($book['author']); ?></p>
            <div class="mt-auto flex items-center justify-between">
              <span class="text-2xl font-black text-slate-900">₹<?php echo $book['book_rent']; ?></span>
              <a href="book-details.php?id=<?php echo $book['books_id']; ?>" class="bg-white text-orange-600 shadow-sm border border-slate-200 hover:bg-orange-600 hover:text-white hover:border-orange-600 px-5 py-2.5 rounded-xl text-sm font-bold transition-all">
                Rent
              </a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Testimonials & Team -->
<section class="py-32 bg-slate-900 text-white relative overflow-hidden">
  <div class="absolute top-0 inset-x-0 h-px bg-gradient-to-r from-transparent via-slate-700 to-transparent"></div>
  <!-- Decorative background circles -->
  <div class="absolute top-0 right-0 w-96 h-96 bg-orange-500 rounded-full mix-blend-multiply filter blur-3xl opacity-10 blur-3xl translate-x-1/2 -translate-y-1/2"></div>
  <div class="absolute bottom-0 left-0 w-96 h-96 bg-blue-500 rounded-full mix-blend-multiply filter blur-3xl opacity-10 blur-3xl -translate-x-1/2 translate-y-1/2"></div>
  
  <div class="max-w-7xl mx-auto px-6 grid lg:grid-cols-2 gap-20 items-center relative z-10">
    
    <!-- Testimonials -->
    <div>
      <h2 class="text-4xl md:text-5xl font-extrabold mb-12 bg-clip-text text-transparent bg-gradient-to-r from-white to-slate-400">Loved by Readers</h2>
      <div class="space-y-8">
        <div class="bg-slate-800/40 p-8 rounded-3xl border border-slate-700 backdrop-blur-md shadow-2xl">
          <div class="flex items-center gap-4 mb-6">
            <img src="https://randomuser.me/api/portraits/women/79.jpg" class="w-14 h-14 rounded-full border-2 border-orange-500" alt="Aditi">
            <div>
              <h4 class="font-bold text-lg">Aditi Sharma</h4>
              <div class="text-orange-400 text-sm">★★★★★</div>
            </div>
          </div>
          <p class="text-slate-300 leading-relaxed text-lg">"The platform is beautifully designed and makes renting books a breeze. I completely love the seamless interface!"</p>
        </div>
        <div class="bg-slate-800/40 p-8 rounded-3xl border border-slate-700 backdrop-blur-md shadow-2xl lg:ml-12">
          <div class="flex items-center gap-4 mb-6">
            <img src="https://randomuser.me/api/portraits/men/32.jpg" class="w-14 h-14 rounded-full border-2 border-orange-500" alt="Ravi">
            <div>
              <h4 class="font-bold text-lg">Ravi Joshi</h4>
              <div class="text-orange-400 text-sm">★★★★★</div>
            </div>
          </div>
          <p class="text-slate-300 leading-relaxed text-lg">"BookNest is the absolute best. Tracking my borrowed books and exploring new ones feels like magic."</p>
        </div>
      </div>
    </div>

    <!-- Developer -->
    <div class="text-center bg-gradient-to-br from-slate-800/80 to-slate-900/80 p-12 lg:p-16 rounded-[3rem] border border-slate-700 shadow-2xl relative backdrop-blur-lg">
      <div class="absolute -top-6 -right-6 w-32 h-32 bg-orange-500 rounded-full blur-3xl opacity-20"></div>
      <h2 class="text-3xl font-extrabold mb-10 text-slate-100">Meet the Creator</h2>
      <div class="relative w-48 h-48 mx-auto mb-8">
        <div class="absolute inset-0 bg-orange-500 rounded-full blur-xl opacity-30 animate-pulse"></div>
        <img src="https://i.ibb.co/0VjdzSbH/lakshay.jpg" alt="Lakshay" class="relative w-48 h-48 rounded-full border-4 border-orange-500 shadow-2xl object-cover">
      </div>
      <h3 class="text-3xl font-bold mb-2">Lakshay</h3>
      <p class="text-orange-400 font-bold mb-6 tracking-wide uppercase text-sm">Lead Developer & Designer</p>
      <p class="text-slate-400 text-base leading-relaxed mb-10 max-w-sm mx-auto">Driving the vision behind BookNest. Passionate about creating premium, user-centric web applications and delivering exceptional digital experiences.</p>
      <a href="https://www.linkedin.com/in/lakshay156" target="_blank" class="inline-flex items-center gap-3 bg-white text-slate-900 hover:bg-orange-500 hover:text-white border border-transparent hover:border-orange-500 transition-all px-8 py-4 rounded-full text-sm font-black shadow-lg">
        Connect on LinkedIn
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M4.98 3.5C4.98 4.88 3.88 6 2.5 6S0 4.88 0 3.5 1.12 1 2.5 1s2.48 1.12 2.48 2.5zM.5 8h4V24h-4V8zm7.5 0h3.6v2.2h.1c.5-1 1.7-2.2 3.4-2.2 3.6 0 4.3 2.3 4.3 5.3V24h-4v-7.7c0-1.8-.03-4.1-2.5-4.1-2.5 0-2.9 1.9-2.9 4v7.8h-4V8z"/></svg>
      </a>
    </div>

  </div>
</section>

<!-- Footer -->
<footer class="bg-slate-950 text-slate-400 py-12 px-6 border-t border-slate-800">
  <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-6">
    <div class="flex items-center gap-3">
      <img src="https://i.ibb.co/Kc0pZmw6/logo-no-background.png" class="h-8 filter cursor-pointer grayscale opacity-50 hover:grayscale-0 hover:opacity-100 transition duration-300 hidden md:block">
    </div>
    <div class="text-sm font-medium">
      &copy; 2025 BookNest Premium. Developed by Lakshay. All rights reserved.
    </div>
  </div>
</footer>

<script>
  const menuToggle = document.getElementById("menu-toggle");
  const mobileMenu = document.getElementById("menu");
  menuToggle.addEventListener("click", () => {
    mobileMenu.classList.toggle("hidden");
    mobileMenu.classList.toggle("flex");
    mobileMenu.classList.toggle("flex-col");
    mobileMenu.classList.toggle("absolute");
    mobileMenu.classList.toggle("top-24");
    mobileMenu.classList.toggle("left-4");
    mobileMenu.classList.toggle("right-4");
    mobileMenu.classList.toggle("bg-white/95");
    mobileMenu.classList.toggle("backdrop-blur-xl");
    mobileMenu.classList.toggle("p-8");
    mobileMenu.classList.toggle("shadow-2xl");
    mobileMenu.classList.toggle("rounded-3xl");
    mobileMenu.classList.toggle("border");
    mobileMenu.classList.toggle("border-slate-200");
    mobileMenu.classList.toggle("space-y-6");
    if(mobileMenu.classList.contains("space-x-8")) {
        mobileMenu.classList.remove("space-x-8");
    }
  });

  const userBtn = document.getElementById("user-info-btn");
  const modal = document.getElementById("user-modal");
  const closeModal = document.getElementById("close-modal");

  if (userBtn && modal && closeModal) {
    userBtn.addEventListener("click", () => modal.classList.remove("hidden"));
    closeModal.addEventListener("click", () => modal.classList.add("hidden"));
    window.addEventListener("click", (e) => { if (e.target === modal) modal.classList.add("hidden"); });
  }
</script>
</body>
</html>
