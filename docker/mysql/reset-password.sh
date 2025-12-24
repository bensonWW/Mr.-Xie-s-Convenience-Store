#!/bin/bash
# ===========================================
# MySQL Password Reset Script
# Mr. Xie's Convenience Store
# ===========================================
# Usage: docker exec -it mr-xies-db bash /scripts/reset-password.sh
# Or run: make reset-db-password

set -e

echo "🔐 MySQL Password Reset Script"
echo "================================"

# Default values (matching docker-compose.yml)
NEW_PASSWORD="${1:-root}"
DB_USER="laravel"
DB_NAME="laravel"
TEST_DB_NAME="laravel_testing"

echo "📦 Resetting password for user: $DB_USER"
echo "📦 Using password: $NEW_PASSWORD"

# Connect as root and reset password
mysql -u root -p"$NEW_PASSWORD" <<EOF
-- Reset laravel user password
ALTER USER '$DB_USER'@'%' IDENTIFIED BY '$NEW_PASSWORD';

-- Ensure databases exist
CREATE DATABASE IF NOT EXISTS $DB_NAME;
CREATE DATABASE IF NOT EXISTS $TEST_DB_NAME;

-- Grant privileges
GRANT ALL PRIVILEGES ON $DB_NAME.* TO '$DB_USER'@'%';
GRANT ALL PRIVILEGES ON $TEST_DB_NAME.* TO '$DB_USER'@'%';
FLUSH PRIVILEGES;

-- Show result
SELECT User, Host FROM mysql.user WHERE User = '$DB_USER';
SHOW DATABASES;
EOF

echo ""
echo "✅ Password reset complete!"
echo "📝 Update your .env file with:"
echo "   DB_PASSWORD=$NEW_PASSWORD"
