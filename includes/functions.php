<?php

function format_bdt(float $n): string {
    return '৳' . number_format($n, 0, '.', ',');
}
function sanitise(mixed $input): string {
    return htmlspecialchars((string)$input, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function flash_message(string $type, string $msg): void {
    $_SESSION['flash'][] = ['type' => $type, 'msg' => $msg];
}
function get_flash(): array {
    $messages = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $messages;
}
function render_flash(): void {
    foreach (get_flash() as $f) {
        $t = sanitise($f['type']); $m = sanitise($f['msg']);
        echo "<div class=\"alert alert-{$t} alert-dismissible fade show\" role=\"alert\">"
            . $m . "<button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\" aria-label=\"Close\"></button></div>";
    }
}

function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(CSRF_TOKEN_LENGTH));
    }
    return $_SESSION['csrf_token'];
}
function csrf_field(): void {
    echo '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}
function verify_csrf(): void {
    $submitted = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!hash_equals(csrf_token(), $submitted)) {
        http_response_code(403); die('CSRF token mismatch.');
    }
}

function paginate(int $total, int $page, int $per_page = 20): array {
    $page        = max(1, $page);
    $total_pages = (int)ceil($total / $per_page);
    $page        = min($page, max(1, $total_pages));
    return ['total'=>$total,'per_page'=>$per_page,'current_page'=>$page,
            'total_pages'=>$total_pages,'offset'=>($page-1)*$per_page];
}
function render_pagination(array $p, string $url_base): void {
    if ($p['total_pages'] <= 1) return;
    $sep = str_contains($url_base, '?') ? '&' : '?';
    echo '<nav aria-label="Pagination"><ul class="pagination justify-content-center">';
    for ($i = 1; $i <= $p['total_pages']; $i++) {
        $active = ($i === $p['current_page']) ? ' active' : '';
        echo "<li class=\"page-item{$active}\"><a class=\"page-link\" href=\"{$url_base}{$sep}page={$i}\">{$i}</a></li>";
    }
    echo '</ul></nav>';
}

function redirect(string $path): never {
    header('Location: ' . BASE_URL . '/' . ltrim($path, '/'));
    exit;
}

