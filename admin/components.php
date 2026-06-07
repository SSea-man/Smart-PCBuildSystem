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
    $brand= input('brand'); $bench=(float)input('benchmark_score'); $tdp=(int)input('tdp_watts');
    $sock = input('socket'); $rgen=input('ram_gen'); $ff=input('form_factor');
    $lmm  = (int)input('length_mm'); $hmm=(int)input('height_mm');
    $m2   = (int)input('m2_slots'); $sata=(int)input('sata_ports'); $rslots=(int)input('ram_slots');
    $psuW = (int)input('psu_wattage'); $siface=input('storage_interface');
    
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
        db_exec('UPDATE component SET component_name=?,type=?,brand=?,benchmark_score=?,tdp_watts=?,socket=?,ram_gen=?,form_factor=?,length_mm=?,height_mm=?,m2_slots=?,sata_ports=?,ram_slots=?,psu_wattage=?,storage_interface=?,image_url=?,startech_url=?,ryans_url=? WHERE component_id=?',
            [$name,$type,$brand,$bench,$tdp,$sock,$rgen,$ff,$lmm,$hmm,$m2,$sata,$rslots,$psuW,$siface,$image_url,$startech_url,$ryans_url,$id]);
        flash_message('success','Component updated.');
    } else {
        db_exec('INSERT INTO component (component_name,type,brand,benchmark_score,tdp_watts,socket,ram_gen,form_factor,length_mm,height_mm,m2_slots,sata_ports,ram_slots,psu_wattage,storage_interface,image_url,startech_url,ryans_url) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)',
            [$name,$type,$brand,$bench,$tdp,$sock,$rgen,$ff,$lmm,$hmm,$m2,$sata,$rslots,$psuW,$siface,$image_url,$startech_url,$ryans_url]);
        flash_message('success','Component added.');
    }
    redirect('admin/components.php');
}

$edit = null;
if (input('action')==='edit' && ($eid=(int)input('id'))) {
    $edit = db_row('SELECT * FROM component WHERE component_id=?',[$eid]);
}

$search = trim(input('search',''));
$cat    = input('cat','');
$where  = '1=1'; $params=[];
if ($search) { $where.=' AND component_name LIKE ?'; $params[]="%{$search}%"; }
if ($cat)    { $where.=' AND type LIKE ?'; $params[]="{$cat}%"; }
$total = (int)db_row("SELECT COUNT(*) c FROM component WHERE $where",$params)['c'];
$pag   = paginate($total,(int)input('page',1),15);
$list  = db_query("SELECT c.*, COALESCE(sa.price,0) as price_bdt, COALESCE(sa.stock_status,'—') as stock_raw
    FROM component c
    LEFT JOIN (SELECT component_id, MIN(price) as price, stock_status FROM storeavailability GROUP BY component_id) sa ON sa.component_id=c.component_id
    WHERE $where ORDER BY c.type, c.component_name LIMIT 15 OFFSET {$pag['offset']}", $params);

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
                        style="border-radius:10px; width:150px;">
                        <option value="">All Categories</option>
                        <?php foreach (['CPU','Motherboard','RAM','Storage','GPU','PSU','Case','Cooling'] as $c): ?>
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
                                    style="font-size:0.7rem; font-weight:600;"><?= sanitise(type_to_category($c['type'])) ?></span>
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
                                    <form method="POST" class="d-inline"
                                        onsubmit="return confirm('Delete this component?')">
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
                                value="<?= sanitise($edit['component_name']??'') ?>" required
                                style="border-radius:8px;">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-600">Image (Optional)</label>
                            <input type="file" name="image" class="form-control form-control-sm" accept="image/*"
                                style="border-radius:8px;">
                            <?php if (!empty($edit['image_url'])): ?>
                            <?php $img_src = str_starts_with($edit['image_url'], 'http') ? $edit['image_url'] : BASE_URL . '/' . $edit['image_url']; ?>
                            <small class="text-muted d-block mt-1">Current: <a href="<?= sanitise($img_src) ?>"
                                    target="_blank">View Image</a></small>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-600">StarTech URL (Optional)</label>
                            <input type="url" name="startech_url" class="form-control form-control-sm"
                                placeholder="https://startech.com.bd/..."
                                value="<?= sanitise($edit['startech_url']??'') ?>" style="border-radius:8px;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-600">Ryans URL (Optional)</label>
                            <input type="url" name="ryans_url" class="form-control form-control-sm"
                                placeholder="https://ryanscomputers.com/..."
                                value="<?= sanitise($edit['ryans_url']??'') ?>" style="border-radius:8px;">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-600">Type</label>
                            <select name="type" class="form-select form-select-sm" required style="border-radius:8px;">
                                <?php foreach ($categories as $ct): ?>
                                <option value="<?= $ct ?>" <?= ($edit['type']??'')===$ct?'selected':'' ?>><?= $ct ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php
            $brands = ['Intel','AMD','NVIDIA','MSI','ASUS','Gigabyte','Corsair','G.Skill','Kingston','Samsung','Western Digital','Seagate','Cooler Master','NZXT','Antec','Thermaltake','DeepCool','XFX','Zotac','Sapphire','ASRock','Palit','EVGA','PNY','Crucial','TeamGroup','Adata','Transcend'];
            sort($brands);
            ?>
                        <div class="col-md-4">
                            <label class="form-label small fw-600">Brand</label>
                            <select name="brand" class="form-select form-select-sm" required style="border-radius:8px;">
                                <option value="">-- Select Brand --</option>
                                <?php foreach ($brands as $b): ?>
                                <option value="<?= $b ?>" <?= ($edit['brand']??'')===$b?'selected':'' ?>><?= $b ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php
            $simple_fields = [
              ['benchmark_score','Benchmark Score','number'],
              ['tdp_watts','TDP Watts','number'],['socket','Socket','text'],
              ['ram_gen','RAM Gen','text'],['form_factor','Form Factor','text'],
              ['length_mm','Length mm','number'],['m2_slots','M.2 Slots','number'],
              ['sata_ports','SATA Ports','number'],['ram_slots','RAM Slots','number'],
              ['psu_wattage','PSU Wattage','number'],['storage_interface','Storage IF','text'],
            ];
            foreach ($simple_fields as [$fn,$lbl,$tp]):?>
                        <div class="col-md-4">
                            <label class="form-label small fw-600"><?= $lbl ?></label>
                            <input type="<?= $tp ?>" name="<?= $fn ?>" class="form-control form-control-sm"
                                value="<?= sanitise((string)($edit[$fn]??'')) ?>" step="any" style="border-radius:8px;">
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid var(--border);">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal"
                        style="border-radius:8px;">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm"
                        style="background:#7c3aed; border-color:#7c3aed; border-radius:8px;">Save Component</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$inline_script = $edit ? "new bootstrap.Modal(document.getElementById('comp-modal')).show();" : '';
include __DIR__ . '/../templates/footer.php';
?>