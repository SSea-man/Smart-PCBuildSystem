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

### Chatbot
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

Every SQL query and subquery used across the entire project, organized by file.

### 1. `includes/functions.php` — Core Component Query Engine

| # | Line | Type | SQL Summary |
|---|------|------|-------------|
| Q1 | 100–131 | SELECT + **Subquery** | `component_base_sql()` — Master query used by 10+ files. Contains a **derived table subquery**: `LEFT JOIN (SELECT component_id, MIN(price) AS price, stock_status, store_id FROM storeavailability GROUP BY component_id) sa` |
| Q2 | 133–137 | SELECT | `get_component($id)` — Fetches single component by ID using `component_base_sql()` |
| Q3 | 140–178 | SELECT | `get_components_by_category($cat, $max_price)` — Fetches components by type with optional price ceiling using `component_base_sql()` |

### 2. `includes/auth.php` — Authentication

| # | Line | Type | SQL Summary |
|---|------|------|-------------|
| Q4 | 66 | SELECT | `SELECT * FROM user WHERE email = ?` — Find user by email for login |
| Q5 | 78 | UPDATE | `UPDATE user SET user_password=? WHERE user_id=?` — Rehash password on login |

### 3. `includes/fps.php` — FPS Estimation

| # | Line | Type | SQL Summary |
|---|------|------|-------------|
| Q6 | 3 | SELECT | `SELECT benchmark_score FROM component WHERE component_id=?` — Get CPU benchmark |
| Q7 | 4 | SELECT | `SELECT benchmark_score FROM component WHERE component_id=?` — Get GPU benchmark |
| Q8 | 5 | SELECT | `SELECT * FROM fps_profiles WHERE game_slug=?` — Get game FPS profile |
| Q9 | 31 | SELECT | `SELECT game_slug, game_name FROM fps_profiles ORDER BY game_name` — List all games |

### 4. `register.php` — User Registration

| # | Line | Type | SQL Summary |
|---|------|------|-------------|
| Q10 | 25 | SELECT | `SELECT user_id FROM user WHERE email = ?` — Check duplicate email |
| Q11 | 30 | INSERT | `INSERT INTO user (user_name, email, user_password) VALUES (?,?,?)` — Create account |

### 5. `store.php` — Component Catalogue

| # | Line | Type | SQL Summary |
|---|------|------|-------------|
| Q12 | 27–31 | SELECT + JOIN | `SELECT MIN(sa.price) as min_val, MAX(sa.price) as max_val FROM storeavailability sa JOIN component c ...` — Price range bounds |
| Q13 | 37–41 | SELECT DISTINCT | `SELECT DISTINCT c.brand FROM component c WHERE c.type LIKE ? AND c.brand IS NOT NULL` — Brand filter list |
| Q14 | 76 | SELECT + **Subquery** | `SELECT COUNT(*) c FROM (component_base_sql() + WHERE ...) sub` — Total count with **derived table subquery** wrapping the base query |
| Q15 | 88 | SELECT + **Subquery** | `component_base_sql() + WHERE ... ORDER BY ... LIMIT ... OFFSET ...` — Paginated catalogue (inherits base subquery) |
| Q16 | 96 | SELECT | `SELECT component_id FROM watchlist WHERE user_id = ?` — User's watchlisted IDs |

### 6. `product.php` — Product Detail

| # | Line | Type | SQL Summary |
|---|------|------|-------------|
| Q17 | 12 | SELECT + **Subquery** | `component_base_sql() WHERE c.component_id = ?` — Single product detail (inherits base subquery) |
| Q18 | 18–23 | SELECT + JOIN | `SELECT sa.price, sa.stock_status, s.store_name FROM storeavailability sa JOIN store s ON s.store_id = sa.store_id WHERE sa.component_id = ?` — All retailers for product |

### 7. `dashboard.php` — User Dashboard

| # | Line | Type | SQL Summary |
|---|------|------|-------------|
| Q19 | 11–12 | SELECT | `SELECT * FROM build WHERE user_id=? ORDER BY created_at DESC LIMIT 10` — User's saved builds |
| Q20 | 15–24 | SELECT + **Subquery** + JOIN | Watchlist query with **derived table subquery**: `LEFT JOIN (SELECT component_id, MIN(price) as price, store_id FROM storeavailability GROUP BY component_id) sa` — Watchlist items with cheapest price |
| Q21 | 30–33 | SELECT | `SELECT DATE(changed_at) as d, new_price FROM pricetracking WHERE component_id=? AND changed_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)` — Price trend for chart |
| Q22 | 41 | SELECT COUNT | `SELECT COUNT(*) c FROM component` — Total component count |
| Q23 | 42 | SELECT COUNT | `SELECT COUNT(*) c FROM store` — Total store count |

