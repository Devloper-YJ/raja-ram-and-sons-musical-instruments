<?php
// header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'db_connect.php';

// કાર્ટનો આંકડો મેળવો
$cart_count = 0;
if (isset($_SESSION['user_id'])) {
    try {
        $stmt_cart = $pdo->prepare("SELECT SUM(quantity) FROM carts WHERE user_id = :u_id");
        $stmt_cart->execute([':u_id' => $_SESSION['user_id']]);
        $cart_count = $stmt_cart->fetchColumn() ?: 0;
    } catch (Exception $e) {}
}

// ડેટાબેઝમાંથી બધી કેટેગરીઝ લાવો (સર્ચ ડ્રોપડાઉન માટે)
try {
    $categories = $pdo->query("SELECT * FROM categories ORDER BY c_id ASC")->fetchAll();
} catch (Exception $e) {
    $categories = [];
}
?>

<div class="bg-black text-white text-center py-2 text-xs font-bold tracking-[0.1em] uppercase">
    <span class="text-brand-gold">FREE SHIPPING</span> ON ALL ORDERS ABOVE ₹5,000 | 100% GENUINE INSTRUMENTS
</div>

<header class="bg-brand-dark text-white border-b border-white/10 sticky top-0 z-50 shadow-md">
    <div class="max-w-[1400px] mx-auto px-6 py-4 flex items-center justify-between gap-8">
        
        <a href="/index.php" class="shrink-0 flex flex-col">
            <h1 class="text-3xl font-black font-serif text-white tracking-tight">RajaRam <span class="text-brand-gold">&</span> Sons</h1>
            <p class="text-[9px] tracking-[0.3em] font-bold text-gray-400 uppercase mt-[-4px]"> Music Mall</p>
        </a>

        <div class="flex-1 max-w-3xl hidden md:block relative">
            <form action="/customer/search.php" method="GET" class="flex items-center bg-white rounded-md overflow-hidden h-11 focus-within:ring-2 focus-within:ring-brand-gold relative z-50">
                <select id="frontSearchCategory" name="category" class="bg-gray-100 text-gray-600 h-full px-3 text-sm border-r border-gray-300 outline-none cursor-pointer">
                    <option value="all">All Categories</option>
                    <?php foreach($categories as $cat): ?>
                        <option value="<?php echo $cat['c_id']; ?>"><?php echo htmlspecialchars($cat['category_name']); ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="text" id="frontSearchInput" name="q" autocomplete="off" placeholder="Search for Guitars, Keyboards, Tabla..." class="flex-1 h-full px-4 text-gray-800 text-sm outline-none">
                <button type="submit" class="bg-brand-gold hover:bg-brand-hover text-brand-dark h-full px-6 transition-colors flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </button>
            </form>
            <div id="frontSearchResults" class="absolute top-full mt-1 left-0 w-full bg-white border border-slate-200 rounded-b-xl shadow-2xl hidden overflow-hidden z-[100]"></div>
        </div>

        <div class="flex items-center gap-6 shrink-0">
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="/customer/user_dashboard.php" class="flex items-center gap-2 hover:text-brand-gold transition-colors font-bold text-sm">
                    <span class="bg-brand-gold text-brand-dark w-8 h-8 rounded-full flex items-center justify-center"><?php echo strtoupper(substr($_SESSION['user_name'] ?? 'U', 0, 1)); ?></span>
                    <span class="hidden sm:inline"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Account'); ?></span>
                </a>
            <?php else: ?>
                <a href="/auth/login.php" class="hidden sm:flex flex-col items-start hover:text-brand-gold transition-colors">
                    <span class="text-[10px] text-gray-400 font-bold uppercase">Welcome</span>
                    <span class="text-sm font-bold leading-tight">Login / Register</span>
                </a>
            <?php endif; ?>
            
            <a href="/customer/cart.php" class="flex items-center gap-2 hover:text-brand-gold transition-colors relative">
                <div class="relative">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    <span class="absolute -top-1 -right-2 bg-brand-gold text-brand-dark w-5 h-5 rounded-full flex items-center justify-center text-[11px] font-black border-2 border-brand-dark"><?php echo $cart_count; ?></span>
                </div>
                <span class="text-sm font-bold hidden lg:block">Cart</span>
            </a>
        </div>
    </div>
</header>