<?php
/**

 */

require_once __DIR__ . '/wattage.php';

function check_compatibility(array $components): array {
    $errors = [];

    $cpu  = $components['CPU']          ?? null;
    $mb   = $components['Motherboard']  ?? null;
    $ram  = $components['RAM']          ?? null;
    $gpu  = $components['GPU']          ?? null;
    $psu  = $components['PSU']          ?? null;
    $case = $components['Case']         ?? null;
    $cool = $components['Cooling']      ?? null;
    $stor = $components['Storage']      ?? null;

    if ($cpu && $mb) {
        if (!empty($cpu['socket']) && !empty($mb['socket']) &&
            strtoupper($cpu['socket']) !== strtoupper($mb['socket'])) {
            $errors[] = "CPU socket <strong>{$cpu['socket']}</strong> does not match "
                      . "motherboard socket <strong>{$mb['socket']}</strong>.";
        }
    }

    if ($ram && $mb) {
        if (!empty($ram['ram_gen']) && !empty($mb['ram_gen']) &&
            $ram['ram_gen'] !== $mb['ram_gen']) {
            $errors[] = "RAM type <strong>{$ram['ram_gen']}</strong> is incompatible with "
                      . "motherboard <strong>{$mb['ram_gen']}</strong> slots.";
        }
    }

    if ($gpu && $case) {
        $gpu_len  = (int)($gpu['length_mm'] ?? 0);
        $clearance = (int)($case['height_mm'] ?? 0);   
        if ($gpu_len > 0 && $clearance > 0 && $gpu_len > $clearance) {
            $errors[] = "GPU length <strong>{$gpu_len}mm</strong> exceeds case GPU clearance "
                      . "<strong>{$clearance}mm</strong>.";
        }
    }

    if ($psu) {
        $tdp     = calculate_tdp($components);
        $min_psu = recommend_psu_wattage($tdp);
        $psu_w   = (int)($psu['psu_wattage'] ?? 0);
        if ($psu_w > 0 && $psu_w < $min_psu) {
            $errors[] = "PSU rated at <strong>{$psu_w}W</strong> is below the recommended "
                      . "<strong>{$min_psu}W</strong> (TDP {$tdp}W × " . PSU_SAFETY_MARGIN . " safety margin).";
        }
    }

    if ($mb && $case) {
        $mbff   = $mb['form_factor']   ?? '';
        $caseff = $case['form_factor'] ?? '';
        $compatible = _case_supports_mb($caseff, $mbff);
        if ($mbff && $caseff && !$compatible) {
            $errors[] = "Motherboard form factor <strong>{$mbff}</strong> is not supported by "
                      . "the case (<strong>{$caseff}</strong>).";
        }
    }

    if ($cool && $case) {
        $cooler_h  = (int)($cool['length_mm'] ?? 0);   
        $clearance = (int)($case['length_mm'] ?? 0);   
        if ($cooler_h > 0 && $clearance > 0 && $cooler_h > $clearance) {
            $errors[] = "CPU cooler height <strong>{$cooler_h}mm</strong> exceeds case cooler "
                      . "clearance <strong>{$clearance}mm</strong>.";
        }
    }

   
    if ($stor && $mb) {
        $iface = $stor['storage_interface'] ?? '';
        if ($iface === 'NVMe') {
            $m2 = (int)($mb['m2_slots'] ?? 0);
            if ($m2 === 0) {
                $errors[] = "Motherboard has <strong>no M.2/NVMe slots</strong> for the selected NVMe storage.";
            }
        } elseif ($iface === 'SATA') {
            $sata = (int)($mb['sata_ports'] ?? 0);
            if ($sata === 0) {
                $errors[] = "Motherboard has <strong>no SATA ports</strong> for the selected SATA storage.";
            }
        }
    }

    return ['pass' => empty($errors), 'errors' => $errors];
}

function _case_supports_mb(string $case_ff, string $mb_ff): bool {
    $matrix = [
        'ATX'  => ['ATX', 'mATX', 'ITX'],
        'mATX' => ['mATX', 'ITX'],
        'ITX'  => ['ITX'],
    ];
    return in_array($mb_ff, $matrix[$case_ff] ?? [], true);
}