### 8. `compare.php` — Component Comparison

| # | Line | Type | SQL Summary |
|---|------|------|-------------|
| Q24 | 14 | SELECT + **Subquery** | `component_base_sql() WHERE c.component_id IN (...)` — Fetch multiple components for comparison (inherits base subquery) |

### 9. `price_history.php` — Price History

| # | Line | Type | SQL Summary |
|---|------|------|-------------|
| Q25 | 15 | SELECT + **Subquery** | `component_base_sql() WHERE c.component_id = ?` — Component detail (inherits base subquery) |
| Q26 | 17–20 | SELECT | `SELECT DATE(changed_at) as d, new_price FROM pricetracking WHERE component_id=? AND changed_at >= DATE_SUB(NOW(), INTERVAL 90 DAY)` — 90-day price history |
| Q27 | 26 | SELECT | `SELECT component_id as id, component_name as name, type FROM component ORDER BY type, component_name` — All components dropdown |

### 10. `upgrade.php` — Upgrade Advisor

| # | Line | Type | SQL Summary |
|---|------|------|-------------|
| Q28 | 23 | SELECT + **Subquery** | `component_base_sql() WHERE c.component_id=?` — Current CPU detail (inherits base subquery) |
| Q29 | 24 | SELECT + **Subquery** | `component_base_sql() WHERE c.component_id=?` — Current GPU detail (inherits base subquery) |
| Q30 | 42–47 | SELECT + **Subquery** | `component_base_sql() WHERE c.type IN ('GPU (graphics)', 'Graphics Card') AND sa.price <= ? AND sa.price > ? ORDER BY c.benchmark_score DESC LIMIT 1` — Find GPU upgrade |
| Q31 | 49–54 | SELECT + **Subquery** | `component_base_sql() WHERE c.type IN ('CPU', 'CPU (processing)') AND sa.price <= ? AND sa.price > ? ORDER BY c.benchmark_score DESC LIMIT 1` — Find CPU upgrade |
| Q32 | 62 | INSERT | `INSERT INTO upgradesuggestion (user_id, build_id, component_id) VALUES (?, NULL, ?)` — Log suggestion |
| Q33 | 67–70 | SELECT + **Subquery** | `SELECT c.component_id, c.component_name, c.benchmark_score, COALESCE(sa.price,0) FROM component c LEFT JOIN (SELECT component_id, MIN(price) as price FROM storeavailability GROUP BY component_id) sa ...` — CPU list with **derived table subquery** |
| Q34 | 71–74 | SELECT + **Subquery** | Same as Q33 but for GPUs — GPU list with **derived table subquery** |

### 11. `forum.php` — Forum Listing (Most Complex Queries)

| # | Line | Type | SQL Summary |
|---|------|------|-------------|
| Q35 | 22–25 | SELECT + **Subquery** | Community detail: `SELECT c.*, (SELECT COUNT(*) FROM community_member cm WHERE cm.community_id = c.community_id) AS member_count FROM community c WHERE c.community_id = ?` — **Correlated scalar subquery** |
| Q36 | 30–32 | SELECT | `SELECT 1 FROM community_member WHERE community_id = ? AND user_id = ?` — Check membership |
| Q37 | 42 | WHERE + **Subquery** | `EXISTS (SELECT 1 FROM posttag pt JOIN tag t ON pt.tag_id = t.tag_id WHERE pt.post_id = p.post_id AND t.name = 'announcement')` — **EXISTS subquery** for announcement filter |
| Q38 | 50–53 | SELECT COUNT | `SELECT COUNT(*) c FROM post p JOIN user u ... WHERE ...` — Total post count for pagination |
| Q39 | 59–73 | SELECT + **5 Subqueries** | Main post listing with **5 correlated scalar subqueries**: `(SELECT COUNT(*) FROM comment WHERE post_id = p.post_id)` as comment_count, `(SELECT COUNT(*) FROM vote WHERE post_id = p.post_id AND vote_type = 'upvote')` as score, `(SELECT GROUP_CONCAT(t.name SEPARATOR ',') FROM posttag pt JOIN tag t ...)` as tags, `(SELECT COUNT(*) FROM vote WHERE ... AND user_id = ?)` as user_vote |
| Q40 | 76–80 | SELECT + **2 Subqueries** | Sidebar communities: `SELECT c.*, (SELECT COUNT(*) FROM community_member ...) AS member_count, (SELECT COUNT(*) FROM community_member ... AND cm.user_id = ?) AS is_joined FROM community c` — **2 correlated scalar subqueries** |

