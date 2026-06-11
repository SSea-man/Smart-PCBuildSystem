<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_auth('admin');

$categories = ['CPU (processing)','Motherboard (connection)','RAM (temporary memory)',
               'Storage (HDD/SSD)','GPU (graphics)','PSU (power)','Case (body)','Cooling'];

if (input('action')==='delete' && is_post()) {
    verify_csrf();
    db_exec('DELETE FROM component WHERE component_id=?', [(int)input('id')]);
    flash_message('success','Component deleted.'); redirect('admin/components.php');
}

if (input('action')==='save' && is_post()) {
    verify_csrf();
    $id   = (int)input('id',0);
    $name = input('name'); $type = input('type');
    
    $brand = input('brand');

if ($brand === '__new') {
    $brand = trim(input('new_brand'));

    if ($brand === '') {
        flash_message('error', 'Brand name required');
        redirect('admin/components.php');
        exit;
    }
}
    
    $bench=(float)input('benchmark_score'); $tdp=(int)input('tdp_watts');
    $sock = input('socket'); $rgen=input('ram_gen'); $ff=input('form_factor');
    $lmm  = (int)input('length_mm'); $hmm=(int)input('height_mm');
    $m2   = (int)input('m2_slots'); $sata=(int)input('sata_ports'); $rslots=(int)input('ram_slots');
    $psuW = (int)input('psu_wattage'); $siface=input('storage_interface');
    
    // Monitor fields
    $ssize = input('screen_size') !== '' ? (float)input('screen_size') : null;
    $res   = input('resolution') !== '' ? input('resolution') : null;
    $refr  = input('refresh_rate') !== '' ? (int)input('refresh_rate') : null;
    $panel = input('panel_type') !== '' ? input('panel_type') : null;
    
    $image_url = null;
    if ($id) {
        $image_url = db_row('SELECT image_url FROM component WHERE component_id=?', [$id])['image_url'] ?? null;
    }
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg','jpeg','png','webp','gif'])) {
            $filename = uniqid('comp_') . '.' . $ext;
            $dest = __DIR__ . '/../uploads/components/' . $filename;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
                $image_url = 'uploads/components/' . $filename;
            }
        }
    }

    $startech_url = input('startech_url');
    $ryans_url = input('ryans_url');
    if ($id) {
        db_exec('UPDATE component SET component_name=?,type=?,brand=?,benchmark_score=?,image_url=?,startech_url=?,ryans_url=? WHERE component_id=?',
            [$name,$type,$brand,$bench,$image_url,$startech_url,$ryans_url,$id]);
        
        // Clean existing subclass entries
        db_exec('DELETE FROM cpu_details WHERE component_id=?', [$id]);
        db_exec('DELETE FROM motherboard_details WHERE component_id=?', [$id]);
        db_exec('DELETE FROM ram_details WHERE component_id=?', [$id]);
        db_exec('DELETE FROM gpu_details WHERE component_id=?', [$id]);
        db_exec('DELETE FROM storage_details WHERE component_id=?', [$id]);
        db_exec('DELETE FROM psu_details WHERE component_id=?', [$id]);
        db_exec('DELETE FROM case_details WHERE component_id=?', [$id]);
        db_exec('DELETE FROM cooling_details WHERE component_id=?', [$id]);
        db_exec('DELETE FROM monitor_details WHERE component_id=?', [$id]);
        
        flash_message('success','Component updated.');
    } else {
        $id = (int)db_exec('INSERT INTO component (component_name,type,brand,benchmark_score,image_url,startech_url,ryans_url) VALUES (?,?,?,?,?,?,?)',
            [$name,$type,$brand,$bench,$image_url,$startech_url,$ryans_url]);
        flash_message('success','Component added.');
    }

    // Insert new subclass details
    $type_lower = strtolower($type);
    if (strpos($type_lower, 'cpu') !== false && strpos($type_lower, 'cooler') === false) {
        db_exec('INSERT INTO cpu_details (component_id, tdp_watts, socket) VALUES (?, ?, ?)', [$id, $tdp, $sock]);
    } elseif (strpos($type_lower, 'motherboard') !== false) {
        db_exec('INSERT INTO motherboard_details (component_id, socket, ram_gen, form_factor, m2_slots, sata_ports, ram_slots) VALUES (?, ?, ?, ?, ?, ?, ?)', [$id, $sock, $rgen, $ff, $m2, $sata, $rslots]);
    } elseif (strpos($type_lower, 'ram') !== false) {
        db_exec('INSERT INTO ram_details (component_id, ram_gen) VALUES (?, ?)', [$id, $rgen]);
    } elseif (strpos($type_lower, 'gpu') !== false || strpos($type_lower, 'graphics') !== false) {
        db_exec('INSERT INTO gpu_details (component_id, tdp_watts, length_mm) VALUES (?, ?, ?)', [$id, $tdp, $lmm]);
    } elseif (strpos($type_lower, 'storage') !== false) {
        db_exec('INSERT INTO storage_details (component_id, storage_interface) VALUES (?, ?)', [$id, $siface]);
    } elseif (strpos($type_lower, 'psu') !== false || strpos($type_lower, 'power') !== false) {
        db_exec('INSERT INTO psu_details (component_id, psu_wattage) VALUES (?, ?)', [$id, $psuW]);
    } elseif (strpos($type_lower, 'casing') !== false || strpos($type_lower, 'case') !== false) {
        db_exec('INSERT INTO case_details (component_id, form_factor, length_mm, height_mm) VALUES (?, ?, ?, ?)', [$id, $ff, $lmm, $hmm]);
    } elseif (strpos($type_lower, 'cooler') !== false || strpos($type_lower, 'cooling') !== false) {
        db_exec('INSERT INTO cooling_details (component_id, height_mm) VALUES (?, ?)', [$id, $hmm]);
    } elseif (strpos($type_lower, 'monitor') !== false) {
        db_exec('INSERT INTO monitor_details (component_id, screen_size, resolution, refresh_rate, panel_type) VALUES (?, ?, ?, ?, ?)', [$id, $ssize, $res, $refr, $panel]);
    }

    redirect('admin/components.php');
}

