AI-Powered PC Builder &
Recommendation System
Full-stack PHP · MySQL · XAMPP / cPanel · Bangladesh market
Version
1.0 Draft
Backend
PHP 8.x
Database
MySQL/MariaDB
Currency
BDT (৳)
Modules
14
Date
May 2026
Contents
1. Introduction
Architecture & scope
2. System Architecture
Request flow · config · tech stack
3. Authentication & Authorisation
Sessions · JWT · roles
4. Module Overview
All 14 modules
5. Module Specifications
Per-module detail
6. Reusable Component Structure
Includes · templates · API
7. Folder Hierarchy
Complete directory tree
8. Deployment Strategy
XAMPP & cPanel
9. Non-Functional Requirements
Performance · security
10. Open Items & Assumptions
1. Introduction
1.1 Project overview
An AI-powered PC Builder and Recommendation System for the Bangladeshi market. The system guides users from purpose selection and BDT budget entry through AI-scored component matching and compatibility validation, generating optimised PC build recommendations using live data from local retailers (Star Tech, Ryans, Techland).

1.2 Architecture philosophy
Every user-facing page is a self-contained .php file that handles both the HTML frontend and its own backend logic — the single-file page pattern. This eliminates framework dependencies and makes the codebase portable between XAMPP and cPanel with zero code changes.

Key constraint
Single-file refers to page routing — not monolithic code. Shared logic lives in includes/; UI fragments in templates/. Each URL maps to exactly one PHP file with no framework dispatcher.
1.3 Scope
In scope
PHP 8.x / MySQL / Bootstrap 5
14 functional modules
XAMPP dev → cPanel prod (same code)
All prices in BDT, BD retailers
Existing schema preserved
Out of scope
Native mobile apps
Multi-currency / international
Automated retailer scrapers
Real-time inventory API sync
2. System Architecture
2.1 HTTP request lifecycle
01
Browser requests page.php
02
Include config.php — env vars, keys
03
Include db.php — PDO connection
04
Include auth.php — session + JWT
05
Include functions.php — utilities
06
Page backend logic runs
07
HTML rendered with data
08
Response sent to browser
2.2 config.php managed settings
Only file that differs between local and production
DB_HOST · DB_NAME · DB_USER · DB_PASS — database credentials
JWT_SECRET — 256-bit signing key, unique per deployment
ANTHROPIC_API_KEY — Claude API key for chatbot
BASE_URL — full URL prefix for assets and redirects
APP_ENV — 'local' or 'production'
SESSION_LIFETIME — seconds before session expiry
PSU_SAFETY_MARGIN — float, default 1.20 (20% headroom)
2.3 Technology stack
Layer	Technology
Frontend	HTML5, CSS3, Bootstrap 5, Vanilla JS, Chart.js
Backend	PHP 8.x — single-file page pattern
Database	MySQL / MariaDB — existing schema (read-only structure)
Auth	PHP Sessions + JWT via firebase/php-jwt
AI / Chatbot	Anthropic Claude API (claude-sonnet-4-20250514)
Charts	Chart.js — price trends, FPS estimates
Dev server	XAMPP (Apache + MySQL)
Production	cPanel shared hosting (Apache, PHP, MySQL)
Config mgmt	config.php — central env/credential store
Database rule
The application uses the MySQL/MariaDB schema provided by the project owner. It must not be modified, dropped, or recreated by application code. Schema changes are applied manually via phpMyAdmin or MySQL CLI only.
3. Authentication & Authorisation
3.1 Registration flow
01
Submit name, email, password on register.php
02
Sanitise inputs; check email uniqueness
03
password_hash() bcrypt, cost ≥ 12
04
Insert user record; show confirmation
3.2 Login flow
01
Submit email + password
02
password_verify() against hash
03
Start PHP session; write user_id, role, name
04
Generate JWT; set HttpOnly + Secure cookie
05
Redirect to dashboard.php
3.3 Role-based access
Role	Accessible pages	Restrictions
Guest	index.php, register.php, login.php	No authenticated pages
User	All user-facing pages	Cannot access admin/
Admin	All pages including admin/	Full access; manages catalogue and users
4. Module Overview
ID	Module	Description	Priority
M01	User Authentication	PHP sessions + JWT, role-based access (user / admin)	High
M02	Purpose Selection	Use-case profiling for budget allocation and scoring weights	High
M03	Budget Recommendation	Intelligent BDT allocation across component categories	High
M04	Compatibility Checker	CPU/MB socket, RAM gen, GPU clearance, PSU, form factor	Critical
M05	Wattage Calculation	TDP aggregation + 20% PSU safety headroom	High
M06	FPS Estimator	Benchmark-based FPS approximation per game title	Medium
M07	Store System	Real-time prices and availability from BD retailers	High
M08	Comparison Tool	Side-by-side spec and benchmark view for 2–4 components	Medium
M09	Build System	Top-3 optimised builds via scoring algorithm	Critical
M10	Custom Builder	Manual component selection with live compatibility feedback	Medium
M11	Price Tracking	Historical price recording and Chart.js trend display	Low
M12	Upgrade Suggestion	Bottleneck detection and budget-aware upgrade advice	Medium
M13	Dashboard	Saved builds, watchlists, activity history per user	Medium
M14	Chatbot	AI-assisted conversational build wizard (Anthropic API)	Low
5. Module Specifications
M01 — User authentication
Registration: name, email, password. bcrypt password storage.
Login: PHP session + JWT HttpOnly cookie on success.
Logout: session_destroy(), cookie cleared, redirect to index.php.
All inputs sanitised with htmlspecialchars() and PDO prepared statements.
M02 — Purpose selection
First step of the Build Wizard after login.
Four profiles: Gaming · Video Editing · Office · General Use.
Stored in $_SESSION['purpose']; defines scoring weight vector used throughout.
M03 — Budget allocation
Budget split by profile (% of total BDT):

