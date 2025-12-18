# Implementation Plan - Online Shopping System (Reform)

## User Review Required
> [!IMPORTANT]
> - **Laravel Version**: Request to use `^12.0` (Latest).
> - **Top-up System**: Implementing an **internal credit/wallet system** first (Mock Payment).
> - **Frontend**: Focus exclusively on `xie_vue`. `xie_nuxt` is out of scope.

## Proposed Changes

### Phase 1-8: Foundation & Initial Features (Completed)
(See `progress.md` for detailed history of Phases 1-8)

### Phase 9: Major Refactoring & Features Integration (Current Focus)
> [!IMPORTANT]
> **Constraint**: Strict 3NF normalization, Soft Deletes, and Admin Security Enforcement.
> **Testing**: All financial computations requiring `FeatureTest`. All Schema changes require `MigrationTest`.

- [x] **Step 1: Admin & Security Hardening (Issues 1, 4, 11)**
    - [x] **Auth**: Remove `VITE_BYPASS_AUTH_DEV`. Implement Admin Login + Redirect Fix.
    - [x] **Security**: Ensure `Logout` clears `LocalStorage` entirely.
    - [x] **Member Level**: Add `is_level_locked` column to `users`. Allow Admin to toggle lock and manually set level.
    - [x] **Test**: `AdminAuthTest.php`, `MemberLevelTest.php`.

- [x] **Step 2: Database Schema & Settings (Issue 7, 12, 15, 16)**
    - [x] **Settings**: Create `settings` table. migrate `free_shipping_threshold` to this table.
    - [x] **Address**: Refactor `addresses` table (City, District, Zip). Add JSON mapping file to frontend.
    - [x] **Normalization**: Audit `products`, `orders`, `users` keys/indexes. Ensure 3NF.
    - [x] **Test**: `SchemaIntegrityTest.php` (Verify FKs and Indices).

- [x] **Step 3: Frontend Experience Refinement (Issues 2, 5, 6, 8, 12)**
    - [x] **Address UI**: Implement City/District Selectors (tw-city-selector logic).
    - [x] **Cart**: Hide Guest Cart Badge. Redirect "Add to Cart" to login if guest.
    - [x] **Badge Sync**: Debounce (500ms) cart update actions.
    - [x] **Order Status**: Implement **Server-side** icons filter (API query params).
    - [x] **Price Display**: Show Original Price, Sale Price, and Floor-rounded Discount %.

- [x] **Step 4: Smart Payment & Logistics (Issues 3, 10, 13)**
    - [x] **Payment**: Hardcode "Wallet Only". Auto-select wallet if sufficiency check passes.
    - [x] **Logistics**: Auto-generate `LOGI-{YYYYMMDD}-{Unique}` on `OrderCreated`.
    - [x] **Top-up**: Add Toast Notification on success. Auto-refresh Wallet Balance.

- [x] **Step 5: Admin Dashboard & Stats (Issues 9, 13, 14)**
    - [x] **Revenue Chart**: Fix "Last 7 Days" logic. Query `wallet_transactions` where type='payment'.
    - [x] **Stats**: Fix "Total Consumption" (Sum of negative payments).
    - [x] **Order List**: Remove inline status edit. Status change only in Detail View (with Confirm Modal).
    - [x] **Test**: `DashboardStatsTest.php` (Verify specific 7-day aggregation logic).

- [x] **Step 6: Comprehensive Database Audit (Issue 16)**
    - [x] **Scalability**: Verify Indexes on `category_id`, `created_at` (for reports), `status`.
    - [x] **Integrity**: Verify `Repeatable Read` isolation and `FOR UPDATE` locks in `OrderService`.
    - [x] **Cleanup**: Remove unused columns/tables if found during audit.

### Phase 10: Final Polish & Launch Readiness (Partially Complete)
- [x] **Step 1: Free Shipping & Add-on Logic (Issue 7)**
    - [x] **Frontend**: Show "Add $X for Free Shipping" in Cart.
    - [x] **Frontend**: "Add Items" button redirects to Product List.
    - [x] **Backend**: Calculate `shipping_fee` in `OrderController` based on `settings.free_shipping_threshold`.
    - [x] **Test**: `ShippingFeeTest.php` (Verify fee application).

- [x] **Step 2: System-wide Verification**
    - [x] **Smoke Test**: Manual pass through all critical flows (Register, Cart, Checkout, Admin, Profile) (Automated via `SystemSmokeTest`).
    - [x] **Security**: Final check on Admin Routes and User Data protection.

## Verification Plan

### Automated Tests
-   **Feature Tests**: `php artisan test --filter Wallet` (Critical)
-   **Browser Tests`: Manual walkthrough required for UI interactions (Debounce, Selectors).

### Manual Verification
-   **Admin**: Login -> Lock User Level -> Verify Auto-upgrade skipped.
-   **User**: Top-up -> Buy -> Verify "Last 7 Days" chart updates correctly for Admin.
-   **Guest**: Attempt to Add to Cart -> Verify Redirect.
