<?php
// manage_products.php
session_start();
require_once '../includes/db_connect.php';

// જો એડમિન લોગ-ઈન ન હોય તો અટકાવો
if (!isset($_SESSION['admin_id'])) {
    header("Location: /auth/admin_login.php");
    exit;
}

$success_msg = "";
$error_msg = "";

// --- પ્રોડક્ટ ડિલીટ કરવાનું સિક્યોર લોજીક (પાસવર્ડ સાથે) ---
// FIXED: Changed 'delete_product' to 'delete_pid' because JS form.submit() doesn't send the button name
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_pid'])) {
    $delete_pid = $_POST['delete_pid'];
    $input_password = trim($_POST['admin_password']);
    $admin_id = $_SESSION['admin_id'];

    try {
        // 1. Fetch real password from database
        $stmt_admin = $pdo->prepare("SELECT password FROM admins WHERE admin_id = :admin_id"); 
        $stmt_admin->execute([':admin_id' => $admin_id]);
        $real_password = $stmt_admin->fetchColumn();

        // FIXED: 2. Actually verify the password before deleting!
        // This checks both secure hashed passwords AND plain text passwords just in case.
        if (password_verify($input_password, $real_password) || $input_password === $real_password) {
            
            // Password is correct, proceed with fetching image and deleting
            $stmt_img = $pdo->prepare("SELECT image FROM products WHERE pid = :pid");
            $stmt_img->execute([':pid' => $delete_pid]);
            $img = $stmt_img->fetchColumn();

            $stmt_del = $pdo->prepare("DELETE FROM products WHERE pid = :pid");
            $stmt_del->execute([':pid' => $delete_pid]);

            if ($stmt_del->rowCount() > 0) {
                // Delete the image file if it exists
                if ($img && file_exists(__DIR__ . "/../uploads/" . $img)) {
                    unlink(__DIR__ . "/../uploads/" . $img);
                }
                $success_msg = "Product deleted successfully!";
            } else {
                $error_msg = "Product not found or already deleted.";
            }

        } else {
            // Password was wrong
            $error_msg = "Incorrect Admin Password! Product was not deleted.";
        }
         
    } catch (PDOException $e) {
        // Foreign key constraint (product already used in an order/cart/wishlist) => error code 23000
        if ($e->getCode() == '23000') {
            $error_msg = "Cannot delete this product because it is linked to existing orders, carts, or wishlists. Please set its stock to 0 to hide it instead.";
        } else {
            $error_msg = "Delete failed: " . $e->getMessage();
        }
        error_log("Product delete failed (pid={$delete_pid}): " . $e->getMessage());
    }
}

// બધી પ્રોડક્ટ્સ લાવો
try {
    $sql = "SELECT p.*, c.category_name 
            FROM products p 
            LEFT JOIN categories c ON p.catid = c.c_id 
            ORDER BY p.pid DESC";
    $products = $pdo->query($sql)->fetchAll();
} catch (PDOException $e) {
    die("Error fetching products: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Products - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-slate-50 flex h-screen overflow-hidden text-slate-800">

    <?php include '../includes/sidebar.php'; ?>

    <main class="flex-1 flex flex-col h-screen overflow-y-auto bg-gradient-to-br from-[#0A192F] via-[#162A4A] to-[#B7915F] relative z-10">
        
        <?php include '../includes/admin_topbar.php'; ?>

            
        <div class="p-8 max-w-7xl mx-auto w-full">
            
            <div class="bg-white/95 backdrop-blur-md p-8 rounded-3xl shadow-[0_20px_50px_rgba(0,0,0,0.3)] border border-white/40">
                
                <div class="flex flex-col md:flex-row md:justify-between md:items-end mb-8 gap-4 border-b border-slate-200 pb-4">
                    <div>
                        <h2 class="text-3xl font-black text-[#0A192F] tracking-tight">Manage Inventory</h2>
                        <p class="text-sm text-slate-500 mt-1 font-medium">View, edit, or securely remove your musical instruments.</p>
                    </div>
                    <div class="flex gap-4">
                        <a href="add_product.php" class="bg-[#B7915F] text-white px-5 py-2.5 rounded-lg font-bold text-sm hover:bg-[#96764a] transition-all shadow-sm flex items-center gap-2 w-max">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg> Add New Product
                        </a>
                    </div>
                </div>

                <div class="mb-6 relative w-full md:w-1/2 lg:w-1/3">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" id="productSearch" placeholder="Filter by product name or ID..." class="w-full pl-10 pr-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-[#B7915F] outline-none bg-slate-50 focus:bg-white transition-colors text-sm font-medium text-slate-700 shadow-sm">
                </div>

                <?php if(!empty($success_msg)): ?>
                    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 p-4 rounded-xl font-bold mb-6 flex items-center gap-2 shadow-sm">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <?php echo $success_msg; ?>
                    </div>
                <?php endif; ?>

                <?php if(!empty($error_msg)): ?>
                    <div class="bg-red-50 border border-red-200 text-red-600 p-4 rounded-xl font-bold mb-6 flex items-start gap-2 text-sm shadow-sm">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        <span><?php echo $error_msg; ?></span>
                    </div>
                <?php endif; ?>

                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                    <div class="overflow-x-auto rounded-xl border border-slate-200">
                        <table class="w-full text-left border-collapse" id="productsTable">
                            <thead>
                                <tr class="bg-[#0A192F] text-xs uppercase font-bold text-white tracking-wider">
                                    <th class="p-4 w-16 text-center border-b border-slate-300">ID</th>
                                    <th class="p-4 border-b border-slate-300">Instrument Info</th>
                                    <th class="p-4 text-center border-b border-slate-300">Stock</th>
                                    <th class="p-4 text-right border-b border-slate-300">Price (₹)</th>
                                    <th class="p-4 text-center border-b border-slate-300">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-sm">
                                <?php if(empty($products)): ?>
                                    <tr id="noProductsRow"><td colspan="5" class="p-8 text-center text-slate-500 font-bold">No products found. Add some instruments first!</td></tr>
                                <?php else: ?>
                                    <?php foreach($products as $p): ?>
                                    <tr class="hover:bg-slate-50/50 transition-colors product-row">
                                        
                                        <td class="p-4 text-center font-bold text-slate-500 align-middle product-id">
                                            #<?php echo str_pad($p['pid'], 3, '0', STR_PAD_LEFT); ?>
                                        </td>

                                        <td class="p-4 align-middle">
                                            <div class="flex items-center gap-4">
                                                <div class="w-16 h-16 bg-slate-100 rounded-lg overflow-hidden border border-slate-200 shrink-0 p-1 shadow-sm flex items-center justify-center">
                                                    <?php if(!empty($p['image'])): ?>
                                                        <img src="/uploads/<?php echo htmlspecialchars($p['image']); ?>" class="w-full h-full object-cover rounded">
                                                    <?php else: ?>
                                                        <div class="text-slate-300 flex items-center justify-center w-full h-full">
                                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M14 8h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <div>
                                                    <p class="font-extrabold text-slate-800 text-base product-name"><?php echo htmlspecialchars($p['product_name']); ?></p>
                                                    <p class="text-[10px] font-bold text-slate-500 mt-1 bg-slate-100 inline-block px-2 py-0.5 rounded uppercase tracking-wider border border-slate-200"><?php echo htmlspecialchars($p['category_name'] ?? 'Uncategorized'); ?></p>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="p-4 text-center align-middle">
                                            <?php if($p['stock_quantity'] > 5): ?>
                                                <span class="bg-emerald-50 text-emerald-700 border border-emerald-200 px-3 py-1 rounded-lg text-xs font-bold shadow-sm"><?php echo $p['stock_quantity']; ?> In Stock</span>
                                            <?php elseif($p['stock_quantity'] > 0): ?>
                                                <span class="bg-amber-50 text-amber-700 border border-amber-200 px-3 py-1 rounded-lg text-xs font-bold shadow-sm animate-pulse">Only <?php echo $p['stock_quantity']; ?> Left!</span>
                                            <?php else: ?>
                                                <span class="bg-red-50 text-red-700 border border-red-200 px-3 py-1 rounded-lg text-xs font-bold shadow-sm">Out of Stock</span>
                                            <?php endif; ?>
                                        </td>

                                        <td class="p-4 text-right align-middle">
                                            <p class="font-black text-[#162A4A] text-lg">₹<?php echo number_format($p['price'], 2); ?></p>
                                        </td>

                                        <td class="p-4 text-center space-y-2 align-middle w-32">
                                            <a href="edit_product.php?id=<?php echo $p['pid']; ?>" class="w-full text-center bg-blue-50 text-blue-600 border border-blue-200 px-3 py-1.5 rounded text-xs font-bold hover:bg-blue-600 hover:text-white transition-colors shadow-sm flex items-center justify-center gap-1.5">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                Edit
                                            </a>
                                            <form action="manage_products.php" method="POST" onsubmit="return confirmDeleteWithPassword(event, this);">
                                                <input type="hidden" name="delete_pid" value="<?php echo $p['pid']; ?>">
                                                <input type="hidden" name="admin_password" class="admin-pwd-input" value="">
                                                <button type="submit" class="w-full bg-red-50 text-red-600 border border-red-200 px-3 py-1.5 rounded text-xs font-bold hover:bg-red-600 hover:text-white transition-colors shadow-sm mb-2 flex items-center justify-center gap-1.5">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    Delete
                                                </button>
                                            </form>
                                            <a href="print_barcode.php?id=<?php echo $p['pid']; ?>" target="_blank" class="w-full text-center bg-slate-100 text-slate-700 border border-slate-300 px-3 py-1.5 rounded text-xs font-bold hover:bg-slate-200 hover:text-slate-900 transition-colors shadow-sm flex items-center justify-center gap-1.5">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a1 1 0 001-1v-4a1 1 0 00-1-1H9a1 1 0 00-1 1v4a1 1 0 001 1zM17 9V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4"></path></svg>
                                                Barcode
                                            </a>
                                        </td>

                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                <tr id="noSearchResults" class="hidden">
                                    <td colspan="5" class="p-8 text-center text-slate-500 font-bold text-sm">No matching products found.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div> </div> </main>

    <script>
        // Password verification for delete
        function confirmDeleteWithPassword(e, form) {
            e.preventDefault(); 
            Swal.fire({
                title: 'Security Verification',
                text: "Please enter your Admin Password to delete this product.",
                input: 'password',
                inputAttributes: { autocapitalize: 'off', autocorrect: 'off', required: 'true', placeholder: 'Enter your password' },
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Verify & Delete',
                preConfirm: (password) => {
                    if (!password) { Swal.showValidationMessage('Password is required!'); }
                    return password;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.querySelector('.admin-pwd-input').value = result.value;
                    form.submit();
                }
            });
        }

        // Live Search Logic for Products
        document.getElementById('productSearch').addEventListener('keyup', function() {
            let filter = this.value.toLowerCase();
            let rows = document.querySelectorAll('.product-row');
            let hasResults = false;

            rows.forEach(row => {
                let name = row.querySelector('.product-name').textContent.toLowerCase();
                let pid = row.querySelector('.product-id').textContent.toLowerCase();
                
                if (name.includes(filter) || pid.includes(filter)) {
                    row.style.display = '';
                    hasResults = true;
                } else {
                    row.style.display = 'none';
                }
            });

            let noProductsFound = document.getElementById('noProductsRow'); 
            let noSearchResults = document.getElementById('noSearchResults'); 
            
            if(noProductsFound && noProductsFound.style.display !== 'none') {
                return; 
            }

            if(hasResults) {
                noSearchResults.classList.add('hidden');
            } else {
                noSearchResults.classList.remove('hidden');
            }
        });
    </script>
</body>
</html>
