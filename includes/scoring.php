<?php
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

    $all_mbs     = get_components_by_category('Motherboard');
    $all_cpus    = get_components_by_category('CPU');
    $all_rams    = get_components_by_category('RAM');
    $all_storage = get_components_by_category('Storage');

    $indep_cats = ['GPU', 'PSU', 'Case', 'Cooling'];
    $indep = [];
    foreach ($indep_cats as $cat) {
        $max  = ($allocation[$cat] ?? ($budget_bdt * 0.10)) * 1.15;
        $rows = get_components_by_category($cat, $max);
        if (empty($rows)) {
            $rows = get_components_by_category($cat);
        }
        $indep[$cat] = $rows;
    }

    $mb_budget  = ($allocation['Motherboard'] ?? ($budget_bdt * 0.12)) * 1.30;
    $cpu_budget = ($allocation['CPU']         ?? ($budget_bdt * 0.20)) * 1.30;
    $ram_budget = ($allocation['RAM']         ?? ($budget_bdt * 0.10)) * 1.30;
    $sto_budget = ($allocation['Storage']     ?? ($budget_bdt * 0.10)) * 1.30;

    $platforms = [];

    foreach ($all_mbs as $mb) {
        $mb_socket  = strtoupper(trim($mb['socket']  ?? ''));
        $mb_ram_gen = strtoupper(trim($mb['ram_gen'] ?? ''));
        $m2_slots   = (int)($mb['m2_slots']   ?? 0);
        $sata_ports = (int)($mb['sata_ports'] ?? 0);

        if ($mb_socket === '') continue;

        $matched_cpus = array_values(array_filter($all_cpus, function ($r) use ($mb_socket) {
            $s = strtoupper(trim($r['socket'] ?? ''));
            return $s !== '' && $s === $mb_socket;
        }));
        if (empty($matched_cpus)) continue;

        $matched_rams = [];
        if ($mb_ram_gen !== '') {
            $matched_rams = array_values(array_filter($all_rams, function ($r) use ($mb_ram_gen) {
                $g = strtoupper(trim($r['ram_gen'] ?? ''));
                return $g !== '' && $g === $mb_ram_gen;
            }));
        }
        if (empty($matched_rams)) {
            $matched_rams = $all_rams;
        }
        if (empty($matched_rams)) continue;

        $matched_storage = array_values(array_filter($all_storage, function ($r) use ($m2_slots, $sata_ports) {
            $iface = strtoupper(trim($r['storage_interface'] ?? ''));
            if ($iface === 'NVME' && $m2_slots > 0) return true;
            if ($iface === 'SATA' && $sata_ports > 0) return true;
            if ($iface === '') return true;
            return false;
        }));
        if (empty($matched_storage)) {
            $matched_storage = $all_storage;
        }

        $platform_key = $mb_socket . '|' . $mb_ram_gen;
        $mb_price = (float)($mb['price_bdt'] ?? 0);

        if (!isset($platforms[$platform_key]) || $mb_price < (float)($platforms[$platform_key]['mb']['price_bdt'] ?? PHP_FLOAT_MAX)) {
            $platforms[$platform_key] = [
                'mb'      => $mb,
                'cpus'    => $matched_cpus,
                'rams'    => $matched_rams,
                'storage' => $matched_storage,
            ];
        }

        $alt_key = $platform_key . '#' . $mb['id'];
        $platforms[$alt_key] = [
            'mb'      => $mb,
            'cpus'    => $matched_cpus,
            'rams'    => $matched_rams,
            'storage' => $matched_storage,
        ];
    }

    if (empty($platforms)) {
        return [];
    }

    $platform_list = array_values($platforms);

    usort($platform_list, function ($a, $b) use ($mb_budget) {
        $a_diff = abs((float)($a['mb']['price_bdt'] ?? 0) - $mb_budget);
        $b_diff = abs((float)($b['mb']['price_bdt'] ?? 0) - $mb_budget);
        return $a_diff <=> $b_diff;
    });

    $builds_raw = [];
    $used_mb_ids = [];

    foreach ($platform_list as $plat) {
        if (count($builds_raw) >= $limit) break;

        $mb = $plat['mb'];
        if (in_array($mb['id'], $used_mb_ids)) continue;
        $used_mb_ids[] = $mb['id'];

        $build = [];
        $total = 0.0;

        $build['Motherboard'] = $mb;
        $total += (float)($mb['price_bdt'] ?? 0);

        $cpu = _pick_best_in_budget($plat['cpus'], $cpu_budget);
        $build['CPU'] = $cpu;
        $total += (float)($cpu['price_bdt'] ?? 0);

        $ram = _pick_best_in_budget($plat['rams'], $ram_budget);
        $build['RAM'] = $ram;
        $total += (float)($ram['price_bdt'] ?? 0);

        $stor = _pick_best_in_budget($plat['storage'], $sto_budget);
        $build['Storage'] = $stor;
        $total += (float)($stor['price_bdt'] ?? 0);

        foreach ($indep_cats as $cat) {
            if ($cat === 'PSU') continue;
            $cat_budget = ($allocation[$cat] ?? ($budget_bdt * 0.10)) * 1.15;
            $pool = $indep[$cat] ?? [];
            if (empty($pool)) continue;
            $pick = _pick_best_in_budget($pool, $cat_budget);
            $build[$cat] = $pick;
            $total += (float)($pick['price_bdt'] ?? 0);
        }

        $tdp     = calculate_tdp($build);
        $min_psu = recommend_psu_wattage($tdp);

        $psu_pool   = $indep['PSU'] ?? [];
        $psu_budget = ($allocation['PSU'] ?? ($budget_bdt * 0.07)) * 1.50;

        $adequate = array_values(array_filter($psu_pool, function ($p) use ($min_psu) {
            return (int)($p['psu_wattage'] ?? 0) >= $min_psu;
        }));

        if (empty($adequate)) {
            $all_psus = get_components_by_category('PSU');
            $adequate = array_values(array_filter($all_psus, function ($p) use ($min_psu) {
                return (int)($p['psu_wattage'] ?? 0) >= $min_psu;
            }));
        }

        if (!empty($adequate)) {
            $psu_pick = _pick_best_in_budget($adequate, $psu_budget);
        } elseif (!empty($psu_pool)) {
            usort($psu_pool, fn($a, $b) => (int)($b['psu_wattage'] ?? 0) <=> (int)($a['psu_wattage'] ?? 0));
            $psu_pick = $psu_pool[0];
        } else {
            $psu_pick = null;
        }

        if ($psu_pick) {
            $build['PSU'] = $psu_pick;
            $total += (float)($psu_pick['price_bdt'] ?? 0);
        }

        $builds_raw[] = ['components' => $build, 'total_bdt' => $total];
    }

    $scored = [];
    foreach ($builds_raw as $b) {
        $compat = check_compatibility($b['components']);
        $s      = score_build($b['components'], $purpose, $budget_bdt);
        $scored[] = array_merge($b, ['score' => $s, 'compat' => $compat]);
    }

    usort($scored, fn($a, $b) => $b['score'] <=> $a['score']);
    return array_slice($scored, 0, $limit);
}

function _pick_best_in_budget(array $pool, float $budget): array {
    $in_budget = array_filter($pool, fn($r) => (float)($r['price_bdt'] ?? 0) <= $budget);
    if (!empty($in_budget)) {
        usort($in_budget, fn($a, $b) => (float)($b['benchmark_score'] ?? 0) <=> (float)($a['benchmark_score'] ?? 0));
        return reset($in_budget);
    }
    usort($pool, fn($a, $b) => (float)($a['price_bdt'] ?? 0) <=> (float)($b['price_bdt'] ?? 0));
    return reset($pool);
}
