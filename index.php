<?php
require_once '../lib/Database.php';

// Simple authentication (in a real app, use proper authentication)
if ($_POST['username'] === 'admin' && $_POST['password'] === 'admin') {
    $_SESSION['authenticated'] = true;
}

if (!$_SESSION['authenticated']) {
    header('Location: login.php');
    exit;
}
require_once 'php/security-headers.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Green Hydrogen GIS</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        .admin-container {
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .admin-nav {
            background: #2c3e50;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .admin-nav a {
            color: white;
            text-decoration: none;
            margin-right: 15px;
            padding: 8px 15px;
            border-radius: 4px;
            background: #3498db;
        }
        
        .admin-nav a:hover {
            background: #2980b9;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .data-table th, .data-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        
        .data-table th {
            background-color: #f2f2f2;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .form-group input, .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <h1>Green Hydrogen GIS Admin Panel</h1>
        
        <div class="admin-nav">
            <a href="?page=dashboard">Dashboard</a>
            <a href="?page=plants">Hydrogen Plants</a>
            <a href="?page=renewables">Renewable Sources</a>
            <a href="?page=infrastructure">Infrastructure</a>
            <a href="?page=logout" style="float: right; background: #e74c3c;">Logout</a>
        </div>
        
        <div class="admin-content">
            <?php
            $page = $_GET['page'] ?? 'dashboard';
            
            switch ($page) {
                case 'plants':
                    include 'manage-plants.php';
                    break;
                case 'renewables':
                    include 'manage-renewables.php';
                    break;
                case 'infrastructure':
                    include 'manage-infrastructure.php';
                    break;
                case 'logout':
                    session_destroy();
                    header('Location: login.php');
                    exit;
                case 'dashboard':
                default:
                    echo '<h2>Dashboard</h2>';
                    echo '<p>Welcome to the Green Hydrogen GIS Admin Panel. Use the navigation above to manage different aspects of the system.</p>';
                    
                    // Display stats
                    $db = Database::getInstance();
                    $plantsCount = $db->fetchOne("SELECT COUNT(*) as count FROM hydrogen_plants")['count'];
                    $renewablesCount = $db->fetchOne("SELECT COUNT(*) as count FROM renewable_sources")['count'];
                    $infrastructureCount = $db->fetchOne("SELECT COUNT(*) as count FROM infrastructure")['count'];
                    
                    echo '<div style="display: flex; gap: 20px; margin-top: 20px;">';
                    echo '<div style="flex: 1; background: #ecf0f1; padding: 15px; border-radius: 8px;">';
                    echo '<h3>Hydrogen Plants</h3>';
                    echo '<p style="font-size: 24px; font-weight: bold;">' . $plantsCount . '</p>';
                    echo '</div>';
                    
                    echo '<div style="flex: 1; background: #ecf0f1; padding: 15px; border-radius: 8px;">';
                    echo '<h3>Renewable Sources</h3>';
                    echo '<p style="font-size: 24px; font-weight: bold;">' . $renewablesCount . '</p>';
                    echo '</div>';
                    
                    echo '<div style="flex: 1; background: #ecf0f1; padding: 15px; border-radius: 8px;">';
                    echo '<h3>Infrastructure</h3>';
                    echo '<p style="font-size: 24px; font-weight: bold;">' . $infrastructureCount . '</p>';
                    echo '</div>';
                    echo '</div>';
                    break;
            }
            ?>
        </div>
    </div>
</body>
</html>