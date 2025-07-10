<?php
// SQLite Database configuration (no server required)
$dbPath = __DIR__ . '/../../../database/streaming.db';

// Create database connection
try {
    $pdo = new PDO("sqlite:" . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create tables if they don't exist
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS admin_users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT UNIQUE NOT NULL,
            password TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS videos (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            iframe_url TEXT NOT NULL,
            thumbnail TEXT DEFAULT NULL,
            title TEXT NOT NULL,
            genre TEXT NOT NULL,
            views INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS settings (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            setting_name TEXT UNIQUE NOT NULL,
            setting_value TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    // Insert default admin user if not exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM admin_users");
    $stmt->execute();
    if ($stmt->fetchColumn() == 0) {
        $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO admin_users (username, password) VALUES (?, ?)");
        $stmt->execute(['admin', $hashedPassword]);
    }
    
    // Insert default settings if not exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM settings");
    $stmt->execute();
    if ($stmt->fetchColumn() == 0) {
        $defaultSettings = [
            ['site_name', 'StreamSite'],
            ['telegram_url', 'https://t.me/streamsite'],
            ['base_url', 'http://localhost:8000'],
            ['seo_title', 'StreamSite - Watch Videos Online'],
            ['seo_description', 'Watch the latest videos online on StreamSite'],
            ['seo_keywords', 'streaming, videos, online, watch']
        ];
        
        $stmt = $pdo->prepare("INSERT INTO settings (setting_name, setting_value) VALUES (?, ?)");
        foreach ($defaultSettings as $setting) {
            $stmt->execute($setting);
        }
    }
    
    // Insert sample videos if not exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM videos");
    $stmt->execute();
    if ($stmt->fetchColumn() == 0) {
        $sampleVideos = [
            [
                'iframe_url' => '<IFRAME SRC="https://movearnpre.com/embed/68e7zzyb52dc" FRAMEBORDER=0 MARGINWIDTH=0 MARGINHEIGHT=0 SCROLLING=NO WIDTH=640 HEIGHT=360 allowfullscreen></IFRAME>',
                'thumbnail' => 'https://via.placeholder.com/300x200/333/fff?text=Sample+Video+1',
                'title' => 'Sample Video 1 - Action Adventure',
                'genre' => 'Action',
                'views' => 1250
            ],
            [
                'iframe_url' => '<IFRAME SRC="https://example.com/embed/sample2" FRAMEBORDER=0 MARGINWIDTH=0 MARGINHEIGHT=0 SCROLLING=NO WIDTH=640 HEIGHT=360 allowfullscreen></IFRAME>',
                'thumbnail' => 'https://via.placeholder.com/300x200/666/fff?text=Sample+Video+2',
                'title' => 'Sample Video 2 - Comedy Special',
                'genre' => 'Comedy',
                'views' => 890
            ],
            [
                'iframe_url' => '<IFRAME SRC="https://example.com/embed/sample3" FRAMEBORDER=0 MARGINWIDTH=0 MARGINHEIGHT=0 SCROLLING=NO WIDTH=640 HEIGHT=360 allowfullscreen></IFRAME>',
                'thumbnail' => 'https://via.placeholder.com/300x200/999/fff?text=Sample+Video+3',
                'title' => 'Sample Video 3 - Drama Series Episode 1',
                'genre' => 'Drama',
                'views' => 2100
            ],
            [
                'iframe_url' => '<IFRAME SRC="https://example.com/embed/sample4" FRAMEBORDER=0 MARGINWIDTH=0 MARGINHEIGHT=0 SCROLLING=NO WIDTH=640 HEIGHT=360 allowfullscreen></IFRAME>',
                'thumbnail' => 'https://via.placeholder.com/300x200/444/fff?text=Sample+Video+4',
                'title' => 'Sample Video 4 - Horror Movie',
                'genre' => 'Horror',
                'views' => 1560
            ],
            [
                'iframe_url' => '<IFRAME SRC="https://example.com/embed/sample5" FRAMEBORDER=0 MARGINWIDTH=0 MARGINHEIGHT=0 SCROLLING=NO WIDTH=640 HEIGHT=360 allowfullscreen></IFRAME>',
                'thumbnail' => 'https://via.placeholder.com/300x200/777/fff?text=Sample+Video+5',
                'title' => 'Sample Video 5 - Romance Story',
                'genre' => 'Romance',
                'views' => 780
            ],
            [
                'iframe_url' => '<IFRAME SRC="https://example.com/embed/sample6" FRAMEBORDER=0 MARGINWIDTH=0 MARGINHEIGHT=0 SCROLLING=NO WIDTH=640 HEIGHT=360 allowfullscreen></IFRAME>',
                'thumbnail' => 'https://via.placeholder.com/300x200/222/fff?text=Sample+Video+6',
                'title' => 'Sample Video 6 - Sci-Fi Adventure',
                'genre' => 'Sci-Fi',
                'views' => 3200
            ]
        ];
        
        $stmt = $pdo->prepare("INSERT INTO videos (iframe_url, thumbnail, title, genre, views) VALUES (?, ?, ?, ?, ?)");
        foreach ($sampleVideos as $video) {
            $stmt->execute([
                $video['iframe_url'],
                $video['thumbnail'],
                $video['title'],
                $video['genre'],
                $video['views']
            ]);
        }
    }
    
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Start session
session_start();

// Site settings
function getSetting($setting_name) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_name = ?");
    $stmt->execute([$setting_name]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['setting_value'] : '';
}

// Update setting
function updateSetting($setting_name, $setting_value) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_name = ?");
    return $stmt->execute([$setting_value, $setting_name]);
}
?>
