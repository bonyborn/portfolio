<?php
// admin/dashboard.php

session_start();
require_once '../backend/config/database.php';

// Simple authentication (in production, use proper authentication)
$valid_username = 'admin';
$valid_password_hash = password_hash('Admin123!', PASSWORD_DEFAULT);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    if ($_POST['username'] === $valid_username && password_verify($_POST['password'], $valid_password_hash)) {
        $_SESSION['admin_logged_in'] = true;
    } else {
        $error = "Invalid credentials";
    }
}

if (!isset($_SESSION['admin_logged_in'])) {
    // Show login form
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin Login</title>
        <style>
            body { font-family: Arial, sans-serif; background: #f5f5f5; }
            .login-container { max-width: 400px; margin: 100px auto; padding: 20px; background: white; border-radius: 5px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
            h2 { text-align: center; color: #333; }
            .form-group { margin-bottom: 15px; }
            label { display: block; margin-bottom: 5px; }
            input { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px; }
            button { width: 100%; padding: 10px; background: #3498db; color: white; border: none; border-radius: 3px; cursor: pointer; }
            .error { color: red; text-align: center; }
        </style>
    </head>
    <body>
        <div class="login-container">
            <h2>Admin Login</h2>
            <?php if (isset($error)): ?>
                <p class="error"><?php echo $error; ?></p>
            <?php endif; ?>
            <form method="POST">
                <div class="form-group">
                    <label>Username:</label>
                    <input type="text" name="username" required>
                </div>
                <div class="form-group">
                    <label>Password:</label>
                    <input type="password" name="password" required>
                </div>
                <button type="submit" name="login">Login</button>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// User is logged in, show dashboard
$database = new Database();
$db = $database->getConnection();

// Get statistics
$stats = [
    'total_registrations' => $db->query("SELECT COUNT(*) FROM mentoring_registrations")->fetchColumn(),
    'pending_registrations' => $db->query("SELECT COUNT(*) FROM mentoring_registrations WHERE status = 'pending'")->fetchColumn(),
    'unread_messages' => $db->query("SELECT COUNT(*) FROM contact_messages WHERE is_read = FALSE")->fetchColumn(),
    'total_projects' => $db->query("SELECT COUNT(*) FROM portfolio_projects")->fetchColumn(),
];

// Get recent registrations
$recentRegistrations = $db->query("SELECT * FROM mentoring_registrations ORDER BY application_date DESC LIMIT 10")->fetchAll();

// Get recent messages
$recentMessages = $db->query("SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 10")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #3498db;
            --light: #ecf0f1;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f5f5; }
        .sidebar { width: 250px; background: var(--primary); color: white; height: 100vh; position: fixed; }
        .sidebar-header { padding: 20px; background: rgba(0,0,0,0.2); }
        .sidebar-menu { list-style: none; }
        .sidebar-menu li { border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar-menu a { color: white; text-decoration: none; padding: 15px 20px; display: block; }
        .sidebar-menu a:hover { background: rgba(255,255,255,0.1); }
        .sidebar-menu a.active { background: var(--secondary); }
        .main-content { margin-left: 250px; padding: 20px; }
        .header { background: white; padding: 20px; border-radius: 5px; margin-bottom: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 20px; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .stat-card i { font-size: 2rem; color: var(--secondary); margin-bottom: 10px; }
        .stat-card h3 { font-size: 2rem; margin: 10px 0; }
        .stat-card p { color: #666; }
        .table-container { background: white; border-radius: 5px; padding: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: var(--light); font-weight: 600; }
        tr:hover { background: #f9f9f9; }
        .badge { padding: 5px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; }
        .badge-pending { background: #f39c12; color: white; }
        .badge-accepted { background: #27ae60; color: white; }
        .btn { padding: 8px 15px; background: var(--secondary); color: white; border: none; border-radius: 3px; cursor: pointer; }
        .btn-sm { padding: 5px 10px; font-size: 0.9rem; }
        .btn-success { background: #27ae60; }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <h2><i class="fas fa-user-shield"></i> Admin Panel</h2>
        </div>
        <ul class="sidebar-menu">
            <li><a href="?page=dashboard" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="?page=registrations"><i class="fas fa-users"></i> Registrations</a></li>
            <li><a href="?page=messages"><i class="fas fa-envelope"></i> Messages</a></li>
            <li><a href="?page=portfolio"><i class="fas fa-briefcase"></i> Portfolio</a></li>
            <li><a href="?page=settings"><i class="fas fa-cog"></i> Settings</a></li>
            <li><a href="?logout=1"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>
    
    <div class="main-content">
        <div class="header">
            <h1>Dashboard Overview</h1>
            <p>Welcome back, Admin!</p>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <i class="fas fa-user-graduate"></i>
                <h3><?php echo $stats['total_registrations']; ?></h3>
                <p>Total Registrations</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-clock"></i>
                <h3><?php echo $stats['pending_registrations']; ?></h3>
                <p>Pending Applications</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-envelope"></i>
                <h3><?php echo $stats['unread_messages']; ?></h3>
                <p>Unread Messages</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-briefcase"></i>
                <h3><?php echo $stats['total_projects']; ?></h3>
                <p>Portfolio Projects</p>
            </div>
        </div>
        
        <div class="table-container">
            <h2>Recent Registrations</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Experience</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($recentRegistrations as $registration): ?>
                    <tr>
                        <td>#<?php echo $registration['id']; ?></td>
                        <td><?php echo htmlspecialchars($registration['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($registration['email']); ?></td>
                        <td><?php echo ucfirst($registration['experience_level']); ?></td>
                        <td>
                            <span class="badge badge-<?php echo $registration['status']; ?>">
                                <?php echo ucfirst($registration['status']); ?>
                            </span>
                        </td>
                        <td><?php echo date('M d, Y', strtotime($registration['application_date'])); ?></td>
                        <td>
                            <button class="btn btn-sm">View</button>
                            <button class="btn btn-sm btn-success">Accept</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div class="table-container">
            <h2>Recent Messages</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Subject</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($recentMessages as $message): ?>
                    <tr>
                        <td>#<?php echo $message['id']; ?></td>
                        <td><?php echo htmlspecialchars($message['name']); ?></td>
                        <td><?php echo htmlspecialchars($message['email']); ?></td>
                        <td><?php echo htmlspecialchars(substr($message['subject'], 0, 50)); ?>...</td>
                        <td><?php echo date('M d, Y', strtotime($message['created_at'])); ?></td>
                        <td>
                            <?php if($message['is_read']): ?>
                                <span class="badge" style="background: #27ae60;">Read</span>
                            <?php else: ?>
                                <span class="badge" style="background: #e74c3c;">Unread</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <button class="btn btn-sm">View</button>
                            <button class="btn btn-sm">Mark as Read</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <script>
        // Handle logout
        document.querySelector('a[href="?logout=1"]').addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to logout?')) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>