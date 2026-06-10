# LuxuryStore - Frontend & Backend Project

## Project Overview

Complete, modern e-commerce platform built with HTML, CSS, vanilla JavaScript, and a robust PHP/MySQL backend.

## Architecture

- **Structure**: Clean folder-based organization
- **Components**: Reusable navbar and footer (in `/components`)
- **Assets**: `/assets/css` (styles), `/assets/js` (app logic)
- **Backend**: `/config` (db.php), `/includes` (auth.php, header/footer)
- **API Layer**: `/api` (RESTful PHP endpoints for AJAX operations)
- **Panels**: 
    - `/user`: Personalized customer dashboard and history
    - `/admin`: Isolated, high-security management interface

## What Was Fixed/Upgraded

- ✅ Project folder structure fully organized
- ✅ Reusable component architecture (navbar/footer)
- ✅ Database-backed cart system (API driven)
- ✅ Order management with Paystack integration
- ✅ Dynamic shipping capture (Country/State/City/Phone APIs)
- ✅ Full Admin Suite with isolated UI (strictly separated from customer dashboard)
- ✅ Automatic Admin redirection: Admins are kept within the Management Suite
- ✅ Premium Admin UI: Modern dark sidebar, high-contrast statistics cards, and professional typography
- ✅ Admin Product CRUD (Add, Edit, Delete with DB sync)
- ✅ Admin User & Order management systems
- ✅ Real-time database synchronization for all admin actions

## Working Features

### **Customer Experience**
- **Shopping**: Product browsing, detailed views, related products (all DB-driven)
- **Cart**: Persistent database-backed cart (synced across devices)
- **Checkout**: Paystack integration with dynamic shipping information capture
- **Account**: Dashboard with order stats, full history, and profile management (Strictly for customers)

### **Admin Control (Strictly Isolated)**
- **Interface**: High-end management dashboard with zero customer-facing clutter
- **Dashboard**: Real-time stats (Total Orders, Revenue, Customers, Products)
- **Inventory**: Full CRUD management (Add/Edit/Delete products) with instant DB sync
- **Orders**: View detailed order info and update statuses (Paid, Shipped, Delivered)
- **Users**: Complete list of registered users and their details
- **Security**: Strict `requireAdmin()` middleware and automatic redirection from customer account pages


## Database Structure

- **users**: id, name, email, password, role (user/admin), created_at
- **categories**: id, name, slug
- **products**: id, name, description, price, image, category_id, stock, created_at
- **cart**: id, user_id, product_id, quantity
- **orders**: id, user_id, total_price, status, first_name, last_name, email, phone, address, country, state, city, zip_code, shipping_cost, created_at
- **order_items**: id, order_id, product_id, quantity, price

## File Structure

```
/
├── admin/                     # Admin strictly isolated pages
│   ├── index.php              # Admin Stats Dashboard
│   ├── orders.php             # Order Management
│   ├── products.php           # Product CRUD Interface
│   └── users.php              # User Management
├── api/                       # API Layer
│   ├── cart-*.php             # Cart operations
│   ├── order-create.php       # Checkout logic
│   ├── product-crud.php       # Admin product logic
│   ├── update-order-status.php
│   └── verify-payment.php     # Paystack verification
├── user/                      # Customer pages
│   ├── dashboard.php
│   ├── orders.php
│   └── profile.php
├── config/                    # DB Config
├── includes/                  # PHP Core
│   ├── auth.php               # Auth Middleware
│   ├── admin-header.php       # Admin UI Shell
│   ├── admin-footer.php       # Admin Scripts
│   ├── header.php             # Customer UI Shell
│   └── footer.php             # Customer Scripts
├── assets/                    # Static Assets
├── components/                # JS UI Components
└── index.php                  # Store Front
```

## Current System State

- **Frontend**: 100% Production-ready demo state
- **Backend**: Fully functional PHP/MySQL architecture
- **Admin**: Complete management suite implemented and secured
- **Payments**: Paystack integrated and tested

## Next Planned Phase

- **Coupons**: Implementation of discount code system
- **Advanced Admin**: Sales reporting and analytics charts
- **Email Notifications**: Automated order confirmation emails