function is_post(): bool { return $_SERVER['REQUEST_METHOD'] === 'POST'; }
function input(string $key, mixed $default = ''): mixed {
    return $_POST[$key] ?? $_GET[$key] ?? $default;
}
function json_response(mixed $data, int $code = 200): never {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function type_to_category(string $type, string $name = ''): string {
    $type = trim($type);
    $name = trim($name);
    return match(true) {
        $type === 'CPU' || str_starts_with($type, 'CPU (')                 => 'CPU',
        $type === 'Motherboard' || str_starts_with($type, 'Motherboard (') => 'Motherboard',
        $type === 'RAM' || str_starts_with($type, 'RAM (')                 => 'RAM',
        $type === 'Storage' || str_starts_with($type, 'Storage (')         => 'Storage',
        $type === 'GPU (graphics)' || $type === 'Graphics Card' || $type === 'GPU' => 'GPU',
        $type === 'PSU (power)' || $type === 'Power Supply' || $type === 'PSU' => 'PSU',
        $type === 'Casing' || $type === 'Case (body)' || $type === 'Case'  => 'Case',
        $type === 'CPU Cooler' || $type === 'Casing Cooler' || $type === 'Cooling' => 'Cooling',
        $type === 'Output devices' && (stripos($name, 'Cooler') !== false || stripos($name, 'Fan') !== false || stripos($name, 'Liquid') !== false || stripos($name, 'Noctua') !== false || stripos($name, 'Kraken') !== false) => 'Cooling',
        $type === 'Input devices' && (stripos($name, 'Keyboard') !== false || stripos($name, 'Kumara') !== false || stripos($name, 'Azoth') !== false) => 'Keyboard',
        $type === 'Input devices' && (stripos($name, 'Mouse') !== false || stripos($name, 'Superlight') !== false || stripos($name, 'DeathAdder') !== false || stripos($name, 'Viper') !== false || stripos($name, 'Aerox') !== false || stripos($name, 'Zowie') !== false || stripos($name, 'Lamzu') !== false || stripos($name, 'Glorious') !== false || stripos($name, 'G304') !== false) => 'Mouse',
        $type === 'Output devices' && (stripos($name, 'Monitor') !== false || strpos($name, '"') !== false || stripos($name, 'Hz') !== false) => 'Monitor',
        default                                                            => $type,
    };
}

function normalize_stock(string $s): string {
    return match(strtolower(trim($s))) {
        'in stock'     => 'in_stock',
        'limited'      => 'in_stock',
        'out of stock' => 'out_of_stock',
        'pre-order', 'pre order' => 'pre_order',
        default        => 'in_stock',
    };
}

function component_base_sql(): string {
    return "SELECT
        c.component_id                                  AS id,
        c.component_name                                AS name,
        c.type,
        CASE
            WHEN c.type = 'CPU' OR c.type LIKE 'CPU (%' THEN 'CPU'
            WHEN c.type = 'Motherboard' OR c.type LIKE 'Motherboard (%' THEN 'Motherboard'
            WHEN c.type = 'RAM' OR c.type LIKE 'RAM (%' THEN 'RAM'
            WHEN c.type = 'Storage' OR c.type LIKE 'Storage (%' THEN 'Storage'
            WHEN c.type = 'GPU (graphics)' OR c.type = 'Graphics Card' THEN 'GPU'
            WHEN c.type = 'PSU (power)' OR c.type = 'Power Supply' THEN 'PSU'
            WHEN c.type = 'Casing' OR c.type = 'Case (body)' THEN 'Case'
            WHEN c.type IN ('CPU Cooler', 'Casing Cooler') OR (c.type = 'Output devices' AND (c.component_name LIKE '%Cooler%' OR c.component_name LIKE '%Fan%' OR c.component_name LIKE '%Liquid%' OR c.component_name LIKE '%Noctua%' OR c.component_name LIKE '%Kraken%')) THEN 'Cooling'
            WHEN c.type = 'Input devices' AND (c.component_name LIKE '%Keyboard%' OR c.component_name LIKE '%Kumara%' OR c.component_name LIKE '%Azoth%') THEN 'Keyboard'
            WHEN c.type = 'Input devices' AND (c.component_name LIKE '%Mouse%' OR c.component_name LIKE '%Superlight%' OR c.component_name LIKE '%DeathAdder%' OR c.component_name LIKE '%Viper%' OR c.component_name LIKE '%Aerox%' OR c.component_name LIKE '%Zowie%' OR c.component_name LIKE '%Lamzu%' OR c.component_name LIKE '%Glorious%' OR c.component_name LIKE '%G304%') THEN 'Mouse'
            WHEN c.type = 'Output devices' AND (c.component_name LIKE '%Monitor%' OR c.component_name LIKE '%\"%' OR c.component_name LIKE '%Hz%') THEN 'Monitor'
            ELSE c.type
        END                                             AS category,
        c.brand, c.benchmark_score,
        COALESCE(cpu.tdp_watts, gpu.tdp_watts, 0)       AS tdp_watts,
        COALESCE(cpu.socket, mb.socket, '')             AS socket,
        COALESCE(mb.ram_gen, ram.ram_gen, '')           AS ram_gen,
        COALESCE(mb.form_factor, cd.form_factor, '')   AS form_factor,
        COALESCE(cd.length_mm, gpu.length_mm, 0)       AS length_mm,
        cd.height_mm                                    AS height_mm,
        mb.m2_slots                                     AS m2_slots,
        mb.sata_ports                                   AS sata_ports,
        mb.ram_slots                                    AS ram_slots,
        psu.psu_wattage                                 AS psu_wattage,
        sd.storage_interface                            AS storage_interface,
        mon.screen_size                                 AS screen_size,
        mon.resolution                                  AS resolution,
        mon.refresh_rate                                AS refresh_rate,
        mon.panel_type                                  AS panel_type,
        c.image_url,
        COALESCE(sa.price, 0)                           AS price_bdt,
        COALESCE(sa.stock_status, 'Out of Stock')       AS stock_status_raw,
        COALESCE(s.store_name, '')                      AS retailer,
        COALESCE(s.store_id, 0)                         AS store_id
    FROM component c
    LEFT JOIN cpu_details cpu ON c.component_id = cpu.component_id
    LEFT JOIN motherboard_details mb ON c.component_id = mb.component_id
    LEFT JOIN ram_details ram ON c.component_id = ram.component_id
    LEFT JOIN gpu_details gpu ON c.component_id = gpu.component_id
    LEFT JOIN psu_details psu ON c.component_id = psu.component_id
    LEFT JOIN storage_details sd ON c.component_id = sd.component_id
    LEFT JOIN case_details cd ON c.component_id = cd.component_id
    LEFT JOIN cooling_details cool ON c.component_id = cool.component_id
    LEFT JOIN monitor_details mon ON c.component_id = mon.component_id
    LEFT JOIN (
        SELECT component_id, MIN(price) AS price, stock_status, store_id
        FROM storeavailability
        GROUP BY component_id
    ) sa ON sa.component_id = c.component_id
    LEFT JOIN store s ON s.store_id = sa.store_id";
}

function get_component(int $id): ?array {
    $sql = component_base_sql() . ' WHERE c.component_id = ?';
    $row = db_row($sql, [$id]);
    if ($row) $row['stock_status'] = normalize_stock($row['stock_status_raw']);
    return $row;
}

function get_components_by_category(string $category, float $max_price = 0): array {
    $sql = "SELECT * FROM (" . component_base_sql() . ") sub WHERE category = ? AND store_id IS NOT NULL AND price_bdt > 0";
    $params = [$category];
    
    if ($max_price > 0) {
        $sql .= " AND price_bdt <= ?";
        $params[] = $max_price;
    }
    $sql .= " ORDER BY benchmark_score DESC, price_bdt DESC";
    
    $rows = db_query($sql, $params);
    foreach ($rows as &$r) {
        $r['stock_status'] = normalize_stock($r['stock_status_raw'] ?? '');
    }
    unset($r);
    return $rows;
}

function purpose_label(string $purpose): string {
    return match($purpose) {
        'gaming'        => 'Gaming',
        'video_editing' => 'Video Editing',
        'office'        => 'Office / Work',
        'general'       => 'General Use',
        default         => ucfirst($purpose),
    };
}
