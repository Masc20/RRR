# RAMP REIGN AND RUNWAY (RRR)
### Real-Time Pageant & Intramural House Cup Tabulation System

[![PHP Version](https://img.shields.io/badge/PHP-%3E%3D%207.4%20%7C%208.3-777bb4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/)
[![Database](https://img.shields.io/badge/Database-MySQL%20%2F%20MariaDB-4479a1?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![Frontend](https://img.shields.io/badge/UI-Bootstrap%204%20%26%20jQuery-563d7c?style=for-the-badge&logo=bootstrap&logoColor=white)](https://getbootstrap.com/)
[![Styling](https://img.shields.io/badge/Design-Custom%20CSS3%20Design%20Tokens-1572b6?style=for-the-badge&logo=css3&logoColor=white)](css/judge.css)
[![Scoring Engine](https://img.shields.io/badge/Engine-ScoreCalculator.php-28a745?style=for-the-badge&logo=calculator&logoColor=white)](lib/scorecalculator.php)
[![Platform](https://img.shields.io/badge/Platform-XAMPP%20%2F%20LAMP%20%2F%20WAMP-orange?style=for-the-badge&logo=apache&logoColor=white)](https://www.apachefriends.org/)

---

## Table of Contents
1. [Overview](#overview)
2. [Repository Directory Structure](#repository-directory-structure)
3. [Central Scoring Engine & Auditing Specification](#central-scoring-engine--auditing-specification)
   - [Mathematical Formulation](#mathematical-formulation)
   - [`ScoreCalculator` API Reference](#scorecalculator-api-reference)
4. [Judge Scoring Experience & UI System](#judge-scoring-experience--ui-system)
5. [Installation & Setup Guide](#installation--setup-guide)
6. [Default User Accounts & Credentials](#default-user-accounts--credentials)
7. [Database Schema Reference](#database-schema-reference)
8. [Architecture & Coding Conventions](#architecture--coding-conventions)
9. [Troubleshooting & Frequently Asked Questions](#troubleshooting--frequently-asked-questions)

---

## Overview

**RAMP REIGN AND RUNWAY (RRR)** is an automated, real-time scoring, tabulation, and event presentation platform built for pageants, runway competitions, and intramural House Cup events.

### Key Capabilities
- **Tactile Adjudicator Workspace:** High-contrast slider controls with physical ruler markings and tactile grip lines, real-time score totalizer badges, and smooth tab transitions.
- **Audit-Grade Calculation Engine:** All mathematical formulas, criteria averaging, category percentage weightings, and division rankings are isolated in a single, self-documenting engine ([`lib/scorecalculator.php`](lib/scorecalculator.php)).
- **Dual Presentation Layer:** Dedicated administrative console with printable score sheets alongside public projector/tabulation views.
- **Session Protection:** Administrative live-lock toggles to freeze or unfreeze judging portals on demand.

---

## Repository Directory Structure

```text
RRR_2024/
│
├── admin/                      # Administrative portal
│   ├── accounts/               # Judge & admin account management
│   ├── candidates/             # Contestant roster CRUD & photo uploads
│   ├── category/               # Category setup & percentage weightings
│   ├── criteria/               # Category criteria rubrics & points
│   ├── config/                 # Event parameters & session lock controls
│   ├── dashboard/              # Event summary dashboard
│   ├── results/                # Tabulation views (overall, top_rank, tab_pane, top_ranking)
│   └── index.php               # Administrative workspace shell
│
├── connection/
│   └── conn.php                # Central MySQLi database connection
│
├── css/
│   └── judge.css               # Design tokens, tactile sliders & judge UI styles
│
├── judge/                      # Adjudicator portal
│   ├── candidates/             # Contestant gallery & tactile scoring modal
│   ├── scores/                 # Adjudicator submitted scores review
│   ├── tabulate/               # Tabulation matrix inputs
│   └── index.php               # Judge workspace shell
│
├── lib/
│   ├── scorecalculator.php     # Standalone audit-grade calculation engine
│   ├── crud.js                 # Shared AJAX CRUD helpers
│   └── validate.js             # Client-side input validation helpers
│
├── login/                      # Authentication portal & session initializers
├── results/                    # Public projector & guest tabulation screens
├── uploads/                    # Contestant portrait images
├── rrr.sql                     # Seed database schema & initial configuration
└── README.md                   # System documentation
```

---

## Central Scoring Engine & Auditing Specification

All calculations across the platform are strictly isolated inside [`lib/scorecalculator.php`](lib/scorecalculator.php). If an event committee, auditor, or contestant questions how scores or rankings are computed, this single file serves as the official legal source of truth.

### Mathematical Formulation

#### 1. Criteria Average

For any criterion $k$ scored by $N$ active adjudicators for contestant $c$:
$$\text{Average}(c, k) = \frac{\sum_{j=1}^{N} \text{ScorePoints}(j, c, k)}{N}$$
*(Rounded to 2 decimal places)*

#### 2. Category Raw Score

The raw total for category $C$ is the sum of criteria averages within that category:
$$\text{RawScore}(c, C) = \sum_{k \in \text{Criteria}(C)} \text{Average}(c, k)$$

#### 3. Factored Category Score

The category's contribution to the grand total, weighted by its percentage weight $W_C$:
$$\text{FactoredScore}(c, C) = \left( \frac{W_C}{100} \right) \times \text{RawScore}(c, C)$$

#### 4. Candidate Grand Total

The sum of all factored regular category scores (excluding the display-only "Total Ranking" category):
$$\text{GrandTotal}(c) = \sum_{C \in \text{RegularCategories}} \text{FactoredScore}(c, C)$$

#### 5. Normalized Overall Percentage

When regular category percentages do not sum to 100%, or for percentage display:
$$\text{NormalizedScore}(c) = \left( \frac{\text{GrandTotal}(c)}{\sum_{C \in \text{RegularCategories}} W_C} \right) \times 100\%$$

#### 6. Ranking & Tie-Breaker

Candidates are sorted descending by `NormalizedScore`. Ties are broken using `GrandTotal` as secondary sort criteria:
$$\text{Rank}(c_1) < \text{Rank}(c_2) \iff \text{NormalizedScore}(c_1) > \text{NormalizedScore}(c_2)$$

---

### `ScoreCalculator` API Reference

All methods in [`lib/scorecalculator.php`](lib/scorecalculator.php) are `public static`:

| Method | Return Type | Description |
| :--- | :--- | :--- |
| `getCriteriaAverage($conn, $candId, $criteriaId)` | `float` | Returns the average score points across all judges for a specific criterion. |
| `getCategoryRawScore($conn, $candId, $categoryId)` | `float` | Sum of all criteria averages within a category for a candidate. |
| `getCategoryFactoredScore($conn, $candId, $categoryId, $pct = null)` | `float` | Raw score multiplied by `(percentage / 100)`. |
| `getActiveCategories($conn, $includeTotalRanking = true)` | `array` | Returns list of active categories ordered by `category_id ASC`. |
| `getTotalPercentageWeight($conn)` | `float` | Sum of percentage weights across all regular categories. |
| `getCandidateScoreBreakdown($conn, $candId)` | `array` | Complete breakdown dictionary: `regular_scores`, `grand_total`, `normalized_score`. |
| `getCandidateGrandTotal($conn, $candId)` | `float` | Grand total factored score for a candidate. |
| `getOverallRankings($conn, $filterGroup = null)` | `array` | Sorted candidate rankings with ranks assigned. Supports division filtering (`FEMALE`, `MALE`, `GROUP`). |
| `getCategoryRankings($conn, $categoryId, $filterGroup = null)` | `array` | Sorted category rankings based on category raw scores. |
| `determineDivision($candNo, $candCategory = null)` | `string` | Extracts division (`FEMALE`, `MALE`, `GROUP`) from `cand_no` suffix (`1F`, `1M`) or category column. |
| `normalizeGroupKey($candCategory, $candNo = '')` | `string` | Normalizes division keys for ranking tabs. |
| `format($score)` | `string` | Formats floats to standard 2-decimal strings (`0.00`). |
| `getJudgeCategoryTotal($conn, $judgeId, $candId, $categoryId)` | `float` | Total points submitted by a single judge for a candidate under a category. |
| `getJudgeCriteriaScore($conn, $judgeId, $candId, $criteriaId)` | `float` | Single atomic score submitted by a specific judge for a criterion. |

---

## Judge Scoring Experience & UI System

The adjudicator portal (`/judge/`) includes custom physical UI controls implemented via [`css/judge.css`](css/judge.css):

1. **Tactile Ruler Slider:**
   - Thick progress track with high-contrast active fill.
   - Laser-aligned tick marks (`5%`, `10%`, `20%` intervals) functioning like an architectural ruler.
   - Custom thumb with embedded vertical tactile grip lines (`|||`).
   - Floating live score indicator showing current points in real time.
2. **Real-Time Totalizer Badge (`#tot`):**
   - Automatically sums criterion values dynamically as sliders move.
   - High-contrast visual styling preventing text washout on bright backgrounds.
3. **Smooth Tab Transitions:**
   - Nav-pills feature instant yet fluid cross-fade transitions (`transition: opacity 0.2s ease-in-out`).
4. **Contestant Gallery:**
   - Clean, decluttered card hierarchy displaying contestant number, name, and division indicator.

---

## Installation & Setup Guide

### Prerequisites

* **Web Server:** [XAMPP](https://www.apachefriends.org/), WAMP, LAMP, or any Apache/Nginx stack.
* **PHP:** Version `>= 7.4` (fully tested up to PHP 8.2) with `mysqli` and `gd` extensions enabled.
* **Database:** MySQL `5.7+` or MariaDB `10.4+`.
* **Web Browser:** Modern browser (Chrome, Safari, Edge, Firefox) with JavaScript enabled.

---

### Step 1: Deploy Project Files

Clone or extract the repository into your local web server's document root:

* **XAMPP (Windows):** `C:\xampp\htdocs\RRR_2024`
* **XAMPP (macOS):** `/Applications/XAMPP/xamppfiles/htdocs/RRR_2024`
* **Linux (Apache):** `/var/www/html/RRR_2024`

```bash
git clone https://github.com/Masc20/RRR.git C:/xampp/htdocs/RRR_2024
```

---

### Step 2: Set Directory Permissions

Ensure the `uploads/` folder is writable by your web server:

```bash
# On Linux / macOS
chmod -R 775 uploads/
```

---

### Step 3: Import the Database

1. Open **phpMyAdmin** (`http://localhost/phpmyadmin`) or MySQL CLI.
2. Create a new database named **`rrr`**:

   ```sql
   CREATE DATABASE `rrr` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

3. Import the [`rrr.sql`](rrr.sql) seed file:

   ```bash
   mysql -u root -p rrr < C:/xampp/htdocs/RRR_2024/rrr.sql
   ```

---

### Step 4: Configure Database Connection

Verify your connection parameters in [`connection/conn.php`](connection/conn.php):

```php
<?php  
$servername = "localhost";
$username   = "root";
$password   = "";        // Default is empty on standard XAMPP installs
$dbname     = "rrr";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
```

---

### Step 5: Launch the Application

Start **Apache** and **MySQL** in your control panel, then navigate to:

```text
http://localhost/RRR_2024/
```

The application will automatically route to the Authentication Portal at `http://localhost/RRR_2024/login/`.

---

## Default User Accounts & Credentials

The seed database [`rrr.sql`](rrr.sql) includes pre-configured administrative and adjudicator accounts:

| Role | Username | Password | Default Portal | Access Permissions |
| :--- | :--- | :--- | :--- | :--- |
| **System Administrator** | `admin` | `admin123` | `/admin/` | Full system control, category setup, user CRUD, lock toggles, tabulation |
| **Committee Chairman** | `comm` | `c0mm` | `/judge/` | Executive judging, overview scoring privileges, score validation |
| **Judge #1** | `judge1` | `judge1` | `/judge/` | Contestant scoring, slider interface, individual score review |
| **Judge #2** | `judge2` | `judge2` | `/judge/` | Contestant scoring, slider interface, individual score review |
| **Judge #3** | `judge3` | `judge3` | `/judge/` | Contestant scoring, slider interface, individual score review |
| **Judge #4** | `judge4` | `judge4` | `/judge/` | Contestant scoring, slider interface, individual score review |

> **Security Best Practice:** Change all default passwords immediately after initial deployment via **Admin Console &rarr; User Accounts**.

---

## Database Schema Reference

| Table | Description | Key Columns |
| :--- | :--- | :--- |
| **`tbl_config`** | Global event parameters and session locking states | `config_id`, `event_title`, `based_type` (`Candidate`/`House`), `judge_status` (`Open`/`Close`) |
| **`tbl_users`** | User credentials, roles, and account status | `user_id`, `full_name`, `user_name`, `pass_word`, `user_type` (`Admin`/`Chairman`/`Judge`), `status` |
| **`tbl_candidates`**| Contestants/Entries, division numbering, photos | `cand_id`, `cand_no` (`1F`, `1M`, etc.), `cand_name`, `cand_pic`, `status` (`Allow`/`Eliminate`) |
| **`tbl_category`**  | Competition rounds/categories and percentage weight | `category_id`, `category_name`, `percentage`, `status` (`Show`/`Hide`) |
| **`tbl_criteria`**  | Specific scoring rubrics under each category | `criteria_id`, `criteria_name`, `criteria_points`, `category_id`, `status` (`Show`/`Hide`) |
| **`tbl_scores`**    | Atomic scores submitted by judges | `score_id`, `category_id`, `criteria_id`, `user_id`, `cand_id`, `score_points`, `date_saved` |
| **`online_judges`** | Active session tracking for connected adjudicators | `session_id`, `user_id`, `time` |

---

## Architecture & Coding Conventions

1. **`__DIR__` Absolute Path Inclusions:**
   - All server-side file inclusions use `__DIR__` (e.g. `require_once __DIR__ . '/../../lib/scorecalculator.php'`).
   - Prevents include path breakage regardless of whether a view is called via root script, AJAX request, or CLI runner.
2. **Database Connection Lifecycle:**
   - Sub-views and partial templates **never** call `$conn->close()`.
   - The database connection remains alive across composite page loads (such as multi-tab results views).
3. **Division Determination:**
   - Divisions (`FEMALE`, `MALE`, `GROUP`) are determined dynamically by candidate number suffix (`1F`, `1M`) or the optional `cand_category` column via [`ScoreCalculator::determineDivision()`](lib/scorecalculator.php).

---

## Troubleshooting & Frequently Asked Questions

### 1. Database Connection Error ("Connection failed")

* Verify MySQL is running in your XAMPP Control Panel.
* Confirm that the database name in [`connection/conn.php`](connection/conn.php) matches `rrr`.
* If you set a root password in MySQL, ensure it is entered into the `$password` parameter in `conn.php`.

### 2. "Judging is now closed. Thank you." Prompt Appears

* This is the session lock feature configured in `tbl_config`.
* Log in as **Admin** (`/admin/`), navigate to **Event Configuration**, and set **Judge Status** to **Open**.

### 3. Contestant Photos Fail to Upload

* Check write permissions on the `uploads/` folder (`chmod -R 775 uploads/`).
* Ensure PHP `upload_max_filesize` and `post_max_size` in `php.ini` are set to at least `8M`.

### 4. How Do Alphanumeric Candidate Numbers Work (`1F`, `1M`)?

* In `tbl_candidates`, `cand_no` is stored as `VARCHAR(11)`.
* Numbers ending with `F` are categorized into the **Female Division**, numbers ending with `M` into the **Male Division**, and numbers ending with `G` into the **Group Division**. All sorting and ranking algorithms handle this automatically via [`ScoreCalculator`](lib/scorecalculator.php).
