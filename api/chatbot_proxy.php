<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');
if (!is_logged_in()) { json_response(['error'=>'Unauthorized'], 401); }

$user = get_auth_user();
$uid  = (int)$user['id'];

$body     = json_decode(file_get_contents('php://input'), true) ?? [];
// Accept both {"messages":[...]} (UI format) and {"message":"..."} (API test format)
$messages = $body['messages'] ?? [];
if (empty($messages) && !empty($body['message'])) {
    $messages = [['role' => 'user', 'content' => (string)$body['message']]];
}
if (empty($messages)) { json_response(['error' => 'No messages provided.'], 400); }

// ── Rate limit check ──────────────────────────────────────────────────────────
$rl = db_row('SELECT * FROM chatbot_rate_limits WHERE user_id = ?', [$uid]);
$now = time();
if ($rl) {
    $window_start = strtotime($rl['window_start']);
    if ($now - $window_start > 3600) {
        db_exec('UPDATE chatbot_rate_limits SET request_count=1, window_start=NOW() WHERE user_id=?', [$uid]);
        $count = 1;
    } else {
        $count = (int)$rl['request_count'] + 1;
        if ($count > CHATBOT_RATE_LIMIT) {
            $reset = 3600 - ($now - $window_start);
            json_response(['error' => "Rate limit reached ({$count}/" . CHATBOT_RATE_LIMIT . "). Resets in " . ceil($reset/60) . " min."], 429);
        }
        db_exec('UPDATE chatbot_rate_limits SET request_count=? WHERE user_id=?', [$count, $uid]);
    }
} else {
    db_exec('INSERT INTO chatbot_rate_limits (user_id, request_count, window_start) VALUES (?,1,NOW())', [$uid]);
}

// ── Database Chatbot Logic ──────────────────────────────────────────────────
$last_message = '';
// Find the last user message
for ($i = count($messages) - 1; $i >= 0; $i--) {
    if ($messages[$i]['role'] === 'user') {
        $last_message = strtolower(trim($messages[$i]['content']));
        break;
    }
}

$reply = "I didn't understand that. You can ask me things like:\n- How many users are registered?\n- How many products do we have?\n- What is the price of Ryzen?\n- Update on RTX 4090";

if (str_contains($last_message, 'user') || str_contains($last_message, 'who')) {
    // If admin is asking, give them detailed user stats
    if (is_admin()) {
        $user_count = (int)db_row('SELECT COUNT(*) c FROM user')['c'];
        $users = db_query('SELECT user_name, email, role FROM user LIMIT 10');
        $reply = "There are currently **{$user_count}** registered users in the database.\n\nHere are some of them:\n";
        foreach ($users as $u) {
            $role = $u['role'] === 'admin' ? '[Admin]' : '[User]';
            $reply .= "- {$u['user_name']} ({$u['email']}) {$role}\n";
        }
    } else {
        $reply = "Sorry, only administrators can view user statistics and information.";
    }
} elseif (str_contains($last_message, 'product') && str_contains($last_message, 'how many')) {
    $comp_count = (int)db_row('SELECT COUNT(*) c FROM component')['c'];
    $reply = "We currently have **{$comp_count}** components available in the catalog.";
} elseif (preg_match('/(?:price of|update on|news on|how much is|tell me about)\s+(.+)/i', $last_message, $m)) {
    $keyword = trim($m[1], " ?.");
    // Search components
    $rows = db_query(component_base_sql() . " WHERE c.component_name LIKE ? LIMIT 5", ["%{$keyword}%"]);
    if ($rows) {
        $reply = "Here are the latest price updates and news for '{$keyword}':\n\n";
        foreach ($rows as $r) {
            $price = $r['price_bdt'] > 0 ? format_bdt((float)$r['price_bdt']) : "Price not set";
            $stock = normalize_stock($r['stock_status_raw'] ?? '') === 'in_stock' ? "✅ In Stock" : "❌ Out of Stock";
            $reply .= "- **{$r['name']}**\n  Price: {$price} | Status: {$stock} | Category: {$r['category']} | Brand: {$r['brand']}\n\n";
        }
    } else {
        $reply = "I couldn't find any components or news matching '{$keyword}'.";
    }
} elseif (str_contains($last_message, 'hello') || str_contains($last_message, 'hi ')) {
    $reply = "Hello! I am the PC Builder BD Database Assistant. Ask me about product prices, stock updates, or general statistics.";
}

json_response(['content' => $reply, 'model' => 'database-bot']);
