<?php
// index.php
session_start();
require_once 'includes/db_connect.php';

// ૩. હોમપેજ પર બતાવવા માટે લેટેસ્ટ 8 પ્રોડક્ટ્સ લાવો (રેટિંગ સાથે)
try {
    $sql_prod = "SELECT p.*, c.category_name, IFNULL(AVG(r.rating), 0) as avg_rating, COUNT(r.review_id) as review_count 
                 FROM products p 
                 LEFT JOIN categories c ON p.catid = c.c_id 
                 LEFT JOIN product_reviews r ON p.pid = r.pid
                 GROUP BY p.pid 
                 ORDER BY p.pid DESC LIMIT 8";
    $products = $pdo->query($sql_prod)->fetchAll();
} catch (Exception $e) {
    $products = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RajaRam & Sons - The Grand Music Mall</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Playfair+Display:ital,wght@0,700;1,700&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'], serif: ['Playfair Display', 'serif'] },
                    colors: {
                        brand: { dark: '#0A192F', gold: '#B7915F', hover: '#D4AF37', light: '#F8FAFC' }
                    }
                }
            }
        }
    </script>
    <style>
        .group:hover .mega-menu { display: block; }
        .swiper-pagination-bullet-active { background: #B7915F !important; }
        .swiper-button-next, .swiper-button-prev { color: white !important; text-shadow: 0 2px 4px rgba(0,0,0,0.5); }
        .product-card:hover .product-img { transform: scale(1.06); }
    </style>
</head>
<body class="bg-brand-light font-sans text-gray-800 flex flex-col min-h-screen">

    <?php include 'includes/header.php'; ?>

    <nav class="bg-white border-b border-gray-200 shadow-sm relative z-40 hidden md:block">
        <div class="max-w-[1400px] mx-auto px-6 flex items-center justify-center gap-8">
            
            <?php foreach($categories as $cat): ?>
            <div class="group py-4 border-b-2 border-transparent hover:border-brand-gold cursor-pointer transition-all">
                <a href="/customer/search.php?category=<?php echo $cat['c_id']; ?>" class="text-sm font-bold text-gray-700 group-hover:text-brand-gold">
                    <?php echo htmlspecialchars($cat['category_name']); ?>
                </a>
                
                <div class="mega-menu hidden absolute top-full left-0 w-full bg-white shadow-2xl border-t border-gray-100 z-50">
                    <div class="max-w-[1400px] mx-auto p-8 grid grid-cols-4 gap-8">
                        <div>
                            <h3 class="font-black text-brand-dark uppercase tracking-wider mb-4 border-b pb-2">Browse All</h3>
                            <ul class="space-y-3 text-sm text-gray-500 font-medium">
                                <li><a href="/customer/search.php?category=<?php echo $cat['c_id']; ?>" class="hover:text-brand-gold">View All <?php echo htmlspecialchars($cat['category_name']); ?></a></li>
                            </ul>
                        </div>
                        <div class="col-span-3 flex items-center justify-center bg-slate-50 rounded-xl p-4 border border-slate-100">
                            <p class="text-sm text-gray-500 font-medium text-center">Handcrafted and tested by experts at RajaRam & Sons since 1951.</p>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            
            <div class="py-4 cursor-pointer">
                <span class="text-sm font-black text-red-600 hover:text-red-700">🔥 Hot Deals</span>
            </div>
        </div>
    </nav>

    <div class="w-full relative bg-[#0A192F]">
        <div class="swiper mySwiper w-full h-[300px] sm:h-[400px] md:h-[480px]">
            <div class="swiper-wrapper">
                
                <div class="swiper-slide relative">
                    <img src="https://images.unsplash.com/photo-1510915361894-db8b60106cb1?q=80&w=2070&auto=format&fit=crop" class="w-full h-full object-cover opacity-50">
                    <div class="absolute inset-0 bg-gradient-to-r from-brand-dark/80 via-brand-dark/40 to-transparent flex flex-col justify-center px-8 md:px-24">
                        <p class="text-brand-gold font-bold tracking-widest uppercase mb-2 text-xs md:text-sm">Craft Your Sound</p>
                        <h2 class="text-3xl md:text-6xl font-serif font-bold text-white mb-6 leading-tight max-w-2xl">Premium Guitars & <br>Strings</h2>
                        <a href="#collection" class="bg-brand-gold text-brand-dark px-6 py-3 w-max font-bold rounded-full hover:bg-white transition-colors text-sm uppercase tracking-wider shadow-lg">Explore Store</a>
                    </div>
                </div>

                <div class="swiper-slide relative">
                    <div class="absolute inset-0 bg-gradient-to-br from-[#162A4A] to-brand-dark opacity-90"></div>
                    <img src="/img/dhol.jpg" class="w-full h-full object-cover opacity-40 mix-blend-overlay" onerror="this.style.display='none'">
                    <div class="absolute inset-0 flex flex-col justify-center px-8 md:px-24">
                        <p class="text-brand-gold font-bold tracking-widest uppercase mb-2 text-xs md:text-sm">Indian Heritage</p>
                        <h2 class="text-3xl md:text-6xl font-serif font-bold text-white mb-6 leading-tight max-w-2xl">Handcrafted Kutchi <br>Dhols & Tablas</h2>
                        <a href="#collection" class="bg-white text-brand-dark px-6 py-3 w-max font-bold rounded-full hover:bg-brand-gold hover:text-white transition-colors text-sm uppercase tracking-wider shadow-lg">View Masterpieces</a>
                    </div>
                </div>

            </div>
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
            <div class="swiper-pagination"></div>
        </div>
    </div>

    <section id="collection" class="max-w-[1400px] mx-auto w-full px-6 py-16 flex-1">
        
        <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-10 border-b border-gray-200 pb-5">
            <div>
                <h4 class="text-brand-gold font-bold uppercase tracking-widest text-xs mb-1">Our Masterpieces</h4>
                <h2 class="text-3xl font-serif font-bold text-brand-dark">Featured Arrivals</h2>
            </div>
            <a href="/customer/search.php" class="text-sm font-bold text-brand-dark hover:text-brand-gold transition-colors mt-2 sm:mt-0 inline-block border-b border-brand-dark hover:border-brand-gold">View All Instruments →</a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <?php if(!empty($products)): ?>
                <?php foreach($products as $p): ?>
                <div class="product-card bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl border border-gray-100 flex flex-col transition-all duration-300 group relative">
                    
                    <?php if($p['stock_quantity'] == 0): ?>
                        <div class="absolute top-4 left-4 z-10 bg-red-600 text-white text-[10px] font-black uppercase tracking-widest px-3 py-1 rounded shadow-md">Out of Stock</div>
                    <?php endif; ?>

                    <div class="h-60 bg-gray-50 p-6 flex items-center justify-center relative overflow-hidden shrink-0">
                        <?php if(!empty($p['image'])): ?>
                            <img src="/uploads/<?php echo htmlspecialchars($p['image']); ?>" class="product-img max-w-full max-h-full object-contain mix-blend-multiply transition-transform duration-500">
                        <?php else: ?>
                            <span class="text-5xl text-gray-300 product-img transition-transform duration-500">🎵</span>
                        <?php endif; ?>
                        
                        <div class="absolute inset-0 bg-brand-dark/5 backdrop-blur-[1px] opacity-0 group-hover:opacity-100 flex items-center justify-center transition-all duration-300">
                            <a href="/customer/product_detail.php?id=<?php echo $p['pid']; ?>" class="bg-brand-dark text-white px-5 py-2 rounded-full font-bold text-xs uppercase tracking-wider hover:bg-brand-gold transition-colors shadow-md">Quick View</a>
                        </div>
                    </div>

                    <div class="p-5 flex flex-col flex-1">
                        <span class="text-[10px] uppercase font-bold tracking-widest text-gray-400 mb-1"><?php echo htmlspecialchars($p['category_name'] ?? 'Instrument'); ?></span>
                        <a href="/customer/product_detail.php?id=<?php echo $p['pid']; ?>" class="text-base font-bold text-brand-dark hover:text-brand-gold transition-colors line-clamp-2 min-h-[3rem]">
                            <?php echo htmlspecialchars($p['product_name']); ?>
                            <div class="flex items-center gap-1 mt-1 mb-2">
    <div class="flex text-yellow-400 text-xs">
        <?php 
        $rating = round($p['avg_rating']);
        for($i=1; $i<=5; $i++) echo ($i <= $rating) ? '★' : '<span class="text-gray-300">★</span>'; 
        ?>
    </div>
    <span class="text-[10px] font-bold text-brand-gold hover:underline">(<?php echo $p['review_count']; ?>)</span>
</div>
                        </a>
                        
                        <div class="mt-4 pt-4 border-t border-gray-50 flex items-center justify-between">
                            <div>
                                <span class="text-xs font-semibold text-gray-400">Price</span>
                                <p class="text-xl font-black text-brand-dark tracking-tight">₹<?php echo number_format($p['price']); ?></p>
                            </div>
                            
                            <?php if($p['stock_quantity'] > 0): ?>
                            <form action="/customer/add_to_cart.php" method="POST">
                                <input type="hidden" name="pid" value="<?php echo $p['pid']; ?>">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="w-9 h-9 rounded-full bg-slate-100 text-brand-dark hover:bg-brand-gold hover:text-white transition-colors flex items-center justify-center shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                                </button>
                            </form>
                            <?php else: ?>
                            <button disabled class="w-9 h-9 rounded-full bg-gray-100 text-gray-300 cursor-not-allowed flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-span-full py-12 text-center bg-white border border-gray-200 rounded-2xl">
                    <p class="text-gray-400 text-sm">No instruments in the showroom yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
    <script>
        var swiper = new Swiper(".mySwiper", {
            loop: true,
            autoplay: { delay: 4000, disableOnInteraction: false },
            pagination: { el: ".swiper-pagination", clickable: true },
            navigation: { nextEl: ".swiper-button-next", prevEl: ".swiper-button-prev" },
        });
    </script>
</body>
</html>