### 12. `forum_create.php` — Create Forum Post

| # | Line | Type | SQL Summary |
|---|------|------|-------------|
| Q41 | 76 | INSERT | `INSERT INTO post (user_id, title, content, image_path, community_id, created_at) VALUES (?, ?, ?, ?, ?, NOW())` |
| Q42 | 79 | SELECT | `SELECT LAST_INSERT_ID() AS id` — Get new post ID |
| Q43 | 85 | SELECT | `SELECT tag_id FROM tag WHERE name = ?` — Check if tag exists |
| Q44 | 89 | INSERT | `INSERT INTO tag (name) VALUES (?)` — Create new tag |
| Q45 | 90 | SELECT | `SELECT LAST_INSERT_ID() AS id` — Get new tag ID |
| Q46 | 92 | INSERT | `INSERT INTO posttag (post_id, tag_id, created_at) VALUES (?, ?, NOW())` — Link tag to post |
| Q47 | 102 | SELECT | `SELECT community_id, name FROM community ORDER BY name ASC` — Community dropdown |

### 13. `forum_post.php` — Forum Post Detail

| # | Line | Type | SQL Summary |
|---|------|------|-------------|
| Q48 | 14–18 | SELECT + **2 Subqueries** | Post detail: `SELECT p.*, u.user_name, c.name AS community_name, (SELECT COUNT(*) FROM vote ... AND vote_type = 'upvote') AS score, (SELECT COUNT(*) FROM vote ... AND user_id = ?) AS user_vote` — **2 correlated scalar subqueries** |
| Q49 | 36 | SELECT | `SELECT post_id, user_id FROM post WHERE post_id = ?` — Verify post ownership for deletion |
| Q50 | 38 | DELETE | `DELETE FROM post WHERE post_id = ?` — Delete post |
| Q51 | 50 | SELECT | `SELECT comment_id, user_id FROM comment WHERE comment_id = ?` — Verify comment ownership |
| Q52 | 52 | DELETE | `DELETE FROM comment WHERE comment_id = ?` — Delete comment |
| Q53 | 62 | INSERT | `INSERT INTO comment (user_id, post_id, content, created_at) VALUES (?, ?, ?, NOW())` — Add comment |
| Q54 | 69–75 | SELECT + **2 Subqueries** | Comments list: `SELECT c.*, u.user_name, (SELECT COUNT(*) FROM vote ... AND vote_type = 'upvote') AS score, (SELECT COUNT(*) FROM vote ... AND user_id = ?) AS user_vote` — **2 correlated scalar subqueries** |
| Q55 | 79–81 | SELECT + JOIN | `SELECT t.name FROM posttag pt JOIN tag t ON pt.tag_id = t.tag_id WHERE pt.post_id = ?` — Post tags |
| Q56 | 88–93 | SELECT + **2 Subqueries** | Community sidebar: `SELECT c.*, (SELECT COUNT(*) FROM community_member ...) AS member_count, (SELECT COUNT(*) FROM community_member ... AND user_id = ?) AS is_joined` — **2 correlated scalar subqueries** |

### 14. `admin/index.php` — Admin Dashboard

| # | Line | Type | SQL Summary |
|---|------|------|-------------|
| Q57 | 8 | SELECT COUNT | `SELECT COUNT(*) c FROM user` — Total users |
| Q58 | 9 | SELECT COUNT | `SELECT COUNT(*) c FROM component` — Total components |
| Q59 | 10 | SELECT COUNT | `SELECT COUNT(*) c FROM build` — Total builds |
| Q60 | 11 | SELECT | `SELECT user_id, user_name, email, role, created_at FROM user ORDER BY created_at DESC LIMIT 5` — Recent users |
| Q61 | 12–15 | SELECT + JOIN | `SELECT b.*, u.user_name FROM build b JOIN user u ON u.user_id = b.user_id ORDER BY b.created_at DESC LIMIT 5` — Recent builds with usernames |

