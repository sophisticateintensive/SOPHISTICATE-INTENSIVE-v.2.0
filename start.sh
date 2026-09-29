#!/bin/bash
set -e

echo "=== Sophisticate Intensive Classes — Railway Startup ==="

# ──────────────────────────────────────────────────────────────
# 1. Resolve DB credentials from Railway's URL environment vars
#    Railway injects: MYSQL_PRIVATE_URL, MYSQL_URL, DATABASE_URL
#    Format: mysql://user:pass@host:port/database
# ──────────────────────────────────────────────────────────────
RAILWAY_DB_URL="${MYSQL_PRIVATE_URL:-${MYSQL_URL:-${DATABASE_URL:-}}}"

if [ -n "$RAILWAY_DB_URL" ]; then
    echo "Parsing DB credentials from URL..."

    # Strip scheme (mysql:// or mysql2://)
    STRIPPED="${RAILWAY_DB_URL#mysql*://}"

    # Extract user:pass@host:port/dbname
    USERINFO="${STRIPPED%%@*}"
    HOSTINFO="${STRIPPED##*@}"

    export DB_USERNAME="${USERINFO%%:*}"
    export DB_PASSWORD="${USERINFO#*:}"
    export DB_HOST="${HOSTINFO%%:*}"
    PORTDB="${HOSTINFO#*:}"
    export DB_PORT="${PORTDB%%/*}"
    export DB_DATABASE="${PORTDB##*/}"
    export DB_CONNECTION="mysql"

    echo "  DB_HOST=$DB_HOST"
    echo "  DB_PORT=$DB_PORT"
    echo "  DB_DATABASE=$DB_DATABASE"
    echo "  DB_USERNAME=$DB_USERNAME"
else
    echo "WARNING: No Railway MySQL URL found. Using existing DB_* env vars."
    echo "  DB_HOST=${DB_HOST:-127.0.0.1}"
    echo "  DB_PORT=${DB_PORT:-3306}"
    echo "  DB_DATABASE=${DB_DATABASE:-laravel}"
fi

# ──────────────────────────────────────────────────────────────
# 2. Clear any cached config so fresh env vars take effect
# ──────────────────────────────────────────────────────────────
echo "Clearing config cache..."
php artisan config:clear --no-ansi 2>/dev/null || true
php artisan cache:clear --no-ansi 2>/dev/null || true

# ──────────────────────────────────────────────────────────────
# 3. Wait for MySQL to accept TCP connections (no artisan needed)
# ──────────────────────────────────────────────────────────────
echo "Waiting for database at ${DB_HOST}:${DB_PORT}..."
MAX_RETRIES=30
COUNT=0
until php -r "
    \$conn = @fsockopen('${DB_HOST}', ${DB_PORT:-3306}, \$errno, \$errstr, 2);
    if (\$conn) { fclose(\$conn); exit(0); } exit(1);
" 2>/dev/null; do
    COUNT=$((COUNT+1))
    if [ "$COUNT" -ge "$MAX_RETRIES" ]; then
        echo "Database not reachable after ${MAX_RETRIES} attempts. Exiting."
        exit 1
    fi
    echo "  DB not ready yet ($COUNT/$MAX_RETRIES)..."
    sleep 2
done
echo "Database is ready!"

# ──────────────────────────────────────────────────────────────
# 4. Run migrations & seed
# ──────────────────────────────────────────────────────────────
echo "Running migrations..."
php artisan migrate --force --no-ansi

echo "Seeding admin account..."
php artisan db:seed --class=AdminSeeder --force --no-ansi

# ──────────────────────────────────────────────────────────────
# 5. Cache config/routes/views for production performance
# ──────────────────────────────────────────────────────────────
echo "Optimizing application..."
php artisan config:cache --no-ansi
php artisan route:cache --no-ansi
php artisan view:cache --no-ansi

# ──────────────────────────────────────────────────────────────
# 6. Start the PHP server
# ──────────────────────────────────────────────────────────────
echo "Starting server on 0.0.0.0:${PORT:-8080}..."
php artisan serve --host=0.0.0.0 --port="${PORT:-8080}"
