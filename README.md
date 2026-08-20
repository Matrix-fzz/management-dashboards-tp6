# Management Dashboards — TP6

Three management dashboard sub-projects demonstrating full-stack development with Node.js/Express, PHP, and MySQL for agricultural accounting and housing management.

## Sub-Projects

### 1. Accounting Dashboard — Node.js (`gestion-comptable/`)

A full-stack SPA with Express.js backend and vanilla JS frontend for managing agencies, accountants, and agricultural operations.

**Tech Stack:** Node.js, Express.js 5.1.0, MySQL (mysql2), Chart.js, Font Awesome

### 2. Accounting Dashboard — PHP (`gestioncompt/`)

A PHP-based management dashboard with RESTful API, CRUD operations, and Chart.js data visualizations.

**Tech Stack:** PHP, MySQL (PDO), Chart.js, Font Awesome

### 3. Housing Management System (`housing/`)

A PHP application for managing housing units, types, neighborhoods, communes, and tenants with server-side rendered pages.

**Tech Stack:** PHP, MySQL (PDO)

## Technologies

| Technology | Usage |
|------------|-------|
| Node.js + Express.js | Backend API (gestion-comptable) |
| PHP | Backend API and rendering (gestioncompt, housing) |
| MySQL | Database for all three sub-projects |
| HTML5 | Frontend structure |
| CSS3 | Dashboard layouts with sidebar navigation |
| JavaScript (ES6+) | SPA logic, API calls, Chart.js integration |
| Chart.js (CDN) | Bar charts, pie/doughnut charts |
| Font Awesome 6.4.0 | Icons (CDN) |

## Database Names

| Sub-Project | Database |
|-------------|----------|
| gestion-comptable | `accounting_db` |
| gestioncompt | `accounting_management` |
| housing | `housing_management` |

## Setup

### gestion-comptable (Node.js)
```bash
cd gestion-comptable
npm install
node server.js
# Server runs on http://localhost:3000
```

### gestioncompt (PHP)
1. Create MySQL database `accounting_management`
2. Update credentials in `api.php`
3. Place in web server document root
4. Open `index.php` in browser

### housing (PHP)
1. Create MySQL database `housing_management`
2. Update credentials in `config.php`
3. Place in web server document root
4. Open `index.php` in browser

## Reference

See `REDME.md` for SQL queries and view definitions used in the accounting dashboard.
