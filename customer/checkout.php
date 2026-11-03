<?php
// checkout.php
session_start();
require_once '../includes/db_connect.php';

$swal_icon = ""; $swal_title = ""; $swal_text = "";

if (!isset($_SESSION['user_id'])) {
    header("Location: /auth/login.php");
    exit;
}
$user_id = $_SESSION['user_id'];

$total_amount = 0;
try {
    $sql = "SELECT c.quantity, p.pid, p.price 
            FROM carts c 
            INNER JOIN products p ON c.product_id = p.pid 
            WHERE c.user_id = :u_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':u_id' => $user_id]);
    $cart_items = $stmt->fetchAll();

    if (empty($cart_items)) {
        header("Location: /customer/cart.php"); 
        exit;
    }

    foreach ($cart_items as $item) {
        $total_amount += ($item['price'] * $item['quantity']);
    }
} catch (PDOException $e) {
    die("Error fetching cart: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = $_POST['fullname'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $pincode = $_POST['pincode'];
    
    $full_address_text = $fullname . " | Ph: " . $phone . " | " . $address;

    try {
        $pdo->beginTransaction(); // Transaction ચાલુ કરો જેથી બધો ડેટા એકસાથે સેવ થાય

        // કાર્ટની દરેક આઈટમ માટે orders અને order_shipping_addresses ટેબલમાં એન્ટ્રી કરો
        foreach ($cart_items as $item) {
            $item_total = $item['price'] * $item['quantity'];
            
            // ૧. Orders ટેબલમાં ડેટા નાખો
            $insert_order = "INSERT INTO orders (uid, product_id, quantity, total_price, shipping_amount, payment_method, status) 
                             VALUES (:uid, :product_id, :quantity, :total_price, 0.00, 'COD', 'Pending')";
            $order_stmt = $pdo->prepare($insert_order);
            $order_stmt->execute([
                ':uid' => $user_id,
                ':product_id' => $item['pid'],
                ':quantity' => $item['quantity'],
                ':total_price' => $item_total
            ]);
            
            // હમણાં જ જે ઓર્ડર સેવ થયો તેનો ID મેળવો (order_shipping_addresses માં નાખવા માટે)
            $last_order_id = $pdo->lastInsertId();

            // ૨. Order_shipping_addresses ટેબલમાં સરનામું નાખો
            $insert_address = "INSERT INTO order_shipping_addresses (order_id, user_id, full_address, city_name, zip_code) 
                               VALUES (:order_id, :user_id, :full_address, :city_name, :zip_code)";
            $addr_stmt = $pdo->prepare($insert_address);
            $addr_stmt->execute([
                ':order_id' => $last_order_id,
                ':user_id' => $user_id,
                ':full_address' => $full_address_text,
                ':city_name' => $city,
                ':zip_code' => $pincode
            ]);
        }

        // ૩. કાર્ટ ખાલી કરો
        $clear_cart = "DELETE FROM carts WHERE user_id = :u_id";
        $clear_stmt = $pdo->prepare($clear_cart);
        $clear_stmt->execute([':u_id' => $user_id]);

        $pdo->commit(); // બધું બરાબર થાય એટલે ડેટા સેવ કરી લો

        $swal_icon = "success";
        $swal_title = "Order Placed Successfully!";
        $swal_text = "Thank you for shopping at RajaRam & Sons.";
        $redirect = true;

    } catch (PDOException $e) {
        $pdo->rollBack(); // જો કોઈ એરર આવે તો આખી પ્રોસેસ કેન્સલ કરો
        $swal_icon = "error";
        $swal_title = "Order Failed";
        $swal_text = "Error placing order: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Checkout - RajaRam & Sons</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased min-h-screen">
    <header class="bg-[#0A192F] shadow-md py-4">
        <div class="max-w-7xl mx-auto px-6 text-center">
            <h1 class="text-2xl font-extrabold font-display text-[#D4AF37] tracking-widest uppercase">Secure Checkout</h1>
        </div>
    </header>

    <section class="max-w-5xl mx-auto px-6 py-12">
        <form action="checkout.php" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-10">
            <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-200">
                <h2 class="text-xl font-bold text-[#0A192F] mb-6 border-b pb-3">Shipping Details</h2>
                <div class="space-y-5">
                    <div><label class="block text-sm font-bold text-slate-600 mb-1">Full Name</label><input type="text" name="fullname" required value="<?php echo htmlspecialchars($_SESSION['user_name']); ?>" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg"></div>
                    <div><label class="block text-sm font-bold text-slate-600 mb-1">Phone Number</label><input type="text" name="phone" required class="w-full px-4 py-2.5 border border-slate-300 rounded-lg"></div>
                    <div><label class="block text-sm font-bold text-slate-600 mb-1">Address</label><textarea name="address" rows="3" required class="w-full px-4 py-2.5 border border-slate-300 rounded-lg"></textarea></div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block text-sm font-bold text-slate-600 mb-1">City</label><input type="text" name="city" required value="Bhuj" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg"></div>
                        <div><label class="block text-sm font-bold text-slate-600 mb-1">Pincode</label><input type="text" name="pincode" required class="w-full px-4 py-2.5 border border-slate-300 rounded-lg"></div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-200">
                    <h2 class="text-xl font-bold text-[#0A192F] mb-6 border-b pb-3">Order Total</h2>
                    <div class="flex justify-between items-center mb-4 text-lg">
                        <span class="text-slate-600 font-medium">Subtotal</span>
                        <span class="font-bold text-slate-800">₹<?php echo number_format($total_amount, 2); ?></span>
                    </div>
                    <div class="flex justify-between items-center mb-6 text-lg border-b pb-6 border-slate-100">
                        <span class="text-slate-600 font-medium">Delivery</span>
                        <span class="font-bold text-green-600">FREE</span>
                    </div>
                    <div class="flex justify-between items-center text-2xl font-extrabold text-[#0A192F] mb-8">
                        <span>Total Pay</span>
                        <span class="text-[#B7915F]">₹<?php echo number_format($total_amount, 2); ?></span>
                    </div>

                    <div class="bg-blue-50 border border-blue-100 p-4 rounded-lg mb-8 flex items-center gap-3">
                        <input type="radio" checked class="w-5 h-5 text-[#0A192F]">
                        <span class="font-bold text-slate-700">Cash on Delivery (COD)</span>
                    </div>

                    <button type="submit" class="w-full bg-[#0A192F] text-white font-bold py-4 rounded-xl hover:bg-[#B7915F] uppercase tracking-wide">
                        Place Order Now
                    </button>
                </div>
            </div>
        </form>
    </section>

    <?php if (!empty($swal_icon)): ?>
    <script>
        Swal.fire({
            icon: '<?php echo $swal_icon; ?>',
            title: '<?php echo $swal_title; ?>',
            text: '<?php echo $swal_text; ?>',
            confirmButtonColor: '#0A192F'
        }).then(() => {
            <?php if (isset($redirect)): ?>
                window.location.href = 'user_dashboard.php';
            <?php endif; ?>
        });
    </script>
    <?php endif; ?>
</body>
</html>