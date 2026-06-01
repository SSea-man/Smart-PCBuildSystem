<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

if (!is_admin()) {
    flash_message('danger', 'Access denied. Admins only.');
    redirect('dashboard.php');
    exit;
}

$editing = false;
$ad = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['ad_id']) ? (int)$_POST['ad_id'] : 0;
    $title = trim($_POST['title'] ?? '');
    $image_url = trim($_POST['image_url'] ?? '');
    $link_url = trim($_POST['link_url'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $active = isset($_POST['active']) ? 1 : 0;
    $start_date = $_POST['start_date'] ?: null;
    $end_date = $_POST['end_date'] ?: null;

    if ($id > 0) {
        $sql = "UPDATE sponsor_ads SET title=?, image_url=?, link_url=?, description=?, active=?, start_date=?, end_date=? WHERE ad_id=?";
        db_exec($sql, [$title, $image_url, $link_url, $description, $active, $start_date, $end_date, $id]);
        flash_message('success', 'Sponsor ad updated.');
    } else {
        $sql = "INSERT INTO sponsor_ads (title, image_url, link_url, description, active, start_date, end_date) VALUES (?,?,?,?,?,?,?)";
        db_exec($sql, [$title, $image_url, $link_url, $description, $active, $start_date, $end_date]);
        flash_message('success', 'Sponsor ad created.');
    }
    redirect('sponsor.php');
    exit;
}

if (isset($_GET['delete'])) {
    $delId = (int)$_GET['delete'];
    db_exec('DELETE FROM sponsor_ads WHERE ad_id=?', [$delId]);
    flash_message('info', 'Sponsor ad removed.');
    redirect('sponsor.php');
    exit;
}

if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $ad = db_row('SELECT * FROM sponsor_ads WHERE ad_id=?', [$editId]);
    $editing = true;
}

$ads = db_all('SELECT * FROM sponsor_ads ORDER BY created_at DESC');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin – Sponsor Ads</title>
    <link href="<?= BASE_URL ?>/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/assets/css/app.css" rel="stylesheet">
</head>
<body class="bg-body text-light">
<?php include __DIR__ . '/../templates/header.php'; ?>
<div class="container py-5">
    <h2 class="fw-800 mb-4">Sponsor Advertisements Management</h2>
    <?php display_flash_messages(); ?>
    <form method="post" class="border p-4 rounded" style="background: var(--bg-card); border: 1px solid var(--border);">
        <input type="hidden" name="ad_id" value="<?= $editing ? (int)($ad['ad_id'] ?? 0) : '' ?>">
        <div class="mb-3">
            <label class="form-label">Title</label>
            <input type="text" name="title" class="form-control" required value="<?= htmlspecialchars($ad['title'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Image URL</label>
            <input type="url" name="image_url" class="form-control" required value="<?= htmlspecialchars($ad['image_url'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Link URL</label>
            <input type="url" name="link_url" class="form-control" required value="<?= htmlspecialchars($ad['link_url'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Description (optional)</label>
            <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($ad['description'] ?? '') ?></textarea>
        </div>
        <div class="row mb-3">
            <div class="col-md-3">
                <label class="form-label">Start Date</label>
                <input type="date" name="start_date" class="form-control" value="<?= $ad['start_date'] ?? '' ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">End Date</label>
                <input type="date" name="end_date" class="form-control" value="<?= $ad['end_date'] ?? '' ?>">
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <div class="form-check ms-2">
                    <input class="form-check-input" type="checkbox" name="active" id="activeChk" <?= isset($ad['active']) && $ad['active'] ? 'checked' : '' ?>>
                    <label class="form-check-label" for="activeChk">Active</label>
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-accent w-100">
            <?= $editing ? 'Update' : 'Create' ?> Advert
        </button>
    </form>
    <hr class="my-5" style="border-color: var(--border);">
    <h3 class="fw-600 mb-3">Existing Ads</h3>
    <table class="table table-dark table-hover">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>Title</th>
                <th>Active</th>
                <th>Period</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($ads as $a): ?>
            <tr>
                <td><?= $a['ad_id'] ?></td>
                <td><?= htmlspecialchars($a['title']) ?></td>
                <td><?= $a['active'] ? '<span class="text-success">Yes</span>' : '<span class="text-danger">No</span>' ?></td>
                <td><?= $a['start_date'] ?: '—' ?> → <?= $a['end_date'] ?: '—' ?></td>
                <td>
                    <a href="?edit=<?= $a['ad_id'] ?>" class="btn btn-sm btn-outline-info me-1">Edit</a>
                    <a href="?delete=<?= $a['ad_id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this ad?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include __DIR__ . '/../templates/footer.php'; ?>
</body>
</html>