### 15. `admin/components.php` — Component CRUD

| # | Line | Type | SQL Summary |
|---|------|------|-------------|
| Q62 | 13 | DELETE | `DELETE FROM component WHERE component_id=?` — Delete component |
| Q63 | 29 | SELECT | `SELECT image_url FROM component WHERE component_id=?` — Get current image before update |
| Q64 | 45–46 | UPDATE | `UPDATE component SET component_name=?, type=?, brand=?, ... WHERE component_id=?` — Update component (18 columns) |
| Q65 | 49–50 | INSERT | `INSERT INTO component (component_name, type, brand, ...) VALUES (?,?,?, ...)` — Add component (18 columns) |
| Q66 | 58 | SELECT | `SELECT * FROM component WHERE component_id=?` — Load component for edit form |
| Q67 | 66 | SELECT COUNT | `SELECT COUNT(*) c FROM component WHERE ...` — Total count for pagination |
| Q68 | 68–71 | SELECT + **Subquery** | Component list with prices: `SELECT c.*, COALESCE(sa.price,0), COALESCE(sa.stock_status,'—') FROM component c LEFT JOIN (SELECT component_id, MIN(price) as price, stock_status FROM storeavailability GROUP BY component_id) sa ...` — **Derived table subquery** |

### 16. `admin/users.php` — User Role Management

| # | Line | Type | SQL Summary |
|---|------|------|-------------|
| Q69 | 14 | UPDATE | `UPDATE user SET role=? WHERE user_id=?` — Change user role |
| Q70 | 41 | SELECT COUNT | `SELECT COUNT(*) c FROM user WHERE ...` — Total user count |
| Q71 | 43 | SELECT | `SELECT * FROM user WHERE ... ORDER BY created_at DESC LIMIT 15 OFFSET ?` — Paginated user list |

### 17. `admin/prices.php` — Price & Stock Management

| # | Line | Type | SQL Summary |
|---|------|------|-------------|
| Q72 | 20 | SELECT | `SELECT price FROM storeavailability WHERE component_id=? LIMIT 1` — Get old price |
| Q73 | 23 | SELECT | `SELECT availability_id FROM storeavailability WHERE component_id=? LIMIT 1` — Check if price row exists |
| Q74 | 25 | UPDATE | `UPDATE storeavailability SET price=?, stock_status=? WHERE component_id=?` — Update price |
| Q75 | 27 | INSERT | `INSERT INTO storeavailability (store_id, component_id, stock_status, price) VALUES (1,?,?,?)` — Create price entry |
| Q76 | 30 | INSERT | `INSERT INTO pricetracking (component_id, old_price, new_price) VALUES (?,?,?)` — Log price change |
| Q77 | 43–44 | SELECT + **Subquery** | `component_base_sql() + WHERE ... ORDER BY ...` — Component list for price editing (inherits base subquery) |

### 18. `admin/sponsor.php` — Sponsor Ad Management

| # | Line | Type | SQL Summary |
|---|------|------|-------------|
| Q78 | 25–26 | UPDATE | `UPDATE sponsor_ads SET title=?, image_url=?, link_url=?, description=?, active=?, start_date=?, end_date=? WHERE ad_id=?` |
| Q79 | 29–30 | INSERT | `INSERT INTO sponsor_ads (title, image_url, link_url, description, active, start_date, end_date) VALUES (?,?,?,?,?,?,?)` |
| Q80 | 39 | DELETE | `DELETE FROM sponsor_ads WHERE ad_id=?` — Remove ad |
| Q81 | 47 | SELECT | `SELECT * FROM sponsor_ads WHERE ad_id=?` — Load ad for editing |

### 19. `api/save_build.php` — Save Build

| # | Line | Type | SQL Summary |
|---|------|------|-------------|
| Q82 | 21 | INSERT | `INSERT INTO build (user_id, name, total_price, score, purpose) VALUES (?,?,?,?,?)` — Save build metadata |
| Q83 | 27 | INSERT | `INSERT IGNORE INTO buildcomponent (build_id, component_id) VALUES (?,?)` — Save each component (loop) |

### 20. `api/delete_build.php` — Delete Build

| # | Line | Type | SQL Summary |
|---|------|------|-------------|
| Q84 | 12 | DELETE | `DELETE FROM build WHERE build_id=? AND user_id=?` — Delete owned build |

