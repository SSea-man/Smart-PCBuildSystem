<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';

$components = [
    ['Intel Core i9 14900K', 'CPU', 'Intel', 100, 125, 'LGA1700', 'DDR5', 0, 85000],
    ['Intel Core i7 14700K', 'CPU', 'Intel', 95, 125, 'LGA1700', 'DDR5', 0, 55000],
    ['Intel Core i5 14600K', 'CPU', 'Intel', 88, 125, 'LGA1700', 'DDR5', 0, 38000],
    ['Intel Core i5 13400F', 'CPU', 'Intel', 75, 65, 'LGA1700', 'DDR4', 0, 22000],
    ['Intel Core i3 13100F', 'CPU', 'Intel', 60, 58, 'LGA1700', 'DDR4', 0, 13500],
    ['Intel Core i9 13900K', 'CPU', 'Intel', 98, 125, 'LGA1700', 'DDR5', 0, 68000],
    ['Intel Core i7 13700K', 'CPU', 'Intel', 92, 125, 'LGA1700', 'DDR5', 0, 48000],
    ['Intel Core Ultra 9 285K', 'CPU', 'Intel', 100, 125, 'LGA1851', 'DDR5', 0, 95000],
    ['Intel Core Ultra 7 265K', 'CPU', 'Intel', 96, 125, 'LGA1851', 'DDR5', 0, 65000],
    ['AMD Ryzen 9 9950X', 'CPU', 'AMD', 100, 170, 'AM5', 'DDR5', 0, 92000],
    ['AMD Ryzen 9 7950X3D', 'CPU', 'AMD', 98, 120, 'AM5', 'DDR5', 0, 88000],
    ['AMD Ryzen 7 7800X3D', 'CPU', 'AMD', 95, 120, 'AM5', 'DDR5', 0, 48000],
    ['AMD Ryzen 5 7600X', 'CPU', 'AMD', 85, 105, 'AM5', 'DDR5', 0, 28000],
    ['AMD Ryzen 5 5600X', 'CPU', 'AMD', 70, 65, 'AM4', 'DDR4', 0, 18500],
    ['AMD Ryzen 7 5700X', 'CPU', 'AMD', 75, 65, 'AM4', 'DDR4', 0, 24000],
    ['AMD Ryzen 9 5900X', 'CPU', 'AMD', 85, 105, 'AM4', 'DDR4', 0, 42000],

    ['Noctua NH-D15', 'CPU Cooler', 'Noctua', 95, 0, '', '', 0, 12000],
    ['Cooler Master Hyper 212', 'CPU Cooler', 'Cooler Master', 70, 0, '', '', 0, 4500],
    ['Corsair iCUE H150i Elite', 'CPU Cooler', 'Corsair', 90, 0, '', '', 0, 22000],
    ['NZXT Kraken Elite 360', 'CPU Cooler', 'NZXT', 92, 0, '', '', 0, 28000],
    ['DeepCool AK620', 'CPU Cooler', 'DeepCool', 85, 0, '', '', 0, 7500],
    ['Lian Li Galahad II Trinity', 'CPU Cooler', 'Lian Li', 88, 0, '', '', 0, 18500],
    ['Arctic Liquid Freezer II 360', 'CPU Cooler', 'Arctic', 94, 0, '', '', 0, 14000],
    ['be quiet! Dark Rock Pro 4', 'CPU Cooler', 'be quiet!', 90, 0, '', '', 0, 11000],
    ['Thermalright Peerless Assassin', 'CPU Cooler', 'Thermalright', 88, 0, '', '', 0, 5500],
    ['MSI MAG CORELIQUID 240R V2', 'CPU Cooler', 'MSI', 80, 0, '', '', 0, 10500],

    ['ASUS ROG Maximus Z790 Hero', 'Motherboard', 'ASUS', 95, 0, 'LGA1700', 'DDR5', 0, 85000],
    ['MSI MAG Z790 TOMAHAWK WIFI', 'Motherboard', 'MSI', 88, 0, 'LGA1700', 'DDR5', 0, 35000],
    ['Gigabyte B760M DS3H', 'Motherboard', 'Gigabyte', 75, 0, 'LGA1700', 'DDR4', 0, 14500],
    ['ASUS TUF GAMING B760-PLUS', 'Motherboard', 'ASUS', 80, 0, 'LGA1700', 'DDR5', 0, 24000],
    ['ASRock Z690 Phantom Gaming', 'Motherboard', 'ASRock', 82, 0, 'LGA1700', 'DDR4', 0, 22000],
    ['Gigabyte Z890 AORUS MASTER', 'Motherboard', 'Gigabyte', 98, 0, 'LGA1851', 'DDR5', 0, 92000],
    ['ASUS ROG CROSSHAIR X670E', 'Motherboard', 'ASUS', 98, 0, 'AM5', 'DDR5', 0, 88000],
    ['MSI B650M MORTAR WIFI', 'Motherboard', 'MSI', 85, 0, 'AM5', 'DDR5', 0, 26000],
    ['Gigabyte B650 AORUS ELITE AX', 'Motherboard', 'Gigabyte', 88, 0, 'AM5', 'DDR5', 0, 32000],
    ['ASUS PRIME B650-PLUS', 'Motherboard', 'ASUS', 80, 0, 'AM5', 'DDR5', 0, 22000],
    ['MSI B550 TOMAHAWK', 'Motherboard', 'MSI', 75, 0, 'AM4', 'DDR4', 0, 18500],
    ['Gigabyte B550M DS3H', 'Motherboard', 'Gigabyte', 65, 0, 'AM4', 'DDR4', 0, 11500],
    ['ASUS ROG STRIX B550-F', 'Motherboard', 'ASUS', 80, 0, 'AM4', 'DDR4', 0, 24000],
    ['ASRock X570 Steel Legend', 'Motherboard', 'ASRock', 82, 0, 'AM4', 'DDR4', 0, 28000],

    ['Corsair Vengeance 32GB (2x16GB) DDR5 6000MHz', 'RAM', 'Corsair', 90, 0, '', 'DDR5', 0, 15500],
    ['G.Skill Trident Z5 RGB 32GB DDR5 6400MHz', 'RAM', 'G.Skill', 95, 0, '', 'DDR5', 0, 18500],
    ['Kingston FURY Beast 16GB DDR5 5200MHz', 'RAM', 'Kingston', 80, 0, '', 'DDR5', 0, 7500],
    ['Corsair Vengeance LPX 16GB DDR4 3200MHz', 'RAM', 'Corsair', 70, 0, '', 'DDR4', 0, 4800],
    ['G.Skill Ripjaws V 32GB DDR4 3600MHz', 'RAM', 'G.Skill', 80, 0, '', 'DDR4', 0, 9500],
    ['TeamGroup T-Force Delta RGB 32GB DDR5 6000', 'RAM', 'TeamGroup', 88, 0, '', 'DDR5', 0, 14500],
    ['Crucial Pro 64GB (2x32GB) DDR5 5600MHz', 'RAM', 'Crucial', 90, 0, '', 'DDR5', 0, 28000],
    ['Patriot Viper Venom 32GB DDR5 6200MHz', 'RAM', 'Patriot', 92, 0, '', 'DDR5', 0, 16000],
    ['AORUS Memory 32GB DDR5 6000MHz', 'RAM', 'Gigabyte', 88, 0, '', 'DDR5', 0, 16500],
    ['Lexar Ares RGB 32GB DDR5 6400MHz', 'RAM', 'Lexar', 90, 0, '', 'DDR5', 0, 14000],

    ['Samsung 990 PRO 2TB PCIe 4.0 NVMe', 'Storage', 'Samsung', 98, 0, '', '', 0, 24000],
    ['WD Black SN850X 1TB PCIe 4.0 NVMe', 'Storage', 'Western Digital', 95, 0, '', '', 0, 11500],
    ['Crucial P3 Plus 1TB PCIe 4.0 NVMe', 'Storage', 'Crucial', 80, 0, '', '', 0, 7500],
    ['Kingston NV2 500GB PCIe 4.0 NVMe', 'Storage', 'Kingston', 70, 0, '', '', 0, 4500],
    ['Seagate Barracuda 2TB 7200RPM HDD', 'Storage', 'Seagate', 40, 0, '', '', 0, 6500],
    ['Samsung 980 1TB NVMe', 'Storage', 'Samsung', 85, 0, '', '', 0, 9500],
    ['Corsair MP600 PRO 2TB PCIe 4.0', 'Storage', 'Corsair', 96, 0, '', '', 0, 22000],
    ['AORUS Gen4 7000s 1TB', 'Storage', 'Gigabyte', 90, 0, '', '', 0, 12500],
    ['Lexar NM790 2TB PCIe 4.0 NVMe', 'Storage', 'Lexar', 92, 0, '', '', 0, 16000],
    ['WD Blue 1TB SATA SSD', 'Storage', 'Western Digital', 60, 0, '', '', 0, 8500],

    ['NVIDIA RTX 5090 32GB', 'Graphics Card', 'NVIDIA', 100, 500, '', '', 0, 350000],
    ['NVIDIA RTX 4090 24GB', 'Graphics Card', 'NVIDIA', 98, 450, '', '', 0, 280000],
    ['NVIDIA RTX 4080 Super 16GB', 'Graphics Card', 'NVIDIA', 95, 320, '', '', 0, 145000],
    ['NVIDIA RTX 4070 Super 12GB', 'Graphics Card', 'NVIDIA', 88, 220, '', '', 0, 85000],
    ['NVIDIA RTX 4060 Ti 8GB', 'Graphics Card', 'NVIDIA', 80, 160, '', '', 0, 55000],
    ['NVIDIA RTX 4060 8GB', 'Graphics Card', 'NVIDIA', 75, 115, '', '', 0, 38000],
    ['AMD Radeon RX 7900 XTX 24GB', 'Graphics Card', 'AMD', 96, 355, '', '', 0, 135000],
    ['AMD Radeon RX 7900 XT 20GB', 'Graphics Card', 'AMD', 92, 315, '', '', 0, 105000],
    ['AMD Radeon RX 7800 XT 16GB', 'Graphics Card', 'AMD', 86, 263, '', '', 0, 72000],
    ['AMD Radeon RX 7600 8GB', 'Graphics Card', 'AMD', 70, 165, '', '', 0, 35000],

    ['Corsair RM1000x 1000W 80+ Gold', 'Power Supply', 'Corsair', 95, 0, '', '', 1000, 22000],
    ['SeaSonic FOCUS GX-850 850W 80+ Gold', 'Power Supply', 'SeaSonic', 92, 0, '', '', 850, 16500],
    ['Cooler Master MWE Gold 750 V2', 'Power Supply', 'Cooler Master', 85, 0, '', '', 750, 11500],
    ['EVGA SuperNOVA 650 GT 650W', 'Power Supply', 'EVGA', 80, 0, '', '', 650, 9500],
    ['Corsair CV550 550W 80+ Bronze', 'Power Supply', 'Corsair', 65, 0, '', '', 550, 5500],
    ['MSI MPG A1000G PCIE5 1000W', 'Power Supply', 'MSI', 96, 0, '', '', 1000, 24000],
    ['Be Quiet! Straight Power 12 850W', 'Power Supply', 'Be Quiet!', 94, 0, '', '', 850, 19000],
    ['Thermaltake Toughpower GF3 1200W', 'Power Supply', 'Thermaltake', 98, 0, '', '', 1200, 28000],
    ['DeepCool PQ850M 850W 80+ Gold', 'Power Supply', 'DeepCool', 88, 0, '', '', 850, 14000],
    ['Gigabyte P650B 650W 80+ Bronze', 'Power Supply', 'Gigabyte', 70, 0, '', '', 650, 6500],

    ['Lian Li PC-O11 Dynamic', 'Casing', 'Lian Li', 95, 0, '', '', 0, 16500],
    ['NZXT H9 Flow', 'Casing', 'NZXT', 94, 0, '', '', 0, 18500],
    ['Corsair 4000D Airflow', 'Casing', 'Corsair', 90, 0, '', '', 0, 11500],
    ['Phanteks Eclipse G360A', 'Casing', 'Phanteks', 85, 0, '', '', 0, 9500],
    ['Cooler Master MasterBox TD500', 'Casing', 'Cooler Master', 80, 0, '', '', 0, 10500],
    ['Montech AIR 903 MAX', 'Casing', 'Montech', 82, 0, '', '', 0, 8500],
    ['Fractal Design North', 'Casing', 'Fractal Design', 96, 0, '', '', 0, 22000],
    ['Antec NX410', 'Casing', 'Antec', 75, 0, '', '', 0, 6500],
    ['DeepCool CH560 Digital', 'Casing', 'DeepCool', 88, 0, '', '', 0, 12500],
    ['MSI MAG FORGE 112R', 'Casing', 'MSI', 78, 0, '', '', 0, 7500],

    ['LG 27GP850-B 27" 165Hz 1440p', 'Monitor', 'LG', 95, 0, '', '', 0, 48000],
    ['Samsung Odyssey G7 27" 240Hz', 'Monitor', 'Samsung', 96, 0, '', '', 0, 68000],
    ['Gigabyte M27Q 27" 170Hz 1440p', 'Monitor', 'Gigabyte', 90, 0, '', '', 0, 42000],
    ['ASUS TUF Gaming VG27AQ 27"', 'Monitor', 'ASUS', 88, 0, '', '', 0, 45000],
    ['AOC 24G2SP 24" 165Hz 1080p', 'Monitor', 'AOC', 80, 0, '', '', 0, 22000],
    ['MSI Optix G241 24" 144Hz', 'Monitor', 'MSI', 78, 0, '', '', 0, 21000],
    ['BenQ Zowie XL2546K 24.5" 240Hz', 'Monitor', 'BenQ', 92, 0, '', '', 0, 55000],
    ['Dell S2721DGF 27" 165Hz', 'Monitor', 'Dell', 90, 0, '', '', 0, 48000],
    ['Acer Nitro VG271U 27" 144Hz', 'Monitor', 'Acer', 82, 0, '', '', 0, 32000],
    ['ViewSonic VX2758-2KP-MHD 27"', 'Monitor', 'ViewSonic', 80, 0, '', '', 0, 35000],

    ['Lian Li UNI FAN SL-INF 120 (3-Pack)', 'Casing Cooler', 'Lian Li', 95, 0, '', '', 0, 11500],
    ['Corsair LL120 RGB (3-Pack)', 'Casing Cooler', 'Corsair', 90, 0, '', '', 0, 10500],
    ['Noctua NF-A12x25 PWM', 'Casing Cooler', 'Noctua', 98, 0, '', '', 0, 3500],
    ['Arctic P12 PWM PST (5-Pack)', 'Casing Cooler', 'Arctic', 92, 0, '', '', 0, 4500],
    ['Cooler Master MasterFan MF120 Halo', 'Casing Cooler', 'Cooler Master', 85, 0, '', '', 0, 2200],
    ['NZXT F120 RGB (3-Pack)', 'Casing Cooler', 'NZXT', 88, 0, '', '', 0, 8500],
    ['DeepCool FC120 (3-Pack)', 'Casing Cooler', 'DeepCool', 82, 0, '', '', 0, 4200],
    ['Be Quiet! Silent Wings 4 120mm', 'Casing Cooler', 'Be Quiet!', 94, 0, '', '', 0, 2800],
    ['Thermalright TL-C12C (3-Pack)', 'Casing Cooler', 'Thermalright', 80, 0, '', '', 0, 1500],
    ['MSI MAG MAX F12A-3H', 'Casing Cooler', 'MSI', 80, 0, '', '', 0, 3800],

    ['Keychron Q1 Pro Wireless Mechanical', 'Keyboard', 'Keychron', 96, 0, '', '', 0, 22000],
    ['Corsair K70 RGB PRO Mechanical', 'Keyboard', 'Corsair', 92, 0, '', '', 0, 18500],
    ['Logitech G Pro X TKL', 'Keyboard', 'Logitech', 94, 0, '', '', 0, 16500],
    ['Razer Huntsman V3 Pro TKL', 'Keyboard', 'Razer', 95, 0, '', '', 0, 24000],
    ['SteelSeries Apex Pro TKL', 'Keyboard', 'SteelSeries', 96, 0, '', '', 0, 21000],
    ['Royal Kludge RK61 Wireless', 'Keyboard', 'Royal Kludge', 80, 0, '', '', 0, 4500],
    ['Redragon K552 Kumara', 'Keyboard', 'Redragon', 75, 0, '', '', 0, 3200],
    ['HyperX Alloy Origins Core', 'Keyboard', 'HyperX', 88, 0, '', '', 0, 11500],
    ['Asus ROG Azoth Wireless', 'Keyboard', 'ASUS', 98, 0, '', '', 0, 28000],
    ['Akko 3098B Plus Wireless', 'Keyboard', 'Akko', 85, 0, '', '', 0, 9500],

    ['Logitech G Pro X Superlight 2', 'Mouse', 'Logitech', 98, 0, '', '', 0, 18500],
    ['Razer DeathAdder V3 Pro', 'Mouse', 'Razer', 96, 0, '', '', 0, 16500],
    ['Zowie EC2-CW Wireless', 'Mouse', 'Zowie', 95, 0, '', '', 0, 15500],
    ['Endgame Gear XM2we', 'Mouse', 'Endgame Gear', 92, 0, '', '', 0, 9500],
    ['Lamzu Atlantis Mini', 'Mouse', 'Lamzu', 94, 0, '', '', 0, 11000],
    ['Glorious Model O Wireless', 'Mouse', 'Glorious', 88, 0, '', '', 0, 8500],
    ['SteelSeries Aerox 3 Wireless', 'Mouse', 'SteelSeries', 85, 0, '', '', 0, 7500],
    ['Corsair Harpoon RGB Wireless', 'Mouse', 'Corsair', 80, 0, '', '', 0, 5500],
    ['Razer Viper Mini', 'Mouse', 'Razer', 85, 0, '', '', 0, 4200],
    ['Logitech G304 Lightspeed', 'Mouse', 'Logitech', 88, 0, '', '', 0, 4500],

    ['Logitech Z906 5.1 Surround', 'Speaker & Home Theater', 'Logitech', 95, 0, '', '', 0, 35000],
    ['Edifier R1280DB Powered Bookshelf', 'Speaker & Home Theater', 'Edifier', 92, 0, '', '', 0, 12500],
    ['Creative Pebble V3', 'Speaker & Home Theater', 'Creative', 80, 0, '', '', 0, 4500],
    ['Razer Leviathan V2 Soundbar', 'Speaker & Home Theater', 'Razer', 88, 0, '', '', 0, 26000],
    ['Microlab X2 2.1 Speaker', 'Speaker & Home Theater', 'Microlab', 75, 0, '', '', 0, 6500],
    ['Fantech GS203 Beat', 'Speaker & Home Theater', 'Fantech', 65, 0, '', '', 0, 1500],
    ['Logitech Z623 2.1 THX', 'Speaker & Home Theater', 'Logitech', 90, 0, '', '', 0, 18500],
    ['Edifier S3000Pro', 'Speaker & Home Theater', 'Edifier', 98, 0, '', '', 0, 65000],

    ['HyperX Cloud III Wireless', 'Headphone', 'HyperX', 92, 0, '', '', 0, 16500],
    ['Logitech G Pro X 2 Lightspeed', 'Headphone', 'Logitech', 95, 0, '', '', 0, 24000],
    ['Razer BlackShark V2 Pro', 'Headphone', 'Razer', 94, 0, '', '', 0, 18500],
    ['SteelSeries Arctis Nova 7', 'Headphone', 'SteelSeries', 90, 0, '', '', 0, 19500],
    ['Corsair HS80 RGB Wireless', 'Headphone', 'Corsair', 88, 0, '', '', 0, 15500],
    ['Audio-Technica ATH-M50x', 'Headphone', 'Audio-Technica', 95, 0, '', '', 0, 18000],
    ['Sennheiser HD 560S', 'Headphone', 'Sennheiser', 96, 0, '', '', 0, 22000],
    ['Fantech HG11 Captain 7.1', 'Headphone', 'Fantech', 70, 0, '', '', 0, 2800],
    ['Havit H2002d Gaming Headset', 'Headphone', 'Havit', 75, 0, '', '', 0, 2200],

    ['TP-Link Archer TX3000E WiFi 6 PCIe', 'Wifi Adapter / LAN Card', 'TP-Link', 95, 0, '', '', 0, 5500],
    ['Asus PCE-AX58BT WiFi 6 PCIe', 'Wifi Adapter / LAN Card', 'ASUS', 94, 0, '', '', 0, 6500],
    ['TP-Link TL-WN823N Mini USB', 'Wifi Adapter / LAN Card', 'TP-Link', 75, 0, '', '', 0, 800],
    ['D-Link DWA-182 AC1200 USB', 'Wifi Adapter / LAN Card', 'D-Link', 80, 0, '', '', 0, 2200],
    ['Netgear Nighthawk A7000 USB', 'Wifi Adapter / LAN Card', 'Netgear', 90, 0, '', '', 0, 7500],
    ['Mercusys MU6H AC650 USB', 'Wifi Adapter / LAN Card', 'Mercusys', 70, 0, '', '', 0, 950],

    ['Kaspersky Total Security 1 User 1 Year', 'Anti Virus', 'Kaspersky', 98, 0, '', '', 0, 1200],
    ['Bitdefender Internet Security 1 User', 'Anti Virus', 'Bitdefender', 96, 0, '', '', 0, 1100],
    ['ESET NOD32 Antivirus 1 User', 'Anti Virus', 'ESET', 95, 0, '', '', 0, 900],
    ['Norton 360 Deluxe 3 Devices', 'Anti Virus', 'Norton', 94, 0, '', '', 0, 2500],
    ['McAfee Total Protection 1 User', 'Anti Virus', 'McAfee', 85, 0, '', '', 0, 850],

    ['Apollo 1200VA Offline UPS', 'UPS', 'Apollo', 80, 0, '', '', 0, 6500],
    ['Digital X 1200VA UPS', 'UPS', 'Digital X', 78, 0, '', '', 0, 5800],
    ['Power Guard 1200VA UPS', 'UPS', 'Power Guard', 75, 0, '', '', 0, 5500],
    ['APC Back-UPS 1200VA', 'UPS', 'APC', 95, 0, '', '', 0, 12500],
    ['MaxGreen 1200VA Offline UPS', 'UPS', 'MaxGreen', 82, 0, '', '', 0, 6200],
    ['Vertiv Liebert ItON 1000VA', 'UPS', 'Vertiv', 90, 0, '', '', 0, 9500],
    ['Walton WUPS 1200VA', 'UPS', 'Walton', 75, 0, '', '', 0, 5200]
];

$pdo = get_db();

// Ensure column types are correct
$pdo->exec("ALTER TABLE component MODIFY type VARCHAR(100) NOT NULL;");
$pdo->exec("ALTER TABLE component MODIFY component_name VARCHAR(255) NOT NULL;");

// Add unique constraint if not exists (prevents duplicates on re-run)
$pdo->exec("ALTER TABLE component ADD UNIQUE INDEX IF NOT EXISTS uq_component_name_type (component_name, type);");

$pdo->beginTransaction();

try {
    // INSERT IGNORE skips silently if (component_name, type) already exists
    $stmt = $pdo->prepare("
        INSERT IGNORE INTO component
            (component_name, type, brand, benchmark_score, tdp_watts, socket, ram_gen, psu_wattage)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $store_stmt = $pdo->prepare("
        INSERT IGNORE INTO storeavailability
            (component_id, store_id, price, stock_status)
        VALUES (?, 1, ?, 'in_stock')
    ");

    $inserted = 0;
    $skipped  = 0;

    foreach ($components as $c) {
        $stmt->execute([$c[0], $c[1], $c[2], $c[3], $c[4], $c[5], $c[6], $c[7]]);
        $comp_id = $pdo->lastInsertId();

        if ($comp_id) {
            $store_stmt->execute([$comp_id, $c[8]]);
            $inserted++;
        } else {
            $skipped++;
        }
    }

    $pdo->commit();
    echo "Seeding complete: $inserted inserted, $skipped skipped (already exist).\n";
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "Failed: " . $e->getMessage() . "\n";
}
