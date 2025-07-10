<?php
require_once 'web/crotah/includes/config.php';

// Get site settings
$siteName = getSetting('site_name');
$seoTitle = getSetting('seo_title');
$seoDescription = getSetting('seo_description');
$seoKeywords = getSetting('seo_keywords');
$baseUrl = getSetting('base_url');

// Get videos from database
$stmt = $pdo->prepare("SELECT * FROM videos ORDER BY created_at DESC");
$stmt->execute();
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
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($seoTitle); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($seoDescription); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars($seoKeywords); ?>">
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
                    <a href="index.php" class="nav-link active">Home</a>
                    <a href="#" class="nav-link">Subbed</a>
                    <a href="#" class="nav-link">JAV List</a>
                    <a href="#" class="nav-link">Genres</a>
                    <a href="#" class="nav-link">Popular</a>
                    <a href="#" class="nav-link">Pornstars</a>
                    <a href="#" class="nav-link">Random</a>
                </nav>
                <div class="search-box">
                    <input type="text" placeholder="search" class="search-input" id="searchInput">
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
                                // Display genres as separate tags
                                $videoGenres = array_map('trim', explode(',', $currentVideo['genre']));
                                foreach ($videoGenres as $genre) {
                                    if (!empty($genre)) {
                                        echo '<span class="genre-tag">' . htmlspecialchars($genre) . '</span> ';
                                    }
                                }
                                ?>
                            </span>
                            <span class="views"><?php echo number_format($currentVideo['views']); ?> views</span>
                        </p>
                    </div>
                </div>
                <a href="index.php" class="back-link">← Back to Home</a>
            <?php else: ?>
                <!-- Genre Filter -->
                <div class="filter-section">
                    <h3>Filter by Genre:</h3>
                    <div class="genre-filters">
                        <button class="genre-btn active" data-genre="all">All</button>
                        <?php foreach ($genres as $genre): ?>
                            <button class="genre-btn" data-genre="<?php echo htmlspecialchars($genre); ?>">
                                <?php echo htmlspecialchars($genre); ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Latest Releases -->
                <section class="latest-releases">
                    <h2>LATEST RELEASES</h2>
                    <div class="video-grid" id="videoGrid">
                        <?php foreach ($videos as $video): ?>
                            <div class="video-card" 
                                 data-genre="<?php echo htmlspecialchars($video['genre']); ?>" 
                                 data-title="<?php echo htmlspecialchars(strtolower($video['title'])); ?>"
                                 data-genres="<?php echo htmlspecialchars(strtolower(str_replace(',', '|', $video['genre']))); ?>">
                                <div class="video-thumbnail">
                                    <?php if ($video['thumbnail']): ?>
                                        <img src="<?php echo htmlspecialchars($video['thumbnail']); ?>" alt="<?php echo htmlspecialchars($video['title']); ?>">
                                    <?php else: ?>
                                        <div class="no-thumbnail">No Image</div>
                                    <?php endif; ?>
                                    <div class="play-button" onclick="window.location.href='index.php?video_id=<?php echo $video['id']; ?>'">▶</div>
                                </div>
                                <div class="video-details">
                                    <h3 class="video-title"><?php echo htmlspecialchars($video['title']); ?></h3>
                                    <p class="video-meta">
                                        <span class="views"><?php echo number_format($video['views']); ?> Views</span>
                                        <span class="date"><?php echo date('M d, Y', strtotime($video['created_at'])); ?></span>
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>
        </div>
    </main>

    <script src="assets/js/main.js"></script>
</body>
</html>
