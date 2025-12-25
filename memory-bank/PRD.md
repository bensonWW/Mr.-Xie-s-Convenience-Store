# Product Requirements Document (PRD)

> **Last Updated**: 2025-12-25
> **Version**: 2.0 (Cold Start Revision)

---

## 1. Project Overview

**謝老闆便利店 Online Shopping System (OSS)** 是一套以 Laravel + Vue.js 建構的 B2C 電商平台，專為單一便利店提供完整的線上購物、會員管理與內部錢包儲值服務。

### 1.1 Core Value Proposition
- **便捷購物體驗**：實體商品線上瀏覽、購物車管理、願望清單
- **儲值金支付**：內部錢包系統取代傳統金流，降低交易成本
- **會員分級制度**：VIP/Platinum 等級提供批發級折扣，鎖定高價值客戶
- **完整後台管理**：商品 CRUD、訂單處理、會員管理、營收報表

### 1.2 Business Model
| 項目 | 現況 |
|------|------|
| **租戶模式** | 單店鋪 (Single-tenant)；`stores` 表為未來連鎖擴展預留 |
| **商業模式** | B2C 終端消費者為主 |
| **商品類型** | 實體商品（需庫存管理與預警機制） |
| **配送範圍** | 僅限 3 個縣市，需區域/階梯運費 |
| **支付方式** | 儲值金 (Internal Wallet) 唯一；無外部金流整合計畫 |

---

## 2. User Roles & Permissions

### 2.1 Guest（訪客）
- 瀏覽商品與分類
- 搜尋商品（SQL LIKE 查詢）
- **限制**：無法查看購物車計數、「加入購物車」跳轉登入頁

### 2.2 Customer/Member（會員）
- 註冊/登入（Sanctum Token，Email 驗證為必填）
- 管理個人資料（多地址簿、城市/區域選擇器）
- **錢包管理**：
  - 儲值 (Deposit)
  - 查看餘額與交易紀錄
  - 錢包付款下訂單
- **購物流程**：
  - 購物車管理（即時 Badge 同步，500ms Debounce）
  - 願望清單（未來支援降價/補貨通知）
  - 下訂單、查看訂單歷史（Server-side 狀態篩選）
  - 申請退款（僅限 `pending`/`processing` 狀態，全額退）

### 2.3 Staff（員工）
- 管理門市商品 (CRUD)
- 庫存管理與調整

### 2.4 Admin（管理員）
- 系統設定（`settings` 表 Key-Value Store）
- 會員管理：
  - 查看/編輯會員資料
  - **鎖定會員等級** (`is_level_locked`)：保護特殊身份不被自動升/降級
  - 手動錢包調整 (Adjustment)
- 訂單管理：
  - 列表檢視（唯讀狀態）
  - 詳情頁更改狀態（需 Confirm Modal）
  - 執行退款
- **儀表板**：
  - 最近 7 天營收圖表
  - 總消費額、訂單數統計

---

## 3. Core Features

### 3.1 User Account & Wallet System

#### 3.1.1 Authentication
- Email/Password 註冊登入（Laravel Sanctum）
- **Email 強制驗證**：未驗證用戶不可下訂單
- 登出時強制清除 `localStorage`
- Token TTL：24 小時，採滑動過期 (Sliding Expiration)

#### 3.1.2 Wallet Transactions
交易類型設計：

| Type | 金額方向 | 說明 |
|------|----------|------|
| `deposit` | + | 用戶儲值 |
| `payment` | - | 訂單付款 |
| `refund` | + | 訂單退款 |
| `adjustment` | +/- | 管理員手動調整 (**待實作**) |

> **財務合規要求**：需建立不可竄改的 `wallet_logs` 表，記錄操作人員 ID、IP 位址、前後餘額變化。

#### 3.1.3 Member Levels
- 等級資料儲存於 `member_levels` 表（可動態配置門檻）
- 等級：Normal → VIP → Platinum
- 折扣率：萬分比 (Basis Points, BPS) 儲存（如 9500 = 95 折）
- **升級機制**：事件驅動即時觸發
- **鎖定機制**：`is_level_locked` 防止自動升/降級（員工、客訴補償帳號）

### 3.2 Product & Inventory