$edit = null;
if (input('action')==='edit' && ($eid=(int)input('id'))) {
    $edit = get_component($eid);
    if ($edit) {
        $edit['component_id'] = $edit['id'];
    }
}

$search = trim(input('search',''));
$cat    = input('cat','');

$category_expr = "CASE
    WHEN type = 'CPU' OR type LIKE 'CPU (%' THEN 'CPU'
    WHEN type = 'Motherboard' OR type LIKE 'Motherboard (%' THEN 'Motherboard'
    WHEN type = 'RAM' OR type LIKE 'RAM (%' THEN 'RAM'
    WHEN type = 'Storage' OR type LIKE 'Storage (%' THEN 'Storage'
    WHEN type = 'GPU (graphics)' OR type = 'Graphics Card' THEN 'GPU'
    WHEN type = 'PSU (power)' OR type = 'Power Supply' THEN 'PSU'
    WHEN type = 'Casing' OR type = 'Case (body)' THEN 'Case'
    WHEN type IN ('CPU Cooler', 'Casing Cooler') OR (type = 'Output devices' AND (component_name LIKE '%Cooler%' OR component_name LIKE '%Fan%' OR component_name LIKE '%Liquid%' OR component_name LIKE '%Noctua%' OR component_name LIKE '%Kraken%')) THEN 'Cooling'
    WHEN type = 'Input devices' AND (component_name LIKE '%Keyboard%' OR component_name LIKE '%Kumara%' OR component_name LIKE '%Azoth%') THEN 'Keyboard'
    WHEN type = 'Input devices' AND (component_name LIKE '%Mouse%' OR component_name LIKE '%Superlight%' OR component_name LIKE '%DeathAdder%' OR component_name LIKE '%Viper%' OR component_name LIKE '%Aerox%' OR component_name LIKE '%Zowie%' OR component_name LIKE '%Lamzu%' OR component_name LIKE '%Glorious%' OR component_name LIKE '%G304%') THEN 'Mouse'
    WHEN type = 'Output devices' AND (component_name LIKE '%Monitor%' OR component_name LIKE '%\"%' OR component_name LIKE '%Hz%') THEN 'Monitor'
    ELSE type
