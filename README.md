# 🖥️ AI-Powered PC Builder & Recommendation System

> **Full-stack PHP · MySQL · XAMPP / cPanel · Bangladesh Market**
> Version 1.0 · May 2026

An AI-powered PC Builder and Recommendation System tailored for the **Bangladeshi market**. It guides users from use-case profiling and BDT budget entry through AI-scored component matching, compatibility validation, and optimised build recommendations — using live data from local retailers (Star Tech, Ryans, Techland).

---

## 📋 Table of Contents

- [Features](#-features)
- [Tech Stack](#-tech-stack)
- [Project Structure](#-project-structure)
- [Modules](#-modules)
- [Getting Started](#-getting-started)
  - [Local Development (XAMPP)](#local-development-xampp)
  - [Production Deployment (cPanel)](#production-deployment-cpanel)
- [Configuration](#️-configuration)
- [Authentication & Roles](#-authentication--roles)
- [API Endpoints](#-api-endpoints)
- [Budget Allocation Profiles](#-budget-allocation-profiles)
- [Compatibility Rules](#-compatibility-rules)
- [Security](#-security)
- [Non-Functional Requirements](#-non-functional-requirements)
- [Open Items](#-open-items)

---

## ✨ Features

| # | Feature | Description |
|---|---------|-------------|
| 🎯 | **Purpose Profiling** | 4 use-case profiles: Gaming, Video Editing, Office, General Use |
| 💰 | **Smart Budget Allocation** | Intelligent BDT split across component categories per profile |
| ✅ | **Compatibility Checker** | CPU/MB socket, RAM gen, GPU clearance, PSU, form factor validation |
| ⚡ | **Wattage Calculator** | TDP aggregation + 20% PSU safety headroom |
| 🎮 | **FPS Estimator** | Benchmark-based FPS approximation per game title |
| 🏪 | **Store System** | Real-time prices & availability from BD retailers |
| ⚖️ | **Comparison Tool** | Side-by-side spec and benchmark view for 2–4 components |
| 🏆 | **Build Recommendation** | Top-3 optimised builds via composite scoring algorithm |
| 🔧 | **Custom Builder** | Manual component selection with live compatibility feedback |
| 📈 | **Price Tracking** | Historical price recording with Chart.js trend display |
| 🔄 | **Upgrade Advisor** | Bottleneck detection and budget-aware upgrade suggestions |
| 📊 | **User Dashboard** | Saved builds, watchlists, and activity history |
| 🤖 | **AI Chatbot** | Conversational build wizard powered by Anthropic Claude API |

---

## 🛠️ Tech Stack

| Layer | Technology |
|-------|-----------|
| **Frontend** | HTML5, CSS3, Bootstrap 5, Vanilla JS, Chart.js |
| **Backend** | PHP 8.x — single-file page pattern |
| **Database** | MySQL / MariaDB |
| **Auth** | PHP Sessions + JWT (`firebase/php-jwt`) |
| **AI / Chatbot** | Anthropic Claude API (`claude-sonnet-4-20250514`) |
| **Charts** | Chart.js — price trends, FPS estimates |
| **Dev Server** | XAMPP (Apache + MySQL) |
| **Production** | cPanel shared hosting (Apache, PHP, MySQL) |
| **Config** | `config.php` — central environment/credential store |

> **Architecture Philosophy:** Every user-facing page is a self-contained `.php` file (single-file page pattern). No framework dispatcher — shared logic lives in `includes/`, UI fragments in `templates/`.

---

## 📁 Project Structure

```
myproject/
│
├── config.php                  # ⚙️ Central config: DB, JWT, API keys, env flags
├── index.php                   # 🏠 Landing page
├── register.php                # 📝 User registration
├── login.php                   # 🔐 Login + session init
├── logout.php                  # 🚪 Session destroy + redirect
├── dashboard.php               # 📊 Personalised user dashboard
├── purpose.php                 # 🎯 Use-case wizard step
├── budget.php                  # 💰 Budget input + allocation preview
├── builds.php                  # 🏆 Top-3 build results
├── custom_builder.php          # 🔧 Manual component picker
├── compare.php                 # ⚖️ Side-by-side comparison
├── store.php                   # 🏪 Retailer listings and prices
├── price_history.php           # 📈 Price trend charts
├── upgrade.php                 # 🔄 Build analyzer + upgrade advisor
├── chatbot.php                 # 🤖 AI chatbot interface
│
├── admin/                      # 🔒 Admin-only section
│   ├── index.php               #   Admin dashboard
│   ├── components.php          #   Component catalogue CRUD
│   ├── users.php               #   User management
│   └── prices.php              #   Price and availability updates
│
├── includes/                   # ♻️ Reusable PHP logic
│   ├── db.php                  #   PDO connection handler (singleton)
│   ├── auth.php                #   Session/JWT verification
│   ├── functions.php           #   Utility helpers
│   ├── compatibility.php       #   Compatibility engine
│   ├── scoring.php             #   Build scoring algorithm
│   ├── wattage.php             #   TDP aggregation + PSU calculator
│   ├── fps.php                 #   FPS estimation logic
│   └── budget_allocator.php   #   Budget allocation per profile
│
├── templates/                  # 🎨 HTML fragment includes
│   ├── header.php              #   HTML head + nav bar
│   ├── footer.php              #   Footer + JS includes
│   ├── component_card.php      #   Reusable component card
│   └── build_card.php          #   Build result card
│
├── assets/                     # 📦 Static files
│   ├── css/style.css           #   Global stylesheet
│   ├── js/app.js               #   Global JS utilities
│   ├── js/compare.js           #   Comparison tool JS
│   └── js/custom_builder.js   #   Live compatibility JS
│
├── api/                        # 🔌 Internal AJAX endpoints (JSON)
│   ├── get_components.php      #   Filtered component list
│   ├── check_compatibility.php #   Live compatibility check
│   ├── get_builds.php          #   Build generation trigger
│   ├── chatbot_proxy.php       #   Anthropic API proxy
│   ├── price_history.php       #   Price history for Chart.js
│   └── watchlist.php           #   Add/remove watchlist items
│
├── schema.sql                  # 🗄️ Full database schema
├── migration.sql               # 🔀 Migration script (existing schema)
├── project_alpha.sql           # 📦 Original project_alpha schema
└── prd.md                      # 📄 Product Requirements Document
```

---

## 🧩 Modules

| ID | Module | Priority |
|----|--------|----------|
| M01 | User Authentication | 🔴 High |
| M02 | Purpose Selection | 🔴 High |
| M03 | Budget Recommendation | 🔴 High |
| M04 | Compatibility Checker | 🚨 Critical |
| M05 | Wattage Calculation | 🔴 High |
| M06 | FPS Estimator | 🟡 Medium |
| M07 | Store System | 🔴 High |
| M08 | Comparison Tool | 🟡 Medium |
| M09 | Build System | 🚨 Critical |
| M10 | Custom Builder | 🟡 Medium |
| M11 | Price Tracking | 🟢 Low |
| M12 | Upgrade Suggestion | 🟡 Medium |
| M13 | Dashboard | 🟡 Medium |
| M14 | AI Chatbot | 🟢 Low |

---

## 🚀 Getting Started

### Local Development (XAMPP)

1. **Install XAMPP** with Apache + MySQL + PHP 8.1+
2. **Clone / copy** the project into your XAMPP htdocs:
   ```bash
   cp -r myproject/ /opt/lampp/htdocs/myproject/
   ```
3. **Import the database schema** via phpMyAdmin:
   ```
   http://localhost/phpmyadmin → Import → schema.sql
   ```
   Then run the migration if needed:
   ```
   Import → migration.sql
   ```
4. **Configure `config.php`**:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'your_db_name');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   define('APP_ENV', 'local');
   define('BASE_URL', 'http://localhost/myproject');
   ```
5. **Start Apache + MySQL** in the XAMPP Control Panel
6. **Visit** → `http://localhost/myproject/`

---

### Production Deployment (cPanel)

1. **Upload** the project ZIP via cPanel File Manager → extract into `public_html/`
2. **Create database** in cPanel → MySQL Databases → assign all privileges
3. **Import schema** via cPanel phpMyAdmin (`schema.sql`, then `migration.sql`)
4. **Edit `config.php`** with production credentials:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'cpanel_dbname');
   define('DB_USER', 'cpanel_user');
   define('DB_PASS', 'strong_password');
   define('APP_ENV', 'production');
   define('BASE_URL', 'https://yourdomain.com');
   define('ANTHROPIC_API_KEY', 'sk-ant-...');
   define('JWT_SECRET', 'your-256-bit-random-secret');
   ```
5. **Verify PHP 8.1+** in cPanel → MultiPHP Manager
6. **App is live** — No Composer, no Node, no build step required ✅

> **Zero-modification portability:** All environment-sensitive values live exclusively in `config.php`. Move from XAMPP to production by uploading files and updating one file.

---

## ⚙️ Configuration

All environment-specific settings are managed in `config.php`:

| Setting | Description |
|---------|-------------|
| `DB_HOST` | Database host |
| `DB_NAME` | Database name |
| `DB_USER` | Database username |
| `DB_PASS` | Database password |
| `JWT_SECRET` | 256-bit JWT signing key (unique per deployment) |
| `ANTHROPIC_API_KEY` | Claude API key for the AI chatbot |
| `BASE_URL` | Full URL prefix for assets and redirects |
| `APP_ENV` | `'local'` or `'production'` |
| `SESSION_LIFETIME` | Seconds before session expiry |
| `PSU_SAFETY_MARGIN` | Float, default `1.20` (20% headroom) |

> ⚠️ **Never commit `config.php` with real credentials to version control.**

---

## 🔐 Authentication & Roles

### Registration Flow
1. Submit name, email, password on `register.php`
2. Sanitise inputs; check email uniqueness
3. `password_hash()` — bcrypt, cost ≥ 12
4. Insert user record; show confirmation

### Login Flow
1. Submit email + password
2. `password_verify()` against stored hash
3. Start PHP session; write `user_id`, `role`, `name`
4. Generate JWT; set `HttpOnly` + `Secure` cookie
5. Redirect to `dashboard.php`

### Role-Based Access

| Role | Accessible Pages | Restrictions |
|------|-----------------|--------------|
| **Guest** | `index.php`, `register.php`, `login.php` | No authenticated pages |
| **User** | All user-facing pages | Cannot access `admin/` |
| **Admin** | All pages including `admin/` | Full access; manages catalogue and users |

---

## 🔌 API Endpoints

All endpoints in `api/` require a **valid session cookie** and return `Content-Type: application/json`.

| Endpoint | Accepts | Returns |
|----------|---------|---------|
| `api/get_components.php` | `category`, `budget_max`, `purpose` | Matching components array |
| `api/check_compatibility.php` | `component_ids` object | Compatibility results array |
| `api/get_builds.php` | `purpose`, `budget` | Top 3 scored builds |
| `api/chatbot_proxy.php` | `messages` array | Claude model response text |
| `api/price_history.php` | `component_id` | Chart.js label/value arrays |
| `api/watchlist.php` | `action` (add/remove), `component_id` | Updated watchlist count |

---

## 💰 Budget Allocation Profiles

Budget is split across component categories based on the selected use-case profile:

| Category | 🎮 Gaming | 🎬 Video Editing | 🏢 Office | 💻 General |
|----------|-----------|-----------------|-----------|------------|
| CPU | 20% | 25% | 15% | 20% |
| Motherboard | 12% | 12% | 12% | 12% |
| RAM | 10% | 15% | 12% | 12% |
| GPU | 35% | 20% | 5% | 18% |
| Storage | 8% | 12% | 20% | 14% |
| PSU | 7% | 7% | 8% | 8% |
| Case | 5% | 5% | 15% | 8% |
| Cooling | 3% | 4% | 13% | 8% |

---

## ✅ Compatibility Rules

| Rule | Validation Logic |
|------|-----------------|
| CPU ↔ Motherboard | Socket types must match (e.g. AM5 ↔ AM5, LGA1700 ↔ LGA1700) |
| RAM ↔ Motherboard | RAM generation must match board spec (DDR4 / DDR5) |
| GPU ↔ Case | GPU length (mm) ≤ case max GPU clearance (mm) |
| PSU Wattage | Calculated TDP × 1.20 safety factor ≤ PSU rated wattage |
| MB ↔ Case | Motherboard form factor supported by case (ATX / mATX / ITX) |
| CPU Cooler ↔ Case | Cooler height (mm) ≤ case max cooler clearance (mm) |
| RAM Slots | Number of sticks selected ≤ motherboard slot count |
| Storage Interface | NVMe → M.2 / PCIe slot; SATA → SATA port availability |

---

## 🔒 Security

- **XSS Prevention** — `htmlspecialchars()` on all output
- **SQL Injection** — PDO prepared statements throughout; zero raw string interpolation
- **CSRF Protection** — Synchroniser tokens on all state-changing forms
- **Password Storage** — bcrypt via `password_hash()`, cost ≥ 12
- **JWT** — `HttpOnly`, `Secure`, `SameSite=Lax` cookies; 256-bit secret
- **API Key** — `ANTHROPIC_API_KEY` stored only in `config.php`, never echoed or logged
- **Rate Limiting** — AI chatbot limited to 20 requests/user/hour via proxy
- **Error Display** — Disabled in production (`APP_ENV=production`)
- **HTTPS** — Enforced via cPanel AutoSSL; HTTP redirected via `.htaccess`
- **`config.php`** — Placed above `public_html` where possible; otherwise `.htaccess Deny from all`
- **OWASP Top 10** mitigated

---

## 📐 Non-Functional Requirements

| Category | Requirement |
|----------|-------------|
| **Performance** | Build generation (top 3) completes within 3s for catalogues up to 500 components. Chart.js charts load within 1.5s |
| **Scalability** | Functional on shared cPanel hosting (512 MB RAM, PHP 8.1, MySQL 5.7+) without modification |
| **Browser Support** | Chrome 120+, Firefox 120+, Safari 17+, Edge 120+. Responsive at ≥ 360px |
| **Accessibility** | All form elements carry `aria-label`. Colour contrast ratio ≥ 4.5:1. Keyboard navigable |
| **Maintainability** | Each page file ≤ 600 lines. Business logic lives in `includes/`, not inline in page files |
| **Data Integrity** | Existing DB schema must not be altered by application code. All schema changes are manual and owner-approved |

---

## 🗂️ Open Items

| Item | Status |
|------|--------|
| Email-based password reset | Pending — confirm SMTP provider availability for v1 |
| Retailer data import | Manual-only vs CSV bulk-import tool in admin panel |
| FPS model coefficients | Require validation against real-world results for ≥ 5 titles |
| Admin CSV export | Clarify whether required for v1 |

---

## 📄 License & Confidentiality

> **Confidential — Internal Use Only**
> Version 1.0 · May 2026

This project is proprietary software intended for internal use. All rights reserved.

---

*Built with ❤️ for the Bangladesh PC enthusiast community.*
