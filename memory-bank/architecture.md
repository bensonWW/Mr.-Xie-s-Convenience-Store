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
-   **Admin Management**: Admins can force deposit/withdrawal via `AdminController`, logging special transaction types (`ADMIN_DEPOSIT`, `ADMIN_WITHDRAW`).

### 3. API Response Standard
-   Standardized JSON structure:
    ```json
    {
        "success": true,
        "data": { ... },
        "message": "Operation successful"
    }
    ```

## Database Schema Updates (Proposed)
-   `users`: Add `decimal('balance', 10, 2) default(0.00)`
-   `wallet_transactions`: New table.

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
