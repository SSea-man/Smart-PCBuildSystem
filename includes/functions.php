<?php
/**
 
 */

/
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


/**
 */
function type_to_category(string $type): string {
    return match(true) {
        str_starts_with($type, 'CPU')         => 'CPU',
        str_starts_with($type, 'Motherboard') => 'Motherboard',
        str_starts_with($type, 'RAM')         => 'RAM',
        str_starts_with($type, 'Storage')     => 'Storage',
        str_starts_with($type, 'GPU')         => 'GPU',
        str_starts_with($type, 'PSU')         => 'PSU',
        str_starts_with($type, 'Case')        => 'Case',
        str_starts_with($type, 'Cooling')     => 'Cooling',
        default                               => $type,
    };
}

/**
 */
function normalize_stock(string $s): string {
    return match(strtolower(trim($s))) {
        'in stock'     => 'in_stock',
        'limited'      => 'in_stock',
        'out of stock' => 'out_of_stock',
        'pre-order', 'pre order' => 'pre_order',
        default        => 'in_stock',
    };
}

/**
 
 */
function component_base_sql(): string {
    return "SELECT
        c.component_id                                  AS id,
        c.component_name                                AS name,
        c.type,
        CASE
            WHEN c.type LIKE 'CPU%'         THEN 'CPU'
            WHEN c.type LIKE 'Motherboard%' THEN 'Motherboard'
            WHEN c.type LIKE 'RAM%'         THEN 'RAM'
            WHEN c.type LIKE 'Storage%'     THEN 'Storage'
            WHEN c.type LIKE 'GPU%'         THEN 'GPU'
            WHEN c.type LIKE 'PSU%'         THEN 'PSU'
            WHEN c.type LIKE 'Case%'        THEN 'Case'
            WHEN c.type LIKE 'Cooling%'     THEN 'Cooling'
            ELSE c.type
        END                                             AS category,
        c.brand, c.benchmark_score, c.tdp_watts, c.socket,
        c.ram_gen, c.form_factor, c.length_mm, c.height_mm,
        c.m2_slots, c.sata_ports, c.ram_slots, c.psu_wattage,
        c.storage_interface, c.image_url,
        COALESCE(sa.price, 0)                           AS price_bdt,
        COALESCE(sa.stock_status, 'Out of Stock')       AS stock_status_raw,
        COALESCE(s.store_name, '')                      AS retailer,
        COALESCE(s.store_id, 0)                         AS store_id
    FROM component c
    LEFT JOIN (
        SELECT component_id, MIN(price) AS price, stock_status, store_id
        FROM storeavailability
        GROUP BY component_id
    ) sa ON sa.component_id = c.component_id
    LEFT JOIN store s ON s.store_id = sa.store_id";
}

/**
 */
function get_component(int $id): ?array {
    $sql = component_base_sql() . ' WHERE c.component_id = ?';
    $row = db_row($sql, [$id]);
    if ($row) $row['stock_status'] = normalize_stock($row['stock_status_raw']);
    return $row;
}

/**
 */
function get_components_by_category(string $category, float $max_price = 0): array {
    $type_prefix = match($category) {
        'CPU'         => 'CPU',
        'Motherboard' => 'Motherboard',
        'RAM'         => 'RAM',
        'Storage'     => 'Storage',
        'GPU'         => 'GPU',
        'PSU'         => 'PSU',
        'Case'        => 'Case',
        'Cooling'     => 'Cooling',
        default       => $category,
    };
    $sql    = component_base_sql() . " WHERE c.type LIKE ?";
    $params = ["{$type_prefix}%"];
    if ($max_price > 0) { $sql .= ' AND COALESCE(sa.price,0) <= ?'; $params[] = $max_price; }
    $sql .= ' ORDER BY c.benchmark_score DESC, sa.price DESC';
    $rows = db_query($sql, $params);
    foreach ($rows as &$r) $r['stock_status'] = normalize_stock($r['stock_status_raw'] ?? '');
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
