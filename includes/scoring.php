<?php
/**
 * includes/scoring.php — adapted for project_alpha schema.
 * Uses component_base_sql() from functions.php to get normalised rows.
 */

require_once __DIR__ . '/compatibility.php';
require_once __DIR__ . '/budget_allocator.php';
require_once __DIR__ . '/wattage.php';
require_once __DIR__ . '/fps.php';

function score_build(array $components, string $purpose, float $budget_bdt): float {
    $weights = _perf_weights($purpose);
    $perf = $w_total = 0.0;
    foreach ($weights as $cat => $w) {
        if (!empty($components[$cat]['benchmark_score'])) {
            $perf    += (float)$components[$cat]['benchmark_score'] * $w;
            $w_total += $w;
        }
    }
    $perf_score = $w_total > 0 ? ($perf / $w_total) : 0;

    $total_price = array_sum(array_map(fn($c) => (float)($c['price_bdt'] ?? 0), $components));
    $value_score = 0.0;
    if ($total_price > 0 && $budget_bdt > 0) {
        $ratio       = $total_price / $budget_bdt;
        $value_score = min(100, $perf_score * (1 / max($ratio, 0.5)));
    }

    $in_stock = $count = 0;
    foreach ($components as $comp) {
        if (is_array($comp)) {
            $count++;
            $st = normalize_stock($comp['stock_status_raw'] ?? $comp['stock_status'] ?? '');
            if ($st === 'in_stock') $in_stock++;
        }
    }
    $avail_score = $count > 0 ? (($in_stock / $count) * 100) : 0;

    return round(($perf_score * 0.60) + ($value_score * 0.30) + ($avail_score * 0.10), 2);
}

function _perf_weights(string $purpose): array {
    return match($purpose) {
        'gaming'        => ['CPU'=>0.30,'GPU'=>0.60,'RAM'=>0.05,'Storage'=>0.05],
        'video_editing' => ['CPU'=>0.45,'GPU'=>0.30,'RAM'=>0.20,'Storage'=>0.05],
        'office'        => ['CPU'=>0.50,'RAM'=>0.30,'Storage'=>0.15,'GPU'=>0.05],
        default         => ['CPU'=>0.40,'GPU'=>0.35,'RAM'=>0.15,'Storage'=>0.10],
    };
}

function get_top_builds(string $purpose, float $budget_bdt, int $limit = TOP_BUILDS_LIMIT): array {
    $allocation = allocate_budget($budget_bdt, $purpose);
    $categories = ['CPU','Motherboard','RAM','GPU','Storage','PSU','Case','Cooling'];
    $candidates = [];

    foreach ($categories as $cat) {
        $max   = ($allocation[$cat] ?? ($budget_bdt * 0.10)) * 1.15;
        $rows  = get_components_by_category($cat, $max);
        if (empty($rows)) {
            // Fallback: cheapest available
            $rows = get_components_by_category($cat);
        }
        $candidates[$cat] = array_slice($rows, 0, 3);
    }

    $builds_raw = [];
    for ($i = 0; $i < 3; $i++) {
        $build = []; $total = 0.0;
        foreach ($categories as $cat) {
            $pool = $candidates[$cat] ?? [];
            if (empty($pool)) continue;
            $idx         = min($i, count($pool) - 1);
            $build[$cat] = $pool[$idx];
            $total      += (float)($pool[$idx]['price_bdt'] ?? 0);
        }
        if (!empty($build)) $builds_raw[] = ['components'=>$build,'total_bdt'=>$total];
    }

    $scored = [];
    foreach ($builds_raw as $b) {
        $compat  = check_compatibility($b['components']);
        $s       = score_build($b['components'], $purpose, $budget_bdt);
        $scored[] = array_merge($b, ['score'=>$s,'compat'=>$compat]);
    }

    usort($scored, fn($a,$b) => $b['score'] <=> $a['score']);
    return array_slice($scored, 0, $limit);
}
