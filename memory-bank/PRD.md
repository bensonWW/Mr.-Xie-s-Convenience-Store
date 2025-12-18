# Product Requirements Document (PRD)

## Project Overview
The **Online Shopping System (OSS)** is a web-based e-commerce platform designed to facilitate browsing, ordering, and store management.

## Core Objectives
1.  **Robust E-commerce Platform**: Support Customer, Staff, and Admin roles.
2.  **Online Top-up System**: Enable users to top-up internal credits (Mock Payment first) and use them for purchases.
3.  **Production Ready**: Dockerized environment and stable Laravel backend.
4.  **Data Integrity**: Strict 3rd Normal Form (3NF) relational database design.

## User Roles
-   **Guest**: Browse products. (Cannot view Cart count; "Add to Cart" redirects to Login).
-   **Customer (Member)**:
    -   Register/Login.
    -   Manage Profile (Address with City/District selector, Phone).
    -   **Manage Wallet** (Top-up, View Balance, View Consumption History).
    -   Browse Products, Add to Cart (Real-time Badge Sync), Place Orders.
    -   View Order History (Server-side status filtering).
-   **Staff**: Manage store products (CRUD) and inventory.
-   **Admin**:
    -   System configuration (`settings` table).
    -   User management (Lock Member Level).
    -   Order Refunds (Full Refund -> Auto-credit).
    -   **Dashboard**: View Revenue (Last 7 Days Sales) and Wallet Stats.

## Key Features

### 1. User Account & Wallet
-   **Registration/Login**: Standard email/password flow (Sanctum).
    -   **Logout**: Must force clear LocalStorage.
-   **Wallet System**:
    -   **Transactions**: Strictly typed: `topup` (Deposit), `payment` (Spend), `refund` (Return).
    -   **Balance**: Calculated via double-entry or strict transaction log aggregation.
    -   **User Experience**: Toast notification on successful Top-up.
-   **Member Levels**:
    -   Automatic upgrade based on **Total Spent** (`abs(sum(payment))`).
    -   **Manual Override**: Admins can manually set level.
        -   **Lock Mechanism**: `is_level_locked` flag prevents auto-downgrade/upgrade by system scheduler.

### 2. Product Management
-   Standard CRUD (Name, Price, Image, Stock, Category).
-   **Search**: SQL-based `LIKE` query (indexed). Meilisearch removed to reduce complexity.
-   **Inventory**: **Anti-Oversell** protection using Database Transactions (`Repeatable Read`) + `SELECT ... FOR UPDATE`.

### 3. Shopping & Checkout
-   **Cart**:
    -   **Guest**: Cart count hidden.
    -   **Sync**: Debounce (500ms) on quantity change before API update.
-   **Price & Discounts**:
    -   Display: Original Price vs Sale Price.
    -   Discount %: Auto-calculated (e.g., 85折 = 85%), Floor rounding.
-   **Shipping**:
    -   **Threshold**: Free shipping amount defined in `settings` table (e.g., 1000).
    -   **Add-on**: "Go Add Items" link redirects to "All Products" page.
    -   **Logistics**: Auto-generate unique number `LOGI-{YYYYMMDD}-{Sequence}` on order creation.
-   **Payment Method**:
    -   **Hardcoded**: "Internal Wallet" only. (DB supports Enum for future, Frontend hides others).
-   **Order Placement**:
    -   Validate Stock (Lock Rows).
    -   Snapshot Address/Phone/Level.
    -   Atomic deduction of Stock & Wallet.

### 4. Admin Panel
-   **Authentication**: Remove `VITE_BYPASS_AUTH_DEV`. Implement proper Admin Login.
-   **Order Management**:
    -   **List View**: Read-only Status. (Remove inline edit).
    -   **Detail View**: Status change requires **Confirm Modal**.
-   **Dashboard**:
    -   **Revenue Chart**: "Last 7 Days" Sales (Transaction type `payment`).
    -   **Stats**: Real-time total consumption, order count.

### 5. Technical Constraints
-   **Backend**: Laravel 12.x.
-   **Frontend**: Vue 3 (`xie_vue`) + **Vuex** + **Tailwind CSS**.
-   **Database**: MySQL 8.0.
    -   **Normalization**: Strict 3NF for Core Tables (Users, Products, Orders).
    -   **Integrity**: Soft Deletes (`deleted_at`), Foreign Keys with `ON DELETE` constraints (prefer Soft Delete).
-   **API**: RESTful JSON API.
-   **Containerization**: Docker (fully functional).
-   **Testing**: Feature Tests for Financial logic (Wallet, Revenue), Migration Tests for Schema.

## Future Scope (Current Out of Scope)
-   `xie_nuxt` implementation.
-   Real Payment Gateway Integration.