Gaming
CPU
20%
MB
12%
RAM
10%
GPU
35%
Storage
8%
PSU
7%
Case
5%
Cooling
3%
Video Editing
CPU
25%
MB
12%
RAM
15%
GPU
20%
Storage
12%
PSU
7%
Case
5%
Cooling
4%
Office
CPU
15%
MB
12%
RAM
12%
GPU
5%
Storage
20%
PSU
8%
Case
15%
Cooling
13%
General
CPU
20%
MB
12%
RAM
12%
GPU
18%
Storage
14%
PSU
8%
Case
8%
Cooling
8%
M04 — Compatibility checker
Rule	Validation logic
CPU ↔ Motherboard	Socket types must match (e.g. AM5 ↔ AM5, LGA1700 ↔ LGA1700)
RAM ↔ Motherboard	RAM generation must match board spec (DDR4 / DDR5)
GPU ↔ Case	GPU length (mm) ≤ case max GPU clearance (mm)
PSU wattage	Calculated TDP × 1.20 safety factor ≤ PSU rated wattage
MB ↔ Case	Motherboard form factor supported by case (ATX / mATX / ITX)
CPU cooler ↔ Case	Cooler height (mm) ≤ case max cooler clearance (mm)
RAM slots	Number of sticks selected ≤ motherboard slot count
Storage interface	NVMe → M.2 / PCIe slot; SATA → SATA port availability
M05 — Wattage calculation
Each component stores a tdp_watts value in the database.
wattage.php sums all TDP values for the build.
Minimum PSU = total TDP × PSU_SAFETY_MARGIN (default 1.20).
Build result shows: estimated TDP, recommended min PSU, selected PSU, and headroom %.
M06 — FPS estimator
Each CPU and GPU stores a normalised benchmark_score.
FPS ≈ (GPU_score × GPU_weight + CPU_score × CPU_weight) ÷ game_difficulty_factor.
Game titles and difficulty factors stored in fps_profiles table (admin-managed).
Output shown as a range, e.g. "60–80 fps at 1080p Medium", with an approximation disclaimer.
M09 — Build system scoring algorithm
Performance score
60%
Weighted benchmarks by purpose profile
Value score
30%
Performance per BDT
Availability score
10%
Bonus for confirmed in-stock
Returns top 3 distinct compatible builds ranked by composite score. Each build card shows full parts list, total BDT, FPS estimate, PSU headroom, and direct retailer links.

