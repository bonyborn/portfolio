<?php
// admin/dashboard.php

session_start();
require_once '../backend/config/database.php';

/*
|--------------------------------------------------------------------------
| LOGOUT HANDLER
|--------------------------------------------------------------------------
*/
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: dashboard.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| DATABASE CONNECTION
|--------------------------------------------------------------------------
*/
try {
    $database = new Database();
    $db = $database->getConnection();
} catch (Exception $e) {
    die("Database connection failed.");
}

/*
|--------------------------------------------------------------------------
| LOGIN HANDLER (DATABASE-BASED)
|--------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {

    $stmt = $db->prepare("SELECT id, username, password_hash FROM users WHERE username = ?");
    $stmt->execute([$_POST['username']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($_POST['password'], $user['password_hash'])) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_user_id'] = $user['id'];
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Invalid username or password";
    }
}

/*
|--------------------------------------------------------------------------
| AUTH CHECK
|--------------------------------------------------------------------------
*/
if (!isset($_SESSION['admin_logged_in'])):
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <style>
        body { font-family: Arial; background: #f5f5f5; }
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        input, button { width: 100%; padding: 10px; margin-top: 10px; }
        button { background: #3498db; color: white; border: none; }
        .error { color: red; text-align: center; }
    </style>
</head>
<body>
<div class="login-container">
    <h2>Admin Login</h2>

    <?php if (isset($error)): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button name="login">Login</button>
    </form>
</div>
</body>
</html>
<?php
exit;
endif;

/*
|--------------------------------------------------------------------------
| DASHBOARD DATA
|--------------------------------------------------------------------------
*/
$stats = [
    'total_registrations' => $db->query("SELECT COUNT(*) FROM mentoring_registrations")->fetchColumn(),
    'pending_registrations' => $db->query("SELECT COUNT(*) FROM mentoring_registrations WHERE status = 'pending'")->fetchColumn(),
    'unread_messages' => $db->query("SELECT COUNT(*) FROM contact_messages WHERE is_read = FALSE")->fetchColumn(),
    'total_projects' => $db->query("SELECT COUNT(*) FROM portfolio_projects")->fetchColumn(),
];

$recentRegistrations = $db->query(
    "SELECT * FROM mentoring_registrations ORDER BY application_date DESC LIMIT 10"
)->fetchAll(PDO::FETCH_ASSOC);

$recentMessages = $db->query(
    "SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 10"
)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    body { margin:0; font-family:Segoe UI; background:#f5f5f5; }
    .sidebar {
        width:250px; height:100vh; background:#2c3e50;
        color:white; position:fixed;
    }
    .sidebar a {
        color:white; text-decoration:none; display:block;
        padding:15px 20px;
    }
    .sidebar a:hover { background:#34495e; }
    .main { margin-left:250px; padding:20px; }
    .card {
        background:white; padding:20px; border-radius:5px;
        margin-bottom:20px;
    }
    .grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:20px; }
    table { width:100%; border-collapse:collapse; }
    th,td { padding:10px; border-bottom:1px solid #ddd; }
    th { background:#ecf0f1; }
    .badge { padding:5px 10px; border-radius:20px; color:white; font-size:0.8em; }
    .pending { background:#f39c12; }
    .accepted { background:#27ae60; }
    .btn { padding:5px 10px; background:#3498db; color:white; border:none; border-radius:3px; }
</style>
</head>

<body>

<div class="sidebar">
    <h2 style="padding:20px;"><i class="fas fa-user-shield"></i> Admin</h2>
    <a href="#">Dashboard</a>
    <a href="#">Registrations</a>
    <a href="#">Messages</a>
    <a href="#">Portfolio</a>
    <a href="?logout=1">Logout</a>
</div>

<div class="main">
    <h1>Dashboard Overview</h1>

    <div class="grid">
        <div class="card">Total Registrations<br><h2><?= $stats['total_registrations'] ?></h2></div>
        <div class="card">Pending<br><h2><?= $stats['pending_registrations'] ?></h2></div>
        <div class="card">Unread Messages<br><h2><?= $stats['unread_messages'] ?></h2></div>
        <div class="card">Projects<br><h2><?= $stats['total_projects'] ?></h2></div>
    </div>

    <div class="card">
        <h2>Recent Registrations</h2>
        <table>
            <tr><th>Name</th><th>Email</th><th>Status</th><th>Date</th></tr>
            <?php foreach ($recentRegistrations as $r): ?>
            <tr>
                <td><?= htmlspecialchars($r['full_name']) ?></td>
                <td><?= htmlspecialchars($r['email']) ?></td>
                <td>
                    <span class="badge <?= $r['status'] ?>">
                        <?= ucfirst($r['status']) ?>
                    </span>
                </td>
                <td><?= date('M d, Y', strtotime($r['application_date'])) ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <div class="card">
        <h2>Recent Messages</h2>
        <table>
            <tr><th>Name</th><th>Email</th><th>Subject</th></tr>
            <?php foreach ($recentMessages as $m): ?>
            <tr>
                <td><?= htmlspecialchars($m['name']) ?></td>
                <td><?= htmlspecialchars($m['email']) ?></td>
                <td><?= htmlspecialchars($m['subject']) ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>

</body>
</html>
