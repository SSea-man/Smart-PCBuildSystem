<?php
/**
 * templates/build_card.php
 * Variables: $build_data, $build_index, $purpose, $show_save
 */
$bc     = $build_data['components'] ?? [];
$total  = (float)($build_data['total_bdt'] ?? 0);
$score  = (float)($build_data['score']     ?? 0);
$compat = $build_data['compat'] ?? ['pass'=>true,'errors'=>[]];
$show_save = $show_save ?? true;
$idx    = $build_index ?? 1;
$bid    = $saved_build_id ?? null;

require_once __DIR__ . '/../includes/wattage.php';
require_once __DIR__ . '/../includes/fps.php';

$tdp   = calculate_tdp($bc);
$psu_w = (int)(($bc['PSU']['psu_wattage'] ?? 0));
$headroom = ($psu_w > 0 && $tdp > 0) ? round((($psu_w - $tdp) / $psu_w) * 100) : 0;

$fps_str = '';
if (!empty($bc['CPU']['id']) && !empty($bc['GPU']['id'])) {
    $slug = (($purpose??'gaming')==='gaming') ? 'csgo2' : 'davinci-resolve';
    $fp   = estimate_fps((int)$bc['CPU']['id'], (int)$bc['GPU']['id'], $slug);
    if ($fp) $fps_str = $fp['min'].'–'.$fp['max'].' FPS @ '.$fp['resolution'];
}

$tier_labels = [1=>'Best Value',2=>'Balanced',3=>'Budget'];
$tier  = $tier_labels[$idx] ?? "Build #{$idx}";
$score_class = $score>=75?'text-success':($score>=50?'text-warning':'text-danger');
$cat_icons   = ['CPU'=>'bi-cpu','Motherboard'=>'bi-motherboard','RAM'=>'bi-memory',
                 'GPU'=>'bi-gpu-card','Storage'=>'bi-device-hdd','PSU'=>'bi-lightning-charge',
                 'Case'=>'bi-pc','Cooling'=>'bi-thermometer-snow'];
?>
<div class="card build-card h-100">
  <div class="card-header build-card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div class="d-flex align-items-center gap-2">
      <span class="build-rank">#<?= $idx ?></span>
      <span class="badge bg-accent-soft"><?= $tier ?></span>
      <?php if(!$compat['pass']):?><span class="badge bg-warning text-dark"><i class="bi bi-exclamation-triangle me-1"></i>Warning</span><?php endif;?>
    </div>
    <div class="d-flex align-items-center gap-2">
      <?php if($fps_str):?><span class="fps-badge"><i class="bi bi-controller me-1"></i><?=sanitise($fps_str)?></span><?php endif;?>
      <span class="score-badge <?=$score_class?>"><i class="bi bi-star-fill me-1"></i><?=number_format($score,1)?>/100</span>
    </div>
  </div>
  <div class="card-body">
    <div class="table-responsive mb-3">
      <table class="table table-sm build-parts-table mb-0">
        <thead><tr><th>Category</th><th>Component</th><th class="text-end">Price</th></tr></thead>
        <tbody>
          <?php foreach($bc as $cat=>$comp): if(!is_array($comp)) continue; ?>
          <tr>
            <td><i class="<?=$cat_icons[$cat]??'bi-box'?> me-1 text-accent"></i><?=sanitise($cat)?></td>
            <td><?=sanitise($comp['name']??'—')?><?php if(!empty($comp['retailer'])):?><small class="text-muted ms-1">(<?=sanitise($comp['retailer'])?>)</small><?php endif;?></td>
            <td class="text-end fw-600"><?=format_bdt((float)($comp['price_bdt']??0))?></td>
          </tr>
          <?php endforeach;?>
        </tbody>
        <tfoot><tr class="total-row"><td colspan="2" class="fw-700">Total</td><td class="text-end fw-700 text-accent"><?=format_bdt($total)?></td></tr></tfoot>
      </table>
    </div>
    <?php if($psu_w>0):?>
    <div class="mb-3">
      <div class="d-flex justify-content-between mb-1">
        <small class="text-muted"><i class="bi bi-lightning-charge me-1"></i>PSU Headroom</small>
        <small class="fw-600 <?=$headroom>=20?'text-success':'text-warning'?>"><?=$headroom?>%</small>
      </div>
      <div class="progress" style="height:8px">
        <div class="progress-bar <?=$headroom>=20?'bg-success':'bg-warning'?>" style="width:<?=min(100,$headroom)?>%"></div>
      </div>
      <div class="d-flex justify-content-between mt-1">
        <small class="text-muted">TDP: <?=$tdp?>W</small>
        <small class="text-muted">PSU: <?=$psu_w?>W</small>
      </div>
    </div>
    <?php endif;?>
    <?php if(!empty($compat['errors'])):?>
    <div class="alert alert-warning py-2 small">
      <?php foreach($compat['errors'] as $e): echo "<div>⚠ {$e}</div>"; endforeach;?>
    </div>
    <?php endif;?>
  </div>
  <div class="card-footer d-flex gap-2 flex-wrap">
    <?php if($show_save && is_logged_in()):?>
      <?php if($bid):?>
        <a href="<?=BASE_URL?>/dashboard.php" class="btn btn-sm btn-success flex-grow-1"><i class="bi bi-check-circle me-1"></i>Saved</a>
      <?php else:?>
        <button class="btn btn-sm btn-accent flex-grow-1 save-build-btn"
                data-build='<?=htmlspecialchars(json_encode(['components'=>array_column(array_values($bc),'id'),'total_bdt'=>$total,'score'=>$score,'purpose'=>$purpose??'general']),ENT_QUOTES)?>'>
          <i class="bi bi-bookmark-plus me-1"></i>Save Build
        </button>
      <?php endif;?>
    <?php elseif($show_save):?>
      <a href="<?=BASE_URL?>/login.php" class="btn btn-sm btn-outline-accent flex-grow-1"><i class="bi bi-bookmark-plus me-1"></i>Login to Save</a>
    <?php endif;?>
    <button class="btn btn-sm btn-outline-secondary share-build-btn" title="Share"><i class="bi bi-share"></i></button>
  </div>
</div>
