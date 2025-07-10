<?php
require_once 'web/crotah/includes/config.php';

// Get site settings
$siteName = getSetting('site_name');
$seoTitle = getSetting('seo_title');
$seoDescription = getSetting('seo_description');
$seoKeywords = getSetting('seo_keywords');
$baseUrl = getSetting('base_url');
$telegramUrl = getSetting('telegram_url');

// Pagination settings
$videosPerPage = 12;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $videosPerPage;

// Get total videos count
$stmt = $pdo->prepare("SELECT COUNT(*) FROM videos");
$stmt->execute();
$totalVideos = $stmt->fetchColumn();
$totalPages = ceil($totalVideos / $videosPerPage);

// Get videos from database with pagination
$stmt = $pdo->prepare("SELECT * FROM videos ORDER BY created_at DESC LIMIT ? OFFSET ?");
$stmt->execute([$videosPerPage, $offset]);
$videos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get genres for filter - split comma-separated genres
$stmt = $pdo->prepare("SELECT DISTINCT genre FROM videos ORDER BY genre");
$stmt->execute();
$genreData = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Split comma-separated genres and create unique list
$genres = [];
foreach ($genreData as $genreString) {
    $splitGenres = array_map('trim', explode(',', $genreString));
    foreach ($splitGenres as $genre) {
        if (!empty($genre) && !in_array($genre, $genres)) {
            $genres[] = $genre;
        }
    }
}
sort($genres);

// Function to generate random views above 50000
function getRandomViews() {
    return rand(50000, 200000);
}

