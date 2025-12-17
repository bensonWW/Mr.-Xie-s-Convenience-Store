# Product Requirements Document (PRD)

## Project Overview
The **Online Shopping System (OSS)** is a web-based e-commerce platform designed to facilitate browsing, ordering, and store management.

## Core Objectives
1.  **Robust E-commerce Platform**: Support Customer, Staff, and Admin roles.
2.  **Online Top-up System**: **[New]** Enable users to top-up internal credits (Mock Payment first) and use them for purchases.
3.  **Production Ready**: Dockerized environment and stable Laravel backend.

## User Roles
-   **Guest**: Browse products.
-   **Customer (Member)**:
    -   Register/Login.
    -   Manage Profile (Address, Phone).
    -   **[New] Manage Wallet (Top-up, View Balance)**.
    -   Browse Products, Add to Cart, Place Orders (use Wallet or potentially others).
    -   View Order History.
-   **Staff**: Manage store products (CRUD) and inventory.
-   **Admin**: System configuration, user management, special events, **Order Refunds**.

## Key Features

### 1. User Account & Wallet
-   **Registration/Login**: Standard email/password flow (Sanctum).
-   **Wallet System**:
    -   Users have a `wallet_balance`.
    -   Users can "Top-up" (Mock: Enter amount -> Admin approves or Auto-approve for dev).
    -   Users can view transaction history (Deposit/Spend/Refund).
    -   **Payment**: Mandatory "Wallet Balance" payment. (No other methods initially).
    -   **Member Levels**:
        -   Automatic upgrade based on Total Spent.
        -   Levels (Normal, VIP, Platinum) provide percentage discounts.

### 2. Product Management
-   Standard CRUD (Name, Price, Image, Stock, Category).
-   Inventory management (deduct stock on order).

### 3. Shopping & Checkout
-   Cart management.
-   Order Placement:
    -   Validate Stock.
    -   Validate Balance versus (Subtotal - Discount + Shipping).
    -   **Low Balance Flow**: Prompt user to Top-up via Inline Modal.
    -   Deduct Stock & Balance (Atomic Transaction).
    -   Snapshot Address/Phone.
    -   Create Order Record (Status: Processing -> Shipped -> Delivered -> Completed).
    -   Support "Full Refund" -> Auto-credit Wallet.

### 4. Technical Constraints
-   **Backend**: Laravel 12.x (Latest).
-   **Frontend**: Vue 3 (`xie_vue`).
-   **API**: RESTful JSON API.
-   **Containerization**: Docker (fully functional).

## Future Scope (Current Out of Scope)
-   `xie_nuxt` implementation.
-   Real Payment Gateway Integration (Stripe/PayPal) - *Wait for Mock verification*.
