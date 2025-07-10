<?php
require_once 'web/crotah/includes/config.php';

// Get site settings
$siteName = getSetting('site_name');
$seoTitle = getSetting('seo_title');
$seoDescription = getSetting('seo_description');
$seoKeywords = getSetting('seo_keywords');
$baseUrl = getSetting('base_url');
$telegramUrl = getSetting('telegram_url');

// Get popular videos (views > 100,000)
$stmt = $pdo->prepare("SELECT * FROM videos WHERE views > 100000 ORDER BY views DESC");
$stmt->execute();
$popularVideos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Popular Videos - <?php echo htmlspecialchars($siteName); ?></title>
    <meta name="description" content="Most popular and trending videos on <?php echo htmlspecialchars($siteName); ?>">
    <meta name="keywords" content="popular videos, trending, most viewed, <?php echo htmlspecialchars($seoKeywords); ?>">
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
                    <a href="jav-list.php" class="nav-link">JAV List</a>
                    <a href="genres.php" class="nav-link">Genres</a>
                    <a href="popular.php" class="nav-link active">Popular</a>
                </nav>
                <div class="header-actions">
                    <?php if ($telegramUrl): ?>
                        <a href="<?php echo htmlspecialchars($telegramUrl); ?>" target="_blank" class="telegram-btn">
                            📱 Join Telegram
                        </a>
                    <?php endif; ?>
                    <div class="search-box">
                        <input type="text" placeholder="search popular videos" class="search-input" id="searchInput">
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main">
        <div class="container">
            <section class="popular-section">
                <h1>Popular Videos</h1>
                <p class="section-description">Most watched videos with over 100,000 views (<?php echo count($popularVideos); ?> videos)</p>
                
                <?php if (empty($popularVideos)): ?>
                    <div class="no-content">
                        <h3>No popular videos yet!</h3>
                        <p>Videos will appear here once they reach 100,000+ views.</p>
                        <p>Keep watching and sharing to help videos reach popular status!</p>
                        <a href="index.php" class="btn-primary">Browse All Videos</a>
                    </div>
                <?php else: ?>
                    <div class="video-grid" id="videoGrid">
                        <?php foreach ($popularVideos as $video): ?>
                            <div class="video-card popular-card" 
                                 data-title="<?php echo htmlspecialchars(strtolower($video['title'])); ?>"
                                 onclick="window.location.href='index.php?video_id=<?php echo $video['id']; ?>'">
                                <div class="video-thumbnail">
                                    <?php if ($video['thumbnail']): ?>
                                        <img src="<?php echo htmlspecialchars($video['thumbnail']); ?>" alt="<?php echo htmlspecialchars($video['title']); ?>">
                                    <?php else: ?>
                                        <div class="no-thumbnail">No Image</div>
                                    <?php endif; ?>
                                    <div class="play-button">▶</div>
                                    <div class="popular-badge">🔥 Popular</div>
                                </div>
                                <div class="video-details">
                                    <h3 class="video-title"><?php echo htmlspecialchars($video['title']); ?></h3>
                                    <p class="video-meta">
                                        <span class="views popular-views"><?php echo number_format($video['views']); ?> Views</span>
                                        <span class="date"><?php echo date('M d, Y', strtotime($video['created_at'])); ?></span>
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
        </div>
    </main>

    <script>
        // Search functionality for popular videos
        document.getElementById('searchInput').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const cards = document.querySelectorAll('.video-card');
            
            cards.forEach(card => {
                const title = card.getAttribute('data-title');
                if (title.includes(searchTerm)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>
