#!/bin/bash
#
# weather34-upgrade.sh
# 
# Automated upgrade script for migrating from steepleian/weewx-Weather34
# to meisnick/weewx-Weather34.
#
# Usage:
#   sudo ./weather34-upgrade.sh [--fresh]
#
# Options:
#   --fresh    Perform a fresh install (uninstall old, reinstall new)
#              Default is in-place update (safer, keeps structure)
#
# Author:  Community-maintained fork
# URL:     https://github.com/meisnick/weewx-Weather34
#

set -euo pipefail

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Script directory (where this script lives)
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

# Default settings
FRESH_INSTALL=false
BACKUP_DIR=""
WEWX_USER="${WEWX_USER:-weewx}"
WEWX_GROUP="${WEWX_GROUP:-weewx}"
WWW_USER="${WWW_USER:-www-data}"
WWW_GROUP="${WWW_GROUP:-www-data}"

# Detect installation paths
detect_paths() {
    echo -e "${BLUE}==> Detecting installation paths...${NC}"
    
    # Detect WeeWX install type and paths
    if [ -d "/usr/share/weewx" ]; then
        WEWX_PREFIX="/usr"
        echo "  WeeWX type: Package-installed"
    elif [ -d "/home/weewx" ]; then
        WEWX_PREFIX="/home/weewx"
        echo "  WeeWX type: Home-installed"
    else
        echo -e "${RED}ERROR: Cannot detect WeeWX installation. Is WeeWX installed?${NC}"
        exit 1
    fi
    
    WEWX_BIN="${WEWX_PREFIX}/bin"
    WEWX_USER_DIR="${WEWX_PREFIX}/share/weewx"
    WEWX_CONF=""
    
    # Find weewx.conf
    for conf in /etc/weewx/weewx.conf "${WEWX_USER_DIR}/weewx.conf" "/home/weewx/weewx.conf"; do
        if [ -f "$conf" ]; then
            WEWX_CONF="$conf"
            break
        fi
    done
    
    if [ -z "$WEWX_CONF" ]; then
        echo -e "${RED}ERROR: Cannot find weewx.conf${NC}"
        exit 1
    fi
    
    echo "  WeeWX config: ${WEWX_CONF}"
    echo "  WeeWX user dir: ${WEWX_USER_DIR}"
    
    # Detect web root
    HTML_ROOT=$(grep -i "^[[:space:]]*HTML_ROOT" "${WEWX_CONF}" 2>/dev/null | cut -d= -f2 | tr -d "'\" " || echo "")
    if [ -z "$HTML_ROOT" ]; then
        HTML_ROOT="/var/www/html"
    fi
    echo "  Web root: ${HTML_ROOT}"
    
    # Detect weather34 path
    W34_DIR="${HTML_ROOT}/weewx/weather34"
    if [ ! -d "$W34_DIR" ]; then
        echo -e "${RED}ERROR: Cannot find Weather34 installation at ${W34_DIR}${NC}"
        exit 1
    fi
    echo "  Weather34 dir: ${W34_DIR}"
}

# Check if running as root
check_root() {
    if [ "${EUID:-$(id -u)}" -ne 0 ]; then
        echo -e "${RED}ERROR: This script must be run as root (use sudo)${NC}"
        exit 1
    fi
}

