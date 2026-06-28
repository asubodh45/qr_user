#!/bin/sh
set -e

# ─── Port ─────────────────────────────────────────────────────────────────────
# Render injects $PORT at runtime (typically 10000). Nginx picks it up via envsubst.
export PORT="${PORT:-8080}"

echo "[entrypoint] Starting on port $PORT"

# ─── Generate Nginx config with correct port ─────────────────────────────────
envsubst '${PORT}' < /etc/nginx/conf.d/app.conf.template \
    > /etc/nginx/conf.d/default.conf

# ─── Wait for the database ───────────────────────────────────────────────────
echo "[entrypoint] Waiting for database..."
RETRIES=30

# Use getenv() inside PHP so special chars in passwords are handled safely
until php -r "
    \$h = getenv('DB_HOST') ?: '127.0.0.1';
    \$p = getenv('DB_PORT') ?: '3306';
    \$d = getenv('DB_DATABASE') ?: '';
    \$u = getenv('DB_USERNAME') ?: '';
    \$pw = getenv('DB_PASSWORD') ?: '';
    try {
        new PDO(\"pgsql:host=\$h;port=\$p;dbname=\$d\", \$u, \$pw);
        exit(0);
    } catch (Exception \$e) {
        exit(1);
    }
" 2>/dev/null; do
    RETRIES=$((RETRIES - 1))
    if [ "$RETRIES" -eq 0 ]; then
        echo "[entrypoint] ERROR: Could not connect to database after 30 attempts. Aborting."
        exit 1
    fi
    echo "[entrypoint] DB not ready — retrying in 3s... ($RETRIES left)"
    sleep 3
done

echo "[entrypoint] Database ready."

# ─── Laravel bootstrap ───────────────────────────────────────────────────────
cd /var/www/html

php artisan migrate --force --no-interaction
echo "[entrypoint] Migrations complete."

# Storage symlink — ignore error if symlink already exists (Render Disk scenario)
php artisan storage:link --force 2>/dev/null || true
echo "[entrypoint] Storage link ready."

# Warm Laravel caches for production performance
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo "[entrypoint] Caches warmed."

# ─── Fix permissions (Render Disk mounts as root) ────────────────────────────
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# ─── Launch Supervisor (manages php-fpm + nginx) ─────────────────────────────
echo "[entrypoint] Launching Supervisor..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
