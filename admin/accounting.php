<?php
// accounting.php
session_start();
require_once '../includes/db_connect.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: /auth/admin_login.php");
    exit;
}

try {
    // ૧. કુલ ઓફલાઈન (POS) આવક
    $offline_rev = $pdo->query("SELECT SUM(sum_total) FROM offline_sales")->fetchColumn() ?: 0;

    // ૨. કુલ ઓનલાઈન આવક (ફક્ત Delivered થયેલા ઓર્ડરની જ આવક ગણાશે)
    $online_rev = $pdo->query("SELECT SUM(total_price) FROM orders WHERE LOWER(status) = 'delivered'")->fetchColumn() ?: 0;

    // ૩. ગ્રાન્ડ ટોટલ (હાથમાં આવેલી રકમ)
    $total_collected = $offline_rev + $online_rev;

    // ૪. COD બાકી રકમ (Pending COD) - જે ઓર્ડર હજુ પહોંચ્યા નથી પણ COD છે
    $pending_cod = $pdo->query("SELECT SUM(total_price) FROM orders WHERE LOWER(payment_method) = 'cod' AND LOWER(status) NOT IN ('delivered', 'cancelled')")->fetchColumn() ?: 0;

    // ૫. ઓનલાઈન પેમેન્ટ પદ્ધતિ મુજબ હિસાબ (GPay, Bank, Card વગેરે) - જે કેન્સલ નથી થયા
    $payment_stats = $pdo->query("SELECT payment_method, SUM(total_price) as total 
                                  FROM orders 
                                  WHERE LOWER(status) != 'cancelled' 
                                  GROUP BY payment_method")->fetchAll();

    // ૬. તાજેતરના ટ્રાન્ઝેક્શન (છેલ્લા ૫ ઓર્ડર/બિલ ભેગા કરીને)
    // આ માત્ર ડેમો માટે છે, તમે વધુ એડવાન્સ બનાવી શકો છો.
    
} catch (PDOException $e) {
    die("Error fetching accounting data: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Accounting - RajaRam Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-slate-50 flex h-screen overflow-hidden text-slate-800">

    <?php include '../includes/sidebar.php'; ?>

    <main class="flex-1 flex flex-col h-screen overflow-y-auto bg-gradient-to-br from-[#0A192F] via-[#162A4A] to-[#B7915F] relative z-10">
        
        <?php include '../includes/admin_topbar.php'; ?>

        <div class="p-8 max-w-7xl mx-auto w-full my-auto">
            
            <div class="mb-8">
                <h2 class="text-3xl font-black text-white tracking-tight drop-shadow-md">Financial Dashboard</h2>
                <p class="text-sm text-slate-300 mt-1 font-medium">Track your total revenue, pending COD, and payment gateways.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                
                <div class="bg-white/95 backdrop-blur-md rounded-3xl p-8 shadow-xl border-b-4 border-[#B7915F] relative overflow-hidden group">
                    <div class="absolute -right-6 -top-6 text-[#B7915F] opacity-5 group-hover:scale-110 transition-transform">
                        <svg class="w-32 h-32" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <p class="text-sm font-bold text-slate-500 uppercase tracking-widest mb-2">Total Collected Revenue</p>
                    <h3 class="text-5xl font-black text-[#0A192F] tracking-tighter">₹<?php echo number_format($total_collected, 2); ?></h3>
                    <p class="text-xs font-bold text-emerald-600 mt-4 bg-emerald-50 inline-block px-3 py-1 rounded-full">Actual amount in hand</p>
                </div>

                <div class="bg-white/95 backdrop-blur-md rounded-3xl p-8 shadow-xl border border-white/40 flex flex-col justify-center">
                    <div class="flex justify-between items-end mb-4 border-b border-slate-100 pb-4">
                        <div>
                            <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-1">Store / POS Sales</p>
                            <h4 class="text-2xl font-black text-slate-700">₹<?php echo number_format($offline_rev, 2); ?></h4>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9.5L12 3l9 6.5M4.5 10v9a1 1 0 001 1h13a1 1 0 001-1v-9M9 20v-6h6v6"></path></svg>
                        </div>
                    </div>
                    <div class="flex justify-between items-end mt-2">
                        <div>
                            <p class="text-[10px] font-extrabold text-blue-400 uppercase tracking-widest mb-1">Website Sales (Delivered)</p>
                            <h4 class="text-2xl font-black text-[#0A192F]">₹<?php echo number_format($online_rev, 2); ?></h4>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0zM3.6 9h16.8M3.6 15h16.8M12 3a15 15 0 014 9 15 15 0 01-4 9 15 15 0 01-4-9 15 15 0 014-9z"></path></svg>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-orange-500 to-red-500 rounded-3xl p-8 shadow-xl text-white relative overflow-hidden group">
                    <div class="absolute -right-4 -bottom-4 opacity-10 group-hover:-rotate-12 transition-transform">
                        <svg class="w-32 h-32" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <p class="text-sm font-bold text-orange-100 uppercase tracking-widest mb-2">Pending COD Expected</p>
                    <h3 class="text-5xl font-black tracking-tighter drop-shadow-md">₹<?php echo number_format($pending_cod, 2); ?></h3>
                    <p class="text-xs font-bold text-white mt-4 bg-black/20 inline-block px-3 py-1 rounded-full">Money yet to be collected from courier</p>
                </div>

            </div>

            <div class="bg-white/95 backdrop-blur-md rounded-3xl p-8 shadow-xl border border-white/40 mb-8">
                <h3 class="text-xl font-black text-[#0A192F] mb-6 flex items-center gap-2">
                    <svg class="w-5 h-5 text-[#B7915F]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    Online Payment Breakdown
                </h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <?php 
                    $has_online_payments = false;
                    foreach($payment_stats as $stat): 
                        if(strtolower($stat['payment_method']) == 'cod') continue; // COD ને અલગ બતાવ્યું છે
                        $has_online_payments = true;
                        
                        // આઈકન સેટ કરવા (SVG paths)
                        $icon_svg = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>'; // default card
                        if(strpos(strtolower($stat['payment_method']), 'upi') !== false || strpos(strtolower($stat['payment_method']), 'gpay') !== false) {
                            $icon_svg = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>'; // mobile/upi
                        }
                        if(strpos(strtolower($stat['payment_method']), 'bank') !== false) {
                            $icon_svg = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 21h18M4 18h16M6 18v-7m4 7v-7m4 7v-7m4 7v-7M4 10l8-6 8 6M4 10h16"></path></svg>'; // bank
                        }
                    ?>
                        <div class="bg-slate-50 border border-slate-200 rounded-2xl p-5 hover:border-[#B7915F] hover:shadow-md transition-all">
                            <div class="w-10 h-10 rounded-full bg-white shadow-sm flex items-center justify-center text-[#B7915F] mb-3 border border-slate-100"><?php echo $icon_svg; ?></div>
                            <p class="text-xs font-extrabold text-slate-500 uppercase tracking-wider mb-1"><?php echo htmlspecialchars($stat['payment_method']); ?></p>
                            <h4 class="text-2xl font-black text-[#0A192F]">₹<?php echo number_format($stat['total'], 2); ?></h4>
                        </div>
                    <?php endforeach; ?>

                    <?php if(!$has_online_payments): ?>
                        <div class="col-span-full text-center py-6 text-slate-400 font-bold">
                            No online prepayments recorded yet.
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mt-8 bg-blue-50/50 border border-blue-100 rounded-xl p-4 text-xs font-bold text-blue-800 flex items-start gap-2">
                    <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <p class="mt-0.5">Note: Offline POS Sales (₹<?php echo number_format($offline_rev, 2); ?>) are generally considered as Cash or Direct Shop UPI payments. If you want a detailed breakdown for offline sales, we can add a payment method dropdown in the Offline Billing page.</p>
                </div>
            </div>

        </div>
    </main>

</body>
</html>