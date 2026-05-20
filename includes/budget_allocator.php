<?php
/**
 
 */


const BUDGET_PROFILES = [
    'gaming' => [
        'CPU'         => 0.20,
        'Motherboard' => 0.12,
        'RAM'         => 0.10,
        'GPU'         => 0.35,
        'Storage'     => 0.08,
        'PSU'         => 0.07,
        'Case'        => 0.05,
        'Cooling'     => 0.03,
    ],
    'video_editing' => [
        'CPU'         => 0.25,
        'Motherboard' => 0.12,
        'RAM'         => 0.15,
        'GPU'         => 0.20,
        'Storage'     => 0.12,
        'PSU'         => 0.07,
        'Case'        => 0.05,
        'Cooling'     => 0.04,
    ],
    'office' => [
        'CPU'         => 0.15,
        'Motherboard' => 0.12,
        'RAM'         => 0.12,
        'GPU'         => 0.05,
        'Storage'     => 0.20,
        'PSU'         => 0.08,
        'Case'        => 0.15,
        'Cooling'     => 0.13,
    ],
    'general' => [
        'CPU'         => 0.20,
        'Motherboard' => 0.12,
        'RAM'         => 0.12,
        'GPU'         => 0.18,
        'Storage'     => 0.14,
        'PSU'         => 0.08,
        'Case'        => 0.08,
        'Cooling'     => 0.08,
    ],
];

/**
 */
function allocate_budget(float $total_bdt, string $purpose): array {
    $profile = BUDGET_PROFILES[$purpose] ?? BUDGET_PROFILES['general'];
    $result  = [];
    foreach ($profile as $category => $pct) {
        $result[$category] = round($total_bdt * $pct, 2);
    }
    return $result;
}

function get_budget_profile(string $purpose): array {
    return BUDGET_PROFILES[$purpose] ?? BUDGET_PROFILES['general'];
}

