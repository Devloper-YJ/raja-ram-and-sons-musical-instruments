<?php
// login.php
session_start();
require_once '../includes/db_connect.php';

$swal_icon = ""; $swal_title = ""; $swal_text = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email_id = $_POST['email_id'];
    $password = $_POST['password'];

    try {
        // ઈમેલ આઈડી પ્રમાણે યુઝર શોધો
        $sql = "SELECT * FROM users WHERE email_id = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':email' => $email_id]);
        $user = $stmt->fetch();

        // જો યુઝર મળે અને પાસવર્ડ મેચ થાય
        if ($user && password_verify($password, $user['password'])) {
            
            // સેમ સેશનમાં યુઝર ડેટા સ્ટોર કરો
            $_SESSION['user_id'] = $user['u_id'];
            $_SESSION['user_name'] = $user['u_name'];
            
            $swal_icon = "success";
            $swal_title = "Welcome Back!";
            $swal_text = "Logged in successfully. Redirecting to store...";
            $redirect = true;
        } else {
            $swal_icon = "error";
            $swal_title = "Login Failed";
            $swal_text = "Invalid email or password. Please try again.";
        }
    } catch (PDOException $e) {
        $swal_icon = "error";
        $swal_title = "Error";
        $swal_text = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - RajaRam & Sons</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-[#0A192F] text-slate-800 font-sans min-h-screen flex items-center justify-center p-6 bg-cover bg-center" style="background-image: linear-gradient(rgba(10, 25, 74, 0.85), rgba(10, 25, 74, 0.85)), url('https://images.unsplash.com/photo-1514320291840-2e0a9bf2a9ae?auto=format&fit=crop&w=1920&q=80');">

    <div class="max-w-md w-full bg-white rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,0.3)] border border-slate-100 overflow-hidden">
        <div class="bg-slate-50 px-8 py-6 border-b border-slate-200 text-center">
            <h2 class="text-3xl font-extrabold font-display text-[#0A192F]">Welcome Back</h2>
            <p class="text-sm text-slate-500 mt-1">Sign in to your music mall account</p>
        </div>

        <form action="login.php" method="POST" class="p-8 space-y-6">
            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 mb-2">Email Address</label>
                <input type="email" name="email_id" required placeholder="john@example.com" class="w-full px-4 py-3 border border-slate-300 rounded-lg bg-slate-50 focus:ring-2 focus:ring-[#B7915F]/50 focus:border-[#B7915F] outline-none transition-all">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 mb-2">Password</label>
                <input type="password" name="password" required placeholder="••••••••" class="w-full px-4 py-3 border border-slate-300 rounded-lg bg-slate-50 focus:ring-2 focus:ring-[#B7915F]/50 focus:border-[#B7915F] outline-none transition-all">
            </div>

            <button type="submit" class="w-full bg-[#0A192F] text-white font-bold py-3.5 rounded-lg hover:bg-[#B7915F] transition-all shadow-lg transform hover:-translate-y-0.5 uppercase tracking-wider text-sm">
                Login
            </button>

            <p class="text-center text-sm text-slate-500 pt-2">
                Don't have an account? <a href="signup.php" class="text-[#B7915F] font-bold hover:underline">Sign up here</a>
            </p>
        </form>
    </div>

    <?php if (!empty($swal_icon)): ?>
    <script>
        Swal.fire({
            icon: '<?php echo $swal_icon; ?>',
            title: '<?php echo $swal_title; ?>',
            text: '<?php echo $swal_text; ?>',
            confirmButtonColor: '#0A192F'
        }).then(() => {
            <?php if (isset($redirect)): ?>
                window.location.href = '../index.php'; // લોગીન થયા પછી સીધા મેઈન સ્ટોર પર
            <?php endif; ?>
        });
    </script>
    <?php endif; ?>

</body>
</html>