// Handle video view
if (isset($_GET['video_id'])) {
    $videoId = $_GET['video_id'];
    // Update view count
    $stmt = $pdo->prepare("UPDATE videos SET views = views + 1 WHERE id = ?");
    $stmt->execute([$videoId]);
    
    // Get video details
    $stmt = $pdo->prepare("SELECT * FROM videos WHERE id = ?");
    $stmt->execute([$videoId]);
    $currentVideo = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Override views with random views above 50000
    if ($currentVideo) {
        $currentVideo['views'] = getRandomViews();
    }
    
    // Get recommended videos (exclude current video)
    $stmt = $pdo->prepare("SELECT * FROM videos WHERE id != ? ORDER BY views DESC, created_at DESC LIMIT 6");
    $stmt->execute([$videoId]);
    $recommendedVideos = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Handle genre filtering
if (isset($_GET['genre'])) {
    $selectedGenre = $_GET['genre'];
    $stmt = $pdo->prepare("SELECT * FROM videos WHERE genre LIKE ? ORDER BY created_at DESC LIMIT ? OFFSET ?");
    $stmt->execute(['%' . $selectedGenre . '%', $videosPerPage, $offset]);
    $videos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get total for this genre
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM videos WHERE genre LIKE ?");
    $stmt->execute(['%' . $selectedGenre . '%']);
    $totalVideos = $stmt->fetchColumn();
    $totalPages = ceil($totalVideos / $videosPerPage);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Basic SEO Meta Tags -->
    <title><?php echo htmlspecialchars($seoTitle); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($seoDescription); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars($seoKeywords); ?>">
    <meta name="author" content="<?php echo htmlspecialchars($siteName); ?>">
    <meta name="robots" content="index, follow">
    <meta name="language" content="English">
    <meta name="revisit-after" content="1 days">
    <meta name="distribution" content="global">
    <meta name="rating" content="general">
    
    <!-- Canonical URL -->
    <link rel="canonical" href="<?php echo htmlspecialchars($baseUrl); ?>">
    
    <!-- Open Graph Meta Tags for Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?php echo htmlspecialchars($seoTitle); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($seoDescription); ?>">
    <meta property="og:image" content="<?php echo htmlspecialchars($baseUrl); ?>/assets/images/og-image.jpg">
    <meta property="og:url" content="<?php echo htmlspecialchars($baseUrl); ?>">
    <meta property="og:site_name" content="<?php echo htmlspecialchars($siteName); ?>">
    <meta property="og:locale" content="en_US">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    
    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo htmlspecialchars($seoTitle); ?>">
    <meta name="twitter:description" content="<?php echo htmlspecialchars($seoDescription); ?>">
    <meta name="twitter:image" content="<?php echo htmlspecialchars($baseUrl); ?>/assets/images/twitter-image.jpg">
    <meta name="twitter:site" content="@<?php echo htmlspecialchars($siteName); ?>">
    <meta name="twitter:creator" content="@<?php echo htmlspecialchars($siteName); ?>">
    
    <!-- Additional SEO Meta Tags -->
    <meta name="theme-color" content="#3498db">
    <meta name="msapplication-TileColor" content="#3498db">
    <meta name="application-name" content="<?php echo htmlspecialchars($siteName); ?>">
    <meta name="apple-mobile-web-app-title" content="<?php echo htmlspecialchars($siteName); ?>">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    
    <!-- Favicon and Icons -->
    <link rel="icon" type="image/x-icon" href="<?php echo htmlspecialchars($baseUrl); ?>/favicon.ico">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo htmlspecialchars($baseUrl); ?>/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo htmlspecialchars($baseUrl); ?>/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo htmlspecialchars($baseUrl); ?>/favicon-16x16.png">
    
    <!-- Structured Data (JSON-LD) -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebSite",
        "name": "<?php echo htmlspecialchars($siteName); ?>",
        "description": "<?php echo htmlspecialchars($seoDescription); ?>",
        "url": "<?php echo htmlspecialchars($baseUrl); ?>",
        "potentialAction": {
            "@type": "SearchAction",
            "target": "<?php echo htmlspecialchars($baseUrl); ?>/?search={search_term_string}",
            "query-input": "required name=search_term_string"
        },
        "publisher": {
            "@type": "Organization",
            "name": "<?php echo htmlspecialchars($siteName); ?>",
            "url": "<?php echo htmlspecialchars($baseUrl); ?>",
            "logo": {
                "@type": "ImageObject",
                "url": "<?php echo htmlspecialchars($baseUrl); ?>/assets/images/logo.png",
                "width": "600",
                "height": "60"
            }
        }
    }
    </script>
    
    <?php if (isset($currentVideo)): ?>
    <!-- Video-specific structured data -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "VideoObject",
        "name": "<?php echo htmlspecialchars($currentVideo['title']); ?>",
        "description": "<?php echo htmlspecialchars($currentVideo['title']); ?>",
        "thumbnailUrl": "<?php echo htmlspecialchars($currentVideo['thumbnail'] ?: $baseUrl . '/assets/images/default-thumb.jpg'); ?>",
        "uploadDate": "<?php echo date('c', strtotime($currentVideo['created_at'])); ?>",
        "duration": "PT10M",
        "interactionStatistic": {
            "@type": "InteractionCounter",
            "interactionType": { "@type": "WatchAction" },
            "userInteractionCount": <?php echo $currentVideo['views']; ?>
        },
        "genre": "<?php echo htmlspecialchars($currentVideo['genre']); ?>",
        "publisher": {
            "@type": "Organization",
            "name": "<?php echo htmlspecialchars($siteName); ?>",
            "logo": {
                "@type": "ImageObject",
                "url": "<?php echo htmlspecialchars($baseUrl); ?>/assets/images/logo.png"
            }
        }
    }
    </script>
    <?php endif; ?>
    
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
                    <a href="index.php" class="nav-link <?php echo !isset($_GET['page_type']) ? 'active' : ''; ?>">Home</a>
                    <a href="jav-list.php" class="nav-link">JAV List</a>
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
                        <input type="text" placeholder="search" class="search-input" id="searchInput">
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main">
        <div class="container">
            <?php if (isset($currentVideo)): ?>
                <!-- Video Player -->
                <div class="video-player-section">
                    <div class="video-player">
                        <?php echo $currentVideo['iframe_url']; ?>
                    </div>
                    <div class="video-info">
                        <h1><?php echo htmlspecialchars($currentVideo['title']); ?></h1>
                        <p class="video-meta">
                            <span class="genre">
                                <?php 
                                // Display genres as clickable tags
                                $videoGenres = array_map('trim', explode(',', $currentVideo['genre']));
                                foreach ($videoGenres as $genre) {
                                    if (!empty($genre)) {
                                        echo '<a href="index.php?genre=' . urlencode($genre) . '" class="genre-tag clickable-tag">' . htmlspecialchars($genre) . '</a> ';
                                    }
                                }
                                ?>
                            </span>
                            <span class="views"><?php echo number_format($currentVideo['views']); ?> views</span>
                        </p>
                    </div>
                </div>
                
                <!-- Recommended Videos Section -->
                <div class="recommendations-section">
                    <h2>Recommended Videos</h2>
                    <div class="video-grid">
                        <?php foreach ($recommendedVideos as $video): ?>
                            <div class="video-card" onclick="window.location.href='index.php?video_id=<?php echo $video['id']; ?>'">
                                <div class="video-thumbnail">
                                    <?php if ($video['thumbnail']): ?>
                                        <img src="<?php echo htmlspecialchars($video['thumbnail']); ?>" alt="<?php echo htmlspecialchars($video['title']); ?>">
                                    <?php else: ?>
                                        <div class="no-thumbnail">No Image</div>
                                    <?php endif; ?>
                                    <div class="play-button">▶</div>
                                </div>
                                <div class="video-details">
                                    <h3 class="video-title"><?php echo htmlspecialchars($video['title']); ?></h3>
                                    <p class="video-meta">
                                        <span class="views"><?php echo getRandomViews(); ?> Views</span>
                                        <span class="date"><?php echo date('M d, Y', strtotime($video['created_at'])); ?></span>
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <a href="index.php" class="back-link">← Back to Home</a>
            <?php else: ?>
                <!-- Latest Releases -->
                <section class="latest-releases">
                    <h2>LATEST RELEASES</h2>
                    <div class="video-grid" id="videoGrid">
                        <?php foreach ($videos as $video): ?>
                            <div class="video-card" 
                                 data-genre="<?php echo htmlspecialchars($video['genre']); ?>" 
                                 data-title="<?php echo htmlspecialchars(strtolower($video['title'])); ?>"
                                 data-genres="<?php echo htmlspecialchars(strtolower(str_replace(',', '|', $video['genre']))); ?>"
                                 onclick="window.location.href='index.php?video_id=<?php echo $video['id']; ?>'">
                                <div class="video-thumbnail">
                                    <?php if ($video['thumbnail']): ?>
                                        <img src="<?php echo htmlspecialchars($video['thumbnail']); ?>" alt="<?php echo htmlspecialchars($video['title']); ?>">
                                    <?php else: ?>
                                        <div class="no-thumbnail">No Image</div>
                                    <?php endif; ?>
                                    <div class="play-button">▶</div>
                                </div>
                                <div class="video-details">
                                    <h3 class="video-title"><?php echo htmlspecialchars($video['title']); ?></h3>
                                    <p class="video-meta">
                                        <span class="views"><?php echo getRandomViews(); ?> Views</span>
                                        <span class="date"><?php echo date('M d, Y', strtotime($video['created_at'])); ?></span>
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?php echo $page - 1; ?><?php echo isset($_GET['genre']) ? '&genre=' . urlencode($_GET['genre']) : ''; ?>" class="pagination-btn">← Previous</a>
                        <?php endif; ?>
                        
                        <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                            <a href="?page=<?php echo $i; ?><?php echo isset($_GET['genre']) ? '&genre=' . urlencode($_GET['genre']) : ''; ?>" 
                               class="pagination-btn <?php echo $i == $page ? 'active' : ''; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>
                        
                        <?php if ($page < $totalPages): ?>
                            <a href="?page=<?php echo $page + 1; ?><?php echo isset($_GET['genre']) ? '&genre=' . urlencode($_GET['genre']) : ''; ?>" class="pagination-btn">Next →</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </main>

    <script src="assets/js/main.js"></script>
</body>
</html>