M12 — Upgrade suggestion
User inputs current build and maximum upgrade budget.
Bottleneck detected by comparing CPU vs GPU benchmark scores — imbalance flags the weaker part.
Constrained scoring algorithm finds best single or dual-component upgrade within budget.
Output: before/after specs, estimated FPS improvement, retailer links.
M14 — AI chatbot
Backed by Anthropic Claude API via api/chatbot_proxy.php.
System prompt constrains model to PC building topics and BDT pricing context.
Conversation history maintained client-side in JS (within 4K token window).
Rate limit: 20 requests per user per hour enforced in the proxy.
Can pre-fill Build Wizard fields via JS postMessage bridge on user confirmation.
6. Reusable Component Structure
6.1 PHP include files (includes/)
File	Responsibilities
db.php	PDO singleton. Returns shared DB connection from config.php. All queries use prepared statements.
auth.php	require_auth($role) — validates session + JWT, redirects on failure. get_current_user(). generate_jwt() / verify_jwt().
functions.php	format_bdt($n), sanitise($input), paginate(), flash_message().
compatibility.php	check_compatibility($components) — returns pass/fail array with human-readable error messages.
scoring.php	score_build($components, $purpose, $budget). get_top_builds($purpose, $budget, $limit=3).
wattage.php	calculate_tdp($components). recommend_psu_wattage($tdp).
fps.php	estimate_fps($cpu_id, $gpu_id, $game_slug) — returns [min, max, resolution, quality].
budget_allocator.php	allocate_budget($total_bdt, $purpose) — returns category => BDT_amount map.
6.2 HTML template fragments (templates/)
File	Purpose
header.php	<!DOCTYPE html> through </nav>. Accepts $page_title. Includes Bootstrap 5, custom CSS, role-aware nav.
footer.php	Closing HTML, Bootstrap JS, app.js, page-specific scripts via $footer_scripts.
component_card.php	Bootstrap card: name, category badge, price, stock status, compare button, watchlist toggle.
build_card.php	Full build result card: parts table, total cost, FPS badge, PSU headroom bar, save/share actions.
6.3 Internal AJAX endpoints (api/)
All endpoints require a valid session cookie and return Content-Type: application/json exclusively.

