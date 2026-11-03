<footer class="bg-brand-dark text-white pt-10 sm:pt-14 pb-6 border-t-4 border-brand-gold mt-auto relative z-50">
    <div class="max-w-[1400px] mx-auto px-5 sm:px-6">
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 sm:gap-10 mb-10 text-center sm:text-left">
            
            <div class="sm:col-span-2 lg:col-span-1">
                <h1 class="text-2xl font-black font-serif tracking-tight mb-2">RajaRam <span class="text-brand-gold italic font-normal">&</span> Sons</h1>
                <p class="text-xs text-gray-400 uppercase tracking-widest mb-4">Preserving Heritage &amp; Melodies Since 1951</p>
                <p class="text-sm text-gray-400 leading-relaxed max-w-xs mx-auto sm:mx-0">Handcrafted instruments, trusted by musicians across Gujarat for over seven decades.</p>
            </div>

            <div>
                <h3 class="text-brand-gold font-bold mb-4 uppercase tracking-wider text-sm">Quick Links</h3>
                <ul class="space-y-2.5 text-sm text-gray-300">
                    <li><a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="hover:text-brand-gold transition-colors">Home</a></li>
                    <li><a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>" class="hover:text-brand-gold transition-colors">All Instruments</a></li>
                    <li><a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="hover:text-brand-gold transition-colors">My Cart</a></li>
                    <li><a href="<?php echo esc_url( get_permalink( get_option('woocommerce_myaccount_page_id') ) ); ?>" class="hover:text-brand-gold transition-colors">My Account</a></li>
                </ul>
            </div>

            <div>
                <h3 class="text-brand-gold font-bold mb-4 uppercase tracking-wider text-sm">Categories</h3>
                <ul class="space-y-2.5 text-sm text-gray-300">
                    <?php
                    $footer_categories = [];
                    if ( class_exists( 'WooCommerce' ) ) {
                        $footer_categories = get_terms( array(
                            'taxonomy'   => 'product_cat',
                            'hide_empty' => false,
                            'number'     => 5
                        ) );
                    }
                    if ( ! empty( $footer_categories ) && ! is_wp_error( $footer_categories ) ) {
                        foreach($footer_categories as $fcat) {
                            if ($fcat->slug == 'uncategorized') continue;
                            echo '<li><a href="' . esc_url( get_term_link( $fcat ) ) . '" class="hover:text-brand-gold transition-colors">' . esc_html($fcat->name) . '</a></li>';
                        }
                    }
                    ?>
                </ul>
            </div>
            
            <div>
                <h3 class="text-brand-gold font-bold mb-4 uppercase tracking-wider text-sm">Contact Us</h3>
                <p class="text-sm text-gray-300 mb-1">Uplipad Road, Opp. Hatkeshwar Temple</p>
                <p class="text-sm text-gray-300 mb-4">Bhuj, Kutch, Gujarat - 370001</p>
                <p class="text-sm text-gray-300 font-bold flex flex-col sm:flex-row items-center sm:items-start justify-center sm:justify-start gap-1 sm:gap-2">
                     <span class="flex items-center gap-1.5"><svg class="w-3.5 h-3.5 text-brand-gold shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg> +91 98799 57792</span>
                     <span class="flex items-center gap-1.5"><svg class="w-3.5 h-3.5 text-brand-gold shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg> +91 98255 80615</span>
                </p>
            </div>
        </div>

        <div class="flex justify-center gap-6 mb-8 border-y border-white/10 py-5">
            <div class="flex items-center gap-2 text-xs font-bold text-gray-300"><svg class="w-4 h-4 text-brand-gold shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> 100% Genuine</div>
            <div class="flex items-center gap-2 text-xs font-bold text-gray-300"><svg class="w-4 h-4 text-brand-gold shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Free Shipping</div>
            <div class="flex items-center gap-2 text-xs font-bold text-gray-300 hidden sm:flex"><svg class="w-4 h-4 text-brand-gold shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Easy Returns</div>
        </div>

        <div class="text-gray-400 text-xs flex flex-col md:flex-row justify-between items-center gap-4 text-center">
            <p>&copy; <?php echo date('Y'); ?> RajaRam & Sons Music Mall. All Rights Reserved.</p>
            
            <p class="font-medium tracking-wide">
                Developed by <span class="text-brand-gold font-bold">Yuvraj Chudasama</span> & <span class="text-brand-gold font-bold">Meet Sonara</span> (74339 65406)
            </p>
        </div>
        
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>