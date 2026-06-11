# PCBuilder BD — Smart PC Build System

A full-stack web platform for the Bangladeshi market that helps users configure, compare, and purchase custom PC builds with real-time compatibility checking, AI-powered recommendations, and multi-retailer price tracking.

---

## Table of Contents

- [Features](#features)
- [Tech Stack](#tech-stack)
- [Quick Start — Windows](#quick-start--windows-xampp)
- [Quick Start — Linux](#quick-start--linux-xampp)
- [Configuration](#configuration)
- [Test Credentials](#test-credentials)
- [Project Structure](#project-structure)
- [Database Schema](#database-schema)
- [API Endpoints](#api-endpoints)
- [Build Wizard Algorithm](#build-wizard-algorithm)
- [SQL Query Reference](#sql-queries--subqueries)
- [Contributors](#contributors)

---

## Features

- **Build Wizard** — 3-step guided flow: Purpose → Budget → Top 3 optimized builds
- **Custom PC Builder** — Drag-and-drop with real-time compatibility checking
- **Component Store** — Multi-retailer price comparison with filtering/sorting
- **AI Chatbot** — Natural language build advice + PDF export
- **Community Forum** — Posts, tags, upvotes, communities, image uploads
- **Price Tracking** — Historical charts + watchlist alerts
- **Upgrade Advisor** — Suggests next-tier upgrades within budget
- **Admin Panel** — Full CRUD for components, users, prices, sponsors
- **Dark/Light Theme** — Persistent toggle, fully responsive (Bootstrap 5.3)

---

## Tech Stack

| Layer | Technology |
|-------|-----------|
| **Backend** | PHP 8.0 (vanilla) |
| **Database** | MySQL 8.0 / MariaDB 10.6+ |
| **Frontend** | HTML5, Vanilla CSS, JavaScript ES6+ |
| **UI Framework** | Bootstrap 5.3.3 |
| **Charts** | Chart.js |
| **Server** | Apache (via XAMPP) |

---

## Quick Start — Windows (XAMPP)

### Step 1 — Install XAMPP

1. Download **XAMPP for Windows** from [https://www.apachefriends.org/](https://www.apachefriends.org/)
   - Choose version **8.0.x** or higher
2. Run the installer → install to `C:\xampp` (default)
3. Open **XAMPP Control Panel** (run as Administrator)

### Step 2 — Start Apache & MySQL

In the XAMPP Control Panel, click **Start** next to:
- ✅ **Apache**
- ✅ **MySQL**

Both status lights should turn green.

> **Port conflict?** If Apache fails to start, another app (Skype, IIS) may be using port 80.
> Click **Config → httpd.conf** and change `Listen 80` to `Listen 8080`, then restart.

### Step 3 — Clone the Repository

Open **Command Prompt** (Win + R → type `cmd` → Enter):

```cmd
cd C:\xampp\htdocs
git clone https://github.com/SSea-man/Smart-PCBuildSystem.git myproject
cd myproject
```

> **Git not installed?** Download from [https://git-scm.com/download/win](https://git-scm.com/download/win) and install with default options.

### Step 4 — Import the Database

Open **Command Prompt**:

```cmd
C:\xampp\mysql\bin\mysql.exe -u root -e "CREATE DATABASE smart_pc_build CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

C:\xampp\mysql\bin\mysql.exe -u root smart_pc_build < C:\xampp\htdocs\myproject\project_alpha.sql
```

**Or use phpMyAdmin (easier for Windows):**
1. Open browser → go to `http://localhost/phpmyadmin`
2. Click **New** in the left sidebar
3. Database name: `smart_pc_build` → Encoding: `utf8mb4_unicode_ci` → **Create**
4. Click the new `smart_pc_build` database → **Import** tab
5. Choose file: `C:\xampp\htdocs\myproject\project_alpha.sql` → **Go**

### Step 5 — Seed Component Data

```cmd
C:\xampp\php\php.exe C:\xampp\htdocs\myproject\seed_components_v2.php
```

Expected output:
```
Seeding complete: 165 inserted, 0 skipped (already exist).
```

### Step 6 — Configuration

You're done! The `BASE_URL` in `config.php` is completely dynamic. It will automatically detect if you are running on port `80`, port `8080`, or sharing your project over a local IP address. You do not need to edit `config.php`.

### Step 7 — Set Upload Folder Permissions

In **File Explorer**, navigate to `C:\xampp\htdocs\myproject\uploads\`

Right-click the `uploads` folder itself → **Properties → Security** → Edit and ensure **Full control** is checked for **Everyone** (or your current user).

> Note: On Windows with XAMPP, folder permissions are usually granted by default. You can skip this step unless you see an error like "Failed to save image" when posting in the forum.

### Step 8 — Open the App

Open your browser and go to:

| Apache Port | URL |
|-------------|-----|
| 80 (default) | `http://localhost/myproject` |
| 8080 | `http://localhost:8080/myproject` |

---

## Quick Start — Linux (XAMPP)

### Step 1 — Install XAMPP

```bash
# Download XAMPP 8.0 for Linux
wget https://sourceforge.net/projects/xampp/files/XAMPP%20Linux/8.0.30/xampp-linux-x64-8.0.30-0-installer.run

# Make executable and install
chmod +x xampp-linux-x64-8.0.30-0-installer.run
sudo ./xampp-linux-x64-8.0.30-0-installer.run
```

### Step 2 — Start XAMPP

```bash
sudo /opt/lampp/lampp start
```

Verify services are running:

```bash
# Check Apache
curl -s -o /dev/null -w "%{http_code}" http://localhost/
# Should print: 200

# Check MySQL
/opt/lampp/bin/mysql -u root -e "SHOW DATABASES;"
```

> **Port 80 in use?** Edit `/opt/lampp/etc/httpd.conf`, change `Listen 80` to `Listen 5173`, then restart: `sudo /opt/lampp/lampp restart`

### Step 3 — Clone the Repository

```bash
cd /opt/lampp/htdocs
sudo git clone https://github.com/SSea-man/Smart-PCBuildSystem.git myproject
sudo chown -R $USER:$USER /opt/lampp/htdocs/myproject
cd /opt/lampp/htdocs/myproject
```

### Step 4 — Import the Database

```bash
# Create database
/opt/lampp/bin/mysql -u root -e "CREATE DATABASE smart_pc_build CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Import schema + data
/opt/lampp/bin/mysql -u root smart_pc_build < /opt/lampp/htdocs/myproject/project_alpha.sql
```

### Step 5 — Seed Component Data

```bash
/opt/lampp/bin/php /opt/lampp/htdocs/myproject/seed_components_v2.php
```

Expected output:
```
Seeding complete: 165 inserted, 0 skipped (already exist).
```

### Step 6 — Configuration

You're done! The `BASE_URL` in `config.php` is now completely dynamic and will automatically detect if you are running on port `80`, port `5173`, or using a local IP address to share over your network. You do not need to edit `config.php`.

### Step 7 — Set Upload Permissions

To ensure image uploads (like forum posts) work perfectly regardless of which user Apache runs under, grant full write access to the uploads directory:

```bash
sudo chmod -R 777 /opt/lampp/htdocs/myproject/uploads/
```

### Step 8 — Open the App

```
http://localhost/myproject
```

Or if using port 5173:
```
http://localhost:5173/myproject
```

---

## One-Command Automated Setup (Linux Only)

```bash
sudo bash /opt/lampp/htdocs/myproject/setup.sh
```

This script automatically: starts XAMPP → creates the database → imports schema → seeds components → sets permissions → verifies the installation.

---

## Troubleshooting

| Problem | Windows Fix | Linux Fix |
|---------|-------------|-----------|
| Apache won't start | Change port in httpd.conf (80→8080) | `sudo /opt/lampp/lampp stopapache && sudo /opt/lampp/lampp startapache` |
| MySQL won't start | Check if MySQL service is already running (Task Manager) | `sudo /opt/lampp/lampp stopmysql && sudo /opt/lampp/lampp startmysql` |
| Blank page / 500 error | Check `C:\xampp\apache\logs\error.log` | Check `/opt/lampp/logs/error_log` |
| DB connection failed | Verify DB name is `smart_pc_build` in `config.php` | Same |
| Upload folder error | Check folder permissions via File Explorer | `sudo chmod 777 uploads/components uploads/forum` |
| `git` not found | Install from git-scm.com | `sudo apt install git` |
| Port 5173 not working | Change BASE_URL to `http://localhost/myproject` | Check `httpd.conf` for `Listen 5173` |

---

## Configuration

All configuration is in `config.php`:

| Constant | Description | Default |
|----------|-------------|---------|
| `DB_HOST` | Database host | `localhost` |
| `DB_NAME` | Database name | `smart_pc_build` |
| `DB_USER` | Database user | `root` |
| `DB_PASS` | Database password | `` (empty) |
| `BASE_URL` | Application base URL | `http://localhost:5173/myproject` |
| `APP_ENV` | Environment (`local`/`production`) | `local` |
| `SESSION_LIFETIME` | Session timeout (seconds) | `7200` |
| `CHATBOT_RATE_LIMIT` | Max chatbot requests/hour | `20` |
| `PSU_SAFETY_MARGIN` | PSU wattage safety multiplier | `1.20` |
| `TOP_BUILDS_LIMIT` | Builds to recommend | `3` |

---

## Test Credentials

> All sample accounts use password: **`pass1234`**

### Admin Accounts

| Email | Password | Name |
|-------|----------|------|
| `shadman1@pcbuild.com` | `pass1234` | Shadman Ahammad |

### Moderator Account

| Email | Password |
|-------|----------|
| `rahim@email.com` | `pass1234` |

### Sample User Accounts

| Email | Password |
|-------|----------|
| `rahim2@pcbuild.com` | `pass1234` |
| `karim3@pcbuild.com` | `pass1234` |
| `tanvir5@pcbuild.com` | `pass1234` |

---

## Project Structure

```
myproject/
├── admin/                    # Admin panel (components, users, prices, sponsors)
├── api/                      # REST-style API endpoints (15 files)
├── assets/
│   ├── css/style.css         # Global stylesheet + CSS variables
│   └── js/                   # app.js, custom_builder.js, compare.js
├── includes/                 # Backend logic modules
│   ├── auth.php              # Authentication & session helpers
│   ├── db.php                # PDO database connection & query helpers
│   ├── functions.php         # Component queries, utility functions
│   ├── compatibility.php     # Hardware compatibility checker
│   ├── scoring.php           # Build scoring & recommendation engine
│   ├── budget_allocator.php  # Budget split profiles per purpose
│   ├── wattage.php           # TDP calculation & PSU recommendation
│   └── fps.php               # FPS estimation from benchmarks
├── templates/                # header.php, footer.php, build_card.php
├── uploads/
│   ├── components/           # Component images (user-uploaded)
│   └── forum/                # Forum post images
├── config.php                # App configuration & constants
├── index.php                 # Landing page
├── store.php                 # Component catalogue
├── chatbot.php               # AI chatbot interface
├── forum.php                 # Forum listing
├── dashboard.php             # User dashboard
├── custom_builder.php        # Custom PC builder
├── project_alpha.sql         # Full database schema + data dump
├── seed_components_v2.php    # Component seeder (idempotent, INSERT IGNORE)
├── setup.sh                  # Automated Linux setup script
└── fix_duplicates.sql        # One-time duplicate cleanup script
```

---

## Database Schema

24 tables in `smart_pc_build` database:

| Table | Purpose |
|-------|---------|
| `user` | Accounts (email, password hash, role: user/moderator/admin) |
| `component` | Hardware with specs (socket, RAM gen, TDP, form factor, etc.) |
| `storeavailability` | Component prices and stock per store |
| `store` | Retailer information |
| `build` | Saved user builds |
| `buildcomponent` | Components in each saved build |
| `post` | Forum posts |
| `comment` | Post comments |
| `vote` | Upvote/downvote on posts |
| `tag` / `posttag` | Forum tags |
| `community` / `community_member` | Forum community groups |
| `watchlist` | User component watchlist |
| `pricetracking` | Historical price records |
| `chatbot` | Chatbot conversation history |
| `fps_profiles` | FPS benchmark data for CPU+GPU combos |
| `upgradesuggestion` | Upgrade recommendations |
| `user_preferences` | User settings |

---

## User Roles

| Role | Permissions |
|------|------------|
| **User** | Browse, build, save, forum, chatbot, watchlist |
| **Moderator** | All User permissions + forum announcement badges |
| **Admin** | All permissions + full admin panel access |

---

## API Endpoints

| Endpoint | Method | Auth | Description |
|----------|--------|------|-------------|
| `/api/get_components.php` | GET | No | Fetch components by category |
| `/api/check_compatibility.php` | POST | No | Validate component compatibility |
| `/api/save_build.php` | POST | Yes | Save build to dashboard |
| `/api/get_builds.php` | GET | Yes | Get user's saved builds |
| `/api/delete_build.php` | POST | Yes | Delete a saved build |
| `/api/vote.php` | POST | Yes | Upvote/downvote forum post |
| `/api/watchlist.php` | POST | Yes | Add/remove from watchlist |
| `/api/price_history.php` | GET | No | Price history for a component |
| `/api/chatbot_proxy.php` | POST | Yes | Send message to AI chatbot |
| `/api/create_community.php` | POST | Yes | Create forum community |
| `/api/join_community.php` | POST | Yes | Join/leave a community |
| `/api/delete_forum_item.php` | POST | Yes | Delete post or comment |
| `/api/get_sponsor.php` | GET | No | Get active sponsor ad |

---

## Build Wizard Algorithm

### Phase 1 — Platform Discovery
- Loads all motherboards; for each finds compatible CPUs (socket match) and RAM (DDR gen match)
- Rejects platforms with missing socket data or no compatible CPU

### Phase 2 — Component Selection
- Picks highest-benchmark component within budget per category
- Fallback: cheapest available if nothing fits budget
- PSU is TDP-aware: total TDP × 1.2 safety margin

### Phase 3 — Scoring
```
Score = (Performance × 0.60) + (Value × 0.30) + (Availability × 0.10)
```

### Budget Allocation Profiles

| Category | Gaming | Video Editing | Office | General |
|----------|--------|---------------|--------|---------|
| CPU | 20% | 25% | 15% | 20% |
| GPU | 35% | 20% | 5% | 18% |
| Motherboard | 12% | 12% | 12% | 12% |
| RAM | 10% | 15% | 12% | 12% |
| Storage | 8% | 12% | 20% | 14% |
| PSU | 7% | 7% | 8% | 8% |
| Case | 5% | 5% | 15% | 8% |
| Cooling | 3% | 4% | 13% | 8% |

---

## SQL Queries & Subqueries

**Total: 121 unique SQL statements across 28 files**

| Type | Count |
|------|-------|
| SELECT | 62 |
| INSERT | 22 |
| UPDATE | 6 |
| DELETE | 12 |
| SELECT COUNT | 12 |
| Queries with subqueries | **27** |

### Subquery Types Used

| Subquery Type | Count | Example Location |
|---------------|-------|-----------------|
| **Derived Table** (FROM clause) | 8 | `component_base_sql()` in functions.php |
| **Correlated Scalar** (SELECT clause) | 16 | `forum.php`, `forum_post.php` |
| **EXISTS** (WHERE clause) | 1 | `forum.php` announcement filter |
| **Wrapper Subquery** | 2 | `store.php` count, chatbot budget query |

### 1. The Component Catalog Matrix (Advanced JOINs & Dynamic Categorization)
* **Location:** `includes/functions.php` (Lines 101 to 133)
* **Variable:** `component_base_sql()`
* **What it does:** This is the backbone of the entire store and builder. It pulls every hardware component and cross-references it with live pricing from multiple retailers.
* **Why it's advanced:**
  - **Dynamic Categorization (CASE WHEN):** We use a massive `CASE` statement with `LIKE` operators to intelligently map raw category strings into clean, unified system categories.
  - **Aggregation Subqueries:** It uses a subquery `(SELECT component_id, MIN(price) ... GROUP BY component_id)` to group multiple store prices together and dynamically fetch the absolute lowest available price for the user.
  - **LEFT JOINS & COALESCE:** It uses `LEFT JOIN` so components without prices still appear, and `COALESCE` to default null prices to 0.

```sql
SELECT c.*,
    CASE WHEN c.type LIKE 'CPU (%' THEN 'CPU' ELSE c.type END AS category,
    COALESCE(sa.price, 0) AS price_bdt,
    COALESCE(sa.stock_status, 'Out of Stock') AS stock_status_raw,
    COALESCE(s.store_name, '') AS retailer
FROM component c
LEFT JOIN (
    SELECT component_id, MIN(price) AS price, stock_status, store_id
    FROM storeavailability
    GROUP BY component_id
) sa ON sa.component_id = c.component_id
LEFT JOIN store s ON s.store_id = sa.store_id;
```

### 2. The Forum Feed Generator (Nested Subqueries & String Aggregation)
* **Location:** `forum.php` (Lines 59 to 74)
* **Variable:** `$posts`
* **What it does:** Generates the social feed for the community forum, loading posts along with dynamic metadata.
* **Why it's advanced:**
  - **Inline Subqueries:** Instead of running 50 different queries to get the comment counts and upvote counts for each post, we use highly optimized inline subqueries directly in the `SELECT` statement.
  - **GROUP_CONCAT:** This is a very advanced aggregation function that takes multiple rows of tags related to a post and merges them into a single comma-separated string (e.g., "gaming,gpu,help") inside the SQL engine itself.

```sql
SELECT p.*, u.user_name, c.name AS community_name,
    (SELECT COUNT(*) FROM comment comm WHERE comm.post_id = p.post_id) AS comment_count,
    (SELECT COUNT(*) FROM vote v WHERE v.post_id = p.post_id AND v.vote_type = 'upvote') AS score,
    (SELECT GROUP_CONCAT(t.name SEPARATOR ',') FROM posttag pt JOIN tag t ON pt.tag_id = t.tag_id WHERE pt.post_id = p.post_id) AS tags
FROM post p
JOIN user u ON p.user_id = u.user_id
LEFT JOIN community c ON p.community_id = c.community_id
ORDER BY p.created_at DESC LIMIT 20;
```

### 3. The Live Watchlist Fetcher (Multi-Table Relational JOINs)
* **Location:** `dashboard.php` (Lines 15 to 25)
* **Variable:** `$watchlist`
* **What it does:** Retrieves the user's saved wishlist items and attaches the current live price and retailer name.
* **Why it's advanced:** It cleanly joins `watchlist`, `component`, `storeavailability`, and `store` in a single query. By using the bridge tables, the user can see real-time price drops on items they saved weeks ago without the database storing redundant price data.

```sql
SELECT c.component_id as id, c.component_name as name, c.type,
       COALESCE(sa.price,0) as price_bdt, COALESCE(s.store_name,"") as retailer,
       w.added_at
FROM watchlist w
JOIN component c ON c.component_id = w.component_id
LEFT JOIN (SELECT component_id, MIN(price) as price, store_id FROM storeavailability GROUP BY component_id) sa ON sa.component_id = c.component_id
LEFT JOIN store s ON s.store_id = sa.store_id
WHERE w.user_id = ? ORDER BY w.added_at DESC LIMIT 8;
```

### 4. Price History Trend Analysis (Date Math & Time-Series Aggregation)
* **Location:** `dashboard.php` (Lines 30 to 34)
* **Variable:** `$history`
* **What it does:** Fetches the raw data used to draw the dynamic 30-day price trend charts for individual components.
* **Why it's advanced:** It uses `DATE(changed_at)` to cast timestamps down to the day, and uses `WHERE changed_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)` to let the database handle the time-series boundary filtering automatically.

```sql
SELECT DATE(changed_at) as d, new_price 
FROM pricetracking
WHERE component_id=? AND changed_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) 
ORDER BY d;
```

### 5. Community Discovery Engine (Correlated Subqueries)
* **Location:** `forum.php` (Lines 76 to 81)
* **Variable:** `$sidebar_communities`
* **What it does:** Ranks the top 5 most popular forum communities to show in the sidebar, and instantly determines if the currently logged-in user is already a member.
* **Why it's advanced:** It uses Correlated Subqueries to count the members dynamically and accepts the `user_id` as a parameter to simultaneously check if the user belongs to the community.

```sql
SELECT c.community_id, c.name,
       (SELECT COUNT(*) FROM community_member cm WHERE cm.community_id = c.community_id) AS member_count,
       (SELECT COUNT(*) FROM community_member cm WHERE cm.community_id = c.community_id AND cm.user_id = ?) AS is_joined
FROM community c
ORDER BY member_count DESC LIMIT 5;
```

---

## Contributors

| Name | Role |
|------|------|
| **Shadman Ahammad Shanto** | Founder & CEO |
| **Shah Mohammed Seaman** | CTO |
| **Jim Hossain** | Co-Founder & CFO |

---

## License

Developed as part of an academic initiative at United International University. All rights reserved.
