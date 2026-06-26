<?php
$host = 'db-18-database18.l.aivencloud.com';
$port = '14824';
$db   = 'defaultdb';
$user = 'avnadmin';
$pass = ''; // Maine tumhara password yahan daal diya hai

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    foreach ($tables as $name => $sql) {
        $stmt = $pdo->query("SELECT count(*) FROM information_schema.tables WHERE table_name = '$name'");
        if ($stmt->fetchColumn() == 0) {
            $pdo->exec($sql);
            echo "Created table: $name <br>";
        } else {
            echo "Table exists: $name <br>";
        }
    }
    
    // Admin check
    $pdo->exec("INSERT INTO admins (username, password, name) VALUES ('mrb', '696969', 'Meet') ON CONFLICT DO NOTHING");

} catch (PDOException $e) {
    die("ERROR: " . $e->getMessage());
}
?>
