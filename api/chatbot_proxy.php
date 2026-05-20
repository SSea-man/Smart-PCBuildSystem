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
$messages = $body['messages'] ?? [];
if (empty($messages) && !empty($body['message'])) {
    $messages = [['role' => 'user', 'content' => (string)$body['message']]];
}
if (empty($messages)) { json_response(['error' => 'No messages provided.'], 400); }

// Rate limiting has been removed at user request.
$last_message = '';
$original_message = '';
for ($i = count($messages) - 1; $i >= 0; $i--) {
    if ($messages[$i]['role'] === 'user') {
        $original_message = trim($messages[$i]['content']);
        $last_message = strtolower($original_message);
        break;
    }
}

$reply = "I didn't understand that. You can ask me things like:\n- How many users are registered?\n- How many products do we have?\n- What is the price of Ryzen?\n- Update on RTX 4090";
$action = null;

if (preg_match('/^(?:run sql|sql|query|execute sql|execute|db):\s*(.+)$/i', $original_message, $matches)) {
    if (!is_admin()) {
        $reply = "❌ Access denied. Only administrators can execute raw SQL queries.";
    } else {
        $sql = trim($matches[1]);
        try {
            $pdo = get_db();
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            
            if (preg_match('/^\s*(SELECT|SHOW|DESCRIBE|EXPLAIN)/i', $sql)) {
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (empty($results)) {
                    $reply = "✅ Query executed successfully. 0 rows returned.";
                } else {
                    // Limit output size to prevent freezing the chat
                    $output = array_slice($results, 0, 20);
                    $more = count($results) > 20 ? "\n... (Showing 20 of " . count($results) . " rows)" : "";
                    $reply = "✅ Query returned " . count($results) . " rows:\n\n```json\n" . json_encode($output, JSON_PRETTY_PRINT) . $more . "\n```";
                }
            } else {
                $affected = $stmt->rowCount();
                $reply = "✅ Query executed successfully. {$affected} rows affected.";
            }
        } catch (PDOException $e) {
            $reply = "❌ **SQL Error:**\n" . $e->getMessage();
        }
    }
} elseif (str_contains($last_message, 'user') || str_contains($last_message, 'who')) {
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
} elseif (str_contains($last_message, 'best gaming pc under')) {
    $reply = "For a gaming PC under ৳80,000, we recommend a strong mid-range setup. A great choice is an AMD Ryzen 5 5600 (or Intel Core i5-12400F) paired with an RTX 3060 or RX 7600. \n\nAllocate about ৳15,000 for the CPU, ৳35,000 for the GPU, and use the rest for 16GB DDR4 RAM, a 500GB NVMe SSD, a B550/B660 Motherboard, and a reliable 550W PSU.";
} elseif (str_contains($last_message, 'bottleneck')) {
    $reply = "**CPU vs GPU Bottleneck:**\n\nA bottleneck happens when one component limits the maximum performance of another.\n\n- **CPU Bottleneck:** Your processor is too weak, meaning your powerful graphics card has to wait for it. This causes stuttering and lower FPS.\n- **GPU Bottleneck:** Your graphics card is at 100% usage but the CPU is fine. For gaming, this is actually what you want—it means you are getting everything possible out of your graphics card!";
} elseif (str_contains($last_message, 'ddr4 vs ddr5')) {
    $reply = "**DDR4 vs DDR5 in Bangladesh:**\n\n- **DDR4:** Highly cost-effective and still provides excellent gaming performance. Perfect if you are building on a budget.\n- **DDR5:** The new standard with much faster speeds (4800MHz+). It requires newer, more expensive motherboards (AM5 or newer Intel boards). \n\nIf you want the best performance for the next 5 years, invest in DDR5. If budget is tight, DDR4 is still amazing.";
} elseif (str_contains($last_message, 'best motherboard for ryzen 7')) {
    $reply = "The best motherboard depends on which Ryzen 7 you have:\n\n- **Ryzen 7 5000 Series (AM4):** A quality B550 board like the *MSI MAG B550 TOMAHAWK* or *Asus ROG STRIX B550-F* is excellent.\n- **Ryzen 7 7000 Series (AM5):** You need a B650 or X670 board. The *Gigabyte B650 AORUS ELITE AX* or *MSI MAG B650 TOMAHAWK WIFI* are great choices with strong VRMs to handle the power.";
} elseif (str_contains($last_message, 'psu wattage')) {
    $reply = "To calculate your PSU wattage:\n\n1. Add up the TDP of your CPU and GPU.\n2. Add ~100W for your motherboard, fans, RAM, and storage.\n3. Multiply the total by 1.2 to give yourself a 20% safety margin.\n\nFor example, a Ryzen 5 + RTX 4060 build usually needs a 550W-650W PSU. A high-end build with an RTX 4080 will need 850W or more. Always buy a good 80+ Bronze or Gold rated PSU!";
} elseif (preg_match('/(dark mode|night mode|dark theme|darkmode|nightmode)/i', $last_message)) {
    $reply = "Switching to dark mode! 🌙 Much easier on the eyes.";
    $action = 'set_theme_dark';
} elseif (preg_match('/(light mode|day mode|light theme|white theme|lightmode|daymode)/i', $last_message)) {
    $reply = "Switching to light mode! ☀️ Bright and clear.";
    $action = 'set_theme_light';
} elseif (preg_match('/(how are you|how are u)/i', $last_message)) {
    $reply = "I'm doing great, thank you for asking! I'm here 24/7 to help you build your dream PC. What can I do for you today?";
} elseif (preg_match('/(who made you|who created you|who is your creator)/i', $last_message)) {
    $reply = "I was created by the developer of PC Builder BD to assist users with finding components, prices, and PC building advice!";
} elseif (preg_match('/(thank you|thanks)/i', $last_message)) {
    $reply = "You're very welcome! Let me know if you need help with anything else.";
} elseif (preg_match('/(bye|goodbye|see you)/i', $last_message)) {
    $reply = "Goodbye! Happy PC building! 👋";
} elseif (preg_match('/(what can you do|help)/i', $last_message)) {
    $reply = "I can do quite a few things!\n\n- Answer common PC building questions (bottlenecks, PSUs, DDR4 vs DDR5)\n- Check live prices and stock (e.g., 'price of RTX 4070')\n- Change the website theme (e.g., 'turn on night mode')\n- Give catalog stats ('how many products')\n\nHow can I help you right now?";
} elseif (preg_match('/(tell me a joke|make me laugh)/i', $last_message)) {
    $reply = "Why did the PC go to the doctor?\nBecause it had a terminal illness! 😆";
}

json_response(['content' => $reply, 'model' => 'database-bot', 'action' => $action]);
