<?php
// /wp-content/themes/rajaram-theme/index.php
get_header(); // This automatically loads your header.php file and the Tailwind config

// ૧. Fetch WooCommerce Categories dynamically to match your old $categories array
$categories = [];
if ( class_exists( 'WooCommerce' ) ) {
    $woo_cats = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => false ) );
    if ( ! is_wp_error( $woo_cats ) ) {
        foreach( $woo_cats as $cat ) {
            if ( $cat->slug === 'uncategorized' ) continue;
            $categories[] = array(
                'c_id'          => $cat->term_id, // Mapping WooCommerce category ID to your old c_id
                'category_name' => $cat->name
            );
        }
    }
}

// ૨. Fetch Latest 8 WooCommerce Products to match your old $products array
$products = [];
if ( class_exists( 'WooCommerce' ) ) {
    $args = array(
        'post_type'      => 'product',
        'posts_per_page' => 8,
        'orderby'        => 'date',
        'order'          => 'DESC'
    );
    $loop = new WP_Query( $args );
    if ( $loop->have_posts() ) {
        while ( $loop->have_posts() ) {
            $loop->the_post();
            global $product;
            
            // Get the primary category name for the badge
            $cat_ids = $product->get_category_ids();
            $cat_name = 'Instrument';
            if ( ! empty( $cat_ids ) ) {
                $term = get_term_by( 'id', $cat_ids[0], 'product_cat' );
                if ( $term ) $cat_name = $term->name;
            }
            
            // Get image URL
            $image_url = wp_get_attachment_url( $product->get_image_id() );
            
            // Map WooCommerce data to your exact old array keys
            $products[] = array(
                'pid'            => $product->get_id(),
                'product_name'   => $product->get_name(),
                'price'          => $product->get_price() ? $product->get_price() : '0',
                'stock_quantity' => $product->is_in_stock() ? ($product->get_stock_quantity() ? $product->get_stock_quantity() : 10) : 0,
                'image'          => $image_url,
                'avg_rating'     => $product->get_average_rating(),
                'review_count'   => $product->get_review_count(),
                'category_name'  => $cat_name
            );
        }
        wp_reset_postdata();
    }
}
?>

    <!-- Preloader script from your old setup -->
    <script>
    window.addEventListener("load", function() {
        const preloader = document.getElementById("preloader");
        const content = document.getElementById("main-content");
        if(preloader) preloader.style.opacity = "0";
        setTimeout(() => {
            if(preloader) preloader.style.display = "none";
            if(content) content.style.display = "block";
        }, 500);
    });
    setTimeout(function() {
        const preloader = document.getElementById("preloader");
        const content = document.getElementById("main-content");
        if (preloader && preloader.style.display !== "none") preloader.style.display = "none";
        if (content) content.style.display = "block";
    }, 4000);
    </script>

    <!-- Navigation (Links untouched as requested) -->
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

    <!-- Swiper Slider (Links untouched as requested) -->
    <div class="w-full relative" style="background-color:#F3ECDD;">
        <div class="swiper mySwiper w-full h-auto">
            <div class="swiper-wrapper">
                
                <div class="swiper-slide relative">
                    <div class="max-w-[1400px] mx-auto flex flex-col md:flex-row items-center gap-3 md:gap-10 px-5 sm:px-8 md:px-12 py-6 sm:py-10 md:py-16">
                        <div class="w-full md:w-1/2 order-1 md:order-2 flex items-center justify-center">
                            <img src="<?php echo get_template_directory_uri(); ?>/uploads/poster.png" class="w-50 h-60 sm:w-48 sm:h-48 md:w-auto md:h-[380px] object-contain drop-shadow-2xl rounded-xl sm:rounded-none" alt="Handcrafted Dhol">
                        </div>
                        <div class="w-full md:w-1/2 text-center md:text-left order-2 md:order-1">
                            <p class="text-brand-gold font-bold tracking-widest uppercase mb-1 sm:mb-2 text-[10px] sm:text-xs">Indian Heritage</p>
                            <h2 class="text-2xl sm:text-4xl md:text-5xl font-serif font-bold text-brand-dark mb-2 sm:mb-4 leading-tight">Handcrafted Kutchi<br>Dhols &amp; Tablas</h2>
                            <p class="text-gray-500 text-xs sm:text-base mb-3 sm:mb-6 max-w-md mx-auto md:mx-0 hidden sm:block">Timeless rhythm. Rich tradition.<br>Crafted with passion and precision.</p>
                            <a href="/customer/search.php" class="inline-block border-2 border-brand-dark text-brand-dark px-5 sm:px-7 py-2 sm:py-3 font-bold rounded-full hover:bg-brand-dark hover:text-white transition-colors text-[11px] sm:text-sm uppercase tracking-wider">View Masterpieces</a>
                        </div>
                    </div>
                </div>

                <div class="swiper-slide relative">
                    <div class="max-w-[1400px] mx-auto flex flex-col md:flex-row items-center gap-3 md:gap-10 px-5 sm:px-8 md:px-12 py-6 sm:py-10 md:py-16">
                        <div class="w-full md:w-1/2 order-1 md:order-2 flex items-center justify-center">
                            <img src="<?php echo get_template_directory_uri(); ?>/uploads/poster2.png" class="w-50 h-60 sm:w-48 sm:h-48 md:w-auto md:h-[380px] object-cover object-center drop-shadow-2xl rounded-xl sm:rounded-none" alt="Premium Guitar">
                        </div>
                        <div class="w-full md:w-1/2 text-center md:text-left order-2 md:order-1">
                            <p class="text-brand-gold font-bold tracking-widest uppercase mb-1 sm:mb-2 text-[10px] sm:text-xs">Craft Your Sound</p>
                            <h2 class="text-2xl sm:text-4xl md:text-5xl font-serif font-bold text-brand-dark mb-2 sm:mb-4 leading-tight">Premium Guitars<br>&amp; Strings</h2>
                            <p class="text-gray-500 text-xs sm:text-base mb-3 sm:mb-6 max-w-md mx-auto md:mx-0 hidden sm:block">Studio-grade tone. Concert-ready feel.<br>Built for every musician.</p>
                            <a href="/customer/search.php" class="inline-block bg-brand-dark text-white px-5 sm:px-7 py-2 sm:py-3 font-bold rounded-full hover:bg-brand-gold transition-colors text-[11px] sm:text-sm uppercase tracking-wider">Explore Store</a>
                        </div>
                    </div>
                </div>

                <div class="swiper-slide relative">
                    <div class="max-w-[1400px] mx-auto flex flex-col md:flex-row items-center gap-3 md:gap-10 px-5 sm:px-8 md:px-12 py-6 sm:py-10 md:py-16">
                        <div class="w-full md:w-1/2 order-1 md:order-2 flex items-center justify-center">
                            <img src="<?php echo get_template_directory_uri(); ?>/uploads/poster3.png" class="w-50 h-60 sm:w-48 sm:h-48 md:w-auto md:h-[380px] object-contain drop-shadow-2xl rounded-xl sm:rounded-none" alt="Classical Tabla Set">
                        </div>
                        <div class="w-full md:w-1/2 text-center md:text-left order-2 md:order-1">
                            <p class="text-brand-gold font-bold tracking-widest uppercase mb-1 sm:mb-2 text-[10px] sm:text-xs">Pure Classical Tone</p>
                            <h2 class="text-2xl sm:text-4xl md:text-5xl font-serif font-bold text-brand-dark mb-2 sm:mb-4 leading-tight">Authentic Tabla<br>&amp; Percussion Sets</h2>
                            <p class="text-gray-500 text-xs sm:text-base mb-3 sm:mb-6 max-w-md mx-auto md:mx-0 hidden sm:block">Hand-tuned for perfect pitch.<br>Trusted by classical artists for decades.</p>
                            <a href="/customer/search.php" class="inline-block border-2 border-brand-dark text-brand-dark px-5 sm:px-7 py-2 sm:py-3 font-bold rounded-full hover:bg-brand-dark hover:text-white transition-colors text-[11px] sm:text-sm uppercase tracking-wider">Shop Percussion</a>
                        </div>
                    </div>
                </div>

                <div class="swiper-slide relative">
                    <div class="max-w-[1400px] mx-auto flex flex-col md:flex-row items-center gap-3 md:gap-10 px-5 sm:px-8 md:px-12 py-6 sm:py-10 md:py-16">
                        <div class="w-full md:w-1/2 order-1 md:order-2 flex items-center justify-center">
                            <img src="<?php echo get_template_directory_uri(); ?>/uploads/poster4.png" class="w-50 h-60 sm:w-48 sm:h-48 md:w-auto md:h-[380px] object-contain drop-shadow-2xl rounded-xl sm:rounded-none" alt="Harmonium">
                        </div>
                        <div class="w-full md:w-1/2 text-center md:text-left order-2 md:order-1">
                            <p class="text-brand-gold font-bold tracking-widest uppercase mb-1 sm:mb-2 text-[10px] sm:text-xs">Melody in Every Note</p>
                            <h2 class="text-2xl sm:text-4xl md:text-5xl font-serif font-bold text-brand-dark mb-2 sm:mb-4 leading-tight">Rich-Toned<br>Harmoniums</h2>
                            <p class="text-gray-500 text-xs sm:text-base mb-3 sm:mb-6 max-w-md mx-auto md:mx-0 hidden sm:block">Smooth bellows. Warm resonance.<br>Built for devotional and stage performance.</p>
                            <a href="/customer/search.php" class="inline-block bg-brand-dark text-white px-5 sm:px-7 py-2 sm:py-3 font-bold rounded-full hover:bg-brand-gold transition-colors text-[11px] sm:text-sm uppercase tracking-wider">Explore Store</a>
                        </div>
                    </div>
                </div>

            </div>
            <div class="swiper-button-next !text-brand-dark hidden sm:flex"></div>
            <div class="swiper-button-prev !text-brand-dark hidden sm:flex"></div>
            <div class="swiper-pagination"></div>
        </div>
    </div>

    <!-- Product Grid -->
    <section id="collection" class="max-w-[1400px] mx-auto w-full px-4 sm:px-6 py-10 sm:py-16 flex-1">
        
        <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-6 sm:mb-10 border-b border-gray-200 pb-4 sm:pb-5">
            <div>
                <h4 class="text-brand-gold font-bold uppercase tracking-widest text-xs mb-1">Our Masterpieces</h4>
                <h2 class="text-2xl sm:text-3xl font-serif font-bold text-brand-dark">Featured Arrivals</h2>
            </div>
            <a href="/customer/search.php" class="text-sm font-bold text-brand-dark hover:text-brand-gold transition-colors mt-2 sm:mt-0 inline-block border-b border-brand-dark hover:border-brand-gold">View All Instruments →</a>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-8">
            <?php if(!empty($products)): ?>
                <?php foreach($products as $p): ?>
                <div class="product-card bg-white rounded-xl sm:rounded-2xl overflow-hidden shadow-sm hover:shadow-xl border border-gray-100 flex flex-col transition-all duration-300 group relative">
                    
                    <?php if($p['stock_quantity'] == 0): ?>
                        <div class="absolute top-2 left-2 sm:top-4 sm:left-4 z-10 bg-red-600 text-white text-[9px] sm:text-[10px] font-black uppercase tracking-widest px-2 sm:px-3 py-0.5 sm:py-1 rounded shadow-md">Out of Stock</div>
                    <?php endif; ?>

                    <div class="h-32 sm:h-60 bg-gray-50 p-3 sm:p-6 flex items-center justify-center relative overflow-hidden shrink-0">
                        <?php if(!empty($p['image'])): ?>
                            <img src="<?php echo esc_url($p['image']); ?>" class="product-img max-w-full max-h-full object-contain mix-blend-multiply transition-transform duration-500">
                        <?php else: ?>
                            <span class="text-3xl sm:text-5xl text-gray-300 product-img transition-transform duration-500">🎵</span>
                        <?php endif; ?>
                        
                        <div class="absolute inset-0 bg-brand-dark/5 backdrop-blur-[1px] opacity-0 group-hover:opacity-100 hidden sm:flex items-center justify-center transition-all duration-300">
                            <!-- Links untouched -->
                            <a href="/customer/product_detail.php?id=<?php echo $p['pid']; ?>" class="bg-brand-dark text-white px-5 py-2 rounded-full font-bold text-xs uppercase tracking-wider hover:bg-brand-gold transition-colors shadow-md">Quick View</a>
                        </div>
                    </div>

                    <div class="p-3 sm:p-5 flex flex-col flex-1">
                        <span class="text-[9px] sm:text-[10px] uppercase font-bold tracking-widest text-gray-400 mb-1"><?php echo htmlspecialchars($p['category_name'] ?? 'Instrument'); ?></span>
                        <!-- Links untouched -->
                        <a href="/customer/product_detail.php?id=<?php echo $p['pid']; ?>" class="text-sm sm:text-base font-bold text-brand-dark hover:text-brand-gold transition-colors line-clamp-2 min-h-[2.5rem] sm:min-h-[3rem]">
                            <?php echo htmlspecialchars($p['product_name']); ?>
                            <div class="flex items-center gap-1 mt-1 mb-1 sm:mb-2">
                                <div class="flex text-yellow-400 text-[10px] sm:text-xs">
                                    <?php 
                                    $rating = round($p['avg_rating']);
                                    for($i=1; $i<=5; $i++) echo ($i <= $rating) ? '★' : '<span class="text-gray-300">★</span>'; 
                                    ?>
                                </div>
                                <span class="text-[9px] sm:text-[10px] font-bold text-brand-gold hover:underline">(<?php echo $p['review_count']; ?>)</span>
                            </div>
                        </a>
                        
                        <div class="mt-2 sm:mt-4 pt-2 sm:pt-4 border-t border-gray-50 flex items-center justify-between">
                            <div>
                                <span class="text-[10px] sm:text-xs font-semibold text-gray-400 hidden sm:inline">Price</span>
                                <p class="text-base sm:text-xl font-black text-brand-dark tracking-tight">₹<?php echo number_format((float)$p['price']); ?></p>
                            </div>
                            
                            <?php if($p['stock_quantity'] > 0): ?>
                            <!-- Form Action untouched -->
                            <form action="/customer/add_to_cart.php" method="GET">
                                <input type="hidden" name="pid" value="<?php echo $p['pid']; ?>">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="w-7 h-7 sm:w-9 sm:h-9 rounded-full bg-slate-100 text-brand-dark hover:bg-brand-gold hover:text-white transition-colors flex items-center justify-center shadow-sm shrink-0">
                                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                                </button>
                            </form>
                            <?php else: ?>
                            <button disabled class="w-7 h-7 sm:w-9 sm:h-9 rounded-full bg-gray-100 text-gray-300 cursor-not-allowed flex items-center justify-center shrink-0">
                                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-span-full py-12 text-center bg-white border border-gray-200 rounded-2xl">
                    <p class="text-gray-400 text-sm">No instruments in the showroom yet. Please add products in WooCommerce!</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Swiper Initialization -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
    <script>
        var swiper = new Swiper(".mySwiper", {
            loop: true,
            autoplay: { delay: 4000, disableOnInteraction: false },
            pagination: { el: ".swiper-pagination", clickable: true },
            navigation: { nextEl: ".swiper-button-next", prevEl: ".swiper-button-prev" },
        });
    </script>

<?php get_footer(); ?>