# 前端頁面完整清單 (Frontend Pages & Components)

> **Generated**: 2025-12-25
> **Purpose**: 提供完整前端程式碼結構供重新設計參考

---

## 1. 主要頁面 (Views)

### 1.1 HomeView.vue - 首頁
**路徑**: `xie_vue/src/views/HomeView.vue`
**功能**: 商品分類導航、Hero Banner、Flash Sale 區塊

### 1.2 ItemsView.vue - 商品列表頁
**路徑**: `xie_vue/src/views/ItemsView.vue`
**功能**: 商品搜尋、分類篩選、商品卡片展示、分頁

### 1.3 ProductDetail.vue - 商品詳情頁
**路徑**: `xie_vue/src/views/ProductDetail.vue`
**功能**: 商品圖片、價格、數量選擇、加入購物車、收藏

### 1.4 CarView.vue - 購物車頁
**路徑**: `xie_vue/src/views/CarView.vue`
**功能**: 購物車列表、優惠券套用、運費計算、結帳

### 1.5 ProfileView.vue - 會員中心
**路徑**: `xie_vue/src/views/ProfileView.vue`
**功能**: 登入/註冊、訂單歷史、錢包、收藏、地址管理

### 1.6 AdminView.vue - 後台管理框架
**路徑**: `xie_vue/src/views/AdminView.vue`
**功能**: 後台導航 Sidebar、頁面切換

### 1.7 EmailVerifyView.vue - 信箱驗證頁
**路徑**: `xie_vue/src/views/EmailVerifyView.vue`
**功能**: Email 驗證碼輸入與驗證

### 1.8 AdminProductEdit.vue - 商品編輯頁
**路徑**: `xie_vue/src/views/AdminProductEdit.vue`
**功能**: 新增/編輯商品

### 1.9 AdminUserEdit.vue - 會員編輯頁
**路徑**: `xie_vue/src/views/AdminUserEdit.vue`
**功能**: 新增/編輯會員資料

---

## 2. 共用元件 (Components)

### 2.1 Layout Components
| 元件 | 路徑 | 功能 |
|------|------|------|
| `AppHeader.vue` | `components/AppHeader.vue` | 頂部導航、搜尋、購物車 Badge |
| `AppFooter.vue` | `components/AppFooter.vue` | 底部資訊 |

### 2.2 Home Components
| 元件 | 路徑 | 功能 |
|------|------|------|
| `HeroBanner.vue` | `components/home/HeroBanner.vue` | 首頁輪播 Banner |
| `FlashSaleSection.vue` | `components/home/FlashSaleSection.vue` | 限時特賣區 |
| `CategorySidebar.vue` | `components/home/CategorySidebar.vue` | 分類側邊欄 |

### 2.3 Profile Components
| 元件 | 路徑 | 功能 |
|------|------|------|
| `AuthOverlay.vue` | `components/profile/AuthOverlay.vue` | 登入/註冊表單 |
| `UserSidebar.vue` | `components/profile/UserSidebar.vue` | 會員中心側邊欄 |
| `DashboardStats.vue` | `components/profile/DashboardStats.vue` | 會員儀表板統計 |
| `OrderHistory.vue` | `components/profile/OrderHistory.vue` | 訂單歷史列表 |
| `WalletView.vue` | `components/profile/WalletView.vue` | 錢包餘額與交易紀錄 |
| `UserTopUpModal.vue` | `components/profile/UserTopUpModal.vue` | 儲值彈窗 |
| `CouponWallet.vue` | `components/profile/CouponWallet.vue` | 優惠券列表 |
| `WishlistGrid.vue` | `components/profile/WishlistGrid.vue` | 收藏清單 |
| `ProfileEdit.vue` | `components/profile/ProfileEdit.vue` | 個人資料編輯 |
| `AddressManager.vue` | `components/profile/AddressManager.vue` | 地址管理 |
| `InlineAddressModal.vue` | `components/profile/InlineAddressModal.vue` | 地址新增彈窗 |

