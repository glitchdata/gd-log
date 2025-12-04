# GD Login (PHP Edition)

A lightweight, traditional PHP login portal that uses server-rendered forms, PHP sessions, and a MySQL-backed user store. The app features a split login/registration page and a protected dashboard rendered with classic PHP templates.

## Features

- PHP session-based authentication without external frameworks
- Login and registration handled by `index.php` with friendly error banners
- User data persisted in MySQL via PDO helpers (`public_html/includes/users.php`)
- MySQL schema (`schema/schema.sql`) plus inserts (`schema/seed.sql`) derived from the original JSON data
- Protected `dashboard.php` that greets the signed-in user and exposes account metadata
- Simple `logout.php` endpoint to clear the session

## Requirements

- PHP 8.1+ with PDO + MySQL extension enabled
- MySQL 8+ (or compatible)

## Getting started

1. **Provision MySQL**
   ```bash
   mysql -u root -p < schema/schema.sql
   mysql -u root -p < schema/seed.sql  # optional demo account
   ```
2. **Configure connection (optional)** – Set `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASSWORD` environment variables before running PHP if you deviate from defaults (`127.0.0.1`, `3306`, `gd_login_php`, `root`, empty password).
3. **Run the built-in PHP server**
   ```bash
   php -S localhost:8000 -t public_html
   ```
4. Visit `http://localhost:8000` in your browser. Use the login form or create a new account.

## Project structure

```
.
├── public_html
│   ├── assets
│   │   └── styles.css          # Shared styling for login + dashboard
│   ├── includes
│   │   ├── db.php              # PDO connection helper
│   │   └── users.php           # Helper functions for user CRUD + auth
│   ├── dashboard.php           # Protected area
│   ├── index.php               # Login + registration portal
│   └── logout.php              # Session teardown
├── schema
│   ├── schema.sql              # MySQL schema (DDL)
│   └── seed.sql                # MySQL seed data derived from JSON
└── README.md
```

## Default account

If you ran `schema/seed.sql` you can log in with:

- Email: `demo@example.com`
- Password: `password`

Otherwise, submit the “Create account” form to register a new profile. Users are persisted in MySQL via `users.php` (passwords hashed with BCRYPT). If you need to sync environments, re-run `schema/seed.sql` or craft migrations.

## Notes

- This project is intentionally simple for educational purposes. For production, move the data store to a proper database and enforce CSRF tokens.
- Ensure your PHP runtime can reach the MySQL instance defined by the `DB_*` environment variables.
- The PDO helper creates a single shared connection per request; adjust `includes/db.php` if you need pooling or different credentials handling.
