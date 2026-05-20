<?php
/**

 */

function calculate_tdp(array $components): int {
    $total = 0;
    foreach ($components as $comp) {
        if (is_array($comp)) $total += (int)($comp['tdp_watts'] ?? 0);
    }
    return $total;
}
function recommend_psu_wattage(int $tdp): int {
    return (int)(ceil(($tdp * PSU_SAFETY_MARGIN) / 50) * 50);
}
function psu_headroom_percent(array $components, array $psu_component): float {
    $tdp   = calculate_tdp($components);
    $rated = (int)($psu_component['psu_wattage'] ?? 0);
    if ($rated === 0 || $tdp === 0) return 0.0;
    return round((($rated - $tdp) / $rated) * 100, 1);
}
