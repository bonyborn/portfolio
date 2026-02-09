<?php
// setup.php

echo "<h2>Personal Profile Website Setup</h2>";

// Check PHP version
if (version_compare(PHP_VERSION, '7.4.0') < 0) {
    die("Error: PHP 7.4 or higher is required.");
}

// Check if PDO is available
if (!extension_loaded('pdo_mysql')) {
    die("Error: PDO MySQL extension is not enabled.");
}

// Database configuration
$config = [
    'host' => 'localhost',
    'username' => 'root',
    'password' => '',
    'dbname' => 'personal_profile_db'
];

// Connect to MySQL
try {
    $pdo = new PDO(
        "mysql:host={$config['host']}",
        $config['username'],
        $config['password']
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS {$config['dbname']}");
    echo "✓ Database created successfully<br>";
    
    // Use the database
    $pdo->exec("USE {$config['dbname']}");
    
    // Read SQL file
    $sql = file_get_contents('database_schema.sql');
    if (!$sql) {
        die("Error: Could not read database schema file.");
    }
    
    // Execute SQL
    $pdo->exec($sql);
    echo "✓ Database schema imported successfully<br>";
    
    // Create application user
    $pdo->exec("CREATE USER IF NOT EXISTS 'profile_app'@'localhost' IDENTIFIED BY 'SecurePassword123!'");
    $pdo->exec("GRANT SELECT, INSERT, UPDATE, DELETE, EXECUTE ON personal_profile_db.* TO 'profile_app'@'localhost'");
    $pdo->exec("FLUSH PRIVILEGES");
    echo "✓ Application user created successfully<br>";
    
    echo "<h3>Setup Complete!</h3>";
    echo "<p>You can now:</p>";
    echo "<ul>";
    echo "<li>Access your website at <a href='index.html'>index.html</a></li>";
    echo "<li>Login to admin panel at <a href='admin/dashboard.php'>admin/dashboard.php</a></li>";
    echo "<li>Username: admin</li>";
    echo "<li>Password: Admin123!</li>";
    echo "</ul>";
    
} catch(PDOException $e) {
    die("Setup failed: " . $e->getMessage());
}
?>