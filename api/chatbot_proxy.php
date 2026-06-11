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

$last_message = '';
$original_message = '';
for ($i = count($messages) - 1; $i >= 0; $i--) {
    if ($messages[$i]['role'] === 'user') {
        $original_message = trim($messages[$i]['content']);
        $last_message = strtolower($original_message);
        break;
    }
}

if (strlen($original_message) > 500) {
    json_response([
        'content' => "Whoa, that's a lot of text! My circuits can only process up to 500 characters at a time. Please ask a shorter, more specific question.", 
        'model' => 'database-bot'
    ]);
}

$reply = "I didn't understand that. You can ask me things like:\n- How many users are registered?\n- How many products do we have?\n- What is the price of Ryzen?\n- Update on RTX 4090";
$action = null;


if (isset($_SESSION['chat_build_state'])) {
    $state = $_SESSION['chat_build_state'];
    
    if ($last_message === 'cancel' || $last_message === 'stop' || $last_message === 'quit' || $last_message === 'exit') {
        unset($_SESSION['chat_build_state'], $_SESSION['chat_build_data']);
        $reply = "Build wizard cancelled. What else can I help you with?";
    } elseif ($state === 'ASK_BUDGET') {
        if (preg_match('/(\d+)/', str_replace(',', '', $last_message), $m)) {
            $budget = (int)$m[1];
            if ($budget < 20000) {
                $reply = "৳{$budget} is a bit too low for a full custom PC. Please enter a budget of at least 20,000 BDT.";
            } else {
                $_SESSION['chat_build_data'] = ['budget' => $budget];
                $_SESSION['chat_build_state'] = 'ASK_CPU';
                $reply = "Great! Your budget is **৳" . number_format($budget) . "**.\n\nDo you prefer an **Intel** or **AMD** processor? (Or type 'Any')";
            }
        } else {
            $reply = "I couldn't understand that number. Please enter your total budget in BDT (e.g., 60000). Type 'cancel' to stop.";
        }
    } elseif ($state === 'ASK_CPU') {
        $brand = 'any';
        if (str_contains($last_message, 'intel')) $brand = 'intel';
        if (str_contains($last_message, 'amd')) $brand = 'amd';
        $_SESSION['chat_build_data']['cpu'] = $brand;
        $_SESSION['chat_build_state'] = 'ASK_GPU';
        $reply = "Got it! " . ucfirst($brand) . " for the CPU.\n\nWhat about the Graphics Card? Do you prefer **NVIDIA**, **AMD**, or 'Any'?";
    } elseif ($state === 'ASK_GPU') {
        $brand = 'any';
        if (str_contains($last_message, 'nvidia') || str_contains($last_message, 'rtx') || str_contains($last_message, 'gtx')) $brand = 'nvidia';
        if (str_contains($last_message, 'amd') || str_contains($last_message, 'radeon') || str_contains($last_message, 'rx')) $brand = 'amd';
        
        $budget = $_SESSION['chat_build_data']['budget'];
        $cpu_pref = $_SESSION['chat_build_data']['cpu'];
        $gpu_pref = $brand;
        
        unset($_SESSION['chat_build_state'], $_SESSION['chat_build_data']);
        

        $build = [];
        $total_cost = 0;
        
        // Helper function to query components with fallback to cheapest matching
        $query_comp = function($category, $max_price, $extra_wheres = '', $params = [], $brand_pref = 'any') {
            // First pass: try with brand preference and price limit
            $sql = "SELECT * FROM (" . component_base_sql() . ") c WHERE c.category = ? AND c.price_bdt > 0 AND c.price_bdt <= ? {$extra_wheres}";
            $all_params = array_merge([$category, $max_price], $params);
            if ($brand_pref !== 'any' && $brand_pref !== null) {
                $sql .= " AND c.brand LIKE ?";
                $all_params[] = "%{$brand_pref}%";
            }
            $sql .= " ORDER BY c.price_bdt DESC LIMIT 1";
            $res = db_query($sql, $all_params);
            if ($res) return $res[0];
            
            // Second pass: if brand was specified but not found, try without brand preference (retaining price limit)
            if ($brand_pref !== 'any' && $brand_pref !== null) {
                $sql_nobrand = "SELECT * FROM (" . component_base_sql() . ") c WHERE c.category = ? AND c.price_bdt > 0 AND c.price_bdt <= ? {$extra_wheres} ORDER BY c.price_bdt DESC LIMIT 1";
                $res_nobrand = db_query($sql_nobrand, array_merge([$category, $max_price], $params));
                if ($res_nobrand) return $res_nobrand[0];
            }
            
            // Third pass (Fallback): get the cheapest matching item in this category (ignoring max_price limit)
            $sql_fallback = "SELECT * FROM (" . component_base_sql() . ") c WHERE c.category = ? AND c.price_bdt > 0 {$extra_wheres}";
            $fallback_params = array_merge([$category], $params);
            if ($brand_pref !== 'any' && $brand_pref !== null) {
                $sql_fallback .= " AND c.brand LIKE ?";
                $fallback_params[] = "%{$brand_pref}%";
            }
            $sql_fallback .= " ORDER BY c.price_bdt ASC LIMIT 1";
            $res_fallback = db_query($sql_fallback, $fallback_params);
            if ($res_fallback) return $res_fallback[0];
            
            // Fourth pass: try cheapest without brand preference
            if ($brand_pref !== 'any' && $brand_pref !== null) {
                $sql_fallback_nobrand = "SELECT * FROM (" . component_base_sql() . ") c WHERE c.category = ? AND c.price_bdt > 0 {$extra_wheres} ORDER BY c.price_bdt ASC LIMIT 1";
                $res_fallback_nobrand = db_query($sql_fallback_nobrand, array_merge([$category], $params));
                if ($res_fallback_nobrand) return $res_fallback_nobrand[0];
            }
            
            return null;
        };

        // 1. Select CPU first to establish socket/ram requirements
        $cpu = $query_comp('CPU', $budget * 0.25, '', [], $cpu_pref);
        if ($cpu) { 
            $build['CPU'] = $cpu; 
            $total_cost += $cpu['price_bdt']; 
        }

        // 2. Select Motherboard compatible with CPU socket
        $mobo_wheres = '';
        $mobo_params = [];
        if ($cpu && !empty($cpu['socket'])) {
            $mobo_wheres .= " AND c.socket = ?";
            $mobo_params[] = $cpu['socket'];
        }
        $mobo = $query_comp('Motherboard', $budget * 0.15, $mobo_wheres, $mobo_params);
        if ($mobo) { 
            $build['Motherboard'] = $mobo; 
            $total_cost += $mobo['price_bdt']; 
        }

        // 3. Select RAM compatible with Motherboard's memory generation (ram_gen)
        $ram_wheres = '';
        $ram_params = [];
        if ($mobo && !empty($mobo['ram_gen'])) {
            $ram_wheres .= " AND c.ram_gen = ?";
            $ram_params[] = $mobo['ram_gen'];
        }
        $ram = $query_comp('RAM', $budget * 0.08, $ram_wheres, $ram_params);
        if ($ram) { 
            $build['RAM'] = $ram; 
            $total_cost += $ram['price_bdt']; 
        }

        // 4. Select GPU
        $gpu = $query_comp('GPU', $budget * 0.40, '', [], $gpu_pref);
        if ($gpu) { 
            $build['GPU'] = $gpu; 
            $total_cost += $gpu['price_bdt']; 
        }

        // 5. Select Storage
        $storage = $query_comp('Storage', $budget * 0.08);
        if ($storage) { 
            $build['Storage'] = $storage; 
            $total_cost += $storage['price_bdt']; 
        }

        // 6. Select PSU
        $psu = $query_comp('PSU', $budget * 0.07);
        if ($psu) { 
            $build['PSU'] = $psu; 
            $total_cost += $psu['price_bdt']; 
        }
        
        if (empty($build)) {
            $reply = "I'm sorry, I couldn't find enough components in the database to fit a budget of ৳" . number_format($budget) . ". Try a higher budget.";
        } else {
            $reply = "**Here is your custom PC build for ৳" . number_format($budget) . "!**\n\n";
            $json_arr = [];
            foreach ($build as $cat => $comp) {
                $price_str = number_format($comp['price_bdt']);
                $reply .= "• **{$cat}**: " . sanitise($comp['name']) . " - **৳{$price_str}**\n";
                $json_arr[] = [
                    'category' => $cat,
                    'name' => $comp['name'],
                    'price' => (float)$comp['price_bdt']
                ];
            }
            $reply .= "\n**Total Cost: ৳" . number_format($total_cost) . "**\n\n";
            $encoded = rawurlencode(json_encode($json_arr));
            $reply .= "<button class=\"btn btn-sm btn-accent mt-2\" onclick=\"downloadPdf('{$encoded}')\"><i class=\"bi bi-file-pdf\"></i> Download PDF</button>";
        }
    }
} elseif (preg_match('/build\s+(?:a\s+|me\s+a\s+|my\s+)?pc/i', $last_message) || preg_match('/suggest\s+(?:a\s+)?pc\s+build/i', $last_message)) {
    $_SESSION['chat_build_state'] = 'ASK_BUDGET';
    $reply = "Awesome! Let's build your custom PC step-by-step.\n\nFirst, what is your **total budget** in BDT? (e.g., 80000)\n\n*(Type 'cancel' at any time to stop)*";
} elseif (preg_match('/^(?:run sql|sql|query|execute sql|execute|db):\s*(.+)$/i', $original_message, $matches)) {
    if (!is_admin()) {
        $reply = " Access denied. Only administrators can execute raw SQL queries.";
    } else {
        $sql = trim($matches[1]);
        try {
            $pdo = get_db();
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            
            if (preg_match('/^\s*(SELECT|SHOW|DESCRIBE|EXPLAIN)/i', $sql)) {
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (empty($results)) {
                    $reply = " Query executed successfully. 0 rows returned.";
                } else {

                    $output = array_slice($results, 0, 20);
                    $more = count($results) > 20 ? "\n... (Showing 20 of " . count($results) . " rows)" : "";
                    $reply = " Query returned " . count($results) . " rows:\n\n```json\n" . json_encode($output, JSON_PRETTY_PRINT) . $more . "\n```";
                }
            } else {
                $affected = $stmt->rowCount();
                $reply = " Query executed successfully. {$affected} rows affected.";
            }
        } catch (PDOException $e) {
            $reply = " **SQL Error:**\n" . $e->getMessage();
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
} elseif (preg_match('/(?:compare|vs)\s+(.+?)\s+(?:and|vs|versus)\s+(.+)/i', $last_message, $m) || preg_match('/(.+?)\s+vs\s+(.+)/i', $last_message, $m)) {
    $item1 = trim($m[1]);
    $item2 = trim($m[2]);
    $item1 = preg_replace('/^(?:compare)\s+/i', '', $item1);
    
    $rows1 = db_query(component_base_sql() . " WHERE c.component_name LIKE ? LIMIT 1", ["%{$item1}%"]);
    $rows2 = db_query(component_base_sql() . " WHERE c.component_name LIKE ? LIMIT 1", ["%{$item2}%"]);
    
    if ($rows1 && $rows2) {
        $p1 = $rows1[0];
        $p2 = $rows2[0];
        
        $price1 = $p1['price_bdt'] > 0 ? format_bdt((float)$p1['price_bdt']) : "Price Unlisted";
        $price2 = $p2['price_bdt'] > 0 ? format_bdt((float)$p2['price_bdt']) : "Price Unlisted";
        
        $score1 = $p1['benchmark_score'] > 0 ? number_format($p1['benchmark_score'], 0) : "N/A";
        $score2 = $p2['benchmark_score'] > 0 ? number_format($p2['benchmark_score'], 0) : "N/A";
        
        $reply = "**Side-by-Side Comparison:**\n\n";
        $reply .= "| Specification | " . sanitise($p1['name']) . " | " . sanitise($p2['name']) . " |\n";
        $reply .= "| --- | --- | --- |\n";
        $reply .= "| **Category** | " . sanitise($p1['category']) . " | " . sanitise($p2['category']) . " |\n";
        $reply .= "| **Brand** | " . sanitise($p1['brand'] ?: 'Generic') . " | " . sanitise($p2['brand'] ?: 'Generic') . " |\n";
        $reply .= "| **Price** | " . $price1 . " | " . $price2 . " |\n";
        $reply .= "| **Benchmark Score** | " . $score1 . " | " . $score2 . " |\n";
        $reply .= "| **TDP (Watts)** | " . ($p1['tdp_watts'] ?: 'N/A') . "W | " . ($p2['tdp_watts'] ?: 'N/A') . "W |\n";
        
        if ($p1['socket'] || $p2['socket']) {
            $reply .= "| **Socket** | " . ($p1['socket'] ?: 'N/A') . " | " . ($p2['socket'] ?: 'N/A') . " |\n";
        }
        if ($p1['ram_gen'] || $p2['ram_gen']) {
            $reply .= "| **RAM Support** | " . ($p1['ram_gen'] ?: 'N/A') . " | " . ($p2['ram_gen'] ?: 'N/A') . " |\n";
        }
        
        if ($p1['benchmark_score'] > 0 && $p2['benchmark_score'] > 0) {
            $diff = abs($p1['benchmark_score'] - $p2['benchmark_score']);
            if ($p1['benchmark_score'] > $p2['benchmark_score']) {
                $reply .= "\n **Verdict:** **" . sanitise($p1['name']) . "** is faster than **" . sanitise($p2['name']) . "** by **" . number_format($diff, 0) . " points**.";
            } elseif ($p2['benchmark_score'] > $p1['benchmark_score']) {
                $reply .= "\n **Verdict:** **" . sanitise($p2['name']) . "** is faster than **" . sanitise($p1['name']) . "** by **" . number_format($diff, 0) . " points**.";
            } else {
                $reply .= "\n **Verdict:** Both components have identical performance scores.";
            }
        }
    } else {
        $reply = "I couldn't perform the comparison. Please make sure both components exist in our database. (Found: " . ($rows1 ? "Yes" : "No") . " for '{$item1}', " . ($rows2 ? "Yes" : "No") . " for '{$item2}')";
    }
} elseif (preg_match('/(?:is\s+(.+?)\s+compatible\s+with\s+(.+?)|are\s+(.+?)\s+and\s+(.+?)\s+compatible|compatibility\s+(?:between|of)\s+(.+?)\s+and\s+(.+?))/i', $last_message, $m)) {
    $item1 = '';
    $item2 = '';
    if (!empty($m[1]) && !empty($m[2])) {
        $item1 = trim($m[1]);
        $item2 = trim($m[2]);
    } elseif (!empty($m[3]) && !empty($m[4])) {
        $item1 = trim($m[3]);
        $item2 = trim($m[4]);
    } elseif (!empty($m[5]) && !empty($m[6])) {
        $item1 = trim($m[5]);
        $item2 = trim($m[6]);
    }
    
    $rows1 = db_query(component_base_sql() . " WHERE c.component_name LIKE ? LIMIT 1", ["%{$item1}%"]);
    $rows2 = db_query(component_base_sql() . " WHERE c.component_name LIKE ? LIMIT 1", ["%{$item2}%"]);
    
    if ($rows1 && $rows2) {
        $p1 = $rows1[0];
        $p2 = $rows2[0];
        $type1 = strtolower($p1['category']);
        $type2 = strtolower($p2['category']);
        $compatible = true;
        $reason = "";
        
        if (($type1 === 'cpu' && $type2 === 'motherboard') || 
            ($type2 === 'cpu' && $type1 === 'motherboard')) {
            $cpu = $type1 === 'cpu' ? $p1 : $p2;
            $mobo = $type1 === 'motherboard' ? $p1 : $p2;
            if (!empty($cpu['socket']) && !empty($mobo['socket']) && strtolower(trim($cpu['socket'])) !== strtolower(trim($mobo['socket']))) {
                $compatible = false;
                $reason = "they use different sockets (**{$cpu['socket']}** on CPU vs **{$mobo['socket']}** on Motherboard).";
            } else {
                $reason = "they both use the **" . ($cpu['socket'] ?: $mobo['socket'] ?: 'matching') . "** socket.";
            }
        } elseif (($type1 === 'motherboard' && $type2 === 'ram') || 
                ($type2 === 'motherboard' && $type1 === 'ram')) {
            $mobo = $type1 === 'motherboard' ? $p1 : $p2;
            $ram = $type1 === 'ram' ? $p1 : $p2;
            if (!empty($mobo['ram_gen']) && !empty($ram['ram_gen']) && strtolower(trim($mobo['ram_gen'])) !== strtolower(trim($ram['ram_gen']))) {
                $compatible = false;
                $reason = "they use different memory generations (**{$mobo['ram_gen']}** on Motherboard vs **{$ram['ram_gen']}** on RAM).";
            } else {
                $reason = "they both support the **" . ($mobo['ram_gen'] ?: $ram['ram_gen'] ?: 'matching') . "** standard.";
            }
        } elseif (($type1 === 'cpu' && $type2 === 'ram') || 
                ($type2 === 'cpu' && $type1 === 'ram')) {
            $cpu = $type1 === 'cpu' ? $p1 : $p2;
            $ram = $type1 === 'ram' ? $p1 : $p2;
            if (!empty($cpu['ram_gen']) && !empty($ram['ram_gen']) && strtolower(trim($cpu['ram_gen'])) !== strtolower(trim($ram['ram_gen']))) {
                $compatible = false;
                $reason = "the CPU supports **{$cpu['ram_gen']}** but the RAM is **{$ram['ram_gen']}**.";
            } else {
                $reason = "they both support the **" . ($cpu['ram_gen'] ?: $ram['ram_gen'] ?: 'matching') . "** memory standard.";
            }
        } else {
            $reason = "they are different component types (**{$p1['category']}** and **{$p2['category']}**) and can be used together in a build without socket/RAM limitations.";
        }
        
        if ($compatible) {
            $reply = " **Compatible!**\n\n**" . sanitise($p1['name']) . "** and **" . sanitise($p2['name']) . "** are compatible. Reason: " . $reason;
        } else {
            $reply = " **Incompatible!**\n\n**" . sanitise($p1['name']) . "** and **" . sanitise($p2['name']) . "** are **not** compatible. Reason: " . $reason;
        }
    } else {
        $reply = "I couldn't perform the compatibility check. Please verify both components exist in our database. (Found: " . ($rows1 ? "Yes" : "No") . " for '{$item1}', " . ($rows2 ? "Yes" : "No") . " for '{$item2}')";
    }
} elseif (preg_match('/(?:(?:what is the|tell me the|show the|find the|get the)\s+)?(socket|tdp|length|ram gen|ram generation|ram slots|m2 slots|sata ports|psu wattage|benchmark score|benchmark|score|price|brand|availability|stock|screen size|resolution|refresh rate|panel type)\s+(?:of|for|on|about)\s+(.+?)(?:\?|$)/i', $last_message, $m)) {
    $attr = strtolower(trim($m[1]));
    $keyword = trim($m[2], " ?.");
    $rows = db_query(component_base_sql() . " WHERE c.component_name LIKE ? LIMIT 1", ["%{$keyword}%"]);
    if ($rows) {
        $r = $rows[0];
        $val = "";
        $friendly_name = "";
        switch ($attr) {
            case 'socket':
                $val = $r['socket'] ? "**{$r['socket']}**" : "not specified or not applicable";
                $friendly_name = "socket type";
                break;
            case 'tdp':
                $val = $r['tdp_watts'] ? "**{$r['tdp_watts']}W**" : "not specified or not applicable";
                $friendly_name = "Power Draw (TDP)";
                break;
            case 'length':
                $val = $r['length_mm'] ? "**{$r['length_mm']} mm**" : "not specified or not applicable";
                $friendly_name = "physical length";
                break;
            case 'ram gen':
            case 'ram generation':
                $val = $r['ram_gen'] ? "**{$r['ram_gen']}**" : "not specified or not applicable";
                $friendly_name = "supported RAM generation";
                break;
            case 'ram slots':
                $val = $r['ram_slots'] ? "**{$r['ram_slots']} slots**" : "not specified or not applicable";
                $friendly_name = "RAM slots count";
                break;
            case 'm2 slots':
                $val = $r['m2_slots'] ? "**{$r['m2_slots']} slots**" : "not specified or not applicable";
                $friendly_name = "M.2 slots count";
                break;
            case 'sata ports':
                $val = $r['sata_ports'] ? "**{$r['sata_ports']} ports**" : "not specified or not applicable";
                $friendly_name = "SATA ports count";
                break;
            case 'psu wattage':
                $val = $r['psu_wattage'] ? "**{$r['psu_wattage']}W**" : "not specified or not applicable";
                $friendly_name = "PSU wattage rating";
                break;
            case 'benchmark':
            case 'benchmark score':
            case 'score':
                $val = $r['benchmark_score'] ? "**" . number_format($r['benchmark_score'], 0) . "/100**" : "not specified or not applicable";
                $friendly_name = "benchmark performance score";
                break;
            case 'price':
                $val = $r['price_bdt'] > 0 ? "**" . format_bdt((float)$r['price_bdt']) . "**" : "unlisted";
                $friendly_name = "retail price";
                break;
            case 'brand':
                $val = $r['brand'] ? "**{$r['brand']}**" : "not specified";
                $friendly_name = "brand/manufacturer";
                break;
            case 'availability':
            case 'stock':
                $val = normalize_stock($r['stock_status_raw'] ?? '') === 'in_stock' ? "**In Stock**" : "**Out of Stock**";
                $friendly_name = "stock availability status";
                break;
            case 'screen size':
                $val = $r['screen_size'] ? "**{$r['screen_size']}\"**" : "not specified or not applicable";
                $friendly_name = "screen size";
                break;
            case 'resolution':
                $val = $r['resolution'] ? "**{$r['resolution']}**" : "not specified or not applicable";
                $friendly_name = "resolution";
                break;
            case 'refresh rate':
                $val = $r['refresh_rate'] ? "**{$r['refresh_rate']} Hz**" : "not specified or not applicable";
                $friendly_name = "refresh rate";
                break;
            case 'panel type':
                $val = $r['panel_type'] ? "**{$r['panel_type']}**" : "not specified or not applicable";
                $friendly_name = "panel type";
                break;
        }
        $reply = "The {$friendly_name} for **" . sanitise($r['name']) . "** is {$val}.";
    } else {
        $reply = "I couldn't find any component matching '" . sanitise($keyword) . "' in the database.";
    }
} elseif (preg_match('/(?:cpu|gpu|graphics card|motherboard|ram|storage|psu|cooling|case|component|product)s?\s+(?:under|below|less than|max)\s+(\d+)/i', $last_message, $m)) {
    $budget = (float)$m[1];
    $cat = "";
    if (str_contains($last_message, 'cpu')) { $cat = "CPU (processing)"; }
    elseif (str_contains($last_message, 'gpu') || str_contains($last_message, 'graphics')) { $cat = "GPU (graphics)"; }
    elseif (str_contains($last_message, 'motherboard')) { $cat = "Motherboard (connection)"; }
    elseif (str_contains($last_message, 'ram')) { $cat = "RAM (temporary memory)"; }
    elseif (str_contains($last_message, 'storage')) { $cat = "Storage (HDD/SSD)"; }
    elseif (str_contains($last_message, 'psu')) { $cat = "PSU (power)"; }
    
    $sql = "SELECT * FROM (" . component_base_sql() . ") c WHERE c.price_bdt > 0 AND c.price_bdt <= ?";
    $params = [$budget];
    if ($cat) {
        $sql .= " AND c.type = ?";
        $params[] = $cat;
    }
    $sql .= " ORDER BY c.price_bdt DESC LIMIT 5";
    $rows = db_query($sql, $params);
    if ($rows) {
        $reply = " **Top components under " . format_bdt($budget) . ":**\n\n";
        foreach ($rows as $r) {
            $reply .= "- **" . sanitise($r['name']) . "**\n  Price: **" . format_bdt((float)$r['price_bdt']) . "** | Category: " . sanitise($r['category']) . "\n\n";
        }
    } else {
        $reply = "I couldn't find any components under " . format_bdt($budget) . " in the database.";
    }
} elseif (preg_match('/(cheapest|most expensive|highest price|lowest price|priciest)\s+(cpu|gpu|graphics card|motherboard|ram|storage|psu|component|product)s?/i', $last_message, $m)) {
    $order = "ASC";
    $term = strtolower(trim($m[1]));
    if ($term === 'most expensive' || $term === 'highest price' || $term === 'priciest') { $order = "DESC"; }
    $cat = "";
    $type_keyword = strtolower(trim($m[2]));
    if ($type_keyword === 'cpu') { $cat = "CPU (processing)"; }
    elseif ($type_keyword === 'gpu' || $type_keyword === 'graphics card') { $cat = "GPU (graphics)"; }
    elseif ($type_keyword === 'motherboard') { $cat = "Motherboard (connection)"; }
    elseif ($type_keyword === 'ram') { $cat = "RAM (temporary memory)"; }
    elseif ($type_keyword === 'storage') { $cat = "Storage (HDD/SSD)"; }
    elseif ($type_keyword === 'psu') { $cat = "PSU (power)"; }
    
    $sql = "SELECT * FROM (" . component_base_sql() . ") c WHERE c.price_bdt > 0";
    $params = [];
    if ($cat) {
        $sql .= " AND c.type = ?";
        $params[] = $cat;
    }
    $sql .= " ORDER BY c.price_bdt {$order} LIMIT 1";
    $rows = db_query($sql, $params);
    if ($rows) {
        $r = $rows[0];
        $price = format_bdt((float)$r['price_bdt']);
        $label = $order === 'ASC' ? "cheapest" : "most expensive";
        $reply = "The {$label} " . ($cat ? $type_keyword : "component") . " in our database is **" . sanitise($r['name']) . "** priced at **{$price}**.";
    } else {
        $reply = "I couldn't query that from the database.";
    }
} elseif (preg_match('/(?:show me|list|get)\s+(?:all\s+)?([a-zA-Z0-9\s]+)\s+(?:products|components)/i', $last_message, $m) || preg_match('/(?:products|components)\s+(?:made by|from|by)\s+([a-zA-Z0-9\s]+)/i', $last_message, $m)) {
    $brand = trim($m[1], " ?.");
    $rows = db_query(component_base_sql() . " WHERE c.brand LIKE ? LIMIT 10", ["%{$brand}%"]);
    if ($rows) {
        $reply = " **Products from " . sanitise(ucfirst($brand)) . ":**\n\n";
        foreach ($rows as $r) {
            $price = $r['price_bdt'] > 0 ? format_bdt((float)$r['price_bdt']) : "Price Unlisted";
            $reply .= "- **" . sanitise($r['name']) . "** (" . sanitise($r['category']) . ")\n  Price: **{$price}**\n\n";
        }
    } else {
        $reply = "I couldn't find any products from **" . sanitise(ucfirst($brand)) . "** in the database.";
    }
} elseif (preg_match('/(?:list|show|get)\s+(?:all\s+)?(cpus|gpus|motherboards|rams|storages|psus|cases|coolers)/i', $last_message, $m)) {
    $type_keyword = strtolower(trim($m[1]));
    $cat = "";
    if (str_starts_with($type_keyword, 'cpu')) { $cat = "CPU (processing)"; }
    elseif (str_starts_with($type_keyword, 'gpu')) { $cat = "GPU (graphics)"; }
    elseif (str_starts_with($type_keyword, 'motherboard')) { $cat = "Motherboard (connection)"; }
    elseif (str_starts_with($type_keyword, 'ram')) { $cat = "RAM (temporary memory)"; }
    elseif (str_starts_with($type_keyword, 'storage')) { $cat = "Storage (HDD/SSD)"; }
    elseif (str_starts_with($type_keyword, 'psu')) { $cat = "PSU (power)"; }
    
    if ($cat) {
        $rows = db_query(component_base_sql() . " WHERE c.type = ? LIMIT 5", [$cat]);
        if ($rows) {
            $reply = " **List of " . sanitise($type_keyword) . ":**\n\n";
            foreach ($rows as $r) {
                $price = $r['price_bdt'] > 0 ? format_bdt((float)$r['price_bdt']) : "Price Unlisted";
                $reply .= "- **" . sanitise($r['name']) . "**\n  Price: **{$price}**\n\n";
            }
        } else {
            $reply = "I couldn't find any " . sanitise($type_keyword) . " in the database.";
        }
    } else {
        $reply = "I couldn't identify that category.";
    }
} elseif (preg_match('/(contact|phone|address|location|email|office|store location|branch)/i', $last_message)) {
    $reply = " **PC Builder BD Store Information:**\n\n- **Office Address:** Multiplan Centre, Elephant Road, Dhaka, Bangladesh\n- **Email:** support@pcbuild.com\n- **Phone:** +880 1711-XXXXXX\n- **Hours:** 10:00 AM - 8:00 PM (Closed on Tuesdays)";
} elseif (preg_match('/(?:price of|update on|news on|how much is|tell me about|details on|specs of|specification of|info on|show me)\s+(.+)/i', $last_message, $m)) {
    $keyword = trim($m[1], " ?.");
    $rows = db_query(component_base_sql() . " WHERE c.component_name LIKE ? LIMIT 15", ["%{$keyword}%"]);
    if ($rows) {
        if (count($rows) === 1) {
            $r = $rows[0];
            $price = $r['price_bdt'] > 0 ? format_bdt((float)$r['price_bdt']) : "Price Unlisted";
            $stock = normalize_stock($r['stock_status_raw'] ?? '') === 'in_stock' ? "In Stock" : "Out of Stock";
            
            $reply = "**Detailed Specifications for " . sanitise($r['name']) . "**:\n\n";
            $reply .= "- **Category**: " . sanitise($r['category']) . "\n";
            $reply .= "- **Brand**: " . sanitise($r['brand'] ?: 'Generic/Generic Brand') . "\n";
            $reply .= "- **Price**: **{$price}**\n";
            $reply .= "- **Availability**: {$stock}\n";
            if (!empty($r['socket'])) {
                $reply .= "- **Socket**: `{$r['socket']}`\n";
            }
            if (!empty($r['ram_gen'])) {
                $reply .= "- **RAM Generation**: `{$r['ram_gen']}`\n";
            }
            if ($r['tdp_watts'] > 0) {
                $reply .= "- **Power Draw (TDP)**: `{$r['tdp_watts']}W`\n";
            }
            if ($r['benchmark_score'] > 0) {
                $reply .= "- **Benchmark Performance Score**: `" . number_format($r['benchmark_score'], 0) . "/100`\n";
            }
            if (!empty($r['form_factor'])) {
                $reply .= "- **Form Factor**: `{$r['form_factor']}`\n";
            }
            if ($r['length_mm'] > 0) {
                $reply .= "- **Physical Length**: `{$r['length_mm']} mm`\n";
            }
            if ($r['m2_slots'] > 0) {
                $reply .= "- **M.2 Slots**: `{$r['m2_slots']}`\n";
            }
            if ($r['sata_ports'] > 0) {
                $reply .= "- **SATA Ports**: `{$r['sata_ports']}`\n";
            }
            if ($r['ram_slots'] > 0) {
                $reply .= "- **RAM Slots**: `{$r['ram_slots']}`\n";
            }
            if ($r['psu_wattage'] > 0) {
                $reply .= "- **PSU Wattage**: `{$r['psu_wattage']}W`\n";
            }
            if (!empty($r['storage_interface'])) {
                $reply .= "- **Storage Interface**: `{$r['storage_interface']}`\n";
            }
            if (!empty($r['screen_size'])) {
                $reply .= "- **Screen Size**: `{$r['screen_size']}\"`\n";
            }
            if (!empty($r['resolution'])) {
                $reply .= "- **Resolution**: `{$r['resolution']}`\n";
            }
            if (!empty($r['refresh_rate'])) {
                $reply .= "- **Refresh Rate**: `{$r['refresh_rate']} Hz`\n";
            }
            if (!empty($r['panel_type'])) {
                $reply .= "- **Panel Type**: `{$r['panel_type']}`\n";
            }
        } else {
            $reply = "Here are the top components matching '" . sanitise($keyword) . "':\n\n";
            foreach ($rows as $r) {
                $price = $r['price_bdt'] > 0 ? format_bdt((float)$r['price_bdt']) : "Price Unlisted";
                $stock = normalize_stock($r['stock_status_raw'] ?? '') === 'in_stock' ? "In Stock" : "Out of Stock";
                $reply .= "- **{$r['name']}**\n  Price: {$price} | Status: {$stock} | Category: {$r['category']}\n\n";
            }
            $reply .= "Tip: Ask for details on a specific component (e.g. 'details on " . sanitise($rows[0]['name']) . "') for a full specifications breakdown!";
        }
    } else {
        $reply = "I couldn't find any components matching '" . sanitise($keyword) . "' in the database.";
    }
} elseif (preg_match('/^(hi|hello|hey|greetings|hola)(?:\s|$|!)/i', $last_message)) {
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
    $reply = "Switching to dark mode!  Much easier on the eyes.";
    $action = 'set_theme_dark';
} elseif (preg_match('/(light mode|day mode|light theme|white theme|lightmode|daymode)/i', $last_message)) {
    $reply = "Switching to light mode!  Bright and clear.";
    $action = 'set_theme_light';
} elseif (preg_match('/(how are you|how are u)/i', $last_message)) {
    $reply = "I'm doing great, thank you for asking! I'm here 24/7 to help you build your dream PC. What can I do for you today?";
} elseif (preg_match('/(who made you|who created you|who is your creator)/i', $last_message)) {
    $reply = "I was created by the developer of PC Builder BD to assist users with finding components, prices, and PC building advice!";
} elseif (preg_match('/(thank you|thanks)/i', $last_message)) {
    $reply = "You're very welcome! Let me know if you need help with anything else.";
} elseif (preg_match('/(bye|goodbye|see you)/i', $last_message)) {
    $reply = "Goodbye! Happy PC building!";
} elseif (preg_match('/(what can you do|help)/i', $last_message)) {
    $reply = "I can do quite a few things!\n\n- Answer common PC building questions (bottlenecks, PSUs, DDR4 vs DDR5)\n- Check live prices and stock (e.g., 'price of RTX 4070')\n- Change the website theme (e.g., 'turn on night mode')\n- Give catalog stats ('how many products')\n\nHow can I help you right now?";
} elseif (preg_match('/(tell me a joke|make me laugh)/i', $last_message)) {
    $reply = "Why did the PC go to the doctor?\nBecause it had a terminal illness!";
} elseif (preg_match('/check compatibility/i', $last_message)) {
    $reply = "To check compatibility between two components, just ask me directly!\n\n**Example:** *'Is Intel Core i5 14600K compatible with ASUS ROG B650?'*";
} elseif (preg_match('/(recommend a|suggest a)\s+(cpu|gpu|motherboard|ram|storage|psu)/i', $last_message, $m)) {
    $cat = strtoupper($m[2]);
    $reply = "I'd be happy to recommend a {$cat}! You can be more specific by asking for budget constraints or extreme values.\n\n**Try asking:**\n- *'{$cat}s under 50000'*\n- *'Cheapest {$cat}'*\n- *'Most expensive {$cat}'*";
} elseif (preg_match('/compare (?:gpu|cpu|motherboard|ram|storage|psu)?\s*prices/i', $last_message)) {
    $reply = "I can compare prices by finding the cheapest or most expensive items for you, or listing items under a certain budget.\n\n**Try asking:**\n- *'Cheapest GPU'*\n- *'Most expensive CPU'*\n- *'GPUs under 50000'*";
} elseif (preg_match('/(summarize|show)\s+(pc\s+)?builds/i', $last_message)) {
    $reply = "To create and summarize your own PC builds, head over to our **Custom Builder** page!\n\nIf you need help picking parts right here, just ask me about specific components (e.g., *'CPUs under 30000'* or *'Show me Corsair products'*).";
} else {
    $search = trim($original_message, " ?.,!");
    $fillers = ['the', 'a', 'an', 'and', 'for', 'with', 'about', 'is', 'are', 'what', 'where', 'who', 'how', 'show', 'tell', 'me', 'please', 'do', 'you', 'have', 'any'];
    $clean_words = [];
    foreach (explode(' ', strtolower($search)) as $w) {
        $w = trim($w);
        if (!empty($w) && !in_array($w, $fillers)) {
            $clean_words[] = $w;
        }
    }
    
    if (!empty($clean_words)) {
        $term = '%' . implode('%', $clean_words) . '%';
        $rows = db_query(component_base_sql() . " WHERE c.component_name LIKE ? OR c.brand LIKE ? OR c.type LIKE ? LIMIT 15", [$term, $term, $term]);
        
        if ($rows) {
            $reply = "**Top Database Search Results for '" . sanitise($search) . "':**\n\n";
            foreach ($rows as $r) {
                $price = $r['price_bdt'] > 0 ? format_bdt((float)$r['price_bdt']) : "Price Unlisted";
                $stock = normalize_stock($r['stock_status_raw'] ?? '') === 'in_stock' ? "In Stock" : "Out of Stock";
                
                $spec_details = "";
                if (!empty($r['socket'])) {
                    $spec_details .= " | Socket: `{$r['socket']}`";
                }
                if (!empty($r['ram_gen'])) {
                    $spec_details .= " | RAM: `{$r['ram_gen']}`";
                }
                if ($r['tdp_watts'] > 0) {
                    $spec_details .= " | TDP: `{$r['tdp_watts']}W`";
                }
                if ($r['benchmark_score'] > 0) {
                    $spec_details .= " | Score: `" . number_format($r['benchmark_score'], 0) . "`";
                }
                if (!empty($r['screen_size'])) {
                    $spec_details .= " | Screen: `{$r['screen_size']}\"`";
                }
                if (!empty($r['refresh_rate'])) {
                    $spec_details .= " | Refresh: `{$r['refresh_rate']}Hz`";
                }
                
                $reply .= "- **" . sanitise($r['name']) . "**\n  Price: **{$price}** | {$stock} | Category: " . sanitise($r['category']) . $spec_details . "\n\n";
            }
            $reply .= "Would you like me to compare any of these, or show their detailed specifications?";
        }
    }
}

json_response(['content' => $reply, 'model' => 'database-bot', 'action' => $action]);