<?php
$error = '';
$success = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $iframe_url = trim($_POST['iframe_url'] ?? '');
                $thumbnail = trim($_POST['thumbnail'] ?? '');
                $title = trim($_POST['title'] ?? '');
                $genre = trim($_POST['genre'] ?? '');
                
                if (empty($iframe_url) || empty($title) || empty($genre)) {
                    $error = 'Please fill in all required fields.';
                } else {
                    try {
                        $stmt = $pdo->prepare("INSERT INTO videos (iframe_url, thumbnail, title, genre) VALUES (?, ?, ?, ?)");
                        $stmt->execute([$iframe_url, $thumbnail, $title, $genre]);
                        $success = 'Video added successfully!';
                    } catch (PDOException $e) {
                        $error = 'Error adding video: ' . $e->getMessage();
                    }
                }
                break;
                
            case 'edit':
                $id = $_POST['id'] ?? 0;
                $iframe_url = trim($_POST['iframe_url'] ?? '');
                $thumbnail = trim($_POST['thumbnail'] ?? '');
                $title = trim($_POST['title'] ?? '');
                $genre = trim($_POST['genre'] ?? '');
                
                if (empty($iframe_url) || empty($title) || empty($genre)) {
                    $error = 'Please fill in all required fields.';
                } else {
                    try {
                        $stmt = $pdo->prepare("UPDATE videos SET iframe_url = ?, thumbnail = ?, title = ?, genre = ? WHERE id = ?");
                        $stmt->execute([$iframe_url, $thumbnail, $title, $genre, $id]);
                        $success = 'Video updated successfully!';
                    } catch (PDOException $e) {
                        $error = 'Error updating video: ' . $e->getMessage();
                    }
                }
                break;
                
            case 'delete':
                $id = $_POST['id'] ?? 0;
                try {
                    $stmt = $pdo->prepare("DELETE FROM videos WHERE id = ?");
                    $stmt->execute([$id]);
                    $success = 'Video deleted successfully!';
                } catch (PDOException $e) {
                    $error = 'Error deleting video: ' . $e->getMessage();
                }
                break;
        }
    }
}

// Get all videos
$stmt = $pdo->prepare("SELECT * FROM videos ORDER BY created_at DESC");
$stmt->execute();
$videos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get video for editing
$editVideo = null;
if (isset($_GET['edit'])) {
    $editId = $_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM videos WHERE id = ?");
    $stmt->execute([$editId]);
    $editVideo = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<div class="page-header">
    <h1>Video Management</h1>
    <p>Add, edit, and manage your videos</p>
</div>

<?php if ($error): ?>
    <div class="alert alert-error">
        <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="alert alert-success">
        <?php echo htmlspecialchars($success); ?>
    </div>
<?php endif; ?>

<!-- Add/Edit Video Form -->
<div class="content-box">
    <h2><?php echo $editVideo ? 'Edit Video' : 'Add New Video'; ?></h2>
    <form method="POST">
        <input type="hidden" name="action" value="<?php echo $editVideo ? 'edit' : 'add'; ?>">
        <?php if ($editVideo): ?>
            <input type="hidden" name="id" value="<?php echo $editVideo['id']; ?>">
        <?php endif; ?>
        
        <div class="form-group">
            <label for="iframe_url">Iframe Embed Code *</label>
            <textarea id="iframe_url" name="iframe_url" rows="3" required placeholder='<IFRAME SRC="LINK EMBED" FRAMEBORDER=0 MARGINWIDTH=0 MARGINHEIGHT=0 SCROLLING=NO WIDTH=640 HEIGHT=360 allowfullscreen></IFRAME>'><?php echo htmlspecialchars($editVideo['iframe_url'] ?? ''); ?></textarea>
        </div>
        
        <div class="form-row">
            <div class="form-col">
                <div class="form-group">
                    <label for="title">Video Title *</label>
                    <input type="text" id="title" name="title" required 
                           value="<?php echo htmlspecialchars($editVideo['title'] ?? ''); ?>">
                </div>
            </div>
            <div class="form-col">
                <div class="form-group">
                    <label for="genre">Genre *</label>
                    <input type="text" id="genre" name="genre" required 
                           value="<?php echo htmlspecialchars($editVideo['genre'] ?? ''); ?>"
                           placeholder="e.g., Action, Comedy, Drama">
                </div>
            </div>
        </div>
        
        <div class="form-group">
            <label for="thumbnail">Thumbnail URL (Optional)</label>
            <input type="url" id="thumbnail" name="thumbnail" 
                   value="<?php echo htmlspecialchars($editVideo['thumbnail'] ?? ''); ?>"
                   placeholder="https://example.com/image.jpg">
        </div>
        
        <div class="form-row">
            <div class="form-col">
                <button type="submit" class="btn btn-primary">
                    <?php echo $editVideo ? 'Update Video' : 'Add Video'; ?>
                </button>
            </div>
            <?php if ($editVideo): ?>
                <div class="form-col">
                    <a href="dashboard.php?page=videos" class="btn btn-warning">Cancel Edit</a>
                </div>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Videos List -->
<div class="content-box">
    <h2>All Videos (<?php echo count($videos); ?>)</h2>
    
    <?php if (empty($videos)): ?>
        <p>No videos found. Add your first video using the form above.</p>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Genre</th>
                    <th>Views</th>
                    <th>Added</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($videos as $video): ?>
                    <tr>
                        <td><?php echo $video['id']; ?></td>
                        <td>
                            <strong><?php echo htmlspecialchars(substr($video['title'], 0, 50)); ?></strong>
                            <?php if (strlen($video['title']) > 50): ?>
                                <br><small><?php echo htmlspecialchars(substr($video['title'], 50)); ?></small>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($video['genre']); ?></td>
                        <td><?php echo number_format($video['views']); ?></td>
                        <td><?php echo date('M d, Y', strtotime($video['created_at'])); ?></td>
                        <td>
                            <a href="dashboard.php?page=videos&edit=<?php echo $video['id']; ?>" 
                               class="btn btn-warning" style="margin-right: 5px;">Edit</a>
                            <form method="POST" style="display: inline;" 
                                  onsubmit="return confirm('Are you sure you want to delete this video?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo $video['id']; ?>">
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                            <a href="../../index.php?video_id=<?php echo $video['id']; ?>" 
                               target="_blank" class="btn btn-success" style="margin-left: 5px;">View</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
