<?php
require_once 'includes/auth.php';
requireLogin();

// Get current page
$page = $_GET['page'] ?? 'home';
$allowedPages = ['home', 'videos', 'settings', 'profile', 'logout', 'ads'];

if (!in_array($page, $allowedPages)) {
    $page = 'home';
}

// Handle logout
if ($page === 'logout') {
    logout();
}

// Get site settings
$siteName = getSetting('site_name');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - <?php echo htmlspecialchars($siteName); ?></title>
    <link rel="stylesheet" href="../../assets/css/admin.css">
</head>
<body>
    <div class="dashboard">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2><?php echo htmlspecialchars($siteName); ?></h2>
                <p>Admin Panel</p>
            </div>
            <ul class="sidebar-menu">
                <li>
                    <a href="dashboard.php?page=home" class="<?php echo $page === 'home' ? 'active' : ''; ?>">
                        📊 Dashboard
                    </a>
                </li>
                <li>
                    <a href="dashboard.php?page=videos" class="<?php echo $page === 'videos' ? 'active' : ''; ?>">
                        🎥 Videos
                    </a>
                </li>
                <li>
                    <a href="dashboard.php?page=ads" class="<?php echo $page === 'ads' ? 'active' : ''; ?>">
                        📢 Ads
                    </a>
                </li>
                <li>
                    <a href="dashboard.php?page=settings" class="<?php echo $page === 'settings' ? 'active' : ''; ?>">
                        ⚙️ Settings
                    </a>
                </li>
                <li>
                    <a href="dashboard.php?page=profile" class="<?php echo $page === 'profile' ? 'active' : ''; ?>">
                        👤 Profile
                    </a>
                </li>
                <li>
                    <a href="dashboard.php?page=logout">
                        🚪 Logout
                    </a>
                </li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <?php
            // Include the appropriate page
            $pageFile = "pages/{$page}.php";
            if (file_exists($pageFile)) {
                include $pageFile;
            } else {
                include 'pages/home.php';
            }
            ?>
        </main>
    </div>

    <script src="../../assets/js/admin.js"></script>
</body>
</html>
