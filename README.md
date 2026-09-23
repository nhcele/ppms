# Recruitment & Workforce Management System (PHP + MySQL)

This project implements a no-framework PHP 8.1+ web app with MySQL 8.x for recruitment, compliance, deployments, reporting, candidate self-service, and interview scheduling.

## Requirements
- PHP 8.1+
- MySQL 8.x
- Web server (Apache/Nginx). For Apache, enable URL access to `public/`.
- SMTP credentials for email sending.
- Cron support for scheduled jobs.

## Directory Structure
- public/
  - index.php (front controller)
  - assets/
  - uploads/ (ensure not directly web-accessible in production)
  - templates/
  - exports/
- app/
  - config.php
  - lib/ (db.php, auth.php, csrf.php, rbac.php, validators.php, file_upload.php, audit.php, mailer.php, helpers.php)
  - controllers/
  - views/
  - services/
  - jobs/
- sql/
  - schema.sql
  - seed.sql
- logs/

## Setup
1. Create a MySQL database and user.
2. Import `sql/schema.sql`.
3. Copy `app/config.php` and set DB/SMTP/base URL settings.
4. Ensure `logs/`, `uploads/`, and `exports/` are writable by the web server user.
5. Configure Apache/Nginx to point to the root directory as document root.
6. Set up cron jobs:
   - php app/jobs/run_alerts.php
   - php app/jobs/update_deployments_status.php
   - php app/jobs/run_scheduled_reports.php
   - php app/jobs/send_interview_reminders.php

## Development
- Routes are handled via query params: `index.php?page={module}&action={action}&id={id}`.
- Use PDO prepared statements.
- CSRF tokens are required for all POST forms.
- RBAC enforced in controllers.

## Initial Credentials
- Seed an admin user via `sql/seed.sql` or manual insert.

## Security Notes
- In production, store uploads outside web root and serve via secured endpoints.
- Always use HTTPS, secure cookies, and set appropriate headers.

## License
Internal project.
