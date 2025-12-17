# Project Progress

## Status Legend
- [ ] Todo
- [/] In Progress
- [x] Done

## Milestones

### 1. Initialization
- [x] Create Memory Bank
- [x] Update to Laravel 12.0
- [x] Stabilize Docker Environment

### 2. Backend Development (Internal Credits)
- [x] Wallet Database Migrations
- [x] WalletService Implementation
- [x] WalletController & Routes
- [x] Automated Tests (Wallet)



### 3. Backend - API Verification & Completeness
- [x] Gap Analysis
- [x] Identify missing endpoints (None found)
- [x] Fix & Test (Verified existing)

### 4. Frontend Development (xie_vue)
- [x] User Profile - Wallet View
- [x] Top-up Modal (Mock)
- [x] Checkout with Wallet Balance

### 5. Verification
- [x] Full E2E Test (Register -> Topup -> Shop)
    - Implemented `tests/Feature/WalletE2ETest.php` simulating user journey.

### 6. Admin Panel Updates (Wallet Management)
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

### 7. Final Polish & Fixes
### 7. Final Polish & Fixes
- [x] **Step 1: Debug Admin Auth Redirection (P0)**
    - **Status**: Done.
    - **Fix**: Updated `router/index.js` to carry `redirect` query param, and `AuthOverlay.vue` to respect it. verified via Browser Subagent that `VUE_APP_BYPASS_AUTH_DEV` works for dev access.
- [x] **Step 2: Wallet UI Polish (P1)**
    - **Status**: Done.
    - **Changes**:
        - `WalletBalanceCard`: Applied `bg-gradient-to-br` (Blue/Purple), added decorative icon, and glassmorphism button.
        - `WalletTransactionTable`: Added rounded corners (`rounded-xl`), pill badges for transaction types, and improved spacing.
        - `WalletActionModal`: Updated to use `backdrop-blur`, rounded inputs, and modern shadows.
        - **Fix**: Updated `docker-compose.yml` to set `VUE_APP_API_URL` to `http://localhost:8000/api` for correct local API targeting.

### 8. Bug Fixes & Refinements (Phase 8)
- [x] **Step 1: Fix Wishlist Refresh Issue (P1)**
    - **Status**: Done.
    - **Fix**:
        - **Backend**: Created `favorites` table (migration), `Favorite` model, and `FavoriteController`. Added routes: `GET/POST /favorites`, `DELETE /favorites/{id}`.
        - **Frontend**: Updated `ProfileView.vue` to fetch real wishlist from API. Modified `WishlistGrid.vue` to emit remove events after API deletion. Updated `ProductDetail.vue` to toggle wishlist status via API.

