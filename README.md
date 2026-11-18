# EcommerceDB Admin Website (PHP + MySQL)

A complete, working admin website built in plain PHP (PDO + MySQL) for the
`EcommerceDB` schema. It has been tested end-to-end against a real MySQL/MariaDB
server and PHP's built-in web server.

## Features

- **Dashboard** — customer/product/order counts, revenue, orders-by-status, recent orders, low-stock alerts
- **Customers** — search, add, edit, delete (cascades to their orders/items/payments)
- **Products** — search, filter by category / low stock, add, edit, delete
- **Categories** — add, edit, delete (blocked if products are still assigned)
- **Suppliers** — search, add, edit, delete (blocked if products are still linked)
- **Orders** — search/filter by status, detail view with line items, status update, payment status update, delete
- **Payments** — filter by payment status, linked back to orders
- **Inventory** — filter by warehouse, low-stock highlighting
- **Reports** — total revenue, revenue by month, top 5 customers, top-selling products, orders-by-status breakdown (adapted from `03_Reports.sql`)

All input is handled with prepared statements (PDO, `ATTR_EMULATE_PREPARES` off), and
all output is escaped with `htmlspecialchars()` to prevent SQL injection and XSS.

> **Note on totals:** in the original seed data, `Orders.TotalAmount` is left at its
> `0.00` default (the `INSERT INTO Orders` statements never set it). This website
> instead computes order totals live from `OrderItems` (`Quantity * UnitPrice`),
> which matches the `Payments.Amount` values in the seed data. If you build new
> checkout logic on top of this, keep populating `OrderItems` and the totals will
> stay accurate automatically.

## Requirements

- PHP 8.0+ with the `pdo_mysql` extension
- MySQL 5.7+ / MariaDB 10.3+
- Any web server (Apache, Nginx) or just PHP's built-in server for local testing

## Setup (XAMPP / WAMP / MAMP)

1. Copy the entire `ecommerce-website` folder into your server's web root
   (e.g. `htdocs/ecommerce-website` for XAMPP).
2. Start Apache and MySQL from your control panel.
3. Open **phpMyAdmin**, create nothing manually — just import
   `database/01_Schema.sql` (it creates the `EcommerceDB` database itself,
   including the sample data). You can also run `database/04_Indexing.sql`
   afterwards to add the recommended indexes.
4. Open `config.php` and confirm the credentials match your setup. XAMPP's
   defaults (`root` / empty password) are already filled in.
5. Visit `http://localhost/ecommerce-website/` in your browser.

## Setup (command line / any Linux/Mac with PHP + MySQL installed)

```bash
# 1. Load the schema (creates the EcommerceDB database + sample data)
mysql -u root -p < database/01_Schema.sql

# 2. (Optional) add the recommended indexes
mysql -u root -p < database/04_Indexing.sql

# 3. Edit config.php with your MySQL username/password if not using
#    the root/no-password default

# 4. Run PHP's built-in server from inside the project folder
php -S localhost:8000

# 5. Open http://localhost:8000 in your browser
```

## Folder Structure

```
ecommerce-website/
├── config.php              # Database connection + helper functions
├── index.php                # Dashboard
├── customers.php             customer_form.php    customer_delete.php
├── products.php              product_form.php     product_delete.php
├── categories.php            category_delete.php
├── suppliers.php             supplier_form.php    supplier_delete.php
├── orders.php                order_view.php       order_delete.php
├── payments.php
├── inventory.php
├── reports.php
├── includes/
│   ├── header.php           # Shared nav bar + flash messages
│   └── footer.php
├── assets/
│   └── style.css            # Custom styling (on top of Bootstrap 5 via CDN)
└── database/
    ├── 01_Schema.sql         # Original schema + seed data you provided
    ├── 02_Quires.sql
    ├── 03_Reports.sql        # Report queries this site's Reports page is based on
    └── 04_Indexing.sql       # Recommended indexes
```

## Security Notes

- All queries use PDO prepared statements — no raw string concatenation of user input into SQL.
- All dynamic output goes through an `h()` helper that calls `htmlspecialchars()`.
- Deletes that would break referential integrity (e.g. deleting a category still
  used by products) are blocked with a friendly error message instead of failing
  with a raw SQL error.
- This is an **admin-style back-office tool** with no login/authentication layer.
  If you deploy it anywhere reachable outside your own machine, put it behind
  HTTP auth, a VPN, or add a proper login system before doing so.