File	Accepts	Returns
get_components.php	category, budget_max, purpose	Matching components array
check_compatibility.php	component_ids object	Compatibility results array
get_builds.php	purpose, budget	Top 3 scored builds
chatbot_proxy.php	messages array	Claude model response text
price_history.php	component_id	Chart.js label/value arrays
watchlist.php	action (add/remove), component_id	Updated watchlist count
7. Folder Hierarchy
📁 pc-builder/ Project root
config.php Central config: DB, JWT, API keys, env flags
index.php Landing page
register.php User registration
login.php Login + session init
logout.php Session destroy + redirect
dashboard.php Personalised user dashboard
purpose.php Use-case wizard step
budget.php Budget input + allocation preview
builds.php Top-3 build results
custom_builder.php Manual component picker
compare.php Side-by-side comparison
store.php Retailer listings and prices
price_history.php Price trend charts
upgrade.php Build analyzer + upgrade advisor
chatbot.php AI chatbot interface
📁 admin/ Admin-only section
index.php Admin dashboard
components.php Component catalogue CRUD
users.php User management
prices.php Price and availability updates
📁 includes/ Reusable PHP logic
db.php PDO connection handler
auth.php Session/JWT verification
functions.php Utility helpers
compatibility.php Compatibility engine
scoring.php Build scoring algorithm
wattage.php TDP aggregation + PSU calculator
fps.php FPS estimation logic
budget_allocator.php Budget allocation per profile
📁 templates/ HTML fragment includes
header.php HTML head + nav bar
footer.php Footer + JS includes
component_card.php Reusable component card
build_card.php Build result card
📁 assets/ Static files
css/style.css Global stylesheet
js/app.js Global JS utilities
js/compare.js Comparison tool JS
js/custom_builder.js Live compatibility JS
📁 api/ Internal AJAX endpoints
get_components.php Filtered component list (JSON)
check_compatibility.php Live compatibility check (JSON)
get_builds.php Build generation trigger (JSON)
chatbot_proxy.php Anthropic API proxy
price_history.php Price history for Chart.js (JSON)
watchlist.php Add/remove watchlist items (JSON)
8. Deployment Strategy
8.1 Local development (XAMPP)
01
Install XAMPP (Apache + MySQL + PHP 8.1+)
02
Copy project to htdocs/pc-builder/
03
Import .sql schema via phpMyAdmin
04
Edit config.php: set local DB creds, BASE_URL, APP_ENV=local
05
Start Apache + MySQL in XAMPP panel
06
Visit localhost/pc-builder/
8.2 cPanel production deployment
01
Upload and extract project zip via File Manager into public_html/
02
Create DB + user in cPanel MySQL Databases; assign all privileges
03
Import .sql schema via cPanel phpMyAdmin
04
Edit config.php: production DB creds, BASE_URL, APP_ENV=production, ANTHROPIC_API_KEY
05
Verify PHP 8.1+ in MultiPHP Manager
06
App is live — no Composer, no Node, no build step
Zero-modification portability
Because all environment-sensitive values live exclusively in config.php, and the application uses only PHP built-ins + PDO (universally available on cPanel), the entire application moves from XAMPP to production by uploading files and updating one file.
8.3 Security hardening (production)
config.php placed above public_html where possible; otherwise .htaccess Deny from all.
APP_ENV=production disables PHP error display (display_errors = Off).
All SQL via PDO prepared statements — no raw string interpolation.
HTTPS enforced via cPanel AutoSSL; HTTP redirected via .htaccess.
JWT secret: 256-bit random string, unique per deployment.
Session cookies: HttpOnly, Secure, SameSite=Lax.
ANTHROPIC_API_KEY stored only in config.php, never echoed or logged.
9. Non-Functional Requirements
Category	Requirement
Performance	Build generation (top 3) completes within 3 s for catalogues up to 500 components per category. Chart.js charts load within 1.5 s.
Scalability	Functional on shared cPanel hosting (512 MB RAM, PHP 8.1, MySQL 5.7+) without modification.
Browser support	Chrome 120+, Firefox 120+, Safari 17+, Edge 120+. Responsive at ≥ 360 px.
Accessibility	All form elements carry aria-label. Colour contrast ratio ≥ 4.5:1. Keyboard navigable.
Maintainability	Each page file ≤ 600 lines. Business logic lives in includes/, not inline in page files.
Security	OWASP Top 10 mitigated: XSS via htmlspecialchars(), SQL injection via prepared statements, CSRF via synchroniser tokens on all state-changing forms.
Data integrity	Existing DB schema must not be altered by application code. All schema changes are manual and owner-approved.
10. Open Items & Assumptions
Assumptions
Project owner will provide the complete .sql schema before development starts.
Retailer data populated manually by admin; no automated scraping in v1.
Anthropic API key supplied by project owner and stored in config.php.
Benchmark scores and FPS difficulty factors seeded by owner via phpMyAdmin or admin panel.
Open items
Email-based password reset — confirm SMTP provider availability for v1 or defer.
Retailer data import — manual-only vs CSV bulk-import tool in admin panel.
FPS model coefficients — require validation against real-world results for ≥ 5 titles.
Admin CSV export — clarify whether required for v1.
End of document — Version 1.0 · May 2026 · Confidential — Internal Use Only