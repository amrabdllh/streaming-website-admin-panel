<?php
// Get statistics
$stmt = $pdo->prepare("SELECT COUNT(*) as total_videos FROM videos");
$stmt->execute();
$totalVideos = $stmt->fetch(PDO::FETCH_ASSOC)['total_videos'];

$stmt = $pdo->prepare("SELECT SUM(views) as total_views FROM videos");
$stmt->execute();
$totalViews = $stmt->fetch(PDO::FETCH_ASSOC)['total_views'] ?? 0;

$stmt = $pdo->prepare("SELECT COUNT(DISTINCT genre) as total_genres FROM videos");
$stmt->execute();
$totalGenres = $stmt->fetch(PDO::FETCH_ASSOC)['total_genres'];

// Get most viewed videos
$stmt = $pdo->prepare("SELECT * FROM videos ORDER BY views DESC LIMIT 10");
$stmt->execute();
$popularVideos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get recent videos
$stmt = $pdo->prepare("SELECT * FROM videos ORDER BY created_at DESC LIMIT 5");
$stmt->execute();
$recentVideos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="page-header">
    <h1>Dashboard</h1>
    <p>Welcome back, <?php echo htmlspecialchars($_SESSION['admin_username']); ?>!</p>
</div>

<!-- Statistics Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <h3><?php echo number_format($totalVideos); ?></h3>
        <p>Total Videos</p>
    </div>
    <div class="stat-card">
        <h3><?php echo number_format($totalViews); ?></h3>
        <p>Total Views</p>
    </div>
    <div class="stat-card">
        <h3><?php echo number_format($totalGenres); ?></h3>
        <p>Total Genres</p>
    </div>
    <div class="stat-card">
        <h3><?php echo $totalVideos > 0 ? number_format($totalViews / $totalVideos, 1) : '0'; ?></h3>
        <p>Avg Views per Video</p>
    </div>
</div>

<div class="form-row">
    <!-- Most Popular Videos -->
    <div class="form-col">
        <div class="content-box">
            <h2>Most Popular Videos</h2>
            <?php if (empty($popularVideos)): ?>
                <p>No videos found. <a href="dashboard.php?page=videos">Add some videos</a> to see statistics.</p>
            <?php else: ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Genre</th>
                            <th>Views</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($popularVideos as $video): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars(substr($video['title'], 0, 50)); ?><?php echo strlen($video['title']) > 50 ? '...' : ''; ?></strong>
                                </td>
                                <td><?php echo htmlspecialchars($video['genre']); ?></td>
                                <td><?php echo number_format($video['views']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <!-- Recent Videos -->
    <div class="form-col">
        <div class="content-box">
            <h2>Recent Videos</h2>
            <?php if (empty($recentVideos)): ?>
                <p>No videos found. <a href="dashboard.php?page=videos">Add your first video</a>.</p>
            <?php else: ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Genre</th>
                            <th>Added</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentVideos as $video): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars(substr($video['title'], 0, 40)); ?><?php echo strlen($video['title']) > 40 ? '...' : ''; ?></strong>
                                </td>
                                <td><?php echo htmlspecialchars($video['genre']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($video['created_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="content-box">
    <h2>Quick Actions</h2>
    <div class="form-row">
        <div class="form-col">
            <a href="dashboard.php?page=videos" class="btn btn-primary">Add New Video</a>
        </div>
        <div class="form-col">
            <a href="dashboard.php?page=settings" class="btn btn-success">Manage Settings</a>
        </div>
        <div class="form-col">
            <a href="../../index.php" target="_blank" class="btn btn-warning">View Website</a>
        </div>
        <div class="form-col">
            <a href="dashboard.php?page=profile" class="btn btn-primary">Update Profile</a>
        </div>
    </div>
</div>
