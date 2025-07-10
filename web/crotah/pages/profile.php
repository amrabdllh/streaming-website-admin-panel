<?php
$error = '';
$success = '';

// Handle password update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'update_password') {
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            $error = 'Please fill in all fields.';
        } elseif ($newPassword !== $confirmPassword) {
            $error = 'New passwords do not match.';
        } elseif (strlen($newPassword) < 6) {
            $error = 'New password must be at least 6 characters long.';
        } else {
            // Verify current password
            $stmt = $pdo->prepare("SELECT password FROM admin_users WHERE id = ?");
            $stmt->execute([$_SESSION['admin_id']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($currentPassword, $user['password'])) {
                if (updatePassword($newPassword)) {
                    $success = 'Password updated successfully!';
                } else {
                    $error = 'Error updating password. Please try again.';
                }
            } else {
                $error = 'Current password is incorrect.';
            }
        }
    }
}

// Get user info
$stmt = $pdo->prepare("SELECT username, created_at FROM admin_users WHERE id = ?");
$stmt->execute([$_SESSION['admin_id']]);
$userInfo = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<div class="page-header">
    <h1>Profile Management</h1>
    <p>Manage your admin account settings</p>
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

<!-- User Information -->
<div class="content-box">
    <h2>Account Information</h2>
    <div class="form-row">
        <div class="form-col">
            <div class="form-group">
                <label>Username</label>
                <input type="text" value="<?php echo htmlspecialchars($userInfo['username']); ?>" disabled>
                <small>Username cannot be changed</small>
            </div>
        </div>
        <div class="form-col">
            <div class="form-group">
                <label>Account Created</label>
                <input type="text" value="<?php echo date('F d, Y', strtotime($userInfo['created_at'])); ?>" disabled>
            </div>
        </div>
    </div>
</div>

<!-- Change Password -->
<div class="content-box">
    <h2>Change Password</h2>
    <form method="POST">
        <input type="hidden" name="action" value="update_password">
        
        <div class="form-group">
            <label for="current_password">Current Password *</label>
            <input type="password" id="current_password" name="current_password" required>
        </div>
        
        <div class="form-row">
            <div class="form-col">
                <div class="form-group">
                    <label for="new_password">New Password *</label>
                    <input type="password" id="new_password" name="new_password" required minlength="6">
                    <small>Minimum 6 characters</small>
                </div>
            </div>
            <div class="form-col">
                <div class="form-group">
                    <label for="confirm_password">Confirm New Password *</label>
                    <input type="password" id="confirm_password" name="confirm_password" required minlength="6">
                </div>
            </div>
        </div>
        
        <button type="submit" class="btn btn-primary">Update Password</button>
    </form>
</div>

<!-- Security Information -->
<div class="content-box">
    <h2>Security Information</h2>
    <div class="form-row">
        <div class="form-col">
            <h4>Password Security Tips</h4>
            <ul>
                <li>Use a strong password with at least 8 characters</li>
                <li>Include uppercase and lowercase letters</li>
                <li>Include numbers and special characters</li>
                <li>Don't use common words or personal information</li>
                <li>Change your password regularly</li>
            </ul>
        </div>
        <div class="form-col">
            <h4>Account Security</h4>
            <ul>
                <li>Always log out when finished</li>
                <li>Don't share your login credentials</li>
                <li>Use a secure internet connection</li>
                <li>Keep your browser updated</li>
                <li>Clear browser cache regularly</li>
            </ul>
        </div>
    </div>
</div>

<!-- Session Information -->
<div class="content-box">
    <h2>Current Session</h2>
    <div class="form-row">
        <div class="form-col">
            <p><strong>Logged in as:</strong> <?php echo htmlspecialchars($_SESSION['admin_username']); ?></p>
            <p><strong>Session ID:</strong> <?php echo substr(session_id(), 0, 10); ?>...</p>
        </div>
        <div class="form-col">
            <p><strong>Login Time:</strong> <?php echo date('F d, Y H:i:s'); ?></p>
            <p><strong>IP Address:</strong> <?php echo $_SERVER['REMOTE_ADDR'] ?? 'Unknown'; ?></p>
        </div>
    </div>
    
    <div class="mt-20">
        <a href="dashboard.php?page=logout" class="btn btn-danger" 
           onclick="return confirm('Are you sure you want to logout?');">
            Logout from All Sessions
        </a>
    </div>
</div>

<script>
// Password confirmation validation
document.getElementById('confirm_password').addEventListener('input', function() {
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = this.value;
    
    if (newPassword !== confirmPassword) {
        this.setCustomValidity('Passwords do not match');
    } else {
        this.setCustomValidity('');
    }
});

document.getElementById('new_password').addEventListener('input', function() {
    const confirmPassword = document.getElementById('confirm_password');
    if (confirmPassword.value && this.value !== confirmPassword.value) {
        confirmPassword.setCustomValidity('Passwords do not match');
    } else {
        confirmPassword.setCustomValidity('');
    }
});
</script>
