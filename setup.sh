#!/bin/bash

# ============================================================
#  Smart PC Build System — Project Setup Script
#  Run as root or with sudo: sudo bash setup.sh
# ============================================================

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
BOLD='\033[1m'
RESET='\033[0m'

PROJECT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
LAMPP="/opt/lampp"
PHP="$LAMPP/bin/php"
MYSQL="$LAMPP/bin/mysql"
DB_NAME="smart_pc_build"
DB_USER="root"
DB_PASS=""

print_header() {
    echo ""
    echo -e "${CYAN}${BOLD}============================================================${RESET}"
    echo -e "${CYAN}${BOLD}   Smart PC Build System — Automated Setup${RESET}"
    echo -e "${CYAN}${BOLD}============================================================${RESET}"
    echo ""
}

print_step() {
    echo -e "\n${BLUE}${BOLD}[ STEP $1 ]${RESET} ${BOLD}$2${RESET}"
    echo -e "${BLUE}------------------------------------------------------------${RESET}"
}

print_ok()   { echo -e "  ${GREEN}✓${RESET} $1"; }
print_warn() { echo -e "  ${YELLOW}⚠${RESET} $1"; }
print_err()  { echo -e "  ${RED}✗${RESET} $1"; }
print_info() { echo -e "  ${CYAN}→${RESET} $1"; }

check_root() {
    if [[ "$EUID" -ne 0 ]]; then
        print_err "This script must be run as root."
        echo -e "  ${YELLOW}Usage:${RESET} sudo bash setup.sh"
        exit 1
    fi
}

start_xampp() {
    print_step "1" "Starting XAMPP Services"

    if pgrep -x "httpd" > /dev/null 2>&1; then
        print_ok "Apache is already running."
    else
        print_info "Starting Apache..."
        $LAMPP/lampp startapache > /dev/null 2>&1
        sleep 2
        if pgrep -x "httpd" > /dev/null 2>&1; then
            print_ok "Apache started successfully."
        else
            print_err "Apache failed to start. Check $LAMPP/logs/error_log"
            exit 1
        fi
    fi

    if pgrep -x "mysqld" > /dev/null 2>&1; then
        print_ok "MySQL is already running."
    else
        print_info "Starting MySQL..."
        $LAMPP/lampp startmysql > /dev/null 2>&1
        sleep 3
        if pgrep -x "mysqld" > /dev/null 2>&1; then
            print_ok "MySQL started successfully."
        else
            print_err "MySQL failed to start. Check $LAMPP/var/mysql/*.err"
            exit 1
        fi
    fi
}

setup_database() {
    print_step "2" "Setting Up Database"

    DB_EXISTS=$($MYSQL -u "$DB_USER" ${DB_PASS:+-p"$DB_PASS"} -e "SHOW DATABASES LIKE '$DB_NAME';" 2>/dev/null | grep -c "$DB_NAME")

    if [[ "$DB_EXISTS" -eq 0 ]]; then
        print_info "Creating database: $DB_NAME"
        $MYSQL -u "$DB_USER" ${DB_PASS:+-p"$DB_PASS"} -e "CREATE DATABASE \`$DB_NAME\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>/dev/null
        print_ok "Database created."
    else
        print_ok "Database '$DB_NAME' already exists."
    fi

    print_info "Running migration (schema)..."
    if [[ -f "$PROJECT_DIR/project_alpha.sql" ]]; then
        $MYSQL -u "$DB_USER" ${DB_PASS:+-p"$DB_PASS"} "$DB_NAME" < "$PROJECT_DIR/project_alpha.sql" 2>/dev/null
        print_ok "Schema applied from project_alpha.sql"
    elif [[ -f "$PROJECT_DIR/migration.sql" ]]; then
        $MYSQL -u "$DB_USER" ${DB_PASS:+-p"$DB_PASS"} "$DB_NAME" < "$PROJECT_DIR/migration.sql" 2>/dev/null
        print_ok "Schema applied from migration.sql"
    else
        print_warn "No SQL migration file found. Skipping schema import."
    fi

    COMPONENT_COUNT=$($MYSQL -u "$DB_USER" ${DB_PASS:+-p"$DB_PASS"} "$DB_NAME" -se "SELECT COUNT(*) FROM components;" 2>/dev/null || echo "0")
    if [[ "$COMPONENT_COUNT" -lt 10 ]]; then
        print_info "Seeding components..."
        if [[ -f "$PROJECT_DIR/seed_components_v2.php" ]]; then
            $PHP "$PROJECT_DIR/seed_components_v2.php" > /dev/null 2>&1
            print_ok "Components seeded from seed_components_v2.php"
        elif [[ -f "$PROJECT_DIR/seed_components.php" ]]; then
            $PHP "$PROJECT_DIR/seed_components.php" > /dev/null 2>&1
            print_ok "Components seeded from seed_components.php"
        fi
    else
        print_ok "Components already seeded ($COMPONENT_COUNT records)."
    fi
}