END";

$where  = '1=1'; $params=[];
if ($search) { $where.=' AND component_name LIKE ?'; $params[]="%{$search}%"; }
if ($cat)    { $where.=" AND ($category_expr) = ?"; $params[]=$cat; }

$total = (int)db_row("SELECT COUNT(*) c FROM component WHERE $where",$params)['c'];
$pag   = paginate($total,(int)input('page',1),15);
$list  = db_query("SELECT c.*, COALESCE(sa.price,0) as price_bdt, COALESCE(sa.stock_status,'—') as stock_raw
    FROM component c
    LEFT JOIN (SELECT component_id, MIN(price) as price, stock_status FROM storeavailability GROUP BY component_id) sa ON sa.component_id=c.component_id
    WHERE $where ORDER BY c.type, c.component_name LIMIT 15 OFFSET {$pag['offset']}", $params);

$all_cats_query = db_query("SELECT DISTINCT ($category_expr) as cat_val FROM component WHERE type IS NOT NULL AND type != '' ORDER BY cat_val ASC");
$categories_list = [];
foreach ($all_cats_query as $row) {
    if ($row['cat_val']) {
        $categories_list[] = $row['cat_val'];
    }
}

$db_types_query = db_query("SELECT DISTINCT type FROM component WHERE type IS NOT NULL AND type != '' ORDER BY type ASC");
$all_db_types = [];
foreach ($db_types_query as $row) {
    $all_db_types[] = $row['type'];
}

$page_title = 'Manage Components';
include __DIR__ . '/../templates/header.php';
?>

<style>
#main-nav {
    display: none !important;
}

#main-content {
    padding-top: 0 !important;
}

.admin-layout-wrapper {
    display: grid;
    grid-template-columns: 240px 1fr;
    min-height: 100vh;
    background: var(--bg-base);
}

@media (max-width: 992px) {
    .admin-layout-wrapper {
        grid-template-columns: 1fr;
    }

    .admin-sidebar {
        display: none !important;
    }
}

.admin-sidebar {
    background: var(--bg-card);
    border-right: 1px solid var(--border);
    padding: 1.5rem 1rem;
    display: flex;
    flex-direction: column;
    height: 100vh;
    position: sticky;
    top: 0;
    justify-content: space-between;
}

.admin-brand {
    font-family: var(--font-head);
    font-weight: 800;
    font-size: 1.35rem;
    color: #7c3aed;
    display: flex;
    align-items: center;
    gap: 0.6rem;
    margin-bottom: 2rem;
    padding-left: 0.5rem;
    text-decoration: none;
}

.admin-brand i {
    font-size: 1.5rem;
}

.sidebar-group-header {
    font-size: 0.72rem;
    font-weight: 700;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    padding: 0.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.sidebar-group-content {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
    margin-bottom: 1.5rem;
}

.sidebar-nav-link {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.6rem 0.75rem;
    font-size: 0.88rem;
    font-weight: 500;
    color: var(--text-secondary);
    border-radius: 10px;
    text-decoration: none;
    transition: all 0.2s ease;
}

.sidebar-nav-link:hover {
    background: var(--bg-card-hover);
    color: var(--text-primary);
}

.sidebar-nav-link.active {
    background: #7c3aed;
    color: #ffffff !important;
    font-weight: 600;
}

.sidebar-nav-link i {
    font-size: 1.1rem;
}

.admin-workspace {
    padding: 1.5rem 2rem;
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.admin-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1.5rem;
}

.admin-search {
    position: relative;
    width: 300px;
}

.admin-search input {
    width: 100%;
    padding: 0.45rem 1rem 0.45rem 2.2rem;
    border-radius: 20px;
    border: 1px solid var(--border);
    background: var(--bg-card);
    color: var(--text-primary);
    font-size: 0.85rem;
    outline: none;
}

.admin-search i {
    position: absolute;
    left: 0.85rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-secondary);
    font-size: 0.85rem;
}

