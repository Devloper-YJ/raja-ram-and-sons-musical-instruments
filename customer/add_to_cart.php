<?php
// add_to_cart.php
session_start();
require_once '../includes/db_connect.php';

// ૧. જો યુઝર લોગ-ઈન ન હોય, તો તેને પહેલા લોગીન કરવા માટે પોપ-અપ બતાવો
if (!isset($_SESSION['user_id'])) {
    echo "
    <!DOCTYPE html>
    <html>
    <head>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    </head>
    <body>
    <script>
        Swal.fire({
            icon: 'warning',
            title: 'Please Login First',
            text: 'You need to login to your account to add items to the cart.',
            confirmButtonColor: '#0A192F',
            confirmButtonText: 'Go to Login'
        }).then(() => {
            window.location.href = 'login.php';
        });
    </script>
    </body>
    </html>";
    exit;
}

// ૨. URL માંથી પ્રોડક્ટ આઈડી મેળવો
$product_id = isset($_GET['pid']) ? $_GET['pid'] : 0;
$user_id = $_SESSION['user_id'];
$quantity = 1; // ડિફોલ્ટ એક આઈટમ ઉમેરાશે

if ($product_id > 0) {
    try {
        // ૩. ચેક કરો કે આ પ્રોડક્ટ આ યુઝરે કાર્ટમાં પહેલેથી ઉમેરેલી છે કે નહીં 
        // (સુધારો: u_id ની જગ્યાએ user_id અને pid ની જગ્યાએ product_id કર્યું છે)
        $check_sql = "SELECT * FROM carts WHERE user_id = :u_id AND product_id = :pid";
        $check_stmt = $pdo->prepare($check_sql);
        $check_stmt->execute([':u_id' => $user_id, ':pid' => $product_id]);
        $cart_item = $check_stmt->fetch();

        if ($cart_item) {
            // જો પહેલેથી હોય, તો ફક્ત ક્વોન્ટિટી +1 કરી દો
            $update_sql = "UPDATE carts SET quantity = quantity + 1 WHERE cart_id = :cart_id";
            $update_stmt = $pdo->prepare($update_sql);
            $update_stmt->execute([':cart_id' => $cart_item['cart_id']]);
        } else {
            // જો પહેલેથી ના હોય, તો નવી લાઈન એડ કરો 
            // (સુધારો: u_id ની જગ્યાએ user_id અને pid ની જગ્યાએ product_id કર્યું છે)
            $insert_sql = "INSERT INTO carts (user_id, product_id, quantity) VALUES (:u_id, :pid, :quantity)";
            $insert_stmt = $pdo->prepare($insert_sql);
            $insert_stmt->execute([
                ':u_id' => $user_id,
                ':pid' => $product_id,
                ':quantity' => $quantity
            ]);
        }

        // સફળતાનો SweetAlert મેસેજ
        echo "
        <!DOCTYPE html>
        <html>
        <head>
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        </head>
        <body>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Added to Cart!',
                text: 'The instrument has been added to your shopping cart.',
                showCancelButton: true,
                confirmButtonColor: '#0A192F',
                cancelButtonColor: '#B7915F',
                confirmButtonText: 'View Cart',
                cancelButtonText: 'Continue Shopping'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'cart.php'; 
                } else {
                    window.location.href = 'index.php'; 
                }
            });
        </script>
        </body>
        </html>";

    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
} else {
    header("Location: /index.php");
    exit;
}
?>