### 21. `api/vote.php` — Upvote/Downvote

| # | Line | Type | SQL Summary |
|---|------|------|-------------|
| Q85 | 24 | SELECT | `SELECT vote_id FROM vote WHERE user_id = ? AND post_id/comment_id = ?` — Check existing vote |
| Q86 | 27 | DELETE | `DELETE FROM vote WHERE vote_id = ?` — Toggle off (remove vote) |
| Q87 | 30 | INSERT | `INSERT INTO vote (user_id, post_id/comment_id, vote_type, created_at) VALUES (?, ?, 'upvote', NOW())` — Cast vote |
| Q88 | 34 | SELECT COUNT | `SELECT COUNT(*) c FROM vote WHERE post_id/comment_id = ? AND vote_type = 'upvote'` — New score |

### 22. `api/watchlist.php` — Watchlist Toggle

| # | Line | Type | SQL Summary |
|---|------|------|-------------|
| Q89 | 14 | INSERT | `INSERT IGNORE INTO watchlist (user_id, component_id) VALUES (?,?)` — Add to watchlist |
| Q90 | 16 | DELETE | `DELETE FROM watchlist WHERE user_id=? AND component_id=?` — Remove from watchlist |
| Q91 | 18 | SELECT COUNT | `SELECT COUNT(*) c FROM watchlist WHERE user_id=?` — Current count |

### 23. `api/price_history.php` — Price Chart Data

| # | Line | Type | SQL Summary |
|---|------|------|-------------|
| Q92 | 13–16 | SELECT | `SELECT DATE(changed_at) as label, new_price as value FROM pricetracking WHERE component_id = ? ORDER BY changed_at` — Chart data |

### 24. `api/create_community.php` — Create Community

| # | Line | Type | SQL Summary |
|---|------|------|-------------|
| Q93 | 38 | SELECT | `SELECT community_id FROM community WHERE LOWER(name) = LOWER(?)` — Check duplicate name |
| Q94 | 44 | INSERT | `INSERT INTO community (name, description, created_by, created_at) VALUES (?, ?, ?, NOW())` |
| Q95 | 48 | SELECT | `SELECT LAST_INSERT_ID() AS id` — Get new community ID |
| Q96 | 50 | INSERT | `INSERT IGNORE INTO community_member (community_id, user_id) VALUES (?, ?)` — Auto-join creator |

### 25. `api/join_community.php` — Join/Leave Community

| # | Line | Type | SQL Summary |
|---|------|------|-------------|
| Q97 | 22 | SELECT | `SELECT community_id FROM community WHERE community_id = ?` — Verify exists |
| Q98 | 27 | SELECT | `SELECT * FROM community_member WHERE community_id = ? AND user_id = ?` — Check membership |
| Q99 | 30 | DELETE | `DELETE FROM community_member WHERE community_id = ? AND user_id = ?` — Leave |
| Q100 | 33 | INSERT | `INSERT INTO community_member (community_id, user_id) VALUES (?, ?)` — Join |
| Q101 | 37 | SELECT COUNT | `SELECT COUNT(*) c FROM community_member WHERE community_id = ?` — Updated count |

### 26. `api/delete_forum_item.php` — Delete Post/Comment

| # | Line | Type | SQL Summary |
|---|------|------|-------------|
| Q102 | 48 | SELECT | `SELECT post_id, user_id FROM post WHERE post_id = ?` — Verify post ownership |
| Q103 | 61 | DELETE | `DELETE FROM post WHERE post_id = ?` — Delete post |
| Q104 | 78 | SELECT | `SELECT comment_id, user_id FROM comment WHERE comment_id = ?` — Verify comment ownership |
| Q105 | 91 | DELETE | `DELETE FROM comment WHERE comment_id = ?` — Delete comment |

### 27. `api/get_sponsor.php` — Sponsor Ad

| # | Line | Type | SQL Summary |
|---|------|------|-------------|
| Q106 | 6–10 | SELECT | `SELECT * FROM sponsor_ads WHERE active = 1 AND start_date <= CURDATE() AND end_date >= CURDATE() LIMIT 1` — Active sponsor ad |

### 28. `api/chatbot_proxy.php` — AI Chatbot (15 Query Contexts)

