<?php
require_once 'web/crotah/includes/config.php';

echo "<h2>Setting up Streaming Website...</h2>";

try {
    // Check if admin user exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM admin_users");
    $stmt->execute();
    $adminCount = $stmt->fetchColumn();
    
    if ($adminCount == 0) {
        // Create default admin user
        $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO admin_users (username, password) VALUES (?, ?)");
        $stmt->execute(['admin', $hashedPassword]);
        echo "<p>✓ Default admin user created (username: admin, password: admin123)</p>";
    } else {
        echo "<p>✓ Admin user already exists</p>";
    }
    
    // Check if settings exist
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM settings");
    $stmt->execute();
    $settingsCount = $stmt->fetchColumn();
    
    if ($settingsCount == 0) {
        // Insert default settings
        $defaultSettings = [
            ['site_name', 'StreamSite'],
            ['telegram_url', 'https://t.me/streamsite'],
            ['base_url', 'http://localhost'],
            ['seo_title', 'StreamSite - Watch Videos Online'],
            ['seo_description', 'Watch the latest videos online on StreamSite'],
            ['seo_keywords', 'streaming, videos, online, watch']
        ];
        
        $stmt = $pdo->prepare("INSERT INTO settings (setting_name, setting_value) VALUES (?, ?)");
        foreach ($defaultSettings as $setting) {
            $stmt->execute($setting);
        }
        echo "<p>✓ Default settings created</p>";
    } else {
        echo "<p>✓ Settings already exist</p>";
    }
    
    // Add sample videos
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM videos");
    $stmt->execute();
    $videoCount = $stmt->fetchColumn();
    
    if ($videoCount == 0) {
        $sampleVideos = [
            [
                'iframe_url' => '<IFRAME SRC="https://movearnpre.com/embed/68e7zzyb52dc" FRAMEBORDER=0 MARGINWIDTH=0 MARGINHEIGHT=0 SCROLLING=NO WIDTH=640 HEIGHT=360 allowfullscreen></IFRAME>',
                'thumbnail' => 'https://via.placeholder.com/300x200/333/fff?text=Sample+Video+1',
                'title' => 'Sample Video 1 - Action Adventure',
                'genre' => 'Action'
            ],
            [
                'iframe_url' => '<IFRAME SRC="https://example.com/embed/sample2" FRAMEBORDER=0 MARGINWIDTH=0 MARGINHEIGHT=0 SCROLLING=NO WIDTH=640 HEIGHT=360 allowfullscreen></IFRAME>',
                'thumbnail' => 'https://via.placeholder.com/300x200/666/fff?text=Sample+Video+2',
                'title' => 'Sample Video 2 - Comedy Special',
                'genre' => 'Comedy'
            ],
            [
                'iframe_url' => '<IFRAME SRC="https://example.com/embed/sample3" FRAMEBORDER=0 MARGINWIDTH=0 MARGINHEIGHT=0 SCROLLING=NO WIDTH=640 HEIGHT=360 allowfullscreen></IFRAME>',
                'thumbnail' => 'https://via.placeholder.com/300x200/999/fff?text=Sample+Video+3',
                'title' => 'Sample Video 3 - Drama Series Episode 1',
                'genre' => 'Drama'
            ],
            [
                'iframe_url' => '<IFRAME SRC="https://example.com/embed/sample4" FRAMEBORDER=0 MARGINWIDTH=0 MARGINHEIGHT=0 SCROLLING=NO WIDTH=640 HEIGHT=360 allowfullscreen></IFRAME>',
                'thumbnail' => 'https://via.placeholder.com/300x200/444/fff?text=Sample+Video+4',
                'title' => 'Sample Video 4 - Horror Movie',
                'genre' => 'Horror'
            ],
            [
                'iframe_url' => '<IFRAME SRC="https://example.com/embed/sample5" FRAMEBORDER=0 MARGINWIDTH=0 MARGINHEIGHT=0 SCROLLING=NO WIDTH=640 HEIGHT=360 allowfullscreen></IFRAME>',
                'thumbnail' => 'https://via.placeholder.com/300x200/777/fff?text=Sample+Video+5',
                'title' => 'Sample Video 5 - Romance Story',
                'genre' => 'Romance'
            ],
            [
                'iframe_url' => '<IFRAME SRC="https://example.com/embed/sample6" FRAMEBORDER=0 MARGINWIDTH=0 MARGINHEIGHT=0 SCROLLING=NO WIDTH=640 HEIGHT=360 allowfullscreen></IFRAME>',
                'thumbnail' => 'https://via.placeholder.com/300x200/222/fff?text=Sample+Video+6',
                'title' => 'Sample Video 6 - Sci-Fi Adventure',
                'genre' => 'Sci-Fi'
            ]
        ];
        
        $stmt = $pdo->prepare("INSERT INTO videos (iframe_url, thumbnail, title, genre, views) VALUES (?, ?, ?, ?, ?)");
        foreach ($sampleVideos as $index => $video) {
            $views = rand(100, 5000); // Random view count
            $stmt->execute([
                $video['iframe_url'],
                $video['thumbnail'],
                $video['title'],
                $video['genre'],
                $views
            ]);
        }
        echo "<p>✓ Sample videos created</p>";
    } else {
        echo "<p>✓ Videos already exist</p>";
    }
    
    echo "<h3>Setup Complete!</h3>";
    echo "<p><strong>Frontend:</strong> <a href='index.php' target='_blank'>View Website</a></p>";
    echo "<p><strong>Admin Panel:</strong> <a href='web/crotah/index.php' target='_blank'>Admin Login</a></p>";
    echo "<p><strong>Login Credentials:</strong> username: <code>admin</code>, password: <code>admin123</code></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    echo "<p>Make sure your database is properly configured in web/crotah/includes/config.php</p>";
}
?>

<style>
body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
h2, h3 { color: #333; }
p { margin: 10px 0; }
code { background: #f4f4f4; padding: 2px 5px; border-radius: 3px; }
a { color: #007bff; text-decoration: none; }
a:hover { text-decoration: underline; }
</style>
