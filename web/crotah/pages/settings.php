<?php
$error = '';
$success = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'general':
                $siteName = trim($_POST['site_name'] ?? '');
                $telegramUrl = trim($_POST['telegram_url'] ?? '');
                $baseUrl = trim($_POST['base_url'] ?? '');
                
                if (empty($siteName) || empty($baseUrl)) {
                    $error = 'Site Name and Base URL are required.';
                } else {
                    try {
                        updateSetting('site_name', $siteName);
                        updateSetting('telegram_url', $telegramUrl);
                        updateSetting('base_url', $baseUrl);
                        $success = 'General settings updated successfully!';
                    } catch (Exception $e) {
                        $error = 'Error updating settings: ' . $e->getMessage();
                    }
                }
                break;
                
            case 'seo':
                $seoTitle = trim($_POST['seo_title'] ?? '');
                $seoDescription = trim($_POST['seo_description'] ?? '');
                $seoKeywords = trim($_POST['seo_keywords'] ?? '');
                
                if (empty($seoTitle)) {
                    $error = 'SEO Title is required.';
                } else {
                    try {
                        updateSetting('seo_title', $seoTitle);
                        updateSetting('seo_description', $seoDescription);
                        updateSetting('seo_keywords', $seoKeywords);
                        $success = 'SEO settings updated successfully!';
                    } catch (Exception $e) {
                        $error = 'Error updating SEO settings: ' . $e->getMessage();
                    }
                }
                break;
                
            case 'appearance':
                // For future appearance settings
                $success = 'Appearance settings updated successfully!';
                break;
        }
    }
}

// Get current settings
$siteName = getSetting('site_name');
$telegramUrl = getSetting('telegram_url');
$baseUrl = getSetting('base_url');
$seoTitle = getSetting('seo_title');
$seoDescription = getSetting('seo_description');
$seoKeywords = getSetting('seo_keywords');
?>

<div class="page-header">
    <h1>Settings</h1>
    <p>Manage your website settings</p>
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

<!-- General Settings -->
<div class="content-box">
    <h2>General Settings</h2>
    <form method="POST">
        <input type="hidden" name="action" value="general">
        
        <div class="form-group">
            <label for="site_name">Site Name *</label>
            <input type="text" id="site_name" name="site_name" required 
                   value="<?php echo htmlspecialchars($siteName); ?>"
                   placeholder="The name of your website">
            <small>This will be displayed as the logo on the frontend</small>
        </div>
        
        <div class="form-group">
            <label for="telegram_url">Telegram URL</label>
            <input type="url" id="telegram_url" name="telegram_url" 
                   value="<?php echo htmlspecialchars($telegramUrl); ?>"
                   placeholder="https://t.me/yourchannel">
            <small>Your Telegram channel or group URL</small>
        </div>
        
        <div class="form-group">
            <label for="base_url">Base URL *</label>
            <input type="url" id="base_url" name="base_url" required 
                   value="<?php echo htmlspecialchars($baseUrl); ?>"
                   placeholder="http://localhost or https://yourdomain.com">
            <small>The root URL of your website (triggered when logo is clicked)</small>
        </div>
        
        <button type="submit" class="btn btn-primary">Update General Settings</button>
    </form>
</div>

<!-- SEO Settings -->
<div class="content-box">
    <h2>SEO Settings</h2>
    <form method="POST">
        <input type="hidden" name="action" value="seo">
        
        <div class="form-group">
            <label for="seo_title">SEO Title *</label>
            <input type="text" id="seo_title" name="seo_title" required 
                   value="<?php echo htmlspecialchars($seoTitle); ?>"
                   placeholder="Your Website - Watch Videos Online">
            <small>Title tag for search engines (appears in browser tab and search results)</small>
        </div>
        
        <div class="form-group">
            <label for="seo_description">SEO Description</label>
            <textarea id="seo_description" name="seo_description" rows="3"
                      placeholder="A brief description of your website for search engines"><?php echo htmlspecialchars($seoDescription); ?></textarea>
            <small>Meta description for search engines (appears in search results)</small>
        </div>
        
        <div class="form-group">
            <label for="seo_keywords">SEO Keywords</label>
            <input type="text" id="seo_keywords" name="seo_keywords" 
                   value="<?php echo htmlspecialchars($seoKeywords); ?>"
                   placeholder="streaming, videos, online, watch">
            <small>Comma-separated keywords for SEO</small>
        </div>
        
        <button type="submit" class="btn btn-success">Update SEO Settings</button>
    </form>
</div>

<!-- Appearance Settings -->
<div class="content-box">
    <h2>Appearance Settings</h2>
    <form method="POST">
        <input type="hidden" name="action" value="appearance">
        
        <div class="form-group">
            <label>Theme Color</label>
            <p>Currently using default dark theme with blue accents.</p>
            <small>Future updates will include customizable themes and colors.</small>
        </div>
        
        <div class="form-group">
            <label>Layout Options</label>
            <p>Currently using responsive grid layout.</p>
            <small>Future updates will include different layout options.</small>
        </div>
        
        <div class="form-group">
            <label>Custom CSS</label>
            <textarea rows="5" placeholder="/* Add your custom CSS here */" disabled></textarea>
            <small>Custom CSS functionality will be available in future updates.</small>
        </div>
        
        <button type="submit" class="btn btn-warning" disabled>Update Appearance (Coming Soon)</button>
    </form>
</div>

<!-- Current Settings Preview -->
<div class="content-box">
    <h2>Current Settings Preview</h2>
    <div class="form-row">
        <div class="form-col">
            <h4>General</h4>
            <ul>
                <li><strong>Site Name:</strong> <?php echo htmlspecialchars($siteName); ?></li>
                <li><strong>Telegram URL:</strong> <?php echo htmlspecialchars($telegramUrl ?: 'Not set'); ?></li>
                <li><strong>Base URL:</strong> <?php echo htmlspecialchars($baseUrl); ?></li>
            </ul>
        </div>
        <div class="form-col">
            <h4>SEO</h4>
            <ul>
                <li><strong>Title:</strong> <?php echo htmlspecialchars($seoTitle); ?></li>
                <li><strong>Description:</strong> <?php echo htmlspecialchars(substr($seoDescription, 0, 100)); ?><?php echo strlen($seoDescription) > 100 ? '...' : ''; ?></li>
                <li><strong>Keywords:</strong> <?php echo htmlspecialchars($seoKeywords); ?></li>
            </ul>
        </div>
    </div>
    
    <div class="mt-20">
        <a href="../../index.php" target="_blank" class="btn btn-primary">Preview Website</a>
    </div>
</div>