- 商品 CRUD（名稱、價格、原價、圖片、庫存、分類）
- **分類正規化**：`categories` 表與 `products.category_id` 已完成落地
- **庫存管理**：
  - 悲觀鎖 (`SELECT ... FOR UPDATE`) 防止超賣
  - 零庫存商品禁止加入購物車
  - **效能瓶頸**：500+ TPS 秒殺場景計畫遷移至 Redis Lua Script
- **庫存預警**：待實作

### 3.3 Shopping & Checkout

#### 3.3.1 Cart
- Guest：購物車 Badge 隱藏
- Member：數量變更 Debounce (500ms)

#### 3.3.2 Price & Discounts
- 顯示原價 vs 售價及折扣百分比（Floor 取整）
- 會員等級折扣
- 優惠券折扣（**未來支援節日優惠券疊加**）

#### 3.3.3 Shipping
- **區域運費**：僅配送 3 個縣市，需階梯/區域運費（待實作）
- 免運門檻：`settings.free_shipping_threshold`
- 運費記錄於 `orders.shipping_fee`

#### 3.3.4 Order Creation
1. 驗證庫存（悲觀鎖）
2. 快照買家資訊至 `order_snapshots` + `order_addresses`
3. 生成物流編號 `LOGI-{YYYYMMDD}-{Sequence}`
4. 原子性扣減庫存與錢包餘額
5. 建立訂單與訂單項目

#### 3.3.5 Refund
- 僅支援**全額退款**
- 已發貨訂單需走「退貨退款 (Return & Refund)」逆向物流流程

### 3.4 Wishlist（願望清單）
- **短期**：收藏功能
- **中期**：精準行銷（推薦商品、降價通知、補貨通知）

### 3.5 Admin Panel

#### 3.5.1 Dashboard
- 最近 7 天營收圖表（基於 `wallet_transactions.type = 'payment'`）
- 總消費額、訂單數統計

#### 3.5.2 Order Management
- 列表：唯讀狀態
- 詳情：狀態變更需 Confirm Modal
- **狀態機**：待建立硬性狀態轉換圖 (State Machine)

---

## 4. Technical Constraints

### 4.1 Backend
- Laravel 12.x / PHP 8.2+
- MySQL 8.0（嚴格 3NF 正規化）
- Redis（Cache / Session / Queue 三合一）
- Sanctum Token 認證

### 4.2 Frontend
- Vue 3 (`xie_vue`)
- Pinia 狀態管理（已從 Vuex 遷移）
- Tailwind CSS
- Vue Router（動態路由懶加載）

### 4.3 Infrastructure
- Docker + Docker Compose
- 開發環境保留 Meilisearch，生產環境移除
- Kubernetes 架構預留（尚未實際使用）

---

## 5. Future Scope (Out of Current Scope)

| 功能 | 狀態 |
|------|------|
| `xie_nuxt` 實作 | Out of Scope |
| 真實金流整合（綠界/藍新） | 永久 Out of Scope（採儲值金） |
| App / Line 購物整合 | Out of Scope |
| 會員積點/回購獎勵 | 暫不考慮 |
| 多店鋪 (Multi-tenant) | 架構預留，未來展望 |

---

## 6. Known Gaps & Technical Debt

> 以下項目為冷啟動分析識別之待辦事項

| 類別 | 項目 | 優先級 |
|------|------|--------|
| **金融合規** | `wallet_logs` 審計表缺失 | P0 |
| **安全性** | HttpOnly Cookie 遷移 | P1 |
| **安全性** | Admin Rate Limiting | P1 |
| **安全性** | Log Sanitizer 實作 | P1 |
| **測試** | PriceCalculator 單元測試 | P1 |
| **測試** | 並發交易測試 | P1 |
| **架構** | OrderCreationService 付款邏輯重構（引入 CheckoutCoordinator） | P2 |
| **架構** | 訂單狀態機正規化 | P2 |
| **架構** | `ADJUSTMENT` 交易類型 | P2 |
| **功能** | 區域/階梯運費 | P2 |
| **功能** | 庫存預警機制 | P2 |
| **監控** | Sentry 錯誤監控整合 | P2 |
| **文件** | OpenAPI/Swagger 文件 | P3 |
| **文件** | ADR 目錄建立 | P3 |
