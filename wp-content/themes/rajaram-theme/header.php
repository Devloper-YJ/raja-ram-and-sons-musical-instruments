<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php wp_title('|', true, 'right'); ?></title>
    
    <!-- Tailwind and Fonts -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Playfair+Display:ital,wght@0,700;1,700&display=swap" rel="stylesheet">
    
    <!-- Custom Tailwind Config -->
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
    
    <?php wp_head(); ?>
</head>
<body <?php body_class("bg-brand-light font-sans text-gray-800 flex flex-col min-h-screen"); ?>>

<?php
// Get live WooCommerce Cart Count
$cart_count = 0;
if ( class_exists( 'WooCommerce' ) && ! is_null( WC()->cart ) ) {
    $cart_count = WC()->cart->get_cart_contents_count();
}

// Get WooCommerce Product Categories dynamically
$categories = [];
if ( class_exists( 'WooCommerce' ) ) {
    $categories = get_terms( array(
        'taxonomy'   => 'product_cat',
        'hide_empty' => false,
    ) );
}
?>

<header class="bg-white text-brand-dark border-b border-gray-100 sticky top-0 z-50 shadow-sm">
    <div class="max-w-[1400px] mx-auto px-4 sm:px-6 py-3 sm:py-4 flex items-center justify-between gap-4 sm:gap-8">
        
        <button id="mobileMenuBtn" type="button" class="md:hidden shrink-0 text-brand-dark p-1 -ml-1">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
        </button>

        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="shrink-0 flex flex-col items-center md:items-start mx-auto md:mx-0">
            <h1 class="text-xl sm:text-2xl md:text-3xl font-black font-serif text-brand-dark tracking-tight whitespace-nowrap">RajaRam <span class="text-brand-gold">&</span> Sons</h1>
            <p class="text-[7px] sm:text-[9px] tracking-[0.25em] sm:tracking-[0.3em] font-bold text-gray-400 uppercase mt-[-2px] sm:mt-[-4px]">Music Mall</p>
        </a>

        <!-- Desktop Search (Now tied to WooCommerce) -->
        <div class="flex-1 max-w-3xl hidden md:block relative">
            <form action="<?php echo esc_url( home_url( '/' ) ); ?>" method="GET" class="flex items-center bg-gray-50 border border-gray-200 rounded-md overflow-hidden h-11 focus-within:ring-2 focus-within:ring-brand-gold relative z-50">
                <!-- Forces WordPress to search only Products -->
                <input type="hidden" name="post_type" value="product" />
                
                <select id="frontSearchCategory" name="product_cat" class="bg-gray-100 text-gray-600 h-full px-3 text-sm border-r border-gray-300 outline-none cursor-pointer">
                    <option value="">All Categories</option>
                    <?php 
                    if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) {
                        foreach($categories as $cat) {
                            if ($cat->slug == 'uncategorized') continue;
                            echo '<option value="' . esc_attr($cat->slug) . '">' . esc_html($cat->name) . '</option>';
                        }
                    }
                    ?>
                </select>
                <!-- Search input name changed to 's' for standard WP query -->
                <input type="text" id="frontSearchInput" name="s" autocomplete="off" placeholder="Search for Guitars, Keyboards, Tabla..." class="flex-1 h-full px-4 text-gray-800 text-sm outline-none bg-transparent">
                <button type="submit" class="bg-brand-gold hover:bg-brand-hover text-white h-full px-6 transition-colors flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </button>
            </form>
        </div>

        <div class="flex items-center gap-3 sm:gap-6 shrink-0">
           <?php if( is_user_logged_in() ): ?>
                <!-- Mobile Account Icon -->
                <a href="<?php echo esc_url( get_permalink( get_option('woocommerce_myaccount_page_id') ) ); ?>" class="md:hidden text-brand-dark p-1">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                </a>
            <?php else: ?>
                <!-- Mobile Login Icon -->
                <a href="<?php echo esc_url( get_permalink( get_option('woocommerce_myaccount_page_id') ) ); ?>" class="md:hidden text-brand-dark p-1">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                </a>
            <?php endif; ?>

            <?php if( is_user_logged_in() ): 
                $current_user = wp_get_current_user();
            ?>
                <!-- Desktop Logged In State -->
                <a href="<?php echo esc_url( get_permalink( get_option('woocommerce_myaccount_page_id') ) ); ?>" class="hidden md:flex items-center gap-2 hover:text-brand-gold transition-colors font-bold text-sm">
                    <span class="bg-brand-gold text-white w-8 h-8 rounded-full flex items-center justify-center"><?php echo strtoupper(substr($current_user->display_name, 0, 1)); ?></span>
                    <span class="hidden lg:inline"><?php echo esc_html($current_user->display_name); ?></span>
                </a>
                <a href="<?php echo esc_url( wp_logout_url( home_url() ) ); ?>" title="Logout" class="hidden md:flex items-center text-gray-400 hover:text-red-500 transition-colors p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                </a>
            <?php else: ?>
                <!-- Desktop Logged Out State -->
                <a href="<?php echo esc_url( get_permalink( get_option('woocommerce_myaccount_page_id') ) ); ?>" class="hidden md:flex flex-col items-start hover:text-brand-gold transition-colors">
                    <span class="text-[10px] text-gray-400 font-bold uppercase">Welcome</span>
                    <span class="text-sm font-bold leading-tight">Login / Register</span>
                </a>
            <?php endif; ?>
            
            <!-- Cart Link pointing to Woo Cart -->
            <a href="<?php echo function_exists('wc_get_cart_url') ? esc_url( wc_get_cart_url() ) : '#'; ?>" class="flex items-center gap-2 hover:text-brand-gold transition-colors relative p-1">
                <div class="relative">
                    <svg class="w-6 h-6 sm:w-7 sm:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    <span class="absolute -top-1.5 -right-2 bg-brand-gold text-white w-4 h-4 sm:w-5 sm:h-5 rounded-full flex items-center justify-center text-[9px] sm:text-[11px] font-black border-2 border-white"><?php echo $cart_count; ?></span>
                </div>
                <span class="text-sm font-bold hidden lg:block">Cart</span>
            </a>
        </div>
    </div>

    <!-- Mobile Search Bar -->
    <div id="mobileSearchBar" class="md:hidden px-4 pb-3 relative">
        <form action="<?php echo esc_url( home_url( '/' ) ); ?>" method="GET" class="flex items-center bg-gray-50 border border-gray-200 rounded-md overflow-hidden h-10">
            <input type="hidden" name="post_type" value="product" />
            <input type="text" name="s" autocomplete="off" placeholder="Search for Guitars, Keyboards, Tabla..." class="flex-1 h-full px-3 text-gray-800 text-sm outline-none bg-transparent">
            <button type="submit" class="bg-brand-gold text-white h-full px-4 flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </button>
        </form>
    </div>

    <!-- Mobile Menu -->
    <div id="mobileMenuPanel" class="md:hidden hidden bg-white border-t border-gray-100 shadow-lg">
        <nav class="flex flex-col py-2">
            <?php 
            if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) {
                foreach($categories as $cat) {
                    if ($cat->slug == 'uncategorized') continue;
                    echo '<a href="' . esc_url( get_term_link( $cat ) ) . '" class="px-5 py-3 text-sm font-bold text-gray-700 hover:bg-gray-50 hover:text-brand-gold border-b border-gray-50">' . esc_html($cat->name) . '</a>';
                }
            }
            ?>
            <?php if( is_user_logged_in() ): ?>
                <a href="<?php echo esc_url( get_permalink( get_option('woocommerce_myaccount_page_id') ) ); ?>" class="px-5 py-3 text-sm font-bold text-gray-700 hover:bg-gray-50 hover:text-brand-gold border-b border-gray-50">My Account</a>
                <a href="<?php echo esc_url( wp_logout_url( home_url() ) ); ?>" class="px-5 py-3 text-sm font-bold text-red-600 hover:bg-red-50 border-b border-gray-50">Logout</a>
            <?php else: ?>
                <a href="<?php echo esc_url( get_permalink( get_option('woocommerce_myaccount_page_id') ) ); ?>" class="px-5 py-3 text-sm font-bold text-gray-700 hover:bg-gray-50 hover:text-brand-gold border-b border-gray-50">Login / Register</a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const menuBtn = document.getElementById('mobileMenuBtn');
        const menuPanel = document.getElementById('mobileMenuPanel');
        if (menuBtn && menuPanel) {
            menuBtn.addEventListener('click', function() {
                menuPanel.classList.toggle('hidden');
            });
        }
    });
</script>