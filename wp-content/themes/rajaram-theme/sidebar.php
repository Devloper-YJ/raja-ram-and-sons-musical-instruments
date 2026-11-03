<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Find out which WooCommerce endpoint is currently active
global $wp;
$current_endpoint = isset( $wp->query_vars ) ? key( $wp->query_vars ) : '';
?>

<!-- We kept your exact Tailwind classes, just made it slightly responsive for mobile -->
<aside class="w-full md:w-64 bg-[#06101E] text-white flex flex-col shadow-[5px_0_20px_rgba(0,0,0,0.5)] border border-[#B7915F]/30 shrink-0 relative rounded-xl overflow-hidden mb-8 md:mb-0">
    
    <div class="h-20 flex items-center justify-center border-b border-white/10">
        <h1 class="text-xl sm:text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-[#B7915F] to-[#D4AF37]">My Account</h1>
    </div>
    
    <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
        <?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) : ?>
            <?php
            // Determine active/inactive class
            $is_active = false;
            if ( $endpoint === 'dashboard' && empty( $current_endpoint ) ) {
                $is_active = true;
            } elseif ( $current_endpoint === $endpoint ) {
                $is_active = true;
            }
            
            // Your exact button designs
            $active_class = "flex items-center gap-3 bg-[#B7915F] text-white px-4 py-3 rounded-lg font-bold shadow-md transition-colors";
            $inactive_class = "flex items-center gap-3 text-slate-300 hover:text-white hover:bg-white/5 px-4 py-3 rounded-lg font-medium transition-colors";
            $class = $is_active ? $active_class : $inactive_class;
            
            // Assign your SVGs dynamically based on the WooCommerce link
            $svg = '';
            switch ($endpoint) {
                case 'dashboard':
                    $svg = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>';
                    break;
                case 'orders':
                    $svg = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>';
                    break;
                case 'edit-address':
                    $svg = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>';
                    break;
                case 'edit-account':
                    $svg = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>';
                    break;
                case 'customer-logout':
                    $svg = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>';
                    break;
                default:
                    $svg = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
            }
            ?>
            
            <a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>" class="<?php echo $class; ?>">
                <?php echo $svg; ?>
                <?php echo esc_html( $label ); ?>
            </a>
            
        <?php endforeach; ?>
    </nav>
</aside>