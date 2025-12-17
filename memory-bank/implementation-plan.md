# Implementation Plan - Online Shopping System (Reform)

## User Review Required
> [!IMPORTANT]
> - **Laravel Version**: Request to use `^12.0` (Latest).
> - **Top-up System**: Implementing an **internal credit/wallet system** first (Mock Payment).
> - **Frontend**: Focus exclusively on `xie_vue`. `xie_nuxt` is out of scope.

## Proposed Changes

### Phase 1: Foundation & Clean-up
- [x] **Memory Bank Initialization**
    - [x] Create `PRD.md`, `tech-stack.md`, `implementation-plan.md`, `architecture.md`, `progress.md`.
- [x] **Environment Stabilization**
    - [x] Update `composer.json` to use Laravel `^12.0`.
    - [x] Update `Dockerfile` and `docker-compose.yml` for production-readiness.
    - [x] Clean re-install of dependencies (`vendor`, `node_modules`).
    - [x] Verify application boot.

### Phase 2: Core Backend - Internal Credit System
- [x] **Database Schema**
    - [x] Create migration for `wallet_balance` in `users` table (or separate `wallets` table).
    - [x] Create migration for `wallet_transactions` table (audit log).
- [x] **Backend Logic**
    - [x] Implement `WalletService` (deposit, withdraw, check balance).
    - [x] Create `WalletController` (API endpoints).
    - [x] Write Feature Tests (`WalletTest.php`).

### Phase 3: Backend - API Verification & Completeness
- [x] **Gap Analysis**
    - [x] Match `xie_vue` API calls against `routes/api.php`.
    - [x] Identify missing endpoints for "Product Management", "Orders", "Profile".
- [x] **Fix & Test**
    - [x] Implement missing Controllers/Methods.
    - [x] Write tests for critical flows (Checkout with Credits).

### Phase 4: Frontend - Integration & Verification (`xie_vue`)
- [x] **Top-up UI**
    - [x] Add "My Wallet" section in User Profile.
    - [x] Implement "Top-up" modal/page (Mock flow).
- [x] **Payment Integration**
    - [x] Update Checkout flow to allow payment via "Internal Balance".
- [x] **Manual Verification**
    - [x] Record walkthroughs for: Register -> Top-up -> Buy -> Deduct Balance -> Order History.

### Phase 5: Admin Panel Updates (Wallet Management)
- [x] **Backend: Wallet Administration**
    - [x] Create `POST /api/admin/users/{id}/wallet/transaction` (Admin Deposit/Withdraw).
    - [x] Update `AdminController@users` to ensure balance visibility.
    - [x] Update `AdminController@showUser` to include `walletTransactions`.
- [x] **Frontend: User List**
    - [x] Update `AdminUsers.vue` to show "Wallet Balance" column.
- [x] **Frontend: User Details & Actions**
    - [x] Update `AdminUserEdit.vue` to display balance.
    - [x] Implement "Admin Top-up/Deduct" modal in `AdminUserEdit.vue`.
    - [x] Display transaction history in `AdminUserEdit.vue`.

### Phase 6: Admin Auth Fix & Wallet UI Polish
- [x] **Step 1: Debug Admin Authentication**
    - [x] Create `.env` variable `VITE_BYPASS_AUTH_DEV` for temporary access.
    - [x] Debug `router/index.js` guard logic via code update.
    - [x] Update `UserSeeder` with diverse transaction data (Deposit/Withdraw types).
    - [x] **Rebuild Frontend Container**: Run `docker compose up -d --build frontend` to apply Auth/Env changes.
- [x] **Step 2: Admin UI Components Refactor (Tailwind)**
    - [x] Extract "Balance Card" to `components/admin/WalletBalanceCard.vue`.
    - [x] Extract "Transaction List" to `components/admin/WalletTransactionTable.vue`.
    - [x] Extract "Action Modal" to `components/admin/WalletActionModal.vue`.
    - [x] Apply CSS Transitions for hover/modal effects.
- [x] **Step 3: Customer UI (WalletView) Consistency**
    - [x] Apply same design tokens (`xieOrange`, `xieBlue`) to Member Center Wallet View.

### Phase 7: Final Polish & Fixes
- [x] **Step 1: Debug Admin Auth Redirection (P0)**
    - [x] Investigate `router/index.js` guard loop or failure.
    - [x] Create a browser verification test.
    - [x] Fix logic to allow Admin Login to correctly redirect.
- [x] **Step 2: Wallet UI Polish (P1)**
    - [x] Visual verification of new Admin Wallet components.
    - [x] Enhance "Wow" factor (Animations/Gradients).

### Phase 8: Bug Fixes & Refinements (Current Focus)
> [!NOTE]
> **General Rule**: All new logic in this phase requires **Strict PHPUnit Feature Tests**.

- [x] **Step 1: Fix Wishlist Refresh Issue (P1)**
    - [x] **Issue**: Items removed/added to wishlist reappear or persist after page refresh.
    - [x] **Task**: Verify `WishlistGrid.vue` reactivity and API state management (Local vs Server sync).
- [x] **Step 2: Simplify Admin Product Creation (P2)**
    - [x] **Issue**: Product creation validation is too strict (`Store ID required`) and UI has unnecessary fields.
    - [x] **Task**:
        - Remove SKU, Barcode, etc. from "Required" list in Backend Validation.
        - Set default `store_id` (e.g., to 1) if not provided by Admin UI.
- [x] **Step 3: Fix Add to Cart Notification (P2)**
    - [x] **Issue**: Clicking "Add to Cart" directly (e.g. from Product Card/Detail) provides no visual feedback/toast.
    - [x] **Task**: Implement Global Toast Notification (using `vue-toastification` or similar existing lib).
- [x] **Step 4: Fix Cart Count Reactivity (P2)**
    - [x] **Issue**: The cart badge number (Navbar) does not update immediately when items are added/removed.
    - [x] **Task**: Implement a global Event Bus or use Vuex/Pinia state to sync cart count across components (`Navbar` vs `Cart`).
- [x] **Step 5: Implement Missing Profile Sections (P2)**
    - [x] **Issue**: "Order History" and "Address Management" UI sections are incomplete or placeholders.
    - [x] **Task**:
        - Implement `OrderHistory.vue`: List user orders with status and detail view.
        - Implement `AddressManager.vue` (or `ProfileEdit.vue` expansion): CRUD for user addresses.

- [x] **Step 6: Order Status Workflow & Refunds (P1)**
    - [x] **Logic**:
        - **Status Flow**: `Processing` (Paid immediately on order) -> `Shipped` -> `Delivered` -> `Completed`.
        - **Refunds**: support **Full Refund Only** (Cancelled).
        - **Automation**: Triggering Refund MUST automatically credit `wallet_balance` and log `REFUND` transaction.
    - [x] **Task**:
        - Update `Order` model states.
        - Create `OrderService::refund(Order $order)` logic.
        - Update `OrderHistory.vue` with tabs for statuses.
        - Add "Refund/Cancel" button (only for allowed states).
    - [x] **Test**: `OrderRefundTest.php` (Verify balance restoration).

- [x] **Step 7: Enforce Wallet-Only Payment & Discount Prep (P1)**
    - [x] **Logic**:
        - **Payment**: Hardcode to "Internal Wallet". Hide other options.
        - **Validation**: If `Balance < Total`, show **"Top-up Needed" Modal** (Guide user, don't just disable).
        - **Calculation**: Prepare `OrderService` to handle `Subtotal - Discount + Shipping = Total` (Prep for Step 11).
    - [x] **Task**:
        - Refactor Checkout UI.
        - Implement "Insufficient Balance" Modal with redirection/inline top-up link.
        - Ensure Atomic Transaction (Deduct Balance + Create Order).

- [x] **Step 8: Order Snapshots (Data Integrity) (P2)**
    - [x] **Logic**: User address/phone might change. Order must preserve data at time of purchase.
    - [x] **Task**:
        - **Migration**: Add `snapshot_data` (JSON) column to `orders` table.
        - **Backend**: Store Address/Phone/MemberLevel snapshot when creating order.
        - **Admin UI**: Read from snapshot in `AdminOrderDetails`.
    - [x] **Test**: Verify snapshot data persists after User profile update.

- [x] **Step 9: Smart Check-out Address UX (P2)**
    - [x] **Logic**: Uninterrupted flow.
    - [x] **Task**:
        - If no address exists during checkout, show **Inline Modal** to add one (keep user on page).
        - Auto-select the new address.

- [x] **Step 10: Admin User Edit Improvements (P3)**
    - [x] **Task**: Allow Name edit, Optional Password update.

- [x] **Step 11: Member Levels & Discounts (P2 - Logic integrated with Order)**
    - [x] **Logic**:
        - **Config-based**: Define Levels (e.g., Normal, VIP, Platinum) and Discounts in `config/shop.php` (Mock DB for now).
        - **Automation**: Listener on `OrderCompleted` -> Check `total_spent` -> Upgrade Level automatically.
        - **Display**: Discount shown as separate **Line Item** in Cart/Order.
    - [x] **Task**:
        - Implement `MemberLevelService`.
        - Integrate discount calculation into `Cart` and `OrderService`.
        - Update Admin UI to allow manual level override.
    - [x] **Test**: `MemberUpgradeTest.php`, `DiscountCalculationTest.php`.

- [x] **Step 12: Fix Wishlist Navigation (P3)**
    - [x] **Task**: Navbar Heart icon -> Redirect to `/profile?tab=wishlist`.

## Verification Plan

### Automated Tests
- Run `php artisan test` for all backend logic.
- Target coverage: Auth, Wallet, Products, Orders.

### Manual Verification
- **Step-by-Step Walkthroughs**: Documented in `progress.md` after each feature.
- **Docker Production Test**: `docker compose up --build` should result in a fully functional app.
