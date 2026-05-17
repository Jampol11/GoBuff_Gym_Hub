# GoBuff: Gym Hub

## Overview

GoBuff: Gym Hub is a gym management and fitness platform designed to improve gym operations and support individuals in achieving healthier lifestyles. The system provides features for workout scheduling, membership management, attendance monitoring, fitness coaching, and nutrition awareness. It aims to create a more accessible and organized fitness environment for gym coaches, workout enthusiasts, and health-conscious individuals.

---

## Problem Statement

- **Limited availability of time and resources** for workout engagement among individuals whose responsibilities are divided across family and personal commitments.
- **Low prioritization of workout-related spending** among individuals who perceive exercise as a leisure activity.
- **Low level of nutrition knowledge** among individuals, limiting their ability to make informed dietary decisions.

---

## Objectives

1. Maximize workout frequency among time-constrained individuals to achieve an **Activity Resumption Index of not more than 5.74 out of 25**.
2. Secure increased wallet share by converting **30% of "leisure-only" users into "health-investors"** through value-based messaging.
3. Increase the consistency of healthy dietary practices among individuals to at least **81% adherence** to recommended daily nutrition guidelines.

---

## Target Users / Personas

### 1. Gym Coach / Fitness Coach
Professionals who manage workout programs, monitor member progress, provide fitness guidance, and assist users in achieving their health goals.

### 2. Workout Enthusiast
Individuals who actively participate in physical fitness activities and seek convenient tools for workout scheduling, gym attendance tracking, and performance monitoring.

### 3. Health-Conscious Individual
People who aim to maintain a healthy lifestyle by improving their physical activity, nutrition habits, and overall wellness through guided fitness support.

---

## Features

| Module | Description |
|---|---|
| Authentication | Login, register, logout, CSRF, session management |
| Role-Based Access | Gym Owner, Admin, Marketing, Trainer, Maintenance, Member |
| Dashboard | Analytics cards, charts, expiring memberships, recent activity |
| Members | Full CRUD, profile, photo upload, CSV export |
| Memberships | Plans, approval workflow, payment recording, expiry tracking |
| Check-Ins | Manual check-in/out, membership code validation, stats |
| Bookings | Trainer session booking, conflict detection |
| Fitness Plans | Trainer-assigned workout programs |
| Nutrition Plans | Dietary guidance with macro tracking |
| Progress Tracking | Weight, BMI, body fat monitoring |
| Dietary Log | Daily meal logging with calorie/macro tracking |
| Equipment | Inventory management with condition status |
| Maintenance | Issue reporting, verification, completion workflow |
| Staff Attendance | Clock-in/out, CSV export |
| Campaigns | Marketing promotions with banner images |
| Notifications | System alerts, membership expiry, broadcast messages |
| Security | PDO prepared statements, XSS protection, CSRF tokens, bcrypt |

---

## Requirements

- PHP 8.0+
- MySQL 8.0+ / MariaDB 10.4+
- Apache with `mod_rewrite` enabled
- XAMPP / Laragon / WAMP

---

## Installation

### 1. Clone / Copy Files

Place the `GoBuff` folder inside your web server root:
- XAMPP: `C:/xampp/htdocs/GoBuff`
- Laragon: `C:/laragon/www/GoBuff`

### 2. Create Database

```sql
mysql -u root -p < database/gobuff.sql
```

Or import via phpMyAdmin:
1. Open phpMyAdmin
2. Create database `gobuff_db`
3. Import `database/gobuff.sql`

### 3. Configure Database

Edit `config/database.php`:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'gobuff_db');
define('DB_USER', 'root');
define('DB_PASS', '');  // your MySQL password
```

### 4. Configure App URL

Edit `config/config.php`:

```php
define('APP_URL', 'http://localhost/GoBuff/public');
```

### 5. Enable Apache mod_rewrite

Ensure `mod_rewrite` is enabled in `httpd.conf`:
```
LoadModule rewrite_module modules/mod_rewrite.so
```

And `AllowOverride All` is set for your htdocs directory.

### 6. Create Upload Directories

The following directories need write permissions:
```
public/assets/uploads/members/
public/assets/uploads/campaigns/
storage/logs/
storage/backups/
```

### 7. Access the Application

Open your browser: `http://localhost/GoBuff/public`

---

## Default Login Credentials

> ⚠️ **Change all passwords immediately after first login!**

| Role | Email | Password |
|---|---|---|
| Gym Owner | owner@gobuff.com | password |
| Admin | admin@gobuff.com | password |
| Marketing | marketing@gobuff.com | password |
| Trainer | trainer@gobuff.com | password |
| Maintenance | maintenance@gobuff.com | password |
| Member | member@gobuff.com | password |

---

## Folder Structure

```
GoBuff/
├── app/
│   ├── controllers/     # Request handlers
│   ├── models/          # Database models
│   ├── views/           # HTML templates
│   ├── core/            # Framework core (Router, DB, Session, etc.)
│   ├── middleware/       # Auth & role middleware
│   └── helpers/         # Utility functions
├── public/              # Web root (point Apache here)
│   ├── assets/          # CSS, JS, images, uploads
│   ├── index.php        # Front controller
│   └── .htaccess        # URL rewriting
├── routes/
│   └── web.php          # All application routes
├── database/
│   └── gobuff.sql       # Full schema + seed data
├── config/
│   ├── config.php       # App configuration
│   └── database.php     # DB credentials
└── storage/
    ├── logs/            # Error & activity logs
    └── backups/         # Database backups
```

---

## Security Features

- **Passwords**: bcrypt hashed (cost 12)
- **SQL Injection**: PDO prepared statements throughout
- **XSS**: `htmlspecialchars()` on all output via `e()` helper
- **CSRF**: Token validation on all POST forms
- **Session**: Secure session configuration, timeout, regeneration
- **File Uploads**: MIME type validation, random filename generation
- **Headers**: X-Frame-Options, X-XSS-Protection, X-Content-Type-Options
- **Directory Listing**: Disabled via `.htaccess`

---

## Roles & Permissions

| Feature | Owner | Admin | Marketing | Trainer | Maintenance | Member |
|---|:---:|:---:|:---:|:---:|:---:|:---:|
| Dashboard | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Members CRUD | ✅ | ✅ | ❌ | 👁 | ❌ | ❌ |
| Memberships | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ |
| Check-Ins | ✅ | ✅ | ❌ | ✅ | ❌ | ✅ |
| Bookings | ✅ | ✅ | ❌ | ✅ | ❌ | ✅ |
| Fitness Plans | ✅ | ✅ | ❌ | ✅ | ❌ | 👁 |
| Equipment | ✅ | ✅ | ❌ | ❌ | ✅ | 👁 |
| Maintenance | ✅ | ✅ | ❌ | ❌ | ✅ | ❌ |
| Attendance | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ |
| Campaigns | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ |
| Notifications | ✅ | ✅ | ❌ | ❌ | ❌ | 👁 |
| Dietary Log | ✅ | ✅ | ❌ | ✅ | ❌ | ✅ |

✅ Full Access | 👁 View Only | ❌ No Access

---

## Tech Stack

- **Backend**: PHP 8.0+
- **Database**: MySQL 8.0+
- **Frontend**: HTML5, CSS3, Bootstrap 5.3, JavaScript ES6+
- **Charts**: Chart.js 4.4
- **Icons**: Bootstrap Icons 1.11
- **Fonts**: Inter (Google Fonts)
- **Architecture**: MVC (custom framework, no dependencies)

---

## License

MIT License — Free to use and modify for personal and commercial projects.

---

*Built with ❤️ for fitness professionals and gym management.*
