<?php
// search.php
session_start();
require_once '../includes/db_connect.php';

$cart_count = 0;
if (isset($_SESSION['user_id'])) {
    try {
        $stmt_cart = $pdo->prepare("SELECT SUM(quantity) FROM carts WHERE user_id = :u_id");
        $stmt_cart->execute([':u_id' => $_SESSION['user_id']]);
        $cart_count = $stmt_cart->fetchColumn() ?: 0;
    } catch (Exception $e) {}
}

try {
    $categories = $pdo->query("SELECT * FROM categories ORDER BY c_id ASC")->fetchAll();
} catch (Exception $e) {
    $categories = [];
}

$search_query = isset($_GET['q']) ? trim($_GET['q']) : '';
$category_filter = isset($_GET['category']) ? $_GET['category'] : 'all';

$products = [];
$search_title = "All Instruments";

try {
    $sql = "SELECT p.*, c.category_name 
            FROM products p 
            LEFT JOIN categories c ON p.catid = c.c_id 
            WHERE 1=1";
    $params = [];

    if ($search_query !== '') {
        $sql .= " AND (p.product_name LIKE :q OR p.description LIKE :q)";
        $params[':q'] = "%$search_query%";
        $search_title = 'Search results for "' . htmlspecialchars($search_query) . '"';
    }

    if ($category_filter !== 'all') {
        $sql .= " AND p.catid = :catid";
        $params[':catid'] = $category_filter;
        
        $stmt_c = $pdo->prepare("SELECT category_name FROM categories WHERE c_id = :cid");
        $stmt_c->execute([':cid' => $category_filter]);
        $cat_name = $stmt_c->fetchColumn();
        if($cat_name && $search_query === '') {
            $search_title = htmlspecialchars($cat_name) . " Collection";
        }
    }

    $sql .= " ORDER BY p.pid DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $products = $stmt->fetchAll();

} catch (Exception $e) {
    $products = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $search_title; ?> - RajaRam & Sons</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Playfair+Display:ital,wght@0,700;1,700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'], serif: ['Playfair Display', 'serif'] },
                    colors: { brand: { dark: '#0A192F', gold: '#B7915F', hover: '#D4AF37', light: '#F8FAFC' } }
                }
            }
        }
    </script>
    <style>
        .group:hover .mega-menu { display: block; }
        .product-card:hover .product-img { transform: scale(1.06); }
    </style>
