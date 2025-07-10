<?php
require_once 'web/crotah/includes/config.php';

// Get site settings
$siteName = getSetting('site_name');
$seoTitle = getSetting('seo_title');
$seoDescription = getSetting('seo_description');
$seoKeywords = getSetting('seo_keywords');
$baseUrl = getSetting('base_url');
$telegramUrl = getSetting('telegram_url');

// Get genres for filter - split comma-separated genres
$stmt = $pdo->prepare("SELECT DISTINCT genre FROM videos ORDER BY genre");
$stmt->execute();
$genreData = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Split comma-separated genres and create unique list with video counts
$genres = [];
foreach ($genreData as $genreString) {
    $splitGenres = array_map('trim', explode(',', $genreString));
    foreach ($splitGenres as $genre) {
        if (!empty($genre) && !in_array($genre, $genres)) {
            $genres[] = $genre;
        }
    }
}
sort($genres, SORT_NATURAL | SORT_FLAG_CASE);

// Get video count for each genre
$genreStats = [];
foreach ($genres as $genre) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM videos WHERE genre LIKE ?");
    $stmt->execute(['%' . $genre . '%']);
    $count = $stmt->fetchColumn();
    $genreStats[$genre] = $count;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Genres - <?php echo htmlspecialchars($siteName); ?></title>
    <meta name="description" content="Browse videos by genre on <?php echo htmlspecialchars($siteName); ?>" />
    <meta name="keywords" content="genres, categories, video types, <?php echo htmlspecialchars($seoKeywords); ?>" />
    <link rel="stylesheet" href="assets/css/style.css" />
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
                    <a href="genres.php" class="nav-link active">Genres</a>
                    <a href="popular.php" class="nav-link">Popular</a>
                </nav>
                <div class="header-actions">
                    <?php if ($telegramUrl): ?>
                        <a href="<?php echo htmlspecialchars($telegramUrl); ?>" target="_blank" class="telegram-btn">
                            📱 Join Telegram
                        </a>
                    <?php endif; ?>
                    <div class="search-box">
                        <input type="text" placeholder="search genres" class="search-input" id="searchInput">
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main">
        <div class="container">
            <section class="jav-list-section">
                <h1>Video Genres</h1>
                <p class="section-description">Browse videos by category (<?php echo count($genres); ?> genres available)</p>
                
                <div class="jav-list-container" id="genresList">
                    <?php foreach ($genres as $genre): ?>
                        <div class="jav-list-item" data-genre="<?php echo htmlspecialchars(strtolower($genre)); ?>">
                            <a href="index.php?genre=<?php echo urlencode($genre); ?>" class="jav-title-link">
                                <h3 class="jav-title"><?php echo htmlspecialchars($genre); ?></h3>
                                <div class="jav-meta">
                                    <span class="views"><?php echo $genreStats[$genre]; ?> videos</span>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        </div>
    </main>

    <script>
        // Search functionality for genres list
        document.getElementById('searchInput').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const items = document.querySelectorAll('.jav-list-item');
            
            items.forEach(item => {
                const genre = item.getAttribute('data-genre');
                if (genre.includes(searchTerm)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>
