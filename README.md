# Smart Smile Clinic

A web-based dental clinic management system for handling patients, appointments, billing, prescriptions, and administrative settings from a single dashboard.

Built with plain PHP and MySQL, designed for small-to-medium dental practices running on XAMPP or similar LAMP/WAMP stacks.

---

## Features

| Module | Description |
|--------|-------------|
| **Dashboard** | Overview cards: patients, invoices, today's appointments, revenue, users |
| **Patients** | Register, view, edit, and manage patient records |
| **Appointments** | Interactive calendar (FullCalendar) with booking rules (e.g. no Friday bookings) |
| **Services** | Manage dental services and pricing *(Administrator)* |
| **Invoices** | Create invoices, cart workflow, print/export |
| **Prescriptions** | Add and manage patient prescriptions |
| **Sales Reports** | Revenue and sales reporting *(Administrator)* |
| **Settings** | Users, currency, print templates, autonumbers *(Administrator)* |

### UI Highlights

- Bootstrap **5.3** with a modern sidebar + top navbar layout
- Bootstrap Icons
- Optional **dark mode** (saved in `localStorage`)
- Fully responsive (desktop, tablet, mobile)
- RTL-ready CSS structure (`dir="ltr"` by default)

---

## Tech Stack

| Layer | Technology |
|-------|------------|
| Backend | PHP (procedural + light OOP) |
| Database | MySQL / MariaDB via `mysqli` |
| Frontend | Bootstrap 5.3, custom CSS (`assets/css/`) |
| JS | jQuery 3.7, DataTables, FullCalendar, Select2 |
| Server | Apache (XAMPP recommended) |

---

## Project Structure

```
DentalClinic/
├── index.php              # Main entry → dashboard
├── login.php              # Login page
├── process.php            # Login handler
├── logout.php             # Session destroy
├── dashboard.php          # Dashboard widgets
├── assets/
│   ├── css/               # theme.css, layout.css, components.css
│   └── js/                # theme.js (sidebar, dark mode)
├── include/
│   ├── config.php         # DB + app branding constants
│   ├── initialize.php     # Bootstrap loader
│   ├── database.php       # Database class
│   ├── session.php        # Session helpers
│   └── *.php              # Domain models (patients, invoicing, etc.)
├── theme/
│   ├── templates.php      # Main layout shell
│   └── partials/          # head, sidebar, navbar, footer, scripts
├── patients/              # Patient CRUD module
├── appointments/          # Calendar + appointment module
├── invoices/              # Billing module
├── prescription/          # Prescription module
├── services/              # Services module
├── reports/               # Sales reports
├── user/                  # User management
├── settings/              # Invoice print setup
├── currency/              # Currency settings
└── autonumber/            # Auto-numbering config
```

### Routing Pattern

Each module follows the same pattern:

```php
// e.g. patients/index.php
$content = 'list.php';
require_once('../theme/templates.php'); // injects $content into layout
```

---

## Requirements

- PHP **7.4+** (PHP 8.x recommended)
- MySQL **5.7+** / MariaDB **10.3+**
- Apache with `mod_rewrite` (optional)
- XAMPP, WAMP, or Laragon on Windows

---

## Installation

### 1. Clone the repository

```bash
git clone <repository-url> DentalClinic
```

Place the folder under your web root, e.g.:

```
C:\xampp\htdocs\DentalClinic
```

### 2. Create the database

1. Open **phpMyAdmin** → `http://localhost/phpmyadmin`
2. Create a database (default name in config: `db-dental-07-10`)
3. Import your SQL dump into that database

> **Note:** A `.sql` schema file is not included in this repository. Use your existing clinic database backup.

### 3. Configure the application

Edit `include/config.php`:

```php
define("server", "localhost");
define("user", "root");
define("pass", "");
define("database_name", "db-dental-07-10");

$web_root = "http://localhost:8080/DentalClinic/";
define('web_root', $web_root);

define('app_name', 'Smart Smile Clinic');
define('app_tagline', 'Your clinic tagline here.');
define('app_logo', web_root . 'dist/img/Smart_Smile_Clinic_logo_v2.svg');
```

| Constant | Purpose |
|----------|---------|
| `web_root` | Base URL — must match your Apache port and folder path |
| `app_name` | Clinic name shown in login, sidebar, footer |
| `app_tagline` | Tagline on the login brand panel |
| `app_logo` | Logo path (SVG recommended) |

### 4. Start the server

- Start **Apache** and **MySQL** from XAMPP Control Panel
- If using port `8080`, access:

```
http://localhost:8080/DentalClinic/
```

Or for default port 80:

```
http://localhost/DentalClinic/
```

### 5. Log in

Open:

```
http://localhost:8080/DentalClinic/login.php
```

Use credentials from your `tblusers` table.

---

## User Roles

| Role | Access |
|------|--------|
| **Administrator** | Full access: services, reports, settings, users |
| **Staff** | Clinical and billing modules (varies by page) |
| **doctor** | Limited prescription access |

Role checks use `$_SESSION['ADMIN_ROLE']` across module pages.

---

## Configuration Reference

### Branding (centralized)

All branding is driven from `include/config.php`:

- Login page title, logo, tagline, copyright
- Sidebar clinic name and logo

To rebrand the clinic, update `app_name`, `app_tagline`, and `app_logo` only.

### Session timeout

Auto-logout is configured in `theme/partials/scripts.php` (30 minutes). Adjust if needed.

---

## Development Notes

### Modernized pages

These pages use the new Bootstrap 5 layout:

- `login.php`
- `dashboard.php`
- `patients/list.php`
- `appointments/list.php`
- `theme/templates.php` + partials

Other modules (invoices, services, forms) may still use legacy Bootstrap 3 class names and are migrated incrementally.

### Adding a new module page

1. Create `yourmodule/index.php` with auth check and `$content` routing
2. Create view files (`list.php`, `add.php`, etc.)
3. Pages automatically inherit layout from `theme/templates.php`

### Dark mode

Toggle via the moon/sun icon in the navbar. Preference is stored in `localStorage` under key `dc-theme`.

---

## Security Considerations

> Review before deploying to production.

- Passwords are currently hashed with **SHA1** in `process.php` — migrate to `password_hash()` / `password_verify()`
- Database credentials live in `include/config.php` — never commit production secrets
- Add HTTPS in production
- Validate and sanitize all user inputs on write endpoints
- Restrict write access to `user/` upload directory

---

## Troubleshooting

| Issue | Solution |
|-------|----------|
| Blank page / DB error | Check MySQL is running and `database_name` in `config.php` is correct |
| CSS/JS not loading | Verify `web_root` matches your actual URL (port + folder) |
| Login redirect loop | Ensure session is started in `include/session.php` |
| Calendar not showing | Confirm FullCalendar assets load; check browser console for JS errors |
| 403 on assets | Check Apache permissions on `assets/` folder |

---

## Roadmap

- [ ] Migrate remaining modules to Bootstrap 5 UI
- [ ] Replace SHA1 with `password_hash()`
- [ ] Add Arabic RTL UI support
- [ ] Bundle Bootstrap/DataTables locally (offline CDN fallback)
- [ ] Environment-based config (`.env` instead of hardcoded `config.php`)

---

## Author

| | |
|---|---|
| **Developer** | Mahmoud Al-Jabour |
| **Role** | Full-Stack Web Developer |
| **Stack** | PHP, MySQL, Bootstrap 5, jQuery |
| **Client** | Smart Smile Clinic |

**Links:** [GitHub](https://github.com/mahmoud-aljabour) · [Portfolio](https://github.com/mahmoud-aljabour/portfolio) · [LinkedIn](https://www.linkedin.com/in/mahmoud-al-jabour/)
