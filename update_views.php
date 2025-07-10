<?php
require_once 'web/crotah/includes/config.php';

echo "<h2>Updating video views for Popular section...</h2>";

try {
    // Update some videos to have views over 1500
    $updates = [
        [1, 2500],  // Sample Video 1
        [3, 3200],  // Sample Video 3
        [6, 1800],  // Sample Video 6
        [2, 1650],  // Sample Video 2
        [4, 2100],  // Sample Video 4
    ];
    
    foreach ($updates as $update) {
        $stmt = $pdo->prepare("UPDATE videos SET views = ? WHERE id = ?");
        $stmt->execute([$update[1], $update[0]]);
        echo "<p>✓ Updated video ID {$update[0]} to {$update[1]} views</p>";
    }
    
    echo "<h3>Update Complete!</h3>";
    echo "<p>Popular videos now have over 1,500 views and will appear in the Popular section.</p>";
    echo "<p><a href='popular.php'>View Popular Page</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>

<style>
body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
h2, h3 { color: #333; }
p { margin: 10px 0; }
a { color: #007bff; text-decoration: none; }
a:hover { text-decoration: underline; }
</style>