.admin-header-actions {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.btn-header-icon {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-secondary);
    border: 1px solid var(--border);
    background: var(--bg-card);
    font-size: 0.95rem;
    cursor: pointer;
}

.btn-header-icon:hover {
    background: var(--bg-card-hover);
    color: var(--text-primary);
}

.admin-user-info {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.admin-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #7c3aed;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.85rem;
}

.admin-username {
    font-size: 0.85rem;
    font-weight: 600;
    color: var(--text-primary);
}
</style>

<div class="admin-layout-wrapper">
    <aside class="admin-sidebar">
        <div>
            <a href="<?= BASE_URL ?>/admin/index.php" class="admin-brand" style="color: var(--text-primary);">
                <i class="bi bi-cpu-fill" style="color: #7c3aed;"></i>
                <span>PC Builder BD</span>
            </a>

            <div class="sidebar-group-header">
                <span>My View</span>
                <i class="bi bi-chevron-down text-muted" style="font-size:0.75rem;"></i>
            </div>
            <div class="sidebar-group-content mb-2">
                <a href="<?= BASE_URL ?>/dashboard.php" class="sidebar-nav-link">
                    <i class="bi bi-grid-fill"></i>User Dashboard
                </a>
                <a href="<?= BASE_URL ?>/index.php" class="sidebar-nav-link">
                    <i class="bi bi-house"></i>Home Page
                </a>
            </div>

            <div class="sidebar-group-header text-primary">
                <span>Admin View</span>
                <i class="bi bi-chevron-down" style="font-size:0.75rem;"></i>
            </div>

            <div class="sidebar-group-content">
                <a href="<?= BASE_URL ?>/admin/index.php" class="sidebar-nav-link">
                    <i class="bi bi-grid"></i>Dashboard
                </a>
                <a href="<?= BASE_URL ?>/admin/components.php" class="sidebar-nav-link active">
                    <i class="bi bi-cpu"></i>Components
                </a>
                <a href="<?= BASE_URL ?>/admin/users.php" class="sidebar-nav-link">
                    <i class="bi bi-people"></i>User Roles
                </a>
                <a href="<?= BASE_URL ?>/admin/prices.php" class="sidebar-nav-link">
                    <i class="bi bi-tags"></i>Price Config
                </a>
            </div>
        </div>

        <div class="text-center text-muted" style="font-size:0.7rem;">
            &copy; <?= date('Y') ?> PC Builder BD Admin
        </div>
    </aside>

    <main class="admin-workspace">
        <div class="admin-header">
            <div class="admin-search">
                <i class="bi bi-search"></i>
                <form method="GET" action="">
                    <input type="text" name="search" placeholder="Search catalog..." value="<?= sanitise($search) ?>">
                    <?php if($cat): ?><input type="hidden" name="cat" value="<?= sanitise($cat) ?>"><?php endif; ?>
                </form>
            </div>

            <div class="admin-header-actions">
                <button class="btn-header-icon" title="Settings"
                    onclick="alert('Admin settings are managed globally.')">
                    <i class="bi bi-gear"></i>
                </button>
                <button class="btn-header-icon" title="Notifications" onclick="alert('No new system alerts.')">
                    <i class="bi bi-bell"></i>
                </button>

                <div class="admin-user-info">
                    <div class="admin-avatar">
                        <?= strtoupper(substr(get_auth_user()['name'], 0, 1)) ?>
                    </div>
                    <span class="admin-username"><?= sanitise(get_auth_user()['name']) ?></span>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-1 flex-wrap gap-2">
            <div>
                <h2
                    style="font-family: var(--font-head); font-weight:800; font-size:1.6rem; color:var(--text-primary); margin:0;">
                    Component Catalogue</h2>
                <p class="text-muted small" style="margin:0;">Add, modify or delete physical hardware listings.</p>
            </div>
            <div class="d-flex gap-2">
                <form method="GET" class="d-flex gap-2">
                    <select name="cat" class="form-select form-select-sm" onchange="this.form.submit()"
                        style="border-radius:10px; width:170px;">
                        <option value="">All Categories</option>
                        <?php foreach ($categories_list as $c): ?>
                        <option value="<?= $c ?>" <?= $cat===$c?'selected':'' ?>><?= $c ?></option>
                        <?php endforeach; ?>
                    </select>
                </form>
                <button class="btn btn-primary btn-sm"
                    style="background:#7c3aed; border-color:#7c3aed; border-radius:12px; font-weight:600;"
                    data-bs-toggle="modal" data-bs-target="#comp-modal">
                    <i class="bi bi-plus-lg me-1"></i>Add Component
                </button>
            </div>
        </div>

        <div class="card p-0" style="border-radius:16px; overflow:hidden; border:1px solid var(--border);">
            <div class="table-responsive">
                <table class="table table-hover table-sm mb-0">
                    <thead>
                        <tr style="background:var(--bg-card-hover);">
                            <th class="ps-3 py-2">Name</th>
                            <th class="py-2">Category</th>
                            <th class="py-2">Brand</th>
                            <th class="py-2">Price</th>
                            <th class="py-2">Stock Level</th>
                            <th class="py-2">Benchmark Score</th>
                            <th class="pe-3 py-2 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($list)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-cpu display-6 d-block mb-2"></i>
                                No components matched your search parameters.
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($list as $c): ?>
                        <tr>
                            <td class="fw-600 ps-3 py-2"
                                style="font-size:0.85rem; max-width:320px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                <?= sanitise($c['component_name']) ?>
                            </td>
                            <td class="py-2">
                                <span class="badge bg-accent-soft text-accent"
                                    style="font-size:0.7rem; font-weight:600;"><?= sanitise(type_to_category($c['type'], $c['component_name'])) ?></span>
                            </td>
                            <td class="py-2" style="font-size:0.85rem;"><?= sanitise($c['brand']??'') ?></td>
                            <td class="text-accent fw-600 py-2" style="font-size:0.85rem;">
                                <?= format_bdt((float)$c['price_bdt']) ?></td>
                            <td class="py-2">
                                <?php
                      $stockClass = (strtolower($c['stock_raw']??'') === 'out of stock') ? 'badge-stock-out' : 'badge-stock-in';
                    ?>
                                <span class="badge <?= $stockClass ?>"
                                    style="font-size:0.7rem;"><?= sanitise($c['stock_raw']??'—') ?></span>
                            </td>
                            <td class="py-2" style="font-size:0.85rem; font-weight:600;">
                                <?= number_format((float)$c['benchmark_score'], 0) ?></td>
                            <td class="pe-3 py-2 text-end">
                                <div class="d-inline-flex gap-1">
                                    <a href="?action=edit&id=<?= $c['component_id'] ?>"
                                        class="btn btn-sm btn-outline-secondary"
                                        style="border-radius:8px; padding:0.2rem 0.5rem;" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST" action="<?= BASE_URL ?>/admin/components.php" class="d-inline"
                                        onsubmit="return confirm('Delete this component? This cannot be undone.')">
                                        <?php csrf_field(); ?>
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= (int)$c['component_id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                            style="border-radius:8px; padding:0.2rem 0.5rem;" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-2">
            <?php render_pagination($pag, BASE_URL.'/admin/components.php?search='.urlencode($search).'&cat='.urlencode($cat)); ?>
        </div>
    </main>
</div>

<div class="modal fade" id="comp-modal" tabindex="-1" aria-labelledby="comp-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content"
            style="background:var(--bg-card); border:1px solid var(--border); border-radius:16px;">
            <div class="modal-header" style="border-bottom:1px solid var(--border);">
                <h5 class="modal-title fw-700" id="comp-modal-title"><?= $edit?'Edit Component':'Add Component' ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <?php csrf_field(); ?>

                <input type="hidden" name="action" value="save">
                <input type="hidden" name="id" value="<?= (int)($edit['component_id']??0) ?>">

                <div class="modal-body">
                    <div class="row g-3">


                        <div class="col-md-8">
                            <label class="form-label small fw-600">Name</label>
                            <input type="text" name="name" class="form-control form-control-sm"
                                value="<?= sanitise($edit['component_name']??'') ?>" required>
                        </div>


                        <div class="col-md-12">
                            <label class="form-label small fw-600">Image</label>
                            <input type="file" name="image" class="form-control form-control-sm">
                        </div>


                        <div class="col-md-6">
                            <label class="form-label small fw-600">StarTech URL</label>
                            <input type="url" name="startech_url" class="form-control form-control-sm"
                                value="<?= sanitise($edit['startech_url']??'') ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-600">Ryans URL</label>
                            <input type="url" name="ryans_url" class="form-control form-control-sm"
                                value="<?= sanitise($edit['ryans_url']??'') ?>">
                        </div>


                        <!-- TYPE -->
                        <?php
                        $standard_types = [
                            'CPU (processing)',
                            'Motherboard (connection)',
                            'RAM (temporary memory)',
                            'Storage (HDD/SSD)',
                            'GPU (graphics)',
                            'PSU (power)',
                            'Case (body)',
                            'CPU Cooler',
                            'Casing Cooler',
                            'Monitor',
                            'Keyboard',
                            'Mouse',
                            'Input devices',
                            'Output devices'
                        ];
                        $merged_types = array_unique(array_merge($standard_types, $all_db_types));
                        sort($merged_types);
                        ?>
                        <div class="col-md-4">
                            <label class="form-label small fw-600">Type</label>
                            <select name="type" id="typeSelect" class="form-select form-select-sm" required>
                                <?php foreach ($merged_types as $ct): ?>
                                <option value="<?= $ct ?>" <?= ($edit['type']??'')===$ct?'selected':'' ?>>
                                    <?= $ct ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- BRAND -->
                        <?php
$brands = [
    'Intel','AMD','NVIDIA','MSI','ASUS',
    'Gigabyte','Corsair','Kingston','Samsung','Seagate'
];
sort($brands);
?>

                        <div class="col-md-4">
                            <label class="form-label small fw-600">Brand</label>

                            <select name="brand" id="brandSelect" class="form-select form-select-sm" required>
                                <option value="">-- Select Brand --</option>

                                <?php foreach ($brands as $b): ?>
                                <option value="<?= $b ?>" <?= ($edit['brand']??'')===$b?'selected':'' ?>>
                                    <?= $b ?>
                                </option>
                                <?php endforeach; ?>

                                <option value="__new">+ Add New Brand</option>
                            </select>

                            <div id="newBrandBox" style="display:none; margin-top:8px;">
                                <input type="text" name="new_brand" id="newBrandInput"
                                    class="form-control form-control-sm" placeholder="Enter new brand name">
                            </div>
                        </div>

                        <script>
                        document.addEventListener('DOMContentLoaded', function() {

                            const brandSelect = document.getElementById('brandSelect');
                            const newBrandBox = document.getElementById('newBrandBox');
                            const newBrandInput = document.getElementById('newBrandInput');

                            brandSelect.addEventListener('change', function() {

                                if (this.value === '__new') {
                                    newBrandBox.style.display = 'block';
                                    newBrandInput.setAttribute('required', 'required');
                                } else {
                                    newBrandBox.style.display = 'none';
                                    newBrandInput.removeAttribute('required');
                                    newBrandInput.value = '';
                                }

                            });

                        });
                        </script>

                        <?php
$fields = [
'cpu' => [
 ['socket','Socket','text'],
 ['tdp_watts','TDP Watts','number'],
],
'motherboard' => [
 ['socket','Socket','text'],
 ['ram_gen','RAM Gen','text'],
 ['form_factor','Form Factor','text'],
 ['m2_slots','M.2 Slots','number'],
 ['sata_ports','SATA Ports','number'],
 ['ram_slots','RAM Slots','number'],
],
'ram' => [
 ['ram_gen','RAM Gen','text'],
],
'gpu' => [
 ['tdp_watts','TDP Watts','number'],
 ['length_mm','Length (mm)','number'],
],
'storage' => [
 ['storage_interface','Storage Interface','text'],
],
'psu' => [
 ['psu_wattage','PSU Wattage','number'],
],
'case' => [
 ['form_factor','Form Factor','text'],
 ['length_mm','GPU Length Clearance (mm)','number'],
 ['height_mm','CPU Cooler Height Clearance (mm)','number'],
],
'cooling' => [
 ['height_mm','Cooler Height (mm)','number'],
],
'monitor' => [
 ['screen_size','Screen Size (inches)','number" step="0.1'],
 ['resolution','Resolution','text'],
 ['refresh_rate','Refresh Rate (Hz)','number'],
 ['panel_type','Panel Type','text'],
]
];

foreach ($fields as $group => $items):
foreach ($items as [$fn,$lbl,$tp]): ?>
                        <div class="col-md-4 field-<?= $group ?>">
                            <label class="form-label small fw-600"><?= $lbl ?></label>
                            <input type="<?= $tp ?>" name="<?= $fn ?>" class="form-control form-control-sm"
                                value="<?= sanitise((string)($edit[$fn]??'')) ?>">
                        </div>
                        <?php endforeach; endforeach; ?>

                    </div>
                </div>

                <div class="modal-footer" style="border-top:1px solid var(--border);">

                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal"
                        style="border-radius:8px;">
                        Cancel
                    </button>

                    <button type="submit" class="btn btn-primary btn-sm"
                        style="background:#7c3aed; border-color:#7c3aed; border-radius:8px;">
                        Save Component
                    </button>

                </div>

            </form>


            <style>
            [class*="field-"] {
                display: none;
            }
            </style>


            <script>
            const typeSelect = document.getElementById('typeSelect');

            function updateFields() {
                const type = typeSelect.value.toLowerCase();
                let category = 'other';
                if (type.includes('cpu') && !type.includes('cooler')) category = 'cpu';
                else if (type.includes('motherboard')) category = 'motherboard';
                else if (type.includes('ram')) category = 'ram';
                else if (type.includes('storage')) category = 'storage';
                else if (type.includes('gpu') || type.includes('graphics')) category = 'gpu';
                else if (type.includes('psu') || type.includes('power')) category = 'psu';
                else if (type.includes('casing') || type.includes('case')) category = 'case';
                else if (type.includes('cooler') || type.includes('cooling')) category = 'cooling';
                else if (type.includes('monitor')) category = 'monitor';

                document.querySelectorAll('[class*="field-"]').forEach(el => {
                    el.style.display = 'none';
                });

                show('field-' + category);
            }

            function show(cls) {
                document.querySelectorAll('.' + cls).forEach(el => {
                    el.style.display = 'block';
                });
            }

            typeSelect.addEventListener('change', updateFields);
            window.addEventListener('load', updateFields);
            </script>

            </div>
        </div>
    </div>

    <?php
$inline_script = $edit ? "new bootstrap.Modal(document.getElementById('comp-modal')).show();" : '';
include __DIR__ . '/../templates/footer.php';
?>