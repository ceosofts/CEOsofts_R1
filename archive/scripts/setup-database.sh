#!/bin/bash

# สคริปต์นี้ช่วยในการตั้งค่าฐานข้อมูลสำหรับ CEOsofts_R1
echo "===== CEOsofts Database Setup ====="

# กำหนดตัวแปร
DB_NAME="ceosofts_db_R1"
DB_TEST="${DB_NAME}_test"

# ตรวจสอบการเชื่อมต่อ MySQL
echo "Checking MySQL connection..."
if ! command -v mysql &> /dev/null; then
    echo "MySQL client not found. Please install MySQL client first."
    exit 1
fi

# สร้างฐานข้อมูล
echo "Creating databases..."
mysql -u root -e "CREATE DATABASE IF NOT EXISTS \`${DB_NAME}\`;"
mysql -u root -e "CREATE DATABASE IF NOT EXISTS \`${DB_TEST}\`;"
echo "Databases created successfully."

# ตั้งค่า environment
echo "Setting up environment files..."
if [ ! -f .env ]; then
    cp .env.example .env
    sed -i '' "s/DB_DATABASE=laravel/DB_DATABASE=${DB_NAME}/g" .env
    echo ".env file created and configured."
else
    echo ".env file already exists. Please update DB_DATABASE to ${DB_NAME} manually if needed."
fi

# กำหนด Sail aliases
echo "Setting up Sail aliases..."
if ! grep -q "alias sail=" ~/.zshrc 2>/dev/null && ! grep -q "alias sail=" ~/.bashrc 2>/dev/null; then
    SHELL_CONFIG=""
    if [ -f "$HOME/.zshrc" ]; then
        SHELL_CONFIG="$HOME/.zshrc"
    elif [ -f "$HOME/.bashrc" ]; then
        SHELL_CONFIG="$HOME/.bashrc"
    fi

    if [ ! -z "$SHELL_CONFIG" ]; then
        echo "alias sail='[ -f sail ] && sh sail || sh vendor/bin/sail'" >> $SHELL_CONFIG
        echo "alias artisan-local=\"php -d variables_order=EGPCS artisan --env=local\"" >> $SHELL_CONFIG
        echo "Alias added to $SHELL_CONFIG. Please run 'source $SHELL_CONFIG' to apply."
    else
        echo "Could not find .zshrc or .bashrc. Please add 'alias sail='[ -f sail ] && sh sail || sh vendor/bin/sail'' manually."
    fi
fi

# สรุป
echo -e "\n===== Setup Complete ====="
echo "Database '${DB_NAME}' is ready for use."
echo "To run migrations: php artisan migrate"
echo "To run locally: artisan-local migrate (after sourcing your shell config)"
echo "To run with Sail: ./vendor/bin/sail artisan migrate"