- [x] **Step 2: Simplify Admin Product Creation (P2)**
    - **Status**: Done.
    - **Fix**:
        - **Backend**: Updated `ProductController.php` to default `store_id` (fallback to 1 or auth user's store), removing strict validation blockers.
        - **Frontend**: Simplified `AdminProductEdit.vue` by hiding complicated/unused fields (SKU, Barcode, Brand, Tags).

- [x] **Step 3: Fix Add to Cart Notification (P2)**
    - **Status**: Done.
    - **Fix**:
        - **Frontend**: Updated `ItemsView.vue` to import and use `vue-toastification`. Implemented `addToCart(item)` with `@click.prevent` to avoid router link issues. Added global toast success/error feedback.

- [x] **Step 4: Fix Cart Count Reactivity (P2)**
    - **Status**: Done.
    - **Fix**:
        - **Vuex**: Added `cart` module with `count` state and `fetchCount` action.
        - **Frontend**: Updated `AppHeader.vue` to bind to `cart/count` and dispatch `fetchCount` on load.
        - **Actions**: Updated `ItemsView`, `ProductDetail`, and `ProfileView` to dispatch 'cart/fetchCount' after adding items, ensuring the badge updates immediately.

    - [x] **Step 5: Implement Missing Profile Sections (P2)**
    - [x] **Issue**: "Order History" and "Address Management" UI sections are incomplete or placeholders.
    - [x] **Task**:
        - Implement `OrderHistory.vue`: List user orders with status and detail view.
        - Implement `AddressManager.vue` (or `ProfileEdit.vue` expansion): CRUD for user addresses.

- [x] **Step 6: Order Status Workflow & Refunds (P1)**
    - **Status**: Done.
    - **Implementation**:
        - **Backend**: Created `OrderService` with `refund` method (Transaction: Credit Wallet + Restore Stock + set Status 'refunded').
        - **API**: Added `POST /api/orders/{id}/refund` endpoint.
        - **Frontend**: Updated `OrderHistory.vue` with Status Tabs (All, Processing, Shipped...) and "Cancel Order" button for processing orders.
        - **Test**: `OrderRefundTest.php` Passed (4 tests). Fixed `ProductFactory` and `StoreFactory` dependencies.
        - **Linting**: Fixed ESLint errors in `WalletActionModal.vue`, `WalletBalanceCard.vue`, `WalletTransactionTable.vue`, `OrderHistory.vue`, `WishlistGrid.vue`, `store/index.js`, `AdminProductEdit.vue` to pass build pipeline.

- [x] **Step 7: Enforce Wallet-Only Payment & Discount Prep (P1)**
    - **Status**: Done.
    - **Implementation**:
        - **Frontend**: Created `UserTopUpModal.vue` component. Integrated it into `CartView` (Checkout) and `ProfileView`.
        - **Checkout UX**: `CartView` now checks Wallet Balance vs Cart Total. If insufficient, shows warning and "Top Up Now" button (Inline Modal).
        - **Backend**: Updated `OrderController::store` to enforce Atomic Transaction (Deduct Balance + Create Order). Returns `402` if insufficient balance.
        - **Verification**: Verified flow where insufficient balance blocks checkout until user tops up inline.

- [x] **Step 8: Order Snapshots (Data Integrity) (P2)**
    - **Status**: Done.
    - **Implementation**:
        - **Migration**: Added `snapshot_data` (JSON) to `orders` table.
        - **Backend**: Updated `OrderController` to store User Name, Email, Phone, and Address in `snapshot_data` during order creation.
        - **Frontend**: Updated `AdminOrderDetail.vue` to display customer info from Snapshot (with "快照" badge) to ensure historical accuracy.
        - **Test**: `OrderSnapshotTest.php` Passed (Verifies data persistence after profile update).

- [x] **Step 9: Smart Check-out Address UX (P2)**
    - **Status**: Done.
    - **Implementation**:
        - **Frontend**: Created `InlineAddressModal.vue` for quick address entry without leaving checkout.
        - **Logic**: Updated `CarView.vue` to check for empty address on Checkout click. Calls Inline Modal if needed.
        - **UX**: User fills address -> Modal Closes -> Can immediately proceed to Payment. No page reload/redirect.

- [x] **Step 10: Admin User Edit Improvements (P3)**
    - **Status**: Done.
    - **Implementation**:
        - **Backend Verification**: Confirmed `AdminController::updateUser` handles optional password.
        - **Test**: Created `tests/Feature/AdminUserUpdateTest.php`. Validated Admin can update Name/Email without requiring Password reset. Validated Password update works when provided.

- [x] **Step 11: Member Levels & Discounts (P2)**
    - **Status**: Done.
    - **Implementation**:
        - **Schema**: Added `member_level` to `users` and `discount_amount` to `orders`.
        - **Config**: Created `shop.php` for Level Definitions (Normal, VIP, Platinum).
        - **Backend**: Implemented `MemberLevelService` and `UpgradeMemberLevel` Listener. Discounts applied in `OrderController`.
        - **Test**: `MemberUpgradeTest.php` Passed (Verifies Spending -> Upgrade -> Discount Application).
        - **Frontend**: Updated `CarView.vue` to fetch User Level and display calculated Discount.

- [x] **Step 12: Fix Wishlist Navigation (P3)**
    - **Status**: Done.
    - **Implementation**:
        - **Frontend**: Updated `AppHeader.vue` to link Heart icon to `/profile?tab=wishlist`.
        - **Logic**: Updated `ProfileView.vue` to watch for `tab` query parameter and switch tabs automatically.
