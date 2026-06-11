-- ==========================================
-- Smart PC Build System - SQL Query Reference
-- ==========================================
-- This file contains the core SQL queries used in the project,
-- demonstrating Joins, Subqueries, Aggregation, and Grouping.

-- ---------------------------------------------------------
-- 1. Component Catalog Query (Complex JOIN & Aggregation)
--    Located in: includes/functions.php -> component_base_sql()
--    Purpose: Fetches all components, standardizes their category using CASE,
--             and finds the absolute lowest price across multiple stores using 
--             a subquery with GROUP BY.
-- ---------------------------------------------------------
SELECT
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
        WHEN c.type IN ('CPU Cooler', 'Casing Cooler') THEN 'Cooling'
        ELSE c.type
    END                                             AS category,
    c.brand, c.benchmark_score, c.tdp_watts, c.socket,
    c.ram_gen, c.form_factor, c.length_mm, c.height_mm,
    c.m2_slots, c.sata_ports, c.ram_slots, c.psu_wattage,
    c.storage_interface, c.image_url,
    COALESCE(sa.price, 0)                           AS price_bdt,
    COALESCE(sa.stock_status, 'Out of Stock')       AS stock_status_raw,
    COALESCE(s.store_name, '')                      AS retailer
FROM component c
LEFT JOIN (
    SELECT component_id, MIN(price) AS price, stock_status, store_id
    FROM storeavailability
    GROUP BY component_id
) sa ON sa.component_id = c.component_id
LEFT JOIN store s ON s.store_id = sa.store_id
ORDER BY c.benchmark_score DESC;

-- ---------------------------------------------------------
-- 2. Forum Feed with Nested Subqueries & Data Aggregation
--    Located in: forum.php
--    Purpose: Retrieves forum posts, joins the author's details, 
--             and uses nested subqueries to calculate total comments,
--             total upvote scores, the current user's upvote status, 
--             and concatenates all tags into a single string.
-- ---------------------------------------------------------
SELECT 
    p.post_id, p.user_id, p.title, p.content, p.created_at, p.image_path,
    u.user_name,
    c.name AS community_name,
    (SELECT COUNT(*) FROM comment comm WHERE comm.post_id = p.post_id) AS comment_count,
    (SELECT COUNT(*) FROM vote v WHERE v.post_id = p.post_id AND v.vote_type = 'upvote') AS score,
    (SELECT GROUP_CONCAT(t.name SEPARATOR ',') FROM posttag pt JOIN tag t ON pt.tag_id = t.tag_id WHERE pt.post_id = p.post_id) AS tags,
    (SELECT COUNT(*) FROM vote v WHERE v.post_id = p.post_id AND v.user_id = 1 AND v.vote_type = 'upvote') AS user_vote
FROM post p
JOIN user u ON p.user_id = u.user_id
LEFT JOIN community c ON p.community_id = c.community_id
ORDER BY p.created_at DESC
LIMIT 20 OFFSET 0;


-- ---------------------------------------------------------
-- 3. Watchlist Fetcher (Multiple Joins)
--    Located in: dashboard.php
--    Purpose: Gets the user's saved components alongside the
--             latest dynamic pricing from the store tables.
-- ---------------------------------------------------------
SELECT 
    c.component_id as id, 
    c.component_name as name, 
    c.type,
    COALESCE(sa.price,0) as price_bdt, 
    COALESCE(s.store_name,"") as retailer,
    w.added_at
FROM watchlist w
JOIN component c ON c.component_id = w.component_id
LEFT JOIN (
    SELECT component_id, MIN(price) as price, store_id 
    FROM storeavailability 
    GROUP BY component_id
) sa ON sa.component_id = c.component_id
LEFT JOIN store s ON s.store_id = sa.store_id
WHERE w.user_id = 1 
ORDER BY w.added_at DESC 
LIMIT 8;


-- ---------------------------------------------------------
-- 4. Price History Trend Analysis (Date Aggregation)
--    Located in: dashboard.php
--    Purpose: Uses DATE() casting and DATE_SUB() to retrieve 
--             price tracking history strictly within the last 30 days.
-- ---------------------------------------------------------
SELECT DATE(changed_at) as d, new_price 
FROM pricetracking
WHERE component_id = 15
AND changed_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) 
ORDER BY d ASC;


-- ---------------------------------------------------------
-- 5. Forum Community Discovery
--    Located in: forum.php
--    Purpose: Ranks forum communities by member count.
-- ---------------------------------------------------------
SELECT 
    c.community_id, c.name,
    (SELECT COUNT(*) FROM community_member cm WHERE cm.community_id = c.community_id) AS member_count,
    (SELECT COUNT(*) FROM community_member cm WHERE cm.community_id = c.community_id AND cm.user_id = 1) AS is_joined
FROM community c
ORDER BY member_count DESC 
LIMIT 5;
