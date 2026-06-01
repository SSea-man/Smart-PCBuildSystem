# PCBuilder BD — Smart PC Build System

A full-stack web platform for the Bangladeshi market that helps users configure, compare, and purchase custom PC builds with real-time compatibility checking, AI-powered recommendations, and multi-retailer price tracking.

---

## Table of Contents

- [Features](#features)
- [Tech Stack](#tech-stack)
- [Project Structure](#project-structure)
- [Database Schema](#database-schema)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [User Roles](#user-roles)
- [API Endpoints](#api-endpoints)
- [Build Wizard Algorithm](#build-wizard-algorithm)
- [Contributors](#contributors)
- [License](#license)

---

## Features

### Build Wizard (3-Step Guided Flow)
- **Step 1 — Purpose Selection:** Gaming, Video Editing, Office, or General Use
- **Step 2 — Budget Setting:** Slider + manual input (৳10,000 – ৳5,00,000) with live allocation preview (doughnut chart)
- **Step 3 — Top 3 Builds:** Auto-generated, fully compatible builds ranked by composite score (performance × value × availability)

### Compatibility Engine
- CPU ↔ Motherboard socket matching (AM4, AM5, LGA1700)
- RAM ↔ Motherboard DDR generation matching (DDR4, DDR5)
- Storage ↔ Motherboard interface validation (NVMe M.2 slots, SATA ports)
- GPU length vs. Case clearance
- CPU Cooler height vs. Case clearance
- Motherboard form factor vs. Case support (ATX/mATX/ITX)
- PSU wattage vs. total system TDP (with 1.2× safety margin)

### Custom PC Builder
- Drag-and-drop component selection across 8 categories
- Real-time compatibility checking via AJAX
- Live total price calculation
- TDP / PSU headroom bar
- Save builds to user dashboard
- Export build as printable invoice (PDF-ready print layout)

### Component Store
- Multi-retailer price comparison (StarTech, Ryans, UltraTech, etc.)
- Category filtering with sidebar (Brand, Price Range)
- Grid/List view toggle
- Sort by price, benchmark score, or name
- Product detail pages with specs, key features, and retailer links

### Price Tracking
- Historical price charts per component
- Watchlist system with price alert notifications

### AI Chatbot
- Gemini API-powered PC building assistant
- Rate-limited (20 requests/hour per user)
- Contextual recommendations based on user purpose/budget

### Community Forum
- Create posts with chip-style tag selection (12 presets + custom tags)
- Image upload support (drag-and-drop, paste from clipboard, file picker)
- Upvote/downvote system
- Community groups (create, join)
- Moderator announcement badges
- Post deletion with ownership verification

### Upgrade Advisor
- Analyzes saved builds for potential upgrades
- Suggests next-tier components within budget

### Dashboard
- Saved builds overview
- Build comparison tool
- Quick actions (edit, delete, share)

### Admin Panel
- Component CRUD management (add/edit/delete with image upload)
- User management with role assignment (User / Moderator / Admin)
- Price management and store availability
- Sponsor advertisement management

### Design
- Dark/Light theme toggle with persistent preference
- Fully responsive (mobile-first Bootstrap 5.3)
- Inter + Outfit typography from Google Fonts
- Solid opaque card backgrounds (no transparency artifacts)
- Smooth micro-animations and hover effects

---

## Tech Stack

| Layer | Technology |
|-------|-----------|
| **Backend** | PHP 8.0 (vanilla, no framework) |
| **Database** | MySQL 8.0 (MariaDB compatible) |
| **Frontend** | HTML5, Vanilla CSS, JavaScript ES6+ |
| **UI Framework** | Bootstrap 5.3.3 |
| **Icons** | Bootstrap Icons 1.11.3 |
| **Typography** | Google Fonts (Inter, Outfit) |
| **Charts** | Chart.js |
| **AI** | Google Gemini API (chatbot proxy) |
| **Server** | Apache (XAMPP) |

---

## Project Structure

```
myproject/
├── admin/                   # Admin panel pages
│   ├── index.php            # Admin dashboard
│   ├── components.php       # Component CRUD
│   ├── prices.php           # Price management
│   ├── users.php            # User role management
│   └── sponsor.php          # Sponsor ad management
├── api/                     # REST-style API endpoints
│   ├── check_compatibility.php
│   ├── save_build.php
│   ├── get_components.php
│   ├── get_builds.php
│   ├── chatbot_proxy.php
│   ├── vote.php
│   ├── watchlist.php
│   ├── delete_build.php
│   ├── delete_forum_item.php
│   ├── price_history.php
│   ├── create_community.php
│   ├── join_community.php
│   └── get_sponsor.php
├── assets/
│   ├── css/style.css        # Global stylesheet + CSS variables
│   ├── js/
│   │   ├── app.js           # Core JS (theme toggle, toasts, API helper)
│   │   ├── custom_builder.js # Custom builder page logic
│   │   └── compare.js       # Comparison tool logic
│   └── img/                 # Static images
├── includes/                # Backend logic modules
│   ├── auth.php             # Authentication & session helpers
│   ├── db.php               # PDO database connection & query helpers
│   ├── functions.php        # Utility functions, component queries
│   ├── compatibility.php    # Hardware compatibility checker
│   ├── scoring.php          # Build scoring & recommendation engine
│   ├── budget_allocator.php # Budget split profiles per purpose
│   ├── wattage.php          # TDP calculation & PSU recommendation
│   └── fps.php              # FPS estimation from CPU+GPU benchmarks
├── templates/               # Reusable PHP templates
│   ├── header.php           # HTML head, navbar, theme
│   ├── footer.php           # Scripts, footer HTML
│   ├── build_card.php       # Build result card component
│   └── component_card.php   # Store product card component
├── uploads/                 # User-uploaded files
│   ├── components/          # Component images
│   └── forum/               # Forum post images
├── config.php               # App configuration & constants
├── index.php                # Landing page
├── store.php                # Component catalogue
├── product.php              # Product detail page
├── purpose.php              # Build Wizard Step 1
├── budget.php               # Build Wizard Step 2
├── builds.php               # Build Wizard Step 3
├── custom_builder.php       # Custom PC Builder
├── compare.php              # Component comparison tool
├── dashboard.php            # User dashboard
├── upgrade.php              # Upgrade advisor
├── forum.php                # Forum listing
├── forum_create.php         # Create forum post
├── forum_post.php           # Forum post detail
├── chatbot.php              # AI chatbot interface
├── blog.php                 # Blog/news page
├── about.php                # About Us (visitors only)
├── login.php                # Login page
├── register.php             # Registration page
├── logout.php               # Session logout
├── price_history.php        # Price history page
├── project_alpha.sql        # Database schema dump
├── migration.sql            # Schema migration scripts
├── seed_components.php      # Component seeder (batch 1)
└── seed_components_v2.php   # Component seeder (batch 2)
```

---

## Database Schema

The platform uses **24 tables** in the `project_alpha` database:

| Table | Purpose |
|-------|---------|
| `user` | User accounts (email, password hash, role ENUM: user/moderator/admin) |
| `authentication` | Login sessions and tokens |
| `component` | Hardware components with specs (socket, RAM gen, TDP, form factor, etc.) |
| `store` | Retailer information |
| `storeavailability` | Component prices and stock per store |
| `pricetracking` | Historical price records |
| `build` | Saved user builds |
| `buildcomponent` | Components in each saved build |
| `comparison` | Component comparison sessions |
| `post` | Forum posts |
| `tag` | Forum tags |
| `posttag` | Post ↔ Tag relationship |
| `community` | Forum community groups |
| `community_member` | Community membership |
| `vote` | Upvote/downvote on posts |
| `comment` | Post comments |
| `watchlist` | User component watchlist |
| `chatbot` | Chatbot conversation history |
| `chatbot_rate_limits` | Rate limiting for chatbot API |
| `fps_profiles` | FPS benchmark data for CPU+GPU combos |
| `upgradesuggestion` | Upgrade recommendations |
| `user_preferences` | User settings and preferences |
| `user_project` | User project workspaces |
| `pcnews` | PC hardware news/blog entries |

---

## Installation

### Prerequisites
- [XAMPP](https://www.apachefriends.org/) 8.0+ (Apache + MySQL + PHP)
- PHP 8.0 or higher
- MySQL 8.0 or MariaDB 10.6+

### Steps

1. **Clone the repository:**
   ```bash
   git clone https://github.com/SSea-man/Smart-PCBuildSystem.git /opt/lampp/htdocs/myproject
   cd /opt/lampp/htdocs/myproject
   ```

2. **Import the database:**
   ```bash
   /opt/lampp/bin/mysql -u root < project_alpha.sql
   ```

3. **Seed component data:**
   ```bash
   /opt/lampp/bin/php seed_components.php
   /opt/lampp/bin/php seed_components_v2.php
   ```

4. **Set upload directory permissions:**
   ```bash
   sudo chmod 777 uploads/forum/
   sudo chmod 777 uploads/components/
   ```

5. **Configure Apache to serve on port 5173** (or edit `config.php` to match your port):
   ```
   # In /opt/lampp/etc/httpd.conf
   Listen 5173
   ```

6. **Start XAMPP:**
   ```bash
   sudo /opt/lampp/lampp start
   ```

7. **Visit:** [http://localhost:5173/myproject](http://localhost:5173/myproject)

---

## Configuration

All configuration is in `config.php`:

| Constant | Description | Default |
|----------|-------------|---------|
| `DB_HOST` | Database host | `localhost` |
| `DB_NAME` | Database name | `project_alpha` |
| `DB_USER` | Database user | `root` |
| `DB_PASS` | Database password | `` (empty) |
| `BASE_URL` | Application base URL | `http://localhost:5173/myproject` |
| `APP_ENV` | Environment (local/production) | `local` |
| `JWT_SECRET` | Secret key for JWT tokens | Change before deployment |
| `SESSION_LIFETIME` | Session timeout in seconds | `7200` (2 hours) |
| `CHATBOT_RATE_LIMIT` | Max chatbot requests per hour | `20` |
| `PSU_SAFETY_MARGIN` | PSU wattage safety multiplier | `1.20` (120%) |
| `TOP_BUILDS_LIMIT` | Number of builds to recommend | `3` |

---

## Usage

### For Visitors (Not Logged In)
- Browse the component **Store** and product details
- Read the **Blog**
- View the **About Us** page
- Register or Login

### For Users (Logged In)
- **Build Wizard:** Purpose → Budget → Get 3 optimized builds
- **Custom Builder:** Hand-pick components with live compatibility checking
- **Save Builds:** Bookmark builds to your Dashboard
- **Forum:** Create posts, join communities, upvote/downvote
- **Chatbot:** Ask AI for build advice
- **Watchlist:** Track component prices
- **Compare:** Side-by-side component comparison

### For Admins
- **Manage Components:** Add, edit, delete hardware with images
- **Manage Users:** Promote users to Moderator or Admin
- **Manage Prices:** Update store availability and pricing
- **Sponsor Ads:** Configure promotional advertisements

---

## User Roles

| Role | Permissions |
|------|------------|
| **User** | Browse, build, save, forum, chatbot, watchlist |
| **Moderator** | All User permissions + forum announcement badges |
| **Admin** | All permissions + admin panel access (components, users, prices, sponsors) |

---

## Test Credentials

Use the following accounts to test the platform with different roles:

### Admin Accounts

| Email | Password | Name |
|-------|----------|------|
| `smseaman7@gmail.com` | *(your personal password)* | Seaman |
| `shadman1@pcbuild.com` | `pass1234` | Shadman Ahammad |

### Moderator Account

| Email | Password | Name |
|-------|----------|------|
| `rahim@email.com` | `pass1234` | Rahim |

### User Accounts (Sample)

| Email | Password | Name |
|-------|----------|------|
| `rahim2@pcbuild.com` | `pass1234` | Rahim Uddin |
| `karim3@pcbuild.com` | `pass1234` | Karim Hasan |
| `nusrat4@pcbuild.com` | `pass1234` | Nusrat Jahan |
| `tanvir5@pcbuild.com` | `pass1234` | Tanvir Ahmed |
| `faria6@pcbuild.com` | `pass1234` | Faria Islam |
| `sabbir7@pcbuild.com` | `pass1234` | Sabbir Hossain |
| `mehedi8@pcbuild.com` | `pass1234` | Mehedi Hasan |
| `tanjila9@pcbuild.com` | `pass1234` | Tanjila Akter |
| `arif10@pcbuild.com` | `pass1234` | Arifur Rahman |

> All test accounts (IDs 2–40) use the password **`pass1234`**.

---

## API Endpoints

| Endpoint | Method | Auth | Description |
|----------|--------|------|-------------|
| `/api/get_components.php` | GET | No | Fetch components by category |
| `/api/check_compatibility.php` | POST | No | Validate component compatibility |
| `/api/save_build.php` | POST | Yes | Save a build to dashboard |
| `/api/get_builds.php` | GET | Yes | Retrieve user's saved builds |
| `/api/delete_build.php` | POST | Yes | Delete a saved build |
| `/api/vote.php` | POST | Yes | Upvote/downvote a forum post |
| `/api/watchlist.php` | POST | Yes | Add/remove from watchlist |
| `/api/price_history.php` | GET | No | Get price history for a component |
| `/api/chatbot_proxy.php` | POST | Yes | Send message to AI chatbot |
| `/api/create_community.php` | POST | Yes | Create a forum community |
| `/api/join_community.php` | POST | Yes | Join/leave a community |
| `/api/delete_forum_item.php` | POST | Yes | Delete a forum post |
| `/api/get_sponsor.php` | GET | No | Get active sponsor advertisement |

---

## Build Wizard Algorithm

The recommendation engine uses a **compatibility-first platform approach:**

### Phase 1 — Platform Discovery
1. Loads all motherboards from the database
2. For each motherboard, finds compatible:
   - **CPUs** matching the motherboard's socket (AM4/AM5/LGA1700)
   - **RAM** matching the motherboard's DDR generation (DDR4/DDR5)
   - **Storage** matching available interfaces (NVMe if M.2 slots exist, SATA if ports exist)
3. Rejects any motherboard with missing socket data or no compatible CPU

### Phase 2 — Component Selection
For each valid platform:
- Picks the **highest-benchmark component within budget** for each category
- If nothing fits budget, picks the **cheapest available** as fallback
- GPU, Case, and Cooling are selected independently (no platform dependency)
- **PSU is TDP-aware:** calculates total system TDP after all parts are selected, then only picks PSUs meeting the recommended wattage (TDP × 1.2 safety margin)

### Phase 3 — Scoring
Each build receives a composite score:

```
Score = (Performance × 0.60) + (Value × 0.30) + (Availability × 0.10)
```

- **Performance:** Weighted benchmark scores (weights vary by purpose — Gaming favors GPU 60%, Video Editing favors CPU 45%)
- **Value:** Performance-per-BDT ratio relative to budget
- **Availability:** Percentage of components currently in stock

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

This section documents all SQL queries used throughout the project, including subqueries (nested `SELECT` statements).

### Subqueries Used

The following files use **subqueries** (a `SELECT` nested inside another `SELECT`):

#### 1. `includes/functions.php` — `component_base_sql()` (Line 100–131)
**Type:** Derived Table Subquery (FROM clause)

Used as the base SQL for ALL component fetches across the entire platform. Contains a subquery to get the minimum price per component:
```sql
SELECT c.component_id AS id, c.component_name AS name, c.type,
       CASE WHEN c.type = 'CPU' ... END AS category,
       c.brand, c.benchmark_score, c.tdp_watts, c.socket, ...
       COALESCE(sa.price, 0) AS price_bdt,
       COALESCE(sa.stock_status, 'Out of Stock') AS stock_status_raw,
       COALESCE(s.store_name, '') AS retailer
FROM component c
LEFT JOIN (
    SELECT component_id, MIN(price) AS price, stock_status, store_id
    FROM storeavailability
    GROUP BY component_id                          -- ← SUBQUERY (derived table)
) sa ON sa.component_id = c.component_id
LEFT JOIN store s ON s.store_id = sa.store_id
```
**Referenced by:** `get_component()`, `get_components_by_category()`, `store.php`, `product.php`, `compare.php`, `upgrade.php`, `api/check_compatibility.php`, `api/chatbot_proxy.php`, `price_history.php`

---

#### 2. `forum.php` — Post Listing Query (Lines 59–67)
**Type:** Correlated Scalar Subqueries (SELECT clause) + EXISTS Subquery (WHERE clause)

Contains **5 subqueries** in a single query:
```sql
SELECT p.*, u.user_name,
    (SELECT COUNT(*) FROM comment comm WHERE comm.post_id = p.post_id) AS comment_count,                    -- ← SUBQUERY 1
    (SELECT COUNT(*) FROM vote v WHERE v.post_id = p.post_id AND v.vote_type = 'upvote') AS score,          -- ← SUBQUERY 2
    (SELECT GROUP_CONCAT(t.name SEPARATOR ',') FROM posttag pt JOIN tag t ON pt.tag_id = t.tag_id
     WHERE pt.post_id = p.post_id) AS tags,                                                                  -- ← SUBQUERY 3
    (SELECT COUNT(*) FROM vote v WHERE v.post_id = p.post_id AND v.user_id = ? AND v.vote_type = 'upvote')
     AS user_vote                                                                                             -- ← SUBQUERY 4
FROM post p
JOIN user u ON u.user_id = p.user_id
```

Additionally, when filtering announcements (Line 42):
```sql
WHERE EXISTS (
    SELECT 1 FROM posttag pt JOIN tag t ON pt.tag_id = t.tag_id
    WHERE pt.post_id = p.post_id AND t.name = 'announcement'         -- ← SUBQUERY 5 (EXISTS)
)
```

---

#### 3. `forum.php` — Community Sidebar (Lines 76–79)
**Type:** Correlated Scalar Subqueries (SELECT clause)

```sql
SELECT c.community_id, c.name,
    (SELECT COUNT(*) FROM community_member cm WHERE cm.community_id = c.community_id) AS member_count,       -- ← SUBQUERY
    (SELECT COUNT(*) FROM community_member cm WHERE cm.community_id = c.community_id AND cm.user_id = ?)
     AS is_joined                                                                                             -- ← SUBQUERY
FROM community c ORDER BY c.name
```

---

#### 4. `forum.php` — Selected Community Detail (Lines 22–24)
**Type:** Correlated Scalar Subquery (SELECT clause)

```sql
SELECT c.community_id, c.name, c.description,
    (SELECT COUNT(*) FROM community_member cm WHERE cm.community_id = c.community_id) AS member_count        -- ← SUBQUERY
FROM community c WHERE c.community_id = ?
```

---

#### 5. `forum_post.php` — Post Detail Query (Lines 14–17)
**Type:** Correlated Scalar Subqueries (SELECT clause)

```sql
SELECT p.*, u.user_name, c.name AS community_name,
    (SELECT COUNT(*) FROM vote v WHERE v.post_id = p.post_id AND v.vote_type = 'upvote') AS score,           -- ← SUBQUERY
    (SELECT COUNT(*) FROM vote v WHERE v.post_id = p.post_id AND v.user_id = ? AND v.vote_type = 'upvote')
     AS user_vote                                                                                             -- ← SUBQUERY
FROM post p JOIN user u ON u.user_id = p.user_id
```

---

#### 6. `forum_post.php` — Comments Query (Lines 69–72)
**Type:** Correlated Scalar Subqueries (SELECT clause)

```sql
SELECT c.*, u.user_name,
    (SELECT COUNT(*) FROM vote v WHERE v.comment_id = c.comment_id AND v.vote_type = 'upvote') AS score,     -- ← SUBQUERY
    (SELECT COUNT(*) FROM vote v WHERE v.comment_id = c.comment_id AND v.user_id = ? AND v.vote_type = 'upvote')
     AS user_vote                                                                                             -- ← SUBQUERY
FROM comment c JOIN user u ON u.user_id = c.user_id WHERE c.post_id = ?
```

---

#### 7. `forum_post.php` — Community Detail Sidebar (Lines 88–91)
**Type:** Correlated Scalar Subqueries (SELECT clause)

```sql
SELECT c.community_id, c.name, c.description, c.created_at,
    (SELECT COUNT(*) FROM community_member cm WHERE cm.community_id = c.community_id) AS member_count,       -- ← SUBQUERY
    (SELECT COUNT(*) FROM community_member cm WHERE cm.community_id = c.community_id AND cm.user_id = ?)
     AS is_joined                                                                                             -- ← SUBQUERY
FROM community c WHERE c.community_id = ?
```

---

#### 8. `dashboard.php` — Watchlist with Prices (Lines 15–21)
**Type:** Derived Table Subquery (FROM clause)

```sql
SELECT c.component_id as id, c.component_name as name, c.type,
       COALESCE(sa.price,0) as price_bdt, COALESCE(s.store_name,'') as retailer
FROM watchlist w
JOIN component c ON c.component_id = w.component_id
LEFT JOIN (
    SELECT component_id, MIN(price) as price, store_id
    FROM storeavailability GROUP BY component_id                     -- ← SUBQUERY (derived table)
) sa ON sa.component_id = c.component_id
LEFT JOIN store s ON s.store_id = sa.store_id
WHERE w.user_id = ?
```

---

#### 9. `upgrade.php` — CPU/GPU Lists with Prices (Lines 67–73)
**Type:** Derived Table Subquery (FROM clause)

```sql
SELECT c.component_id as id, c.component_name as name, c.benchmark_score,
       COALESCE(sa.price,0) as price
FROM component c
LEFT JOIN (
    SELECT component_id, MIN(price) as price
    FROM storeavailability GROUP BY component_id                     -- ← SUBQUERY (derived table)
) sa ON sa.component_id = c.component_id
WHERE c.type IN ('CPU','CPU (processing)')
```

---

#### 10. `admin/components.php` — Component List with Prices (Lines 68–70)
**Type:** Derived Table Subquery (FROM clause)

```sql
SELECT c.*, COALESCE(sa.price,0) as price_bdt, COALESCE(sa.stock_status,'—') as stock_raw
FROM component c
LEFT JOIN (
    SELECT component_id, MIN(price) as price, stock_status
    FROM storeavailability GROUP BY component_id                     -- ← SUBQUERY (derived table)
) sa ON sa.component_id = c.component_id
```

---

#### 11. `store.php` — Total Count with Derived Table (Line 76)
**Type:** Derived Table Subquery (FROM clause)

```sql
SELECT COUNT(*) c FROM (
    SELECT ... FROM component c LEFT JOIN (...) sa ...               -- ← SUBQUERY (wrapping base query)
) sub
```

---

### All Queries by File

| File | Query Type | Tables Involved | Description |
|------|-----------|-----------------|-------------|
| **includes/auth.php** | SELECT | `user` | Find user by email for login |
| **includes/auth.php** | UPDATE | `user` | Rehash password on login |
| **includes/functions.php** | SELECT + Subquery | `component`, `storeavailability`, `store` | Base component query with min price |
| **includes/fps.php** | SELECT | `component` | Get CPU/GPU benchmark scores |
| **includes/fps.php** | SELECT | `fps_profiles` | Get FPS estimation data |
| **register.php** | SELECT | `user` | Check if email already exists |
| **register.php** | INSERT | `user` | Create new user account |
| **store.php** | SELECT | `storeavailability` | Get min/max price bounds |
| **store.php** | SELECT | `component` | Get distinct brands for filter |
| **store.php** | SELECT + Subquery | `component`, `storeavailability` | Count total matching components |
| **store.php** | SELECT | `watchlist` | Get user's watchlisted components |
| **dashboard.php** | SELECT | `build` | Get user's saved builds |
| **dashboard.php** | SELECT + Subquery | `watchlist`, `component`, `storeavailability`, `store` | Watchlist with prices |
| **dashboard.php** | SELECT | `pricetracking` | Price history for watchlist items |
| **dashboard.php** | SELECT COUNT | `component`, `store` | Dashboard stats |
| **compare.php** | SELECT | `component`, `storeavailability`, `store` | Fetch components for comparison |
| **price_history.php** | SELECT | `component`, `storeavailability`, `store` | Component detail |
| **price_history.php** | SELECT | `pricetracking` | Historical price data |
| **price_history.php** | SELECT | `component` | List all components for dropdown |
| **upgrade.php** | SELECT + Subquery | `component`, `storeavailability` | CPU/GPU lists with cheapest price |
| **upgrade.php** | INSERT | `upgradesuggestion` | Save upgrade suggestion |
| **forum.php** | SELECT + 2 Subqueries | `community`, `community_member` | Community details with member count |
| **forum.php** | SELECT | `community_member` | Check if user joined community |
| **forum.php** | SELECT COUNT | `post` | Total post count for pagination |
| **forum.php** | SELECT + 5 Subqueries | `post`, `user`, `comment`, `vote`, `posttag`, `tag` | Full post listing |
| **forum.php** | SELECT + 2 Subqueries | `community`, `community_member` | Sidebar community list |
| **forum_create.php** | INSERT | `post` | Create new forum post |
| **forum_create.php** | SELECT | `tag` | Check if tag exists |
| **forum_create.php** | INSERT | `tag` | Create new tag |
| **forum_create.php** | INSERT | `posttag` | Link tag to post |
| **forum_create.php** | SELECT | `community` | List communities for dropdown |
| **forum_post.php** | SELECT + 2 Subqueries | `post`, `user`, `community`, `vote` | Post detail with vote info |
| **forum_post.php** | SELECT + 2 Subqueries | `comment`, `user`, `vote` | Comments with vote info |
| **forum_post.php** | SELECT | `posttag`, `tag` | Tags for the post |
| **forum_post.php** | SELECT + 2 Subqueries | `community`, `community_member` | Community sidebar |
| **forum_post.php** | DELETE | `post` | Delete post (owner/admin) |
| **forum_post.php** | DELETE | `comment` | Delete comment (owner/admin) |
| **forum_post.php** | INSERT | `comment` | Add a comment |
| **admin/index.php** | SELECT COUNT | `user`, `component`, `build` | Admin dashboard stats |
| **admin/index.php** | SELECT + JOIN | `build`, `user` | Recent builds with user names |
| **admin/users.php** | UPDATE | `user` | Change user role |
| **admin/users.php** | SELECT COUNT | `user` | Total user count |
| **admin/users.php** | SELECT | `user` | Paginated user list |
| **admin/components.php** | INSERT | `component` | Add new component |
| **admin/components.php** | SELECT | `component` | Get component for editing |
| **admin/components.php** | SELECT COUNT | `component` | Total count for pagination |
| **admin/components.php** | SELECT + Subquery | `component`, `storeavailability` | List components with prices |
| **admin/prices.php** | SELECT | `storeavailability` | Get current price |
| **admin/prices.php** | UPDATE | `storeavailability` | Update price/stock |
| **admin/prices.php** | INSERT | `storeavailability` | Create new price entry |
| **admin/prices.php** | INSERT | `pricetracking` | Log price change |
| **admin/sponsor.php** | UPDATE | `sponsor_ads` | Update sponsor ad |
| **admin/sponsor.php** | INSERT | `sponsor_ads` | Create sponsor ad |
| **admin/sponsor.php** | DELETE | `sponsor_ads` | Remove sponsor ad |
| **api/save_build.php** | INSERT | `build` | Save build metadata |
| **api/save_build.php** | INSERT | `buildcomponent` | Save build components |
| **api/delete_build.php** | DELETE | `build` | Delete a saved build |
| **api/vote.php** | SELECT | `vote` | Check existing vote |
| **api/vote.php** | DELETE | `vote` | Remove vote (toggle off) |
| **api/vote.php** | INSERT | `vote` | Cast upvote |
| **api/vote.php** | SELECT COUNT | `vote` | Get new score after vote |
| **api/watchlist.php** | INSERT | `watchlist` | Add to watchlist |
| **api/watchlist.php** | DELETE | `watchlist` | Remove from watchlist |
| **api/watchlist.php** | SELECT COUNT | `watchlist` | Get watchlist count |
| **api/price_history.php** | SELECT | `pricetracking` | Price history chart data |
| **api/create_community.php** | SELECT | `community` | Check if community name exists |
| **api/create_community.php** | INSERT | `community` | Create community |
| **api/create_community.php** | INSERT | `community_member` | Auto-join creator |
| **api/join_community.php** | SELECT | `community` | Verify community exists |
| **api/join_community.php** | SELECT | `community_member` | Check membership |
| **api/join_community.php** | DELETE | `community_member` | Leave community |
| **api/join_community.php** | INSERT | `community_member` | Join community |
| **api/join_community.php** | SELECT COUNT | `community_member` | Updated member count |
| **api/delete_forum_item.php** | SELECT | `post` | Verify post ownership |
| **api/delete_forum_item.php** | DELETE | `post` | Delete post |
| **api/delete_forum_item.php** | SELECT | `comment` | Verify comment ownership |
| **api/delete_forum_item.php** | DELETE | `comment` | Delete comment |
| **api/get_sponsor.php** | SELECT | `sponsor_ads` | Get active sponsor ad |
| **api/chatbot_proxy.php** | SELECT | `component`, `storeavailability`, `store` | Component search for chatbot |

### Summary

| Metric | Count |
|--------|-------|
| Total SQL queries across project | **70+** |
| Files containing subqueries | **11** |
| Correlated scalar subqueries | **16** |
| Derived table subqueries (FROM clause) | **6** |
| EXISTS subqueries (WHERE clause) | **1** |
| Tables involved in subqueries | `storeavailability`, `community_member`, `vote`, `comment`, `posttag`, `tag` |


| Name | Role |
|------|------|
| **Shadman Shakib** | Founder & CEO |
| **Shah Mohammed Seaman** | CTO |
| **Jim Hossain** | Co-Founder & CFO |

---

## License

This project is developed as part of an academic initiative. All rights reserved.