set_permissions() {
    print_step "3" "Setting File Permissions"

    chown -R daemon:daemon "$PROJECT_DIR/uploads" 2>/dev/null || \
    chown -R www-data:www-data "$PROJECT_DIR/uploads" 2>/dev/null || true
    chmod -R 775 "$PROJECT_DIR/uploads"
    print_ok "Uploads directory permissions set."

    chmod 644 "$PROJECT_DIR/config.php"
    print_ok "config.php permissions set."
}

verify_setup() {
    print_step "4" "Verifying Setup"

    HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" "http://localhost:5173/myproject/" 2>/dev/null || echo "000")
    if [[ "$HTTP_CODE" == "200" || "$HTTP_CODE" == "302" ]]; then
        print_ok "Web application is reachable at http://localhost:5173/myproject/"
    else
        print_warn "HTTP response: $HTTP_CODE — Apache may be using a different port."
        print_info "Trying port 80..."
        HTTP_CODE2=$(curl -s -o /dev/null -w "%{http_code}" "http://localhost/myproject/" 2>/dev/null || echo "000")
        if [[ "$HTTP_CODE2" == "200" || "$HTTP_CODE2" == "302" ]]; then
            print_ok "Web application reachable at http://localhost/myproject/"
        else
            print_warn "Application not responding. Verify Apache port in $LAMPP/etc/httpd.conf"
        fi
    fi

    TABLES=$($MYSQL -u "$DB_USER" ${DB_PASS:+-p"$DB_PASS"} "$DB_NAME" -se "SHOW TABLES;" 2>/dev/null | wc -l)
    print_ok "Database has $TABLES tables."

    COMPONENTS=$($MYSQL -u "$DB_USER" ${DB_PASS:+-p"$DB_PASS"} "$DB_NAME" -se "SELECT COUNT(*) FROM components;" 2>/dev/null || echo "0")
    print_ok "Components in DB: $COMPONENTS"

    USERS=$($MYSQL -u "$DB_USER" ${DB_PASS:+-p"$DB_PASS"} "$DB_NAME" -se "SELECT COUNT(*) FROM users;" 2>/dev/null || echo "0")
    print_ok "Users in DB: $USERS"
}

print_summary() {
    echo ""
    echo -e "${GREEN}${BOLD}============================================================${RESET}"
    echo -e "${GREEN}${BOLD}   Setup Complete!${RESET}"
    echo -e "${GREEN}${BOLD}============================================================${RESET}"
    echo ""
    echo -e "  ${BOLD}App URL:${RESET}       http://localhost:5173/myproject/"
    echo -e "  ${BOLD}Admin Panel:${RESET}   http://localhost:5173/myproject/admin/"
    echo -e "  ${BOLD}phpMyAdmin:${RESET}    http://localhost:5173/phpmyadmin/"
    echo -e "  ${BOLD}Database:${RESET}      $DB_NAME"
    echo ""
    echo -e "  ${YELLOW}Default login:${RESET} Register a new account at /myproject/register.php"
    echo ""
}

main() {
    print_header
    check_root
    start_xampp
    setup_database
    set_permissions
    verify_setup
    print_summary
}

main
