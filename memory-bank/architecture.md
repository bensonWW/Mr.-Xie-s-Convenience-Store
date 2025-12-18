# Architecture & Design

## System Architecture
**Monolithic API with SPA Frontend**
-   **Backend**: Laravel 12.x serves as a headless API provider.
-   **Frontend**: Vue.js 3 SPA (`xie_vue`) using **Vuex** for state management and **Tailwind CSS** for styling.
-   **Communication**: RESTful JSON API.

## Key Design Patterns (Backend)

### 1. Service Layer
-   **Problem**: Fat Controllers containing too much business logic.
-   **Solution**: Encapsulate business logic in `Services`.
    -   `WalletService`: Handle strict transaction logging (`topup`, `payment`, `refund`).
    -   `OrderService`: Handle stock deduction, snapshotting, and status transitions.
    -   `MemberLevelService`: Handle upgrades and `is_level_locked` checks.
    -   **Controllers** should only handle Request validation and Response formatting.

### 2. Data Integrity & Normalization (3NF)
-   **Core Tables (Users, Products, Orders)**: Must strictly follow 3NF.
-   **Soft Deletes**: Use `deleted_at` everywhere to preserve history. **[Enforced via Audit]**
-   **Transactions**:
    -   **Isolation**: `Repeatable Read`.
    -   **Locking**: `SELECT ... FOR UPDATE` when checking Stock and Wallet Balance to prevent race conditions.
    -   **Indexing**: Critical columns (`category`, `status`, `created_at`) indexed for performance. **[Implemented]**
-   **Settings Table**:
    -   `key` (string, unique), `value` (text/json), `description`.
    -   Stores `free_shipping_threshold`, `member_level_rules`, etc.

### 3. Wallet & Transactions
-   **Table `wallet_transactions`**:
    -   `user_id`
    -   `type`: ENUM (`topup`, `payment`, `refund`)
    -   `amount`: Decimal. Positive for Top-up, Negative for Payment/Refund debit.
    -   `reference_table`: String (e.g., 'orders')
    -   `reference_id`: Integer
-   **Balance Calculation**:
    -   `users.balance` is a cache column.
    -   Validation: `User balance` MUST equal `SUM(transactions.amount)`.
    -   **Refunds**: Must be atomic. Order Cancelled -> Wallet Credited -> Transaction Logged.

### 4. Address & Logistics
-   **Address Structure**:
    -   Backend: `addresses` table or JSON column? -> **Decision**: `addresses` table for normalization.
    -   Columns: `user_id`, `city`, `district`, `zip_code`, `detail_address`, `recipient_name`, `phone`.
-   **Frontend Data**: JSON file for Taiwan City/District/Zip mapping.
-   **Logistics Number**:
    -   Format: `LOGI-{YYYYMMDD}-{Sequence}`.
    -   Generated via `LogisticsService` ensuring uniqueness.

### 5. Frontend Strategy
-   **State Management**: **Vuex**.
    -   `cart` module: Syncs Badge count (Debounced).
    -   `auth` module: Handles Token and User Profile.
-   **Routing**:
    -   **Auth Guard**: Strictly checks Token validity.
    -   **Dev Mode**: `VITE_BYPASS_AUTH_DEV` REMOVED. **[Implemented]**
-   **Components**:
    -   **Atomic Design**: Use Tailwind CSS utility classes.
    -   **Toast**: Global notification for actions (e.g., "Top-up Successful").

## Database Schema Updates (Proposed)
-   `users`: Add `is_level_locked` (boolean default false). **[Implemented]**
-   `settings`: New table for global configs. **[Implemented]**
-   `orders`:
    -   `logistics_number` (string, unique, nullable). **[Implemented]**
    -   `payment_method` (enum: 'wallet'). **[Implemented]**
-   `addresses`: Refactor to `city`, `district`, `zip_code`. **[Implemented]**
-   `products`: Add `original_price` (decimal). **[Implemented]**

## Order & Payment Flow
-   **Checkout**:
    -   FE: Check `UserBalance` vs `CartTotal`.
    -   If Insufficient: Show Inline Top-up Modal.
    -   **Payment**: Backend validates Balance > Total (Atomic Lock).
    -   **Add-ons**: "Go Add Items" links to Product List.
-   **Revenue Stats**:
    -   Logic: `SUM(amount * -1)` where `type = 'payment'` and `created_at` in range. **[Implemented]**
    -   **Free Shipping**:
    -   Calculated at Order Creation.
    -   Threshold loaded from `settings` table (`Setting::get('free_shipping_threshold')`).
    -   Fee stored in `orders.shipping_fee`.

## Testing Strategy
- **Feature Tests**: Primary verification method.
    - `WalletE2ETest`: Dedicated Wallet flow.
    - `SystemSmokeTest`: End-to-end critical path (Register -> Topup -> Shop -> Admin Ship -> User Verify). Covers both User and Admin journeys.
- **Security Check**: `AdminAuthTest` ensures route protection.
- **Schema Audit**: `SchemaAuditTest` verifies indexes and soft deletes.
