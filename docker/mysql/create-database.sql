-- Create the main database if it doesn't exist
CREATE DATABASE IF NOT EXISTS `ceosofts_db_R1`;

-- Grant all privileges to the database user
GRANT ALL PRIVILEGES ON `ceosofts_db_R1`.* TO '${DB_USERNAME}'@'%';

-- Create test database for automated testing
CREATE DATABASE IF NOT EXISTS `ceosofts_db_R1_test`;
GRANT ALL PRIVILEGES ON `ceosofts_db_R1_test`.* TO '${DB_USERNAME}'@'%';

-- Make sure privileges are applied
FLUSH PRIVILEGES;