### 2.4 Admin Components
| 元件 | 路徑 | 功能 |
|------|------|------|
| `AdminDashboard.vue` | `components/AdminDashboard.vue` | 後台儀表板 |
| `AdminProducts.vue` | `components/AdminProducts.vue` | 商品列表 |
| `AdminOrders.vue` | `components/AdminOrders.vue` | 訂單列表 |
| `AdminOrderDetail.vue` | `components/AdminOrderDetail.vue` | 訂單詳情 |
| `AdminUsers.vue` | `components/AdminUsers.vue` | 會員列表 |
| `AdminCoupon.vue` | `components/AdminCoupon.vue` | 優惠券管理 |
| `AdminAnalytics.vue` | `components/AdminAnalytics.vue` | 銷售分析 |
| `WalletActionModal.vue` | `components/admin/WalletActionModal.vue` | 錢包操作彈窗 |
| `WalletBalanceCard.vue` | `components/admin/WalletBalanceCard.vue` | 餘額卡片 |
| `WalletTransactionTable.vue` | `components/admin/WalletTransactionTable.vue` | 交易紀錄表格 |

---

## 3. 設計風格 (Design System)

### 3.1 顏色系統
```css
/* Tailwind 自訂顏色 (tailwind.config.js) */
:root {
  --xie-blue: #1e3a5f;      /* 主色 - 深藍 */
  --xie-orange: #f97316;    /* 強調色 - 橘色 */
  --main-bg: #f7fafc;       /* 背景 - 淺灰 */
  --main-text: #2d3748;     /* 文字 - 深灰 */
}

.dark {
  --main-bg: #1a202c;
  --main-text: #f7fafc;
}
```

### 3.2 字體
```css
font-family: Avenir, Helvetica, Arial, sans-serif;
```

### 3.3 主要 Tailwind 類別模式

**卡片**:
```html
<div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-lg transition">
```

**按鈕 (Primary)**:
```html
<button class="bg-xieOrange text-white px-6 py-3 rounded-lg font-bold hover:bg-orange-600 transition">
```

**按鈕 (Secondary)**:
```html
<button class="border-2 border-xieOrange text-xieOrange px-6 py-3 rounded-lg font-bold hover:bg-orange-50 transition">
```

**輸入框**:
```html
<input class="border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-xieOrange">
```

**側邊欄項目 (Active)**:
```html
<li class="bg-orange-50 text-xieOrange border-l-4 border-xieOrange px-4 py-3">
```

---

## 4. 路由結構 (Router)

```
/                     → HomeView
/items                → ItemsView
/items/:id            → ProductDetail
/car                  → CarView
/profile              → ProfileView
/verify-email         → EmailVerifyView
/admin                → AdminView (Layout)
  /admin/dashboard    → AdminDashboard
  /admin/products     → AdminProducts
  /admin/products/new → AdminProductEdit
  /admin/products/:id/edit → AdminProductEdit
  /admin/orders       → AdminOrders
  /admin/orders/:id   → AdminOrderDetail
  /admin/coupons      → AdminCoupon
  /admin/users        → AdminUsers
  /admin/users/new    → AdminUserEdit
  /admin/users/:id/edit → AdminUserEdit
  /admin/analytics    → AdminAnalytics
```

---

## 5. 狀態管理 (Stores)

### 5.1 Vuex Store (Legacy)
**路徑**: `xie_vue/src/store/index.js`
**功能**: auth 認證狀態

### 5.2 Pinia Store (New)
**路徑**: `xie_vue/src/stores/cart.js`
**功能**: 購物車狀態管理

---

## 6. 工具函數 (Utils)

| 檔案 | 功能 |
|------|------|
| `utils/currency.js` | `formatPrice()` 金額格式化 |
| `utils/image.js` | `resolveImageUrl()` 圖片路徑處理 |

---

## 7. 靜態資源 (Assets)

| 檔案 | 用途 |
|------|------|
| `assets/logo.png` | 網站 Logo |
| `assets/123.png` | 預設商品圖 |
| `assets/taiwan_districts.json` | 台灣縣市區資料 |
| `assets/items.json` | Mock 商品資料 |

---

## 8. 重新設計建議

### 8.1 改善方向
1. **色彩系統**: 考慮使用更現代的漸層與陰影
2. **動畫**: 加入 micro-interactions 提升 UX
3. **響應式**: 優化行動裝置體驗
4. **Dark Mode**: 完善深色模式支援

### 8.2 保持一致的元素
- 購物車 Badge 計數
- 會員等級顯示
- 價格格式 (分轉元顯示)
- Toast 通知風格



