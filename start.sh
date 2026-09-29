#!/bin/bash
set -e

echo "=== Sophisticate Intensive Classes — Railway Startup ==="

# ──────────────────────────────────────────────────────────────
# 1. Print diagnostics (masked passwords)
# ──────────────────────────────────────────────────────────────
echo "--- ENV DIAGNOSTICS ---"
echo "MYSQL_PRIVATE_URL set: $([ -n "${MYSQL_PRIVATE_URL}" ] && echo YES || echo NO)"
echo "DB_HOST:               ${DB_HOST:-NOT SET}"
echo "DB_PORT:               ${DB_PORT:-NOT SET}"
echo "DB_DATABASE:           ${DB_DATABASE:-NOT SET}"
echo "DB_USERNAME:           ${DB_USERNAME:-NOT SET}"
echo "DB_PASSWORD set:       $([ -n "${DB_PASSWORD}" ] && echo YES || echo NO)"
echo "-----------------------"

# ──────────────────────────────────────────────────────────────
# 2. Try to resolve DB credentials:
#    Priority: MYSQL_PRIVATE_URL > individual DB_* vars
# ──────────────────────────────────────────────────────────────
RAILWAY_DB_URL="${MYSQL_PRIVATE_URL:-${MYSQL_URL:-${DATABASE_URL:-}}}"

if [ -n "$RAILWAY_DB_URL" ]; then
    echo "Parsing DB credentials from Railway URL..."
    STRIPPED="${RAILWAY_DB_URL#mysql*://}"
    USERINFO="${STRIPPED%%@*}"
    HOSTINFO="${STRIPPED##*@}"
    export DB_USERNAME="${USERINFO%%:*}"
    export DB_PASSWORD="${USERINFO#*:}"
    export DB_HOST="${HOSTINFO%%:*}"
    PORTDB="${HOSTINFO#*:}"
    export DB_PORT="${PORTDB%%/*}"
    export DB_DATABASE="${PORTDB##*/}"
    export DB_CONNECTION="mysql"
    echo "  -> DB_HOST=$DB_HOST  DB_PORT=$DB_PORT  DB_DATABASE=$DB_DATABASE"

elif [ -n "${DB_HOST}" ] && [ "${DB_HOST}" != "127.0.0.1" ]; then
    echo "Using individual DB_* environment variables..."
    echo "  -> DB_HOST=$DB_HOST  DB_PORT=${DB_PORT:-3306}  DB_DATABASE=$DB_DATABASE"
    export DB_CONNECTION="mysql"

else
    echo "FATAL: No database credentials found."
    echo "  Set DB_HOST / DB_PORT / DB_DATABASE / DB_USERNAME / DB_PASSWORD"
    echo "  with the actual values from your MySQL-QYu9 service in Railway."
    exit 1
fi

# ──────────────────────────────────────────────────────────────
# 3. Clear cached config so fresh env vars are used
# ──────────────────────────────────────────────────────────────
echo "Clearing config cache..."
php artisan config:clear --no-ansi 2>/dev/null || true
php artisan cache:clear  --no-ansi 2>/dev/null || true

# ──────────────────────────────────────────────────────────────
# 4. Wait for MySQL via TCP socket (no artisan required)
# ──────────────────────────────────────────────────────────────
TARGET_HOST="${DB_HOST}"
TARGET_PORT="${DB_PORT:-3306}"
echo "Waiting for database at ${TARGET_HOST}:${TARGET_PORT}..."
MAX_RETRIES=30
COUNT=0
until php -r "
    \$conn = @fsockopen('${TARGET_HOST}', ${TARGET_PORT}, \$errno, \$errstr, 2);
    if (\$conn) { fclose(\$conn); exit(0); } exit(1);
" 2>/dev/null; do
    COUNT=$((COUNT+1))
    if [ "$COUNT" -ge "$MAX_RETRIES" ]; then
        echo "FATAL: Cannot reach ${TARGET_HOST}:${TARGET_PORT} after ${MAX_RETRIES} attempts."
        exit 1
    fi
    echo "  DB not ready yet ($COUNT/$MAX_RETRIES)..."
    sleep 2
done
echo "Database is ready!"

# ──────────────────────────────────────────────────────────────
# 5. Fix storage permissions & create symlink
# ──────────────────────────────────────────────────────────────
echo "Setting up storage..."
chmod -R 775 storage bootstrap/cache 2>/dev/null || true
php artisan storage:link --force --no-ansi 2>/dev/null || true

# ──────────────────────────────────────────────────────────────
# 6. Migrate & seed
# ──────────────────────────────────────────────────────────────
echo "Running migrations..."
php artisan migrate --force --no-ansi

echo "Seeding admin account..."
php artisan db:seed --class=AdminSeeder --force --no-ansi

# ──────────────────────────────────────────────────────────────
# 6. Cache for production
# ──────────────────────────────────────────────────────────────
echo "Optimizing..."
php artisan config:cache --no-ansi
php artisan route:cache  --no-ansi
php artisan view:cache   --no-ansi

# ──────────────────────────────────────────────────────────────
# 7. Start server
# ──────────────────────────────────────────────────────────────
echo "Starting server on 0.0.0.0:${PORT:-8080}..."
php artisan serve --host=0.0.0.0 --port="${PORT:-8080}"
