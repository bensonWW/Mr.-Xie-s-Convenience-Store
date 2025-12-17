# Architecture & Design

## System Architecture
**Monolithic API with SPA Frontend**
-   **Backend**: Laravel serves as a headless API provider.
-   **Frontend**: Vue.js SPA consumes APIs.
-   **Communication**: RESTful JSON API.

## Key Design Patterns (Backend)

### 1. Service Layer
-   **Problem**: Fat Controllers containing too much business logic.
-   **Solution**: Encapsulate business logic in `Services` (e.g., `WalletService`, `OrderService`, `ImageUploadService`).
    -   *Controllers* should calculate input/output.
    -   *Services* handle the actual transactional logic.

### 2. Wallet & Transactions
-   **Design**: Double-entry bookkeeping style (simplified).
-   **Table `users`**: Contains `wallet_balance` (denormalized for speed) or calculated from transactions.
    -   *Decision*: Add `balance` column to `users` for read performance, but **must** strict update via transactions.
-   **Table `wallet_transactions`**:
    -   `user_id`
    -   `type` (deposit, withdrawal, payment, refund)
    -   `amount`
    -   `reference_id` (e.g., order_id)
    -   `description`
-   **Concurrency**: Use Database Transactions (`DB::transaction`) and `lockForUpdate()` when modifying balance.
-   **Admin Management**: Admins can force deposit/withdrawal via `AdminController`.
-   **Refunds (Automated)**:
    -   Triggering a "Full Refund" MUST wrap the following in a transaction:
        1.  Change Order Status to `refunded` (or `cancelled`).
        2.  Credit `user.wallet_balance`.
        3.  Create `wallet_transactions` record (Type: `REFUND`, Reference: `order_id`).

### 3. Order Processing & Snapshots
-   **Status State Machine**:
    -   `Processing` (Paid): Default state upon successful Wallet deduction.
    -   `Shipped`: Admin marks as shipped.
    -   `Delivered`: Item arrives.
    -   `Completed`: Final successful state.
    -   `Refunded`/`Cancelled`: Exceptional terminal state.
-   **Data Integrity (Snapshots)**:
    -   **Problem**: User updates address/phone after order, breaking historical record.
    -   **Solution**: `orders` table adds `snapshot_data` (JSON).
    -   **Content**: Stores `{ "shipping_address": "...", "contact_phone": "...", "member_level_at_purchase": "..." }` at creation time.
    -   Admin views utilize this snapshot, falling back to live data only if snapshot is null (legacy).

### 4. Member System (Levels & Discounts)
-   **Strategy**: Configuration-based (Code First).
-   **Definition**: Levels and Rules defined in `config/shop.php` (e.g., Thresholds, Discount %).
-   **Automation**: System checks `User::total_spent` on `OrderCompleted` event to auto-upgrade.
-   **Calculation**: Discounts apply to **Subtotal**.
    -   `Total = (Subtotal - LevelDiscount) + Shipping`.
    -   Displayed as a distinct negative Line Item.

### 5. API Response Standard
-   Standardized JSON structure:
    ```json
    {
        "success": true,
        "data": { ... },
        "message": "Operation successful"
    }
    ```

## Database Schema Updates (Proposed)
-   `users`: Add `decimal('balance', 10, 2) default(0.00)` **[Implemented]**
-   `wallet_transactions`: New table. **[Implemented]**
-   `orders`: Add `json('snapshot_data')->nullable()` (For address/phone history). **[Implemented]**
-   `favorites`: New table (Wishlist). **[Implemented]**
-   `users`: Add `member_level` column. **[Implemented]**
-   `orders`: Add `discount_amount` column. **[Implemented]**

### 4. Admin Panel Strategy
-   **User Management**:
    -   `AdminUsers.vue`: List users with a new column for "Wallet Balance".
    -   `AdminUserEdit.vue`: Detailed view including a dedicated "Wallet History" tab and "Manage Wallet" (Top-up/Deduct) modal.
    -   *Security*: Only Admins (role `admin`) can access these routes and invoke wallet modification APIs.

### 5. Frontend Strategy & Constraints
-   **Auth State**:
    -   Continue using `LocalStorage` for token/role persistence (Simpler fix than Pinia migration).
    -   **Dev Bypass**: Use `VITE_BYPASS_AUTH_DEV=true` strictly for development UI verification.
-   **Styling**:
    -   **Framework**: Tailwind CSS (Allowed exemption).
    -   **Tokens**: Primary `xieOrange` (#ed8936), Secondary `xieBlue` (#2d3748).
    -   **Animation**: Pure CSS Transitions (No external libraries like animate.css).
-   **Component Structure (Implemented)**:
    -   **Atomic Design**: Monolithic pages (`AdminUserEdit.vue`) refactored into:
        -   `WalletBalanceCard`: Displays current balance and "Manage" trigger.
        -   `WalletTransactionTable`: Lists history with type-based styling (Deposit=Green, Withdraw=Red).
        -   `WalletActionModal`: Handles manual fund adjustments (Validates amounts and reasons).
        -   `WalletActionModal`: Handles manual fund adjustments (Validates amounts and reasons).
        -   `UserTopUpModal`: **[New]** Shared component for Inline Top-up (used in Profile and Checkout).
        -   `InlineAddressModal`: **[New]** Checkout component for setting address without leaving the page.

### 6. Order & Payment Flow (Refined)
-   **Atomic Payment**:
    -   Backend (`OrderController::store`): Wraps "Stock Deduction", "Order Creation", and "Wallet Withdraw" in a single DB Transaction.
    -   Policy: **Immediate Payment Required**. Orders are created as `processing` (Paid). If balance < total, creation fails (`402`).
-   **Checkout UX**: 
    -   Real-time check of `UserBalance` vs `CartTotal`.
    -   If Insufficient: "Check Out" button replaced by "Top Up" (triggers `UserTopUpModal`).
    -   User stays on page, tops up, and immediately proceeds to checkout.
