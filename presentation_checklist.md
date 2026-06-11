# Presentation & Submission Checklist

Use this guide to verify you have all required materials ready and to structure your 10-minute presentation video.

---

## 1. Submission Items Checklist

| Item | File Path / Location | Status | Details |
| :--- | :--- | :---: | :--- |
| **Database DDL File (.sql)** | `project_alpha.sql` (in root directory) | Ready | Contains schema definitions, tables, constraints, keys, and base seed records. |
| **Demo Video (~10 min)** | To be recorded | *Pending* | Ensure audio is clear and visual transitions are smooth. |

---

## 2. 10-Minute Video Script & Code Locations

### Phase 1: Project Overview (1 Minute)
* **Title:** Smart PC Build System
* **Objective:** AI-assisted PC builder recommendation system with dynamic real-time price comparison across Bangladeshi retailers (Star Tech, Ryans, Tech Land), featuring community forum, upgrade advisor, and game performance (FPS) estimation.
* **Problem Statement:** PC building is complex due to component compatibility, price variations across retailers, lack of local market integration, and user confusion about hardware bottlenecks.

---

### Phase 2: Database Design & Normalization (3 Minutes)
* **DDL Schema Location:** `project_alpha.sql`
* **Relational Schema Key Tables:**
  - `component`: Core specs (TDP, socket, slots, benchmark).
  - `store` & `storeavailability`: Retailers and dynamic pricing.
  - `pricetracking`: Time-series logging of price adjustments.
  - `user` & `build` & `buildcomponent`: Associative entities mapping user configurations.
  - `community`, `post`, `comment`, `tag`, `posttag`: Social discussion platform.
* **Normalization Discussion:**
  - **1NF (Atomic Values):** Every row has atomic columns and a primary key (e.g., `user_id`, `component_id`).
  - **2NF (No Partial Dependency):** Separate tables for `store` and `storeavailability`. Retailer names aren't duplicated per availability row; instead, they reference `store.store_id`.
  - **3NF (No Transitive Dependency):** `posttag` functions as a bridge table between `post` and `tag`. Tag names aren't stored inside the post row, eliminating redundancy.

---

### Phase 3: SQL Query Demonstration (3 Minutes)
*Detailed queries can also be found in the repository `README.md` under the "SQL Queries & Subqueries" section.*

#### 1. Dynamic Component Matrix (CASE WHEN & Derived Tables)
* **Location:** `includes/functions.php` (Lines 101-133)
* **Features:** CASE WHEN, LEFT JOIN, Derived Subquery, GROUP BY, MIN().

#### 2. Forum Post Feed (GROUP_CONCAT & Correlated Subqueries)
* **Location:** `forum.php` (Lines 59-74)
* **Features:** 4 Correlated Subqueries, GROUP_CONCAT.

#### 3. Live Watchlist Fetcher (4-Table JOIN)
* **Location:** `dashboard.php` (Lines 15-25)
* **Features:** JOIN, LEFT JOIN, derived table.

#### 4. Price History Trend Analysis (Time-Series & Date Math)
* **Location:** `dashboard.php` (Lines 30-34)
* **Features:** `DATE()`, `DATE_SUB()`, `INTERVAL`.

#### 5. Community Discovery (Correlated Subquery)
* **Location:** `forum.php` (Lines 76-81)
* **Features:** Dynamic membership status matching.

#### Advanced Features:
- **Indexes:** Defined on foreign keys like `storeavailability.component_id`, `buildcomponent.build_id`, `posttag.post_id`.
- **Triggers:** Implemented to automatically populate `pricetracking` on price updates.

---

### Phase 4: Core Functionalities Demonstration (3 Minutes)
Show the following flow in your browser demo:

1. **Custom PC Builder:** Add parts, show compatibility checking and dynamic pricing sums.
   - *File:* `custom_builder.php`
2. **AI Chatbot & FPS Estimator:** Show interactive recommendations.
   - *File:* `chatbot.php`
3. **Retailer Comparison:** Open a product to compare prices across Star Tech and Ryans.
   - *File:* `product.php`
4. **Community Forum & Modals:** Show posting, commenting, and deleting a post/comment (highlighting the new modern confirm/toast modal system).
   - *Files:* `forum.php`, `forum_post.php`
5. **Dashboard:** Show watchlist components and the Canvasjs price-history chart.
   - *File:* [dashboard.php](file:///opt/lampp/htdocs/myproject/dashboard.php)
