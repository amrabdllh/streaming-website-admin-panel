<?php
require_once '../includes/auth.php';
requireLogin();
require_once '../../web/crotah/includes/config.php';

// Handle form submissions for add, edit, delete, toggle active
$action = $_POST['action'] ?? null;

if ($action === 'add') {
    $name = $_POST['name'] ?? '';
    $code = $_POST['code'] ?? '';
    $position = $_POST['position'] ?? 'head';
    $active = isset($_POST['active']) ? 1 : 0;

    $stmt = $pdo->prepare("INSERT INTO ads (name, code, position, active) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $code, $position, $active]);
    header("Location: dashboard.php?page=ads");
    exit;
} elseif ($action === 'edit') {
    $id = $_POST['id'] ?? 0;
    $name = $_POST['name'] ?? '';
    $code = $_POST['code'] ?? '';
    $position = $_POST['position'] ?? 'head';
    $active = isset($_POST['active']) ? 1 : 0;

    $stmt = $pdo->prepare("UPDATE ads SET name = ?, code = ?, position = ?, active = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
    $stmt->execute([$name, $code, $position, $active, $id]);
    header("Location: dashboard.php?page=ads");
    exit;
} elseif ($action === 'delete') {
    $id = $_POST['id'] ?? 0;
    $stmt = $pdo->prepare("DELETE FROM ads WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: dashboard.php?page=ads");
    exit;
} elseif ($action === 'toggle') {
    $id = $_POST['id'] ?? 0;
    $stmt = $pdo->prepare("SELECT active FROM ads WHERE id = ?");
    $stmt->execute([$id]);
    $current = $stmt->fetchColumn();
    $newStatus = $current ? 0 : 1;
    $stmt = $pdo->prepare("UPDATE ads SET active = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
    $stmt->execute([$newStatus, $id]);
    header("Location: dashboard.php?page=ads");
    exit;
}

// Fetch all ads
$stmt = $pdo->query("SELECT * FROM ads ORDER BY created_at DESC");
$ads = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>Manage Ads</h1>

<button id="addAdBtn" class="btn-primary">+ Add Ad</button>

<table class="admin-table">
    <thead>
        <tr>
            <th>Ad Name</th>
            <th>Position</th>
            <th>Active</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($ads as $ad): ?>
        <tr>
            <td><?php echo htmlspecialchars($ad['name']); ?></td>
            <td><?php echo htmlspecialchars(ucfirst($ad['position'])); ?></td>
            <td><?php echo $ad['active'] ? 'Yes' : 'No'; ?></td>
            <td>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="id" value="<?php echo $ad['id']; ?>">
                    <button type="submit" name="action" value="toggle" class="btn-link">
                        <?php echo $ad['active'] ? 'Deactivate' : 'Activate'; ?>
                    </button>
                </form>
                <button class="btn-link editAdBtn" data-id="<?php echo $ad['id']; ?>" data-name="<?php echo htmlspecialchars($ad['name'], ENT_QUOTES); ?>" data-code="<?php echo htmlspecialchars($ad['code'], ENT_QUOTES); ?>" data-position="<?php echo $ad['position']; ?>" data-active="<?php echo $ad['active']; ?>">Edit</button>
                <form method="post" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this ad?');">
                    <input type="hidden" name="id" value="<?php echo $ad['id']; ?>">
                    <button type="submit" name="action" value="delete" class="btn-link text-red-600">Delete</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<!-- Add/Edit Ad Modal -->
<div id="adModal" class="modal hidden">
    <div class="modal-content">
        <span id="closeModal" class="close" aria-label="Close modal">&times;</span>
        <h2 id="modalTitle" class="text-2xl font-semibold mb-4">Add New Ad</h2>
        <form method="post" id="adForm" class="space-y-6">
            <input type="hidden" name="action" value="add" id="formAction">
            <input type="hidden" name="id" id="adId">
            <div class="form-group">
                <label for="adName" class="block mb-2 font-medium text-gray-900 dark:text-gray-300">Ad Name</label>
                <input type="text" name="name" id="adName" placeholder="e.g., Banner Ad" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
            </div>
            <div class="form-group">
                <label for="adCode" class="block mb-2 font-medium text-gray-900 dark:text-gray-300">Ad Code (HTML/JavaScript)</label>
                <textarea name="code" id="adCode" rows="6" placeholder="Paste HTML or JavaScript ad code here (PHP not allowed)" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"></textarea>
            </div>
            <div class="form-group">
                <label for="adPosition" class="block mb-2 font-medium text-gray-900 dark:text-gray-300">Position</label>
                <select name="position" id="adPosition" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                    <option value="head">Head (before </head>)</option>
                    <option value="body">Body (before </body>)</option>
                </select>
            </div>
            <div class="form-group flex items-center">
                <input type="checkbox" name="active" id="adActive" checked
                    class="mr-2 h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                <label for="adActive" class="font-medium text-gray-900 dark:text-gray-300">Active</label>
            </div>
            <div class="form-actions flex justify-end space-x-4">
                <button type="submit" class="btn btn-primary" id="submitBtn">Add Ad</button>
                <button type="button" class="btn btn-secondary" id="cancelBtn">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('addAdBtn').addEventListener('click', function() {
    document.getElementById('modalTitle').textContent = 'Add New Ad';
    document.getElementById('formAction').value = 'add';
    document.getElementById('adId').value = '';
    document.getElementById('adName').value = '';
    document.getElementById('adCode').value = '';
    document.getElementById('adPosition').value = 'head';
    document.getElementById('adActive').checked = true;
    document.getElementById('submitBtn').textContent = 'Add Ad';
    document.getElementById('adModal').classList.remove('hidden');
});

document.querySelectorAll('.editAdBtn').forEach(function(button) {
    button.addEventListener('click', function() {
        document.getElementById('modalTitle').textContent = 'Edit Ad';
        document.getElementById('formAction').value = 'edit';
        document.getElementById('adId').value = this.dataset.id;
        document.getElementById('adName').value = this.dataset.name;
        document.getElementById('adCode').value = this.dataset.code;
        document.getElementById('adPosition').value = this.dataset.position;
        document.getElementById('adActive').checked = this.dataset.active == 1;
        document.getElementById('submitBtn').textContent = 'Save Changes';
        document.getElementById('adModal').classList.remove('hidden');
    });
});

document.getElementById('closeModal').addEventListener('click', function() {
    document.getElementById('adModal').classList.add('hidden');
});

document.getElementById('cancelBtn').addEventListener('click', function() {
    document.getElementById('adModal').classList.add('hidden');
});
</script>

<style>
.modal {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.3s ease;
}

.modal:not(.hidden) {
    opacity: 1;
    pointer-events: auto;
}

.modal-content {
    background: white;
    padding: 2rem;
    border-radius: 0.5rem;
    width: 500px;
    max-width: 90%;
    color: #111827;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    transform: translateY(-20px);
    transition: transform 0.3s ease;
}

.modal:not(.hidden) .modal-content {
    transform: translateY(0);
}

.close {
    float: right;
    font-size: 1.5rem;
    cursor: pointer;
    color: #6b7280;
    transition: color 0.2s ease;
}

.close:hover {
    color: #111827;
}

.admin-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    color: #111827;
    background: white;
    border-radius: 0.5rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    overflow: hidden;
}

.admin-table th,
.admin-table td {
    padding: 12px 15px;
    border-bottom: 1px solid #e5e7eb;
    text-align: left;
}

.admin-table th {
    background-color: #f9fafb;
    font-weight: 600;
}

.admin-table tr:hover {
    background-color: #f3f4f6;
}

.btn-link {
    background: none;
    border: none;
    color: #4f46e5;
    cursor: pointer;
    padding: 0;
    font-size: 14px;
    text-decoration: underline;
    transition: color 0.2s ease;
}

.btn-link:hover {
    color: #4338ca;
}
