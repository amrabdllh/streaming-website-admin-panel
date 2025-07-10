<?php
require_once 'web/crotah/includes/config.php';

// Get site settings
$siteName = getSetting('site_name');
$seoTitle = getSetting('seo_title');
$seoDescription = getSetting('seo_description');
$seoKeywords = getSetting('seo_keywords');
$baseUrl = getSetting('base_url');
$telegramUrl = getSetting('telegram_url');

// Get all video titles
$stmt = $pdo->prepare("SELECT id, title, views, created_at FROM videos ORDER BY title ASC");
$stmt->execute();
$videoTitles = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JAV List - <?php echo htmlspecialchars($siteName); ?></title>
    <meta name="description" content="Complete list of all JAV videos on <?php echo htmlspecialchars($siteName); ?>">
    <meta name="keywords" content="jav list, video titles, <?php echo htmlspecialchars($seoKeywords); ?>">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="header-content">
                <a href="<?php echo htmlspecialchars($baseUrl); ?>" class="logo">
                    <?php echo htmlspecialchars($siteName); ?>
                </a>
                <nav class="nav">
                    <a href="index.php" class="nav-link">Home</a>
                    <a href="jav-list.php" class="nav-link active">JAV List</a>
                    <a href="genres.php" class="nav-link">Genres</a>
                    <a href="popular.php" class="nav-link">Popular</a>
                </nav>
                <div class="header-actions">
                    <?php if ($telegramUrl): ?>
                        <a href="<?php echo htmlspecialchars($telegramUrl); ?>" target="_blank" class="telegram-btn">
                            📱 Join Telegram
                        </a>
                    <?php endif; ?>
                    <div class="search-box">
                        <input type="text" placeholder="search titles" class="search-input" id="searchInput">
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main">
        <div class="container">
            <section class="jav-list-section">
                <h1>JAV Video List</h1>
                <p class="section-description">Complete collection of all video titles (<?php echo count($videoTitles); ?> videos)</p>
                
                <div class="jav-list-container">
                    <?php foreach ($videoTitles as $video): ?>
                        <div class="jav-list-item" data-title="<?php echo htmlspecialchars(strtolower($video['title'])); ?>">
                            <a href="index.php?video_id=<?php echo $video['id']; ?>" class="jav-title-link">
                                <h3 class="jav-title"><?php echo htmlspecialchars($video['title']); ?></h3>
                                <div class="jav-meta">
                                    <span class="views"><?php echo number_format($video['views']); ?> views</span>
                                    <span class="date"><?php echo date('M d, Y', strtotime($video['created_at'])); ?></span>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        </div>
    </main>

    <script>
        // Search functionality for JAV list
        document.getElementById('searchInput').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const items = document.querySelectorAll('.jav-list-item');
            
            items.forEach(item => {
                const title = item.getAttribute('data-title');
                if (title.includes(searchTerm)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>
