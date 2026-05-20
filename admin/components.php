<?php
// admin/components.php — uses component table (component_id PK, component_name, type)
// Prices/stock managed via storeavailability table
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_auth('admin');

$categories = ['CPU (processing)','Motherboard (connection)','RAM (temporary memory)',
               'Storage (HDD/SSD)','GPU (graphics)','PSU (power)','Case (body)','Cooling'];

// DELETE
if (input('action')==='delete' && is_post()) {
    verify_csrf();
    db_exec('DELETE FROM component WHERE component_id=?', [(int)input('id')]);
    flash_message('success','Component deleted.'); redirect('admin/components.php');
}

// ADD / EDIT
if (input('action')==='save' && is_post()) {
    verify_csrf();
    $id   = (int)input('id',0);
    $name = input('name'); $type = input('type');
    $brand= input('brand'); $bench=(float)input('benchmark_score'); $tdp=(int)input('tdp_watts');
    $sock = input('socket'); $rgen=input('ram_gen'); $ff=input('form_factor');
    $lmm  = (int)input('length_mm'); $hmm=(int)input('height_mm');
    $m2   = (int)input('m2_slots'); $sata=(int)input('sata_ports'); $rslots=(int)input('ram_slots');
    $psuW = (int)input('psu_wattage'); $siface=input('storage_interface');
    
    // Handle image upload
    $image_url = null;
    if ($id) {
        // preserve existing image if not overwritten
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

    if ($id) {
        db_exec('UPDATE component SET component_name=?,type=?,brand=?,benchmark_score=?,tdp_watts=?,socket=?,ram_gen=?,form_factor=?,length_mm=?,height_mm=?,m2_slots=?,sata_ports=?,ram_slots=?,psu_wattage=?,storage_interface=?,image_url=? WHERE component_id=?',
            [$name,$type,$brand,$bench,$tdp,$sock,$rgen,$ff,$lmm,$hmm,$m2,$sata,$rslots,$psuW,$siface,$image_url,$id]);
        flash_message('success','Component updated.');
    } else {
        db_exec('INSERT INTO component (component_name,type,brand,benchmark_score,tdp_watts,socket,ram_gen,form_factor,length_mm,height_mm,m2_slots,sata_ports,ram_slots,psu_wattage,storage_interface,image_url) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)',
            [$name,$type,$brand,$bench,$tdp,$sock,$rgen,$ff,$lmm,$hmm,$m2,$sata,$rslots,$psuW,$siface,$image_url]);
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
<div class="container-xl py-4">
  <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <h1 class="h4 fw-800"><i class="bi bi-cpu me-2 text-accent"></i>Component Catalogue</h1>
    <button class="btn btn-accent btn-sm" data-bs-toggle="modal" data-bs-target="#comp-modal">
      <i class="bi bi-plus-lg me-1"></i>Add Component
    </button>
  </div>
  <form method="GET" class="row g-2 mb-3">
    <div class="col-md-5"><input type="text" name="search" class="form-control form-control-sm" placeholder="Search name…" value="<?=sanitise($search)?>"></div>
    <div class="col-md-3">
      <select name="cat" class="form-select form-select-sm">
        <option value="">All Categories</option>
        <?php foreach (['CPU','Motherboard','RAM','Storage','GPU','PSU','Case','Cooling'] as $c): ?>
        <option value="<?=$c?>" <?=$cat===$c?'selected':''?>><?=$c?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-auto"><button class="btn btn-outline-accent btn-sm">Filter</button></div>
    <div class="col-auto"><a href="<?=BASE_URL?>/admin/components.php" class="btn btn-outline-secondary btn-sm">Clear</a></div>
  </form>

  <div class="card">
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead><tr><th>Name</th><th>Type</th><th>Brand</th><th>Price</th><th>Stock</th><th>Score</th><th>Actions</th></tr></thead>
        <tbody>
          <?php foreach ($list as $c): ?>
          <tr>
            <td class="fw-600"><?=sanitise($c['component_name'])?></td>
            <td><span class="badge bg-accent-soft small"><?=sanitise(type_to_category($c['type']))?></span></td>
            <td><?=sanitise($c['brand']??'')?></td>
            <td class="text-accent fw-600"><?=format_bdt((float)$c['price_bdt'])?></td>
            <td><span class="badge <?=strtolower($c['stock_raw']??'')==='out of stock'?'badge-stock-out':'badge-stock-in'?>"><?=sanitise($c['stock_raw']??'—')?></span></td>
            <td><?=number_format((float)$c['benchmark_score'],0)?></td>
            <td>
              <a href="?action=edit&id=<?=$c['component_id']?>" class="btn btn-sm btn-outline-secondary me-1"><i class="bi bi-pencil"></i></a>
              <form method="POST" class="d-inline" onsubmit="return confirm('Delete?')">
                <?php csrf_field();?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?=(int)$c['component_id']?>">
                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php render_pagination($pag, BASE_URL.'/admin/components.php?search='.urlencode($search).'&cat='.urlencode($cat));?>
</div>

<!-- Modal -->
<div class="modal fade" id="comp-modal" tabindex="-1" aria-labelledby="comp-modal-title" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content" style="background:var(--bg-card);border:1px solid var(--border)">
      <div class="modal-header">
        <h5 class="modal-title fw-700" id="comp-modal-title"><?=$edit?'Edit Component':'Add Component'?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" enctype="multipart/form-data">
        <?php csrf_field();?>
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="id" value="<?=(int)($edit['component_id']??0)?>">
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-8"><label class="form-label small fw-600">Name</label>
              <input type="text" name="name" class="form-control form-control-sm" value="<?=sanitise($edit['component_name']??'')?>" required></div>
            <div class="col-md-12"><label class="form-label small fw-600">Image (Optional)</label>
              <input type="file" name="image" class="form-control form-control-sm" accept="image/*">
              <?php if (!empty($edit['image_url'])): ?>
              <?php $img_src = str_starts_with($edit['image_url'], 'http') ? $edit['image_url'] : BASE_URL . '/' . $edit['image_url']; ?>
              <small class="text-muted d-block mt-1">Current: <a href="<?=sanitise($img_src)?>" target="_blank">View Image</a></small>
              <?php endif; ?>
            </div>
            <div class="col-md-4"><label class="form-label small fw-600">Type</label>
              <select name="type" class="form-select form-select-sm" required>
                <?php foreach ($categories as $ct): ?><option value="<?=$ct?>" <?=($edit['type']??'')===$ct?'selected':''?>><?=$ct?></option><?php endforeach;?>
              </select></div>
            <?php
            $simple_fields = [
              ['brand','Brand','text'],['benchmark_score','Benchmark Score','number'],
              ['tdp_watts','TDP Watts','number'],['socket','Socket','text'],
              ['ram_gen','RAM Gen','text'],['form_factor','Form Factor','text'],
              ['length_mm','Length mm','number'],['m2_slots','M.2 Slots','number'],
              ['sata_ports','SATA Ports','number'],['ram_slots','RAM Slots','number'],
              ['psu_wattage','PSU Wattage','number'],['storage_interface','Storage IF','text'],
            ];
            foreach ($simple_fields as [$fn,$lbl,$tp]):?>
            <div class="col-md-4"><label class="form-label small fw-600"><?=$lbl?></label>
              <input type="<?=$tp?>" name="<?=$fn?>" class="form-control form-control-sm"
                     value="<?=sanitise((string)($edit[$fn]??''))?>" step="any"></div>
            <?php endforeach;?>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-accent btn-sm">Save Component</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php
$inline_script = $edit ? "new bootstrap.Modal(document.getElementById('comp-modal')).show();" : '';
include __DIR__ . '/../templates/footer.php';?>
