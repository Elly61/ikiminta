# Deployment Guide (GitHub Actions + SSH)

This project includes two GitHub Actions workflows:

- `.github/workflows/ci.yml` — PHP syntax lint on push/PR.
- `.github/workflows/deploy.yml` — Syncs files to a server over SSH and runs post-deploy commands.

## Prerequisites

1. A Linux server (Apache or Nginx + PHP + MySQL) with a virtual host pointing to the project root.
2. Database `ikiminta` created and schema imported from `database.sql`.
3. Writable directories: `logs/`, `public/uploads/`, `public/uploads/profile/`.
4. `.htaccess` enabled (mod_rewrite for Apache) or equivalent Nginx rewrites to `index.php`.

## Configure GitHub Secrets

Add these repository secrets:

- `DEPLOY_HOST` — server hostname or IP.
- `DEPLOY_USER` — SSH user.
- `DEPLOY_SSH_KEY` — private key for the SSH user.
- `DEPLOY_REMOTE_PATH` — path on server, e.g., `/var/www/ikiminta`.

Optional:
- `APP_ENV`, `BASE_URL` and any env values you want to export at build/deploy time.

## Server Post-Deploy Steps

The deploy workflow:
- Syncs files via `rsync` excluding Git and non-essential dev content.
- Ensures upload and log directories exist and are writable.
- (Optional) Runs `php migrations/run.php` if you implement it for DB migrations.

Set your environment variables on the server (e.g., in the web server vhost, systemd unit, or an `.env` loader in PHP). See `.env.example` for the list.

### Nginx rewrite example

```
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

### Apache (mod_rewrite) note
Ensure `.htaccess` routes all to `index.php` and `AllowOverride All` is enabled for the site directory.

## Rollback
Keep previous releases or use `rsync --backup` strategy. Alternatively, tag releases and deploy from tags.

## CI
The lint workflow runs `php -l` across all PHP files to catch syntax errors before deployment.