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

## Verification Plan

### Automated Tests
- Run `php artisan test` for all backend logic.
- Target coverage: Auth, Wallet, Products, Orders.

### Manual Verification
- **Step-by-Step Walkthroughs**: Documented in `progress.md` after each feature.
- **Docker Production Test**: `docker compose up --build` should result in a fully functional app.
