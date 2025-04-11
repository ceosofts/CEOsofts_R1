-- SQL script to fix Telescope migrations if they exist already
-- Run this script only if you have issues with Telescope migrations

-- Step 1: Check if telescope_entries table exists
SET @table_check = (SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'telescope_entries');

-- Step 2: Insert telescope migrations into migrations table if tables exist but migrations are not recorded
INSERT INTO migrations (migration, batch)
SELECT 'create_telescope_entries_table', (SELECT MAX(batch) FROM migrations) + 1
FROM DUAL
WHERE @table_check > 0
  AND NOT EXISTS (SELECT 1 FROM migrations WHERE migration = 'create_telescope_entries_table');

-- Step 3: Check if telescope_monitoring table exists
SET @monitoring_check = (SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'telescope_monitoring');

-- Step 4: Insert telescope monitoring migration
INSERT INTO migrations (migration, batch)
SELECT 'telescope_1', (SELECT MAX(batch) FROM migrations) + 1
FROM DUAL
WHERE @monitoring_check > 0
  AND NOT EXISTS (SELECT 1 FROM migrations WHERE migration = 'telescope_1');

-- Step 5: Check if telescope tag tables exist
SET @tags_check = (SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'telescope_entries_tags');

-- Step 6: Insert telescope tags migration
INSERT INTO migrations (migration, batch)
SELECT 'telescope_2', (SELECT MAX(batch) FROM migrations) + 1
FROM DUAL
WHERE @tags_check > 0
  AND NOT EXISTS (SELECT 1 FROM migrations WHERE migration = 'telescope_2');
