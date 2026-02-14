<?php
// setup.php
session_start();

echo "<h2>Personal Profile Website Setup</h2>";

// ------------------------
// 1. Environment checks
// ------------------------
if (version_compare(PHP_VERSION, '7.4.0') < 0) {
    die("<p style='color:red;'>Error: PHP 7.4 or higher is required. Your version: " . PHP_VERSION . "</p>");
}

if (!extension_loaded('pdo_mysql')) {
    die("<p style='color:red;'>Error: PDO MySQL extension is not enabled.</p>");
}

// ------------------------
// 2. Database configuration
// ------------------------
$config = [
    'host' => 'localhost',
    'root_user' => 'root',
    'root_pass' => '', // default XAMPP root password
    'dbname' => 'personal_profile_db',
    'app_user' => 'profile_app',
    'app_pass' => 'SecurePassword123!'
];

try {
    // Connect as root
    $pdo = new PDO(
        "mysql:host={$config['host']}",
        $config['root_user'],
        $config['root_pass']
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✓ Connected to MySQL successfully<br>";

    // ------------------------
    // 3. Create database
    // ------------------------
    $pdo->exec("CREATE DATABASE IF NOT EXISTS {$config['dbname']} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✓ Database '{$config['dbname']}' created or already exists<br>";

    // Use the database
    $pdo->exec("USE {$config['dbname']}");

    // ------------------------
    // 4. Import schema
    // ------------------------
    $sqlFile = __DIR__ . '/database_schema.sql';
    if (!file_exists($sqlFile)) {
        die("<p style='color:red;'>Error: database_schema.sql file not found in setup directory.</p>");
    }

    $sql = file_get_contents($sqlFile);
    $pdo->exec($sql);
    echo "✓ Database schema imported successfully<br>";

    // ------------------------
    // 5. Create application user
    // ------------------------
    $pdo->exec("CREATE USER IF NOT EXISTS '{$config['app_user']}'@'localhost' IDENTIFIED BY '{$config['app_pass']}'");
    $pdo->exec("GRANT SELECT, INSERT, UPDATE, DELETE, EXECUTE ON {$config['dbname']}.* TO '{$config['app_user']}'@'localhost'");
    $pdo->exec("FLUSH PRIVILEGES");
    echo "✓ Application user '{$config['app_user']}' created successfully<br>";

    // ------------------------
    // 6. Setup complete
    // ------------------------
    echo "<h3 style='color:green;'>Setup Complete!</h3>";
    echo "<p>You can now:</p>";
    echo "<ul>";
    echo "<li>Access your website at <a href='index.html'>index.html</a></li>";
    echo "<li>Login to admin panel at <a href='admin/dashboard.php'>admin/dashboard.php</a></li>";
    echo "<li>Username: admin</li>";
    echo "<li>Password: Admin123!</li>";
    echo "</ul>";
    echo "<p><strong>Important:</strong> For security, delete or rename <code>setup.php</code> after running it.</p>";

} catch (PDOException $e) {
    die("<p style='color:red;'>Setup failed: " . $e->getMessage() . "</p>");
}
?>