</head>
<body class="bg-brand-light font-sans text-gray-800 flex flex-col min-h-screen">

    <header class="bg-brand-dark text-white border-b border-white/10 sticky top-0 z-50 shadow-md">
        <div class="max-w-[1400px] mx-auto px-6 py-4 flex items-center justify-between gap-8">
            
            <a href="/index.php" class="shrink-0 flex flex-col">
                <h1 class="text-3xl font-black font-serif text-white tracking-tight">RajaRam <span class="text-brand-gold">&</span> Sons</h1>
                <p class="text-[9px] tracking-[0.3em] font-bold text-gray-400 uppercase mt-[-4px]">The Grand Music Mall</p>
            </a>

            <div class="flex-1 max-w-3xl hidden md:block relative">
                <form action="search.php" method="GET" class="flex items-center bg-white rounded-md overflow-hidden h-11 focus-within:ring-2 focus-within:ring-brand-gold relative z-50">
                    <select id="frontSearchCategory" name="category" class="bg-gray-100 text-gray-600 h-full px-3 text-sm border-r border-gray-300 outline-none cursor-pointer">
                        <option value="all">All Categories</option>
                        <?php foreach($categories as $cat): ?>
                            <option value="<?php echo $cat['c_id']; ?>" <?php echo ($category_filter == $cat['c_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['category_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <input type="text" id="frontSearchInput" name="q" value="<?php echo htmlspecialchars($search_query); ?>" autocomplete="off" placeholder="Search for Guitars, Keyboards, Tabla..." class="flex-1 h-full px-4 text-gray-800 text-sm outline-none">
                    <button type="submit" class="bg-brand-gold hover:bg-brand-hover text-brand-dark h-full px-6 transition-colors flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </button>
                </form>
                <div id="frontSearchResults" class="absolute top-full mt-1 left-0 w-full bg-white border border-slate-200 rounded-b-xl shadow-2xl hidden overflow-hidden z-[100]"></div>
            </div>

            <div class="flex items-center gap-6 shrink-0">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="user_dashboard.php" class="flex items-center gap-2 hover:text-brand-gold transition-colors font-bold text-sm">
                        <span class="bg-brand-gold text-brand-dark w-8 h-8 rounded-full flex items-center justify-center"><?php echo strtoupper(substr($_SESSION['user_name'] ?? 'U', 0, 1)); ?></span>
                        <span class="hidden sm:inline"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Account'); ?></span>
                    </a>
                <?php else: ?>
                    <a href="/auth/login.php" class="hidden sm:flex flex-col items-start hover:text-brand-gold transition-colors">
                        <span class="text-[10px] text-gray-400 font-bold uppercase">Welcome</span>
                        <span class="text-sm font-bold leading-tight">Login / Register</span>
                    </a>
                <?php endif; ?>
                
                <a href="cart.php" class="flex items-center gap-2 hover:text-brand-gold transition-colors relative">
                    <div class="relative">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        <span class="absolute -top-1 -right-2 bg-brand-gold text-brand-dark w-5 h-5 rounded-full flex items-center justify-center text-[11px] font-black border-2 border-brand-dark"><?php echo $cart_count; ?></span>
                    </div>
                    <span class="text-sm font-bold hidden lg:block">Cart</span>
                </a>
            </div>
        </div>
    </header>

    <main class="max-w-[1400px] mx-auto w-full px-6 py-12 flex-1">
        
        <div class="mb-8 border-b border-gray-200 pb-4">
            <h2 class="text-2xl md:text-3xl font-serif font-bold text-brand-dark"><?php echo $search_title; ?></h2>
            <p class="text-sm text-gray-500 mt-2 font-medium">Found <?php echo count($products); ?> instruments matching your criteria.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <?php if(!empty($products)): ?>
                <?php foreach($products as $p): ?>
                <div class="product-card bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl border border-gray-100 flex flex-col transition-all duration-300 group relative">
                    
                    <?php if($p['stock_quantity'] == 0): ?>
                        <div class="absolute top-4 left-4 z-10 bg-red-600 text-white text-[10px] font-black uppercase tracking-widest px-2.5 py-1 rounded shadow-md">Out of Stock</div>
                    <?php endif; ?>

                    <div class="h-60 bg-gray-50 p-6 flex items-center justify-center relative overflow-hidden shrink-0">
                        <?php if(!empty($p['image'])): ?>
                            <img src="/uploads/<?php echo htmlspecialchars($p['image']); ?>" class="product-img max-w-full max-h-full object-contain mix-blend-multiply transition-transform duration-500">
                        <?php else: ?>
                            <span class="text-5xl text-gray-300 product-img transition-transform duration-500">🎵</span>
                        <?php endif; ?>
                        
                        <div class="absolute inset-0 bg-brand-dark/5 backdrop-blur-[1px] opacity-0 group-hover:opacity-100 flex items-center justify-center transition-all duration-300">
                            <a href="product_detail.php?id=<?php echo $p['pid']; ?>" class="bg-brand-dark text-white px-5 py-2 rounded-full font-bold text-xs uppercase tracking-wider hover:bg-brand-gold transition-colors shadow-md">View Details</a>
                        </div>
                    </div>

                    <div class="p-5 flex flex-col flex-1">
                        <span class="text-[10px] uppercase font-bold tracking-widest text-gray-400 mb-1"><?php echo htmlspecialchars($p['category_name'] ?? 'Instrument'); ?></span>
                        <a href="product_detail.php?id=<?php echo $p['pid']; ?>" class="text-base font-bold text-brand-dark hover:text-brand-gold transition-colors line-clamp-2 min-h-[3rem]">
                            <?php echo htmlspecialchars($p['product_name']); ?>
                        </a>
                        
                        <div class="mt-4 pt-4 border-t border-gray-50 flex items-center justify-between">
                            <div>
                                <span class="text-xs font-semibold text-gray-400">Price</span>
                                <p class="text-xl font-black text-brand-dark tracking-tight">₹<?php echo number_format($p['price']); ?></p>
                            </div>
                            
                            <?php if($p['stock_quantity'] > 0): ?>
                            <form action="add_to_cart.php" method="POST">
                                <input type="hidden" name="pid" value="<?php echo $p['pid']; ?>">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="w-10 h-10 rounded-full bg-brand-gold/10 text-brand-gold hover:bg-brand-gold hover:text-white transition-colors flex items-center justify-center shadow-sm">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                </button>
                            </form>
                            <?php else: ?>
                            <button disabled class="w-10 h-10 rounded-full bg-gray-100 text-gray-300 cursor-not-allowed flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-span-full py-20 flex flex-col items-center justify-center bg-white border border-gray-200 rounded-3xl shadow-sm text-center">
                    <span class="text-6xl text-gray-200 mb-4">🔍</span>
                    <h3 class="text-2xl font-bold text-brand-dark mb-2">No Instruments Found</h3>
                    <p class="text-gray-500 mb-6">We couldn't find anything matching "<?php echo htmlspecialchars($search_query); ?>". Try searching for something else.</p>
                    <a href="/index.php" class="bg-brand-dark text-white px-6 py-2.5 rounded-full font-bold text-sm uppercase tracking-wider hover:bg-brand-gold transition-colors">Return to Home</a>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <footer class="bg-brand-dark text-white pt-12 pb-6 border-t-4 border-brand-gold mt-auto">
        <div class="max-w-[1400px] mx-auto px-6 text-center">
            <h1 class="text-2xl font-black font-serif tracking-tight mb-2">RajaRam <span class="text-brand-gold italic font-normal">&</span> Sons</h1>
            <p class="text-xs text-gray-400 uppercase tracking-widest mb-6">Preserving Heritage & Melodies Since 1951</p>
            <div class="text-gray-500 text-[11px] border-t border-white/5 pt-6">
                &copy; 2026 RajaRam & Sons Music Mall. Bhuj, Gujarat. All Rights Reserved.
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const searchInput = document.getElementById('frontSearchInput');
            const searchCategory = document.getElementById('frontSearchCategory');
            const searchResults = document.getElementById('frontSearchResults');

            if(searchInput && searchResults) {
                searchInput.addEventListener('keyup', fetchResults);
                searchCategory.addEventListener('change', fetchResults);

                function fetchResults() {
                    let query = searchInput.value.trim();
                    let category = searchCategory.value;
                    
                    if (query.length >= 2) {
                        fetch('front_ajax_search.php?q=' + encodeURIComponent(query) + '&category=' + encodeURIComponent(category))
                            .then(response => response.text())
                            .then(data => {
                                searchResults.innerHTML = data;
                                searchResults.classList.remove('hidden');
                            });
                    } else {
                        searchResults.innerHTML = '';
                        searchResults.classList.add('hidden');
                    }
                }

                document.addEventListener('click', function(event) {
                    if (!searchInput.contains(event.target) && !searchResults.contains(event.target) && !searchCategory.contains(event.target)) {
                        searchResults.classList.add('hidden');
                    }
                });
            }
        });
    </script>
</body>
</html>