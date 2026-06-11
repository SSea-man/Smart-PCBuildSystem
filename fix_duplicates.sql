-- ============================================================
--  Smart PC Build System — Duplicate Component Cleanup
--  Safe approach:
--   1. Remap all FK references from duplicate IDs to canonical (MIN) ID
--   2. Delete duplicate rows keeping only the lowest component_id
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;

-- Step 1: Create a temp mapping table: duplicate_id -> canonical_id
CREATE TEMPORARY TABLE IF NOT EXISTS dup_map AS
SELECT
    dup.component_id  AS dup_id,
    keep.canonical_id AS keep_id
FROM component dup
JOIN (
    SELECT component_name, type, MIN(component_id) AS canonical_id
    FROM component
    GROUP BY component_name, type
    HAVING COUNT(*) > 1
) keep
  ON dup.component_name = keep.component_name
 AND dup.type           = keep.type
 AND dup.component_id  <> keep.canonical_id;

-- Step 2: Re-point buildcomponent rows to canonical IDs
UPDATE buildcomponent bc
JOIN dup_map dm ON bc.component_id = dm.dup_id
SET bc.component_id = dm.keep_id
WHERE EXISTS (
    SELECT 1 FROM buildcomponent bc2
    WHERE bc2.build_id = bc.build_id AND bc2.component_id = dm.keep_id
) = 0;

-- Delete any buildcomponent rows that now point to a dup_id
-- (i.e., a canonical entry already exists for that build)
DELETE bc
FROM buildcomponent bc
JOIN dup_map dm ON bc.component_id = dm.dup_id;

-- Step 3: Re-point storeavailability rows to canonical IDs
UPDATE storeavailability sa
JOIN dup_map dm ON sa.component_id = dm.dup_id
SET sa.component_id = dm.keep_id
WHERE EXISTS (
    SELECT 1 FROM storeavailability sa2
    WHERE sa2.store_id = sa.store_id AND sa2.component_id = dm.keep_id
) = 0;

-- Delete any storeavailability rows that still reference duplicate IDs
DELETE sa
FROM storeavailability sa
JOIN dup_map dm ON sa.component_id = dm.dup_id;

-- Step 4: Re-point pricetracking rows (if any)
UPDATE pricetracking pt
JOIN dup_map dm ON pt.component_id = dm.dup_id
SET pt.component_id = dm.keep_id;

-- Step 5: Re-point watchlist rows (if any)
UPDATE watchlist w
JOIN dup_map dm ON w.component_id = dm.dup_id
SET w.component_id = dm.keep_id;

-- Step 6: Re-point upgradesuggestion rows (if any)
UPDATE upgradesuggestion u
JOIN dup_map dm ON u.component_id = dm.dup_id
SET u.component_id = dm.keep_id;

-- Step 7: DELETE the duplicate component rows
DELETE FROM component
WHERE component_id IN (SELECT dup_id FROM dup_map);

-- Step 8: Remove stale/junk component types from old seed attempts
DELETE FROM component
WHERE type IN ('RAM (temporary memory)', 'GPU (graphics)', 'Storage (HDD/SSD)', 'Motherboard (connection)', 'PSU (power)', 'CPU (processing)');

SET FOREIGN_KEY_CHECKS = 1;

-- Final verification
SELECT type, COUNT(*) as total, COUNT(DISTINCT component_name) as unique_names
FROM component
GROUP BY type
ORDER BY type;
