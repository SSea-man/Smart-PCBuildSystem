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

### Prerequisites

Before you begin, make sure you have the following installed on your Windows PC:

| Software | Download Link | Purpose |
|----------|--------------|---------|
| **XAMPP 8.0+** | [apachefriends.org](https://www.apachefriends.org/) | Provides Apache, MySQL & PHP |
| **Git** | [git-scm.com](https://git-scm.com/download/win) | To clone the repository |

---

### Step 1 — Install & Start XAMPP

1. Download and install **XAMPP** to `C:\xampp` (default path).
2. Open **XAMPP Control Panel** (right-click → Run as Administrator).
3. Click **Start** next to both **Apache** and **MySQL**.
4. Both status indicators should turn **green**.

> **Warning: Port 80 conflict?** If Apache fails to start (port 80 is used by Skype, IIS, etc.):
> 1. In XAMPP Control Panel, click **Config** next to Apache → open **httpd.conf**
> 2. Find `Listen 80` and change it to `Listen 8080`
> 3. Find `ServerName localhost:80` and change it to `ServerName localhost:8080`
> 4. Save the file and click **Start** again.

---

### Step 2 — Clone the Repository

Open **Command Prompt** (`Win + R` → type `cmd` → press Enter) and run:

```cmd
cd C:\xampp\htdocs
git clone https://github.com/SSea-man/Smart-PCBuildSystem.git myproject
```

After cloning, verify the folder exists:
```cmd
dir C:\xampp\htdocs\myproject
```
You should see files like `index.php`, `config.php`, `project_alpha.sql`, etc.

---

### Step 3 — Create & Import the Database

**Option A — Using Command Prompt (Recommended):**

```cmd
C:\xampp\mysql\bin\mysql.exe -u root -e "CREATE DATABASE smart_pc_build CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

C:\xampp\mysql\bin\mysql.exe -u root smart_pc_build < C:\xampp\htdocs\myproject\project_alpha.sql
```

> If the second command shows no output, it means it worked successfully.

**Option B — Using phpMyAdmin (GUI method):**

1. Open your browser → go to `http://localhost/phpmyadmin` (or `http://localhost:8080/phpmyadmin` if you changed the port).
2. Click **New** in the left sidebar.
3. Type `smart_pc_build` as the database name → select `utf8mb4_unicode_ci` → click **Create**.
4. Click the new `smart_pc_build` database in the left sidebar → click the **Import** tab at the top.
5. Click **Choose File** → navigate to `C:\xampp\htdocs\myproject\project_alpha.sql` → click **Open**.
6. Scroll down and click **Import** (or **Go**).
7. You should see a green success message.

---

### Step 4 — Seed Hardware Components

Open **Command Prompt** and run:

```cmd
C:\xampp\php\php.exe C:\xampp\htdocs\myproject\seed_components_v2.php
```

You should see:
```
Seeding complete: 165 inserted, 0 skipped (already exist).
```

> **Warning: Warning about `SERVER_PORT`?** This is a harmless warning that appears when running PHP from the command line. It does not affect functionality. You can safely ignore it.

---

### Step 5 — Set Upload Folder Permissions

This step ensures forum image uploads work correctly by granting full write permissions to the web server.

Open **Command Prompt** (as Administrator) and run:

```cmd
icacls "C:\xampp\htdocs\myproject\uploads" /grant Everyone:(OI)(CI)F /T
```

> **Note:** This command uses Windows `icacls` to grant "Everyone" full access (`F`) to the `uploads` folder and propagates it to all subdirectories/files (`(OI)(CI)` and `/T`). Run this if you see errors like *"Failed to save image"* when posting in the forum.

---

### Step 6 — Open the App

Open your browser and visit:

| Apache Port | URL |
|-------------|-----|
| **80** (default) | `http://localhost/myproject` |
| **8080** (if changed) | `http://localhost:8080/myproject` |

> **Note: No configuration file editing needed!** The `BASE_URL` in `config.php` is fully dynamic and will auto-detect your port automatically.

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

**Every SQL query used in this project is documented below, organized by file and feature.**

---

### A. Core Reusable Query — `component_base_sql()` 
**File:** `includes/functions.php` · **Lines:** 101–133  
**Used by:** `store.php`, `product.php`, `compare.php`, `upgrade.php`, `price_history.php`, `custom_builder.php`, `chatbot_proxy.php`  
**Features:** CASE WHEN, LEFT JOIN, Derived Table Subquery, GROUP BY, MIN(), COALESCE

```sql
SELECT c.component_id AS id, c.component_name AS name, c.type,
    CASE
        WHEN c.type = 'CPU' OR c.type LIKE 'CPU (%' THEN 'CPU'
        WHEN c.type = 'Motherboard' OR c.type LIKE 'Motherboard (%' THEN 'Motherboard'
        WHEN c.type = 'RAM' OR c.type LIKE 'RAM (%' THEN 'RAM'
        WHEN c.type = 'GPU (graphics)' THEN 'GPU'
        WHEN c.type = 'PSU (power)' THEN 'PSU'
        WHEN c.type = 'Casing' THEN 'Case'
        WHEN c.type IN ('CPU Cooler','Casing Cooler') THEN 'Cooling'
        ELSE c.type
    END AS category,
    c.brand, c.benchmark_score, c.tdp_watts, c.socket,
    COALESCE(sa.price, 0) AS price_bdt,
    COALESCE(sa.stock_status, 'Out of Stock') AS stock_status_raw,
    COALESCE(s.store_name, '') AS retailer
FROM component c
LEFT JOIN (
    SELECT component_id, MIN(price) AS price, stock_status, store_id
    FROM storeavailability
    GROUP BY component_id          -- Derived Table Subquery
) sa ON sa.component_id = c.component_id
LEFT JOIN store s ON s.store_id = sa.store_id;
```

---

### B. Authentication & User Management

#### B1. Login — Check user by email
**File:** `includes/auth.php` · **Line:** 66

```sql
SELECT * FROM `user` WHERE email = ?;
```

#### B2. Rehash password on login
**File:** `includes/auth.php` · **Line:** 78

```sql
UPDATE `user` SET user_password=? WHERE user_id=?;
```

#### B3. Registration — Check duplicate email
**File:** `register.php` · **Line:** 25

```sql
SELECT user_id FROM `user` WHERE email = ?;
```

#### B4. Registration — Insert new user
**File:** `register.php` · **Line:** 30

```sql
INSERT INTO `user` (user_name, email, user_password, role) VALUES (?,?,?,?);
```

---

### C. Forum Module

#### C1. Forum Feed — Posts with Nested Correlated Subqueries & GROUP_CONCAT
**File:** `forum.php` · **Lines:** 59–74  
**Features:** 4 Correlated Subqueries, JOIN, LEFT JOIN, GROUP_CONCAT, ORDER BY, LIMIT/OFFSET

```sql
SELECT p.post_id, p.title, p.content, p.created_at, p.image_path,
    u.user_name, c.name AS community_name,
    (SELECT COUNT(*) FROM comment comm WHERE comm.post_id = p.post_id) AS comment_count,
    (SELECT COUNT(*) FROM vote v WHERE v.post_id = p.post_id AND v.vote_type = 'upvote') AS score,
    (SELECT GROUP_CONCAT(t.name SEPARATOR ',')
        FROM posttag pt JOIN tag t ON pt.tag_id = t.tag_id
        WHERE pt.post_id = p.post_id) AS tags,
    (SELECT COUNT(*) FROM vote v WHERE v.post_id = p.post_id
        AND v.user_id = ? AND v.vote_type = 'upvote') AS user_vote
FROM post p
JOIN user u ON p.user_id = u.user_id
LEFT JOIN community c ON p.community_id = c.community_id
ORDER BY p.created_at DESC
LIMIT 20 OFFSET 0;
```

#### C2. Forum Feed — Total post count for pagination
**File:** `forum.php` · **Lines:** 50–55

```sql
SELECT COUNT(*) c FROM post p JOIN user u ON p.user_id = u.user_id;
```

#### C3. Community sidebar — Correlated Subqueries
**File:** `forum.php` · **Lines:** 76–81  
**Features:** 2 Correlated Subqueries, ORDER BY derived alias

```sql
SELECT c.community_id, c.name,
    (SELECT COUNT(*) FROM community_member cm WHERE cm.community_id = c.community_id) AS member_count,
    (SELECT COUNT(*) FROM community_member cm WHERE cm.community_id = c.community_id AND cm.user_id = ?) AS is_joined
FROM community c
ORDER BY member_count DESC LIMIT 5;
```

#### C4. Get selected community
**File:** `forum.php` · **Line:** 22

```sql
SELECT community_id, name, description FROM community WHERE community_id = ?;
```

#### C5. Check if user joined a community
**File:** `forum.php` · **Line:** 30

```sql
SELECT 1 FROM community_member WHERE community_id = ? AND user_id = ?;
```

#### C6. Single Post Detail — with vote subqueries
**File:** `forum_post.php` · **Lines:** 14–22  
**Features:** JOIN, LEFT JOIN, 2 Correlated Subqueries

```sql
SELECT p.*, u.user_name, c.name AS community_name,
    (SELECT COUNT(*) FROM vote v WHERE v.post_id = p.post_id AND v.vote_type = 'upvote') AS score,
    (SELECT COUNT(*) FROM vote v WHERE v.post_id = p.post_id AND v.user_id = ? AND v.vote_type = 'upvote') AS user_vote
FROM post p
JOIN user u ON p.user_id = u.user_id
LEFT JOIN community c ON p.community_id = c.community_id
WHERE p.post_id = ?;
```

#### C7. Comments list — with vote subqueries
**File:** `forum_post.php` · **Lines:** 41–49  
**Features:** JOIN, 2 Correlated Subqueries

```sql
SELECT c.*, u.user_name,
    (SELECT COUNT(*) FROM vote v WHERE v.comment_id = c.comment_id AND v.vote_type = 'upvote') AS score,
    (SELECT COUNT(*) FROM vote v WHERE v.comment_id = c.comment_id AND v.user_id = ? AND v.vote_type = 'upvote') AS user_vote
FROM comment c
JOIN user u ON c.user_id = u.user_id
WHERE c.post_id = ?
ORDER BY c.created_at ASC;
```

#### C8. Tags for a post — JOIN
**File:** `forum_post.php` · **Lines:** 51–56

```sql
SELECT t.name FROM tag t JOIN posttag pt ON t.tag_id = pt.tag_id WHERE pt.post_id = ?;
```

#### C9. Community details — Correlated Subqueries
**File:** `forum_post.php` · **Lines:** 60–66

```sql
SELECT c.community_id, c.name, c.description, c.created_at,
    (SELECT COUNT(*) FROM community_member cm WHERE cm.community_id = c.community_id) AS member_count,
    (SELECT COUNT(*) FROM community_member cm WHERE cm.community_id = c.community_id AND cm.user_id = ?) AS is_joined
FROM community c WHERE c.community_id = ?;
```

#### C10. Insert new comment
**File:** `forum_post.php` · **Line:** 34

```sql
INSERT INTO comment (user_id, post_id, content, created_at) VALUES (?, ?, ?, NOW());
```

#### C11. Create a new post
**File:** `forum_create.php` · **Line:** 76

```sql
INSERT INTO post (user_id, title, content, image_path, community_id, created_at) VALUES (?, ?, ?, ?, ?, NOW());
```

#### C12. Get last inserted post ID
**File:** `forum_create.php` · **Line:** 79

```sql
SELECT LAST_INSERT_ID() AS id;
```

#### C13. Find or create a tag
**File:** `forum_create.php` · **Lines:** 85–90

```sql
SELECT tag_id FROM tag WHERE name = ?;
INSERT INTO tag (name) VALUES (?);
```

#### C14. Link tag to post
**File:** `forum_create.php` · **Line:** 92

```sql
INSERT INTO posttag (post_id, tag_id, created_at) VALUES (?, ?, NOW());
```

#### C15. List all communities for dropdown
**File:** `forum_create.php` · **Line:** 102

```sql
SELECT community_id, name FROM community ORDER BY name ASC;
```

---

### D. Forum API Endpoints

#### D1. Delete a post (after auth check)
**File:** `api/delete_forum_item.php` · **Lines:** 48–61

```sql
SELECT post_id, user_id FROM post WHERE post_id = ?;
DELETE FROM post WHERE post_id = ?;
```

#### D2. Delete a comment (after auth check)
**File:** `api/delete_forum_item.php` · **Lines:** 74–88

```sql
SELECT comment_id, user_id FROM comment WHERE comment_id = ?;
DELETE FROM comment WHERE comment_id = ?;
```

#### D3. Vote toggle (upvote/remove)
**File:** `api/vote.php` · **Lines:** 24–34

```sql
SELECT vote_id FROM vote WHERE user_id = ? AND post_id = ?;
DELETE FROM vote WHERE vote_id = ?;
INSERT INTO vote (user_id, post_id, vote_type, created_at) VALUES (?, ?, 'upvote', NOW());
SELECT COUNT(*) c FROM vote WHERE post_id = ? AND vote_type = 'upvote';
```

#### D4. Join/Leave community toggle
**File:** `api/join_community.php` · **Lines:** 22–37

```sql
SELECT community_id FROM community WHERE community_id = ?;
SELECT * FROM community_member WHERE community_id = ? AND user_id = ?;
DELETE FROM community_member WHERE community_id = ? AND user_id = ?;
INSERT INTO community_member (community_id, user_id) VALUES (?, ?);
SELECT COUNT(*) c FROM community_member WHERE community_id = ?;
```

---

### E. Dashboard

#### E1. User's saved builds
**File:** `dashboard.php` · **Lines:** 11–13

```sql
SELECT * FROM `build` WHERE user_id=? ORDER BY created_at DESC LIMIT 10;
```

#### E2. Watchlist with live prices — 4-Table JOIN
**File:** `dashboard.php` · **Lines:** 15–25  
**Features:** JOIN, LEFT JOIN, Derived Table Subquery, GROUP BY, MIN(), COALESCE

```sql
SELECT c.component_id as id, c.component_name as name, c.type,
    COALESCE(sa.price,0) as price_bdt, COALESCE(s.store_name,"") as retailer, w.added_at
FROM watchlist w
JOIN component c ON c.component_id = w.component_id
LEFT JOIN (SELECT component_id, MIN(price) as price, store_id
    FROM storeavailability GROUP BY component_id) sa ON sa.component_id = c.component_id
LEFT JOIN store s ON s.store_id = sa.store_id
WHERE w.user_id = ? ORDER BY w.added_at DESC LIMIT 8;
```

#### E3. Price trend chart data — Date math
**File:** `dashboard.php` · **Lines:** 30–34  
**Features:** DATE(), DATE_SUB(), INTERVAL

```sql
SELECT DATE(changed_at) as d, new_price
FROM pricetracking
WHERE component_id=? AND changed_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
ORDER BY d;
```

#### E4. Dashboard KPI counters — Aggregation
**File:** `dashboard.php` · **Lines:** 41–42

```sql
SELECT COUNT(*) c FROM component;
SELECT COUNT(*) c FROM store;
```

---

### F. Store & Product Pages

#### F1. Store price range bounds — Wrapper Subquery
**File:** `store.php` · **Lines:** 29–32

```sql
SELECT MIN(sub.price_bdt) as min_val, MAX(sub.price_bdt) as max_val
FROM (component_base_sql()) sub
WHERE sub.category = ? OR ? = '';
```

#### F2. Store available brands — DISTINCT + Wrapper Subquery
**File:** `store.php` · **Lines:** 38–42

```sql
SELECT DISTINCT sub.brand FROM (component_base_sql()) sub
WHERE (sub.category = ? OR ? = '') AND sub.brand IS NOT NULL AND sub.brand != ''
ORDER BY sub.brand;
```

#### F3. Store component listing — Wrapper Subquery + pagination
**File:** `store.php` · **Lines:** 75–87

```sql
SELECT COUNT(*) c FROM (component_base_sql()) sub WHERE ...;
SELECT sub.* FROM (component_base_sql()) sub WHERE ... ORDER BY ... LIMIT ? OFFSET ?;
```

#### F4. Store watchlist check
**File:** `store.php` · **Line:** 95

```sql
SELECT component_id FROM watchlist WHERE user_id = ?;
```

#### F5. Product detail — Retailer price comparison
**File:** `product.php` · **Lines:** 18–23  
**Features:** JOIN, ORDER BY

```sql
SELECT sa.price, sa.stock_status, s.store_name
FROM storeavailability sa
JOIN store s ON s.store_id = sa.store_id
WHERE sa.component_id = ?
ORDER BY sa.price ASC;
```

#### F6. Compare — Fetch multiple components by ID list
**File:** `compare.php` · **Lines:** 14–16

```sql
SELECT ... FROM component_base_sql() WHERE c.component_id IN (?,?,?);
```

---

### G. Price History & Watchlist API

#### G1. Price history chart data
**File:** `price_history.php` · **Lines:** 17–23  
**Features:** DATE(), ORDER BY

```sql
SELECT DATE(changed_at) d, old_price, new_price, changed_at
FROM pricetracking WHERE component_id = ?
ORDER BY changed_at DESC LIMIT 90;
```

#### G2. All components dropdown
**File:** `price_history.php` · **Line:** 26

```sql
SELECT component_id as id, component_name as name, type FROM component ORDER BY type, component_name;
```

#### G3. Watchlist add/remove toggle
**File:** `api/watchlist.php` · **Lines:** 14–18

```sql
INSERT IGNORE INTO watchlist (user_id, component_id) VALUES (?,?);
DELETE FROM watchlist WHERE user_id=? AND component_id=?;
SELECT COUNT(*) c FROM watchlist WHERE user_id=?;
```

---

### H. Build Wizard & Save

#### H1. Save a build
**File:** `api/save_build.php` · **Lines:** 21–27

```sql
INSERT INTO `build` (user_id, name, purpose, total_price, score) VALUES (?,?,?,?,?);
INSERT IGNORE INTO buildcomponent (build_id, component_id) VALUES (?,?);
```

#### H2. Get components filtered by category & budget
**File:** `includes/functions.php` · **Lines:** 143–158

```sql
SELECT * FROM (component_base_sql()) sub
WHERE category = ? AND price_bdt > 0 AND price_bdt <= ?
ORDER BY benchmark_score DESC;
```

---

### I. Upgrade Advisor

#### I1. Find upgrade by benchmark — Complex WHERE + ORDER BY
**File:** `upgrade.php` · **Lines:** 42–54  
**Features:** Reuses component_base_sql(), filters by type, price ceiling, and benchmark floor

```sql
SELECT ... FROM component_base_sql()
WHERE c.type IN ('GPU (graphics)','Graphics Card')
    AND COALESCE(sa.price,0) <= ?
    AND COALESCE(sa.price,0) > ?
ORDER BY c.benchmark_score DESC LIMIT 1;
```

#### I2. Log upgrade suggestion
**File:** `upgrade.php` · **Line:** 62

```sql
INSERT INTO upgradesuggestion (user_id, build_id, component_id) VALUES (?, NULL, ?);
```

#### I3. CPU & GPU dropdowns — LEFT JOIN + Derived Table
**File:** `upgrade.php` · **Lines:** 67–74

```sql
SELECT c.component_id as id, c.component_name as name, c.benchmark_score,
    COALESCE(sa.price,0) as price_bdt
FROM component c
LEFT JOIN (SELECT component_id, MIN(price) as price
    FROM storeavailability GROUP BY component_id) sa ON sa.component_id=c.component_id
WHERE c.type IN ('CPU','CPU (processing)')
ORDER BY COALESCE(sa.price,0);
```

---

### J. FPS Estimator

#### J1. Fetch benchmark scores for CPU & GPU
**File:** `includes/fps.php` · **Lines:** 3–5

```sql
SELECT benchmark_score FROM component WHERE component_id=?;
SELECT * FROM fps_profiles WHERE game_slug=?;
```

#### J2. List all game profiles
**File:** `includes/fps.php` · **Line:** 31

```sql
SELECT game_slug, game_name FROM fps_profiles ORDER BY game_name;
```

---

### K. Admin Panel

#### K1. Admin dashboard KPIs — COUNT aggregation
**File:** `admin/index.php` · **Lines:** 8–10

```sql
SELECT COUNT(*) c FROM `user`;
SELECT COUNT(*) c FROM component;
SELECT COUNT(*) c FROM `build`;
```

#### K2. Recent users list
**File:** `admin/index.php` · **Line:** 11

```sql
SELECT user_id, user_name, email, role, created_at FROM `user` ORDER BY created_at DESC LIMIT 5;
```

#### K3. Recent builds — JOIN
**File:** `admin/index.php` · **Lines:** 12–15

```sql
SELECT b.build_id, b.name, b.total_price, b.score, b.created_at, u.user_name
FROM `build` b JOIN `user` u ON u.user_id=b.user_id
ORDER BY b.created_at DESC LIMIT 5;
```

#### K4. Admin component listing — LEFT JOIN + Derived Table
**File:** `admin/components.php` · **Lines:** 98–101

```sql
SELECT c.*, COALESCE(sa.price,0) as price_bdt, COALESCE(sa.stock_status,'—') as stock_raw
FROM component c
LEFT JOIN (SELECT component_id, MIN(price) as price, stock_status
    FROM storeavailability GROUP BY component_id) sa ON sa.component_id=c.component_id
WHERE ... ORDER BY c.type, c.component_name LIMIT 15 OFFSET ?;
```

#### K5. Admin — Delete / Update / Insert component
**File:** `admin/components.php` · **Lines:** 13, 58, 62

```sql
DELETE FROM component WHERE component_id=?;
UPDATE component SET component_name=?,type=?,brand=?,... WHERE component_id=?;
INSERT INTO component (component_name,type,brand,...) VALUES (?,?,?,...);
```

#### K6. Admin — Update user role
**File:** `admin/users.php` · **Line:** 14

```sql
UPDATE `user` SET role=? WHERE user_id=?;
```

#### K7. Admin — User listing with pagination
**File:** `admin/users.php` · **Lines:** 55–57

```sql
SELECT COUNT(*) c FROM `user` WHERE ...;
SELECT * FROM `user` WHERE ... ORDER BY created_at DESC LIMIT 15 OFFSET ?;
```

#### K8. Admin — Reset user password
**File:** `admin/users.php` · **Line:** 30

```sql
UPDATE `user` SET user_password=? WHERE user_id=?;
```

#### K9. Admin — Price update with tracking
**File:** `admin/prices.php` · **Lines:** 20–30

```sql
SELECT price FROM storeavailability WHERE component_id=? LIMIT 1;
SELECT availability_id FROM storeavailability WHERE component_id=? LIMIT 1;
UPDATE storeavailability SET price=?, stock_status=? WHERE component_id=?;
INSERT INTO storeavailability (store_id, component_id, stock_status, price) VALUES (1,?,?,?);
INSERT INTO pricetracking (component_id, old_price, new_price) VALUES (?,?,?);
```

#### K10. Admin — Sponsor ad CRUD
**File:** `admin/sponsor.php` · **Lines:** 26–47

```sql
UPDATE sponsor_ads SET title=?,image_url=?,link_url=?,description=?,active=?,start_date=?,end_date=? WHERE ad_id=?;
INSERT INTO sponsor_ads (title,image_url,link_url,description,active,start_date,end_date) VALUES (?,?,?,?,?,?,?);
DELETE FROM sponsor_ads WHERE ad_id=?;
SELECT * FROM sponsor_ads WHERE ad_id=?;
```

---

### L. Miscellaneous API

#### L1. Get sponsor ad
**File:** `api/get_sponsor.php` · **Line:** 10

```sql
SELECT * FROM sponsor_ads WHERE active=1 AND start_date <= CURDATE() AND end_date >= CURDATE() ORDER BY RAND() LIMIT 1;
```

#### L2. Component search API (used by chatbot)
**File:** `api/get_components.php` · **Line:** 28

```sql
SELECT * FROM (component_base_sql()) sub WHERE ... ORDER BY ... LIMIT ?;
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