# Check prerequisites
check_prereqs() {
    echo -e "${BLUE}==> Checking prerequisites...${NC}"
    
    local missing=()
    
    for cmd in git cp mv chmod chown systemctl grep awk; do
        if ! command -v "$cmd" &> /dev/null; then
            missing+=("$cmd")
        fi
    done
    
    if [ ${#missing[@]} -gt 0 ]; then
        echo -e "${RED}ERROR: Missing required commands: ${missing[*]}${NC}"
        exit 1
    fi
    
    # Check WeeWX is running
    if systemctl is-active --quiet weewx 2>/dev/null; then
        echo "  WeeWX service: running"
    else
        echo -e "${YELLOW}WARNING: WeeWX service does not appear to be running${NC}"
    fi
    
    # Check git remotes
    if [ -d "${SCRIPT_DIR}/.git" ]; then
        pushd "${SCRIPT_DIR}" > /dev/null
        CURRENT_REMOTE=$(git remote get-url origin 2>/dev/null || echo "none")
        if [[ "$CURRENT_REMOTE" == *"steepleian"* ]]; then
            echo -e "${YELLOW}WARNING: Repository still pointing to steepleian (EOL repo)${NC}"
        else
            echo "  Git remote: ${CURRENT_REMOTE}"
        fi
        popd > /dev/null
    fi
    
    echo "  All prerequisites met."
}

# Create backup
create_backup() {
    BACKUP_DIR="/tmp/weather34_backup_$(date +%Y%m%d_%H%M%S)"
    echo -e "${BLUE}==> Creating backup in ${BACKUP_DIR}${NC}"
    
    mkdir -p "${BACKUP_DIR}"
    
    # Backup settings1.php (most important)
    if [ -f "${W34_DIR}/settings1.php" ]; then
        cp -p "${W34_DIR}/settings1.php" "${BACKUP_DIR}/"
        echo "  Backed up: settings1.php"
    else
        echo -e "${YELLOW}WARNING: settings1.php not found${NC}"
    fi
    
    # Backup weewx.conf
    if [ -f "${WEWX_CONF}" ]; then
        cp -p "${WEWX_CONF}" "${BACKUP_DIR}/"
        echo "  Backed up: weewx.conf"
    fi
    
    # Backup entire www directory
    cp -r "${W34_DIR}" "${BACKUP_DIR}/weather34_www_backup"
    echo "  Backed up: weather34 www directory"
    
    # Backup user extensions
    mkdir -p "${BACKUP_DIR}/user_extensions"
    if ls "${WEWX_USER_DIR}/user/"*.py &>/dev/null; then
        cp -p "${WEWX_USER_DIR}/user/"*.py "${BACKUP_DIR}/user_extensions/"
        echo "  Backed up: user extensions"
    fi
    
    # Backup skins
    if [ -d "/etc/weewx/skins/Weather34" ]; then
        cp -r "/etc/weewx/skins/Weather34" "${BACKUP_DIR}/"
        echo "  Backed up: Weather34 skin"
    fi
    
    # Save installation info
    cat > "${BACKUP_DIR}/install_info.txt" << EOF
Weather34 Upgrade Backup
Generated: $(date)
========================

Weather34 directory: ${W34_DIR}
WeeWX config: ${WEWX_CONF}
WeeWX user dir: ${WEWX_USER_DIR}
Web root: ${HTML_ROOT}
Backup location: ${BACKUP_DIR}

Installed Weather34 version info:
EOF
    
    if [ -f "${W34_DIR}/w34CombinedData.php" ]; then
        grep -i "version\|W34-" "${W34_DIR}/w34CombinedData.php" 2>/dev/null | head -5 >> "${BACKUP_DIR}/install_info.txt" || true
    fi
    
    echo "  Saved: install_info.txt"
    
    # Create symlink to latest backup
    rm -f /tmp/weather34_latest_backup
    ln -s "${BACKUP_DIR}" /tmp/weather34_latest_backup
    
    echo -e "${GREEN}Backup complete!${NC}"
    echo "  Location: ${BACKUP_DIR}"
    echo "  Symlink: /tmp/weather34_latest_backup"
}

# Stop WeeWX
stop_weewx() {
    echo -e "${BLUE}==> Stopping WeeWX service...${NC}"
    
    if systemctl is-active --quiet weewx 2>/dev/null; then
        systemctl stop weewx
        echo "  WeeWX stopped."
    else
        echo "  WeeWX already stopped."
    fi
}

# Start WeeWX
start_weewx() {
    echo -e "${BLUE}==> Starting WeeWX service...${NC}"
    
    systemctl start weewx
    sleep 2
    
    if systemctl is-active --quiet weewx; then
        echo -e "${GREEN}WeeWX started successfully.${NC}"
    else
        echo -e "${RED}WARNING: WeeWX may not have started properly. Check logs.${NC}"
    fi
}

# Fetch latest from new repo
fetch_latest() {
    echo -e "${BLUE}==> Fetching latest from GitHub...${NC}"
    
    pushd "${SCRIPT_DIR}" > /dev/null
    
    # Check if it's a git repo
    if [ ! -d ".git" ]; then
        echo -e "${RED}ERROR: ${SCRIPT_DIR} is not a git repository${NC}"
        echo "  Please clone the repository first:"
        echo "  cd ~"
        echo "  git clone https://github.com/meisnick/weewx-Weather34.git"
        exit 1
    fi
    
    # Check current remote
    CURRENT_REMOTE=$(git remote get-url origin 2>/dev/null || echo "none")
    
    if [[ "$CURRENT_REMOTE" == *"steepleian"* ]]; then
        echo -e "${YELLOW}==> Updating remote from steepleian to meisnick...${NC}"
        git remote set-url origin https://github.com/meisnick/weewx-Weather34.git
    fi
    
    echo "  Fetching latest changes..."
    git fetch origin
    
    CURRENT_COMMIT=$(git rev-parse HEAD)
    NEW_COMMIT=$(git rev-parse origin/main)
    
    if [ "$CURRENT_COMMIT" == "$NEW_COMMIT" ]; then
        echo -e "${GREEN}Already on latest version (${NEW_COMMIT:0:7})${NC}"
    else
        echo "  Current: ${CURRENT_COMMIT:0:7}"
        echo "  Latest:  ${NEW_COMMIT:0:7}"
        echo -e "${YELLOW}==> Updating to latest version...${NC}"
        git reset --hard origin/main
        echo -e "${GREEN}Updated successfully.${NC}"
    fi
    
    popd > /dev/null
}

# Fresh install method
fresh_install() {
    echo -e "${BLUE}==> Performing fresh install (Option A)${NC}"
    echo -e "${YELLOW}This will uninstall the old version and reinstall.${NC}"
    
    # Run uninstaller if it exists
    if [ -f "${SCRIPT_DIR}/w34_uninstaller.py" ]; then
        echo -e "${YELLOW}==> Running uninstaller...${NC}"
        python3 "${SCRIPT_DIR}/w34_uninstaller.py"
    fi
    
    # Run installer
    echo -e "${YELLOW}==> Running installer...${NC}"
    python3 "${SCRIPT_DIR}/w34_installer.py"
}

# In-place update method
inplace_update() {
    echo -e "${BLUE}==> Performing in-place update (Option B)${NC}"
    echo "  Updating PHP files..."
    rsync -av --delete "${SCRIPT_DIR}/www/" "${W34_DIR}/"
    chown -R "${WWW_USER}:${WWW_GROUP}" "${W34_DIR}"
    
    echo "  Updating skin files..."
    if [ -d "${SCRIPT_DIR}/skins/Weather34" ]; then
        mkdir -p "/etc/weewx/skins"
        rsync -av --delete "${SCRIPT_DIR}/skins/Weather34/" "/etc/weewx/skins/Weather34/"
        chown -R "${WEWX_USER}:${WEWX_GROUP}" "/etc/weewx/skins/Weather34"
    fi
    
    echo "  Updating user extensions..."
    if [ -d "${SCRIPT_DIR}/user" ]; then
        mkdir -p "${WEWX_USER_DIR}/user"
        for pyfile in "${SCRIPT_DIR}/user/"*.py; do
            if [ -f "$pyfile" ]; then
                cp -p "$pyfile" "${WEWX_USER_DIR}/user/"
                echo "    Updated: $(basename "$pyfile")"
            fi
        done
        chown "${WEWX_USER}:${WEWX_GROUP}" "${WEWX_USER_DIR}/user/"*.py
    fi
    
    echo -e "${GREEN}In-place update complete.${NC}"
}

# Restore settings
restore_settings() {
    echo -e "${BLUE}==> Restoring settings...${NC}"
    
    # Restore settings1.php
    if [ -f "${BACKUP_DIR}/settings1.php" ]; then
        cp -p "${BACKUP_DIR}/settings1.php" "${W34_DIR}/"
        chown "${WWW_USER}:${WWW_GROUP}" "${W34_DIR}/settings1.php"
        echo "  Restored: settings1.php"
    else
        echo -e "${YELLOW}WARNING: No settings1.php backup found${NC}"
        echo "  You may need to reconfigure via templateSetup.php"
    fi
    
    # Ensure www directory has correct permissions
    chown -R "${WWW_USER}:${WWW_GROUP}" "${W34_DIR}"
}

# Verify installation
verify() {
    echo -e "${BLUE}==> Verifying installation...${NC}"
    
    local errors=0
    
    # Check key files exist
    echo "  Checking key files..."
    for file in "w34CombinedData.php" "settings1.php" "index.php"; do
        if [ -f "${W34_DIR}/${file}" ]; then
            echo "    ${GREEN}OK:${NC} ${file}"
        else
            echo "    ${RED}MISSING:${NC} ${file}"
            ((errors++))
        fi
    done
    
    # Check skin files
    if [ -f "/etc/weewx/skins/Weather34/skin.conf" ]; then
        echo "    ${GREEN}OK:${NC} Weather34 skin"
    else
        echo "    ${RED}MISSING:${NC} Weather34 skin"
        ((errors++))
    fi
    
    # Check user extensions
    for ext in "weather34.py" "w34highchartsSearchX.py"; do
        if [ -f "${WEWX_USER_DIR}/user/${ext}" ]; then
            echo "    ${GREEN}OK:${NC} ${ext}"
        else
            echo "    ${RED}MISSING:${NC} ${ext}"
            ((errors++))
        fi
    done
    
    if [ $errors -eq 0 ]; then
        echo -e "${GREEN}Verification passed!${NC}"
    else
        echo -e "${RED}Verification found ${errors} issues. Please check logs.${NC}"
    fi
    
    return $errors
}

# Print summary
print_summary() {
    echo ""
    echo "========================================"
    echo -e "${GREEN}Upgrade Complete!${NC}"
    echo "========================================"
    echo ""
    echo "Backup location: ${BACKUP_DIR}"
    echo "Latest backup:  /tmp/weather34_latest_backup"
    echo ""
    echo "Next steps:"
    echo "  1. Browse to http://your-pi/weather34/templateSetup.php"
    echo "  2. Verify your settings are correct"
    echo "  3. Check the weather page is displaying correctly"
    echo "  4. Check for errors:"
    echo "       sudo journalctl -u weewx -n 50 --no-pager"
    echo ""
    echo "If something went wrong:"
    echo "  Backup is at: ${BACKUP_DIR}"
    echo "  To rollback, run:"
    echo "    sudo cp -r ${BACKUP_DIR}/weather34_www_backup/* ${W34_DIR}/"
    echo "    sudo cp ${BACKUP_DIR}/settings1.php ${W34_DIR}/"
    echo "    sudo systemctl restart weewx"
    echo ""
}

# Print usage
usage() {
    cat << EOF
Usage: sudo ./weather34-upgrade.sh [OPTIONS]

Automated upgrade script for Weather34 skin.

Options:
    --fresh       Perform fresh install (uninstall old, reinstall new)
    --no-backup  Skip backup step (NOT RECOMMENDED)
    -h, --help    Show this help message

Examples:
    sudo ./weather34-upgrade.sh           # In-place update (default, safer)
    sudo ./weather34-upgrade.sh --fresh   # Fresh install

For more information, see UPGRADE.md in the repository.
EOF
}

# Main
main() {
    local skip_backup=false
    
    # Parse arguments
    while [[ $# -gt 0 ]]; do
        case $1 in
            --fresh)
                FRESH_INSTALL=true
                shift
                ;;
            --no-backup)
                skip_backup=true
                shift
                ;;
            -h|--help)
                usage
                exit 0
                ;;
            *)
                echo -e "${RED}Unknown option: $1${NC}"
                usage
                exit 1
                ;;
        esac
    done
    
    echo "========================================"
    echo "Weather34 Upgrade Script"
    echo "From steepleian to meisnick fork"
    echo "========================================"
    echo ""
    
    check_root
    detect_paths
    check_prereqs
    
    if [ "$skip_backup" = true ]; then
        echo -e "${YELLOW}WARNING: Skipping backup (--no-backup specified)${NC}"
    else
        create_backup
    fi
    
    stop_weewx
    fetch_latest
    
    if [ "$FRESH_INSTALL" = true ]; then
        fresh_install
    else
        inplace_update
    fi
    
    restore_settings
    verify || true
    
    start_weewx
    print_summary
}

main "$@"