| # | Line | Type | SQL Summary |
|---|------|------|-------------|
| Q107 | 40 | Dynamic SQL | `$pdo->prepare($sql)` — Admin raw SQL execution (admin-only, read-only enforced) |
| Q108 | 63 | SELECT COUNT | `SELECT COUNT(*) c FROM user` — User count for admin chatbot |
| Q109 | 64 | SELECT | `SELECT user_name, email, role FROM user LIMIT 10` — User list for admin chatbot |
| Q110 | 74 | SELECT COUNT | `SELECT COUNT(*) c FROM component` — Product count |
| Q111 | 81 | SELECT + **Subquery** | `component_base_sql() WHERE c.component_name LIKE ?` — Compare item 1 lookup (inherits base subquery) |
| Q112 | 82 | SELECT + **Subquery** | `component_base_sql() WHERE c.component_name LIKE ?` — Compare item 2 lookup |
| Q113 | 137–138 | SELECT + **Subquery** | `component_base_sql() WHERE c.component_name LIKE ?` — Compatibility check items (×2) |
| Q114 | 193 | SELECT + **Subquery** | `component_base_sql() WHERE c.component_name LIKE ?` — Spec attribute lookup |
| Q115 | 266–273 | SELECT + **Subquery** | `SELECT * FROM (component_base_sql()) c WHERE c.price_bdt > 0 AND c.price_bdt <= ? ORDER BY c.price_bdt DESC LIMIT 5` — Budget recommendations (wraps base as **derived table subquery**) |
| Q116 | 295–302 | SELECT + **Subquery** | `SELECT * FROM (component_base_sql()) c WHERE c.price_bdt > 0 ORDER BY c.price_bdt ASC/DESC LIMIT 1` — Cheapest/most expensive lookup |
| Q117 | 313 | SELECT + **Subquery** | `component_base_sql() WHERE c.brand LIKE ?` — Brand product search |
| Q118 | 334 | SELECT + **Subquery** | `component_base_sql() WHERE c.type = ? LIMIT 5` — Category listing |
| Q119 | 351 | SELECT + **Subquery** | `component_base_sql() WHERE c.component_name LIKE ? LIMIT 5` — Product detail/price lookup |
| Q120 | 460 | SELECT + **Subquery** | `component_base_sql() WHERE c.component_name LIKE ? OR c.brand LIKE ? OR c.type LIKE ? LIMIT 5` — Fallback fuzzy search |

### 29. `api/check_compatibility.php` — Compatibility Check API

| # | Line | Type | SQL Summary |
|---|------|------|-------------|
| Q121 | 20 | SELECT + **Subquery** | `component_base_sql() WHERE c.component_id = ?` — Fetch each component for compatibility check (loop, inherits base subquery) |

---

### Subquery Summary

| Subquery Type | Count | Where Used |
|---------------|-------|------------|
| **Derived Table** (FROM clause) | 8 unique locations | `component_base_sql()`, `dashboard.php`, `upgrade.php` (×2), `admin/components.php`, `store.php`, chatbot budget/price queries |
| **Correlated Scalar** (SELECT clause) | 16 instances | `forum.php` (×7), `forum_post.php` (×7), `forum.php` community sidebar (×2) |
| **EXISTS** (WHERE clause) | 1 instance | `forum.php` announcement filter |
| **Wrapper Subquery** | 2 instances | `store.php` count, chatbot budget query |

### Final Totals

| Metric | Count |
|--------|-------|
| **Total unique SQL statements** | **121** |
| SELECT queries | 62 |
| INSERT queries | 22 |
| UPDATE queries | 6 |
| DELETE queries | 12 |
| SELECT COUNT queries | 12 |
| Dynamic SQL (chatbot admin) | 1 |
| Queries containing subqueries | **27** |
| Files with SQL queries | **28** |
| Tables queried | `component`, `storeavailability`, `store`, `user`, `build`, `buildcomponent`, `post`, `comment`, `vote`, `tag`, `posttag`, `community`, `community_member`, `watchlist`, `pricetracking`, `fps_profiles`, `upgradesuggestion`, `sponsor_ads` |

---

## Contributors

| Name | Role |
|------|------|
| **Shadman Ahammad Shanto** | Founder & CEO |
| **Shah Mohammed Seaman** | CTO |
| **Jim Hossain** | Co-Founder & CFO |

---

## License

This project is developed as part of an academic initiative. All rights reserved.

## Contact

For any inquiries, please contact the project maintainers.
