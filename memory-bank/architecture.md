# Architecture & Design

> **Last Updated**: 2025-12-25
> **Version**: 2.0 (Cold Start Revision)

---

## 1. System Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                        Client Layer                              │
│  Vue 3 SPA (xie_vue) + Pinia + Vue Router + Tailwind CSS        │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼ REST API (JSON)
┌─────────────────────────────────────────────────────────────────┐
│                        Web Layer (Nginx)                         │
│              Reverse Proxy + Static Files Serving               │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                   Application Layer (Laravel)                    │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────────┐       │
│  │  Controllers │  │  Middleware  │  │  Form Requests   │       │
│  └──────────────┘  └──────────────┘  └──────────────────┘       │
│  ┌──────────────────────────────────────────────────────┐       │
│  │                   Service Layer                       │       │
│  │  WalletService | OrderService | InventoryService     │       │
│  │  MemberLevelService | PriceCalculator | etc.         │       │
│  └──────────────────────────────────────────────────────┘       │
│  ┌──────────────────────────────────────────────────────┐       │
│  │                   Domain Layer                        │       │
│  │  Models | DTOs | Enums | Events | Exceptions         │       │
│  └──────────────────────────────────────────────────────┘       │
└─────────────────────────────────────────────────────────────────┘
                              │
           ┌──────────────────┼──────────────────┐
           ▼                  ▼                  ▼
     ┌──────────┐       ┌──────────┐       ┌──────────┐
     │  MySQL   │       │  Redis   │       │  Queue   │
     │  8.0     │       │ (Cache)  │       │ (Redis)  │
     └──────────┘       └──────────┘       └──────────┘
```

### 1.1 Architecture Style
- **Monolithic API with SPA Frontend**
- Backend: Laravel 作為 Headless API Provider
- Frontend: Vue 3 SPA，獨立部署為靜態檔案

---

## 2. Design Patterns

### 2.1 Service Layer Pattern
**問題**：Fat Controllers 包含過多業務邏輯
**解決方案**：將業務邏輯封裝至 Services

| Service | Responsibility |
|---------|---------------|
| `WalletService` | 錢包交易處理 (deposit/payment/refund/adjustment) |
| `OrderService` | 訂單狀態轉換、退款流程 |
| `OrderCreationService` | 訂單建立（不含付款） |
| `InventoryService` | 庫存鎖定與扣減 |
| `MemberLevelService` | 會員等級升級邏輯 |
| `PriceCalculator` | 價格計算（折扣、運費） |
| `OrderSequenceGenerator` | 物流編號生成 |
| `CheckoutCoordinator` | **計畫中** - 結帳協調器 |

> **技術債 TD-007**：引入 `CheckoutCoordinator` 封裝 `OrderCreationService` + `WalletService`，
> 確保「建立訂單 + 扣款」的 Transaction 原子性，避免「訂單成功但扣款失敗，庫存鎖死」

### 2.2 Data Transfer Objects (DTO)
**目的**：領域層解耦，明確定義資料結構

```
app/Services/DTO/
├── CreateOrderData.php    # 訂單建立輸入
└── ...
```

### 2.3 Event-Driven Architecture
**應用範圍**：非核心路徑

| Event | Listener | Purpose |
|-------|----------|---------|
| MemberPayment | UpgradeMemberLevel | 消費後檢查升級 |

> **升級觸發**：事件驅動即時觸發（非排程）

### 2.4 Domain Exceptions
**計畫**：定義豐富的領域例外

| Exception | Scenario |
|-----------|----------|
| `InsufficientBalanceException` | 餘額不足 |
| (待實作更多) | ... |

---

## 3. Database Design

### 3.1 Normalization Strategy (3NF)
- **核心表**：Users, Products, Orders 嚴格遵循 3NF
- **訂單快照**：拆分為 `order_snapshots` + `order_addresses`
- **理由**：財務審計、索引效能、歷史資料凍結

### 3.2 Key Tables & Relationships

```
┌─────────────────┐     ┌─────────────────┐
│     users       │────<│     orders      │
└─────────────────┘     └─────────────────┘
        │                       │
        │                       ├────<┌─────────────────┐
        │                       │     │   order_items   │
        │                       │     └─────────────────┘
        │                       │             │
        │                       │     ┌───────▼─────────┐
        │                       │     │order_item_options│
        │                       │     └─────────────────┘
        │                       │
        │               ┌───────▼─────────┐
        │               │ order_addresses │
        │               └─────────────────┘
        │               ┌───────▼─────────┐
        │               │ order_snapshots │
        │               └─────────────────┘
        │
┌───────▼─────────┐     ┌─────────────────┐
│wallet_transactions│    │  member_levels  │
└─────────────────┘     └─────────────────┘
```

### 3.3 Monetary Storage
- **策略**：整數「分 (Cents)」儲存
- **欄位**：`balance`, `total_amount`, `price`, `shipping_fee`
- **優點**：避免浮點數精度問題

### 3.4 Member Level Configuration
- **儲存位置**：`member_levels` 表（可動態配置）
- **折扣率單位**：萬分比 (Basis Points, BPS)，整數儲存
  - 例：95 折 = 9500 BPS
  - **計算公式**：`floor(price * discount_bps / 10000)`
  - **取整規則**：無條件捨去 (Floor)，避免超出用戶預算或產生微小差額
  - **理由**：與金額整數化原則一致，避免浮點數精度問題
- **鎖定機制**：`users.is_level_locked` 防止自動升/降級

### 3.5 Sequence-based Logistics Number
- **格式**：`LOGI-{YYYYMMDD}-{Sequence}`
- **表**：`sequences`（日期前綴 + 自動遞增）
- **循環設計**：達 INT 上限時循環重用
- **理由**：業務易讀性、物流商規範

### 3.6 Soft Deletes
- **應用**：Users, Products, Orders
- **資料保留策略**：目前無清理機制，計畫採分級保留原則

---

## 4. Wallet & Transaction System

### 4.1 Transaction Types
| Type | Direction | Description |
|------|-----------|-------------|
| `deposit` | + | 用戶儲值 |
| `payment` | - | 訂單付款 |
| `refund` | + | 訂單退款 |
| `withdrawal` | - | 提款（未使用） |
| `adjustment` | +/- | 管理員調整 (**待實作**) |

### 4.2 Balance Calculation
- **Cache 欄位**：`users.balance`
- **Validation**：餘額 = SUM(transactions.amount)
- **約束**：嚴禁負值餘額

### 4.3 Pessimistic Locking
```php
// WalletService::processTransaction()
$lockedUser = User::where('id', $user->id)->lockForUpdate()->first();
```

### 4.4 Audit Requirements (待實作)
建立 `wallet_logs` 表：
- `operator_id` - 操作人員
- `ip_address` - 請求 IP
- `balance_before` / `balance_after` - 餘額變化
- `reason` - 交易備註

---

## 5. Order State Machine

### 5.1 Current States (OrderStatus Enum)
```
pending_payment → processing → shipped → delivered → completed
       ↓              ↓
   cancelled      cancelled
       ↓              ↓
   refunded       refunded
```

### 5.2 Transition Rules (待正規化)
| From | To | Condition | Auto Action |
|------|-----|-----------|-------------|
| pending_payment | processing | 付款成功 | - |
| pending_payment | cancelled | 用戶取消/逾時 | - |
| processing | shipped | 管理員發貨 | - |
| processing | cancelled | 用戶/管理員取消 | **自動觸發退款** |
| shipped | delivered | 物流送達 | - |
| shipped | return_requested | 用戶申請退貨 | **進入人工審核** |
| return_requested | returned | 物流退回確認 | - |
| returned | refunded | 管理員審核通過 | 執行退款 |
| cancelled | refunded | 執行退款 | - |
| delivered | completed | 用戶確認收貨 | - |

> [!IMPORTANT]
> **shipped 之後禁止自動退款**：已發貨訂單不可直接取消，必須走「退貨退款 (Return & Refund)」流程，
> 經人工審核 + 物流退回後方可執行退款。

> **待實作**：硬性狀態轉換圖 (State Machine Library)

---

## 6. Inventory Management

### 6.1 Anti-Oversell Strategy
```php
// InventoryService::lockAndCheckStock()
$products = Product::whereIn('id', $productIds)
    ->lockForUpdate()
    ->get();
```

### 6.2 Performance Considerations
- **現況**：悲觀鎖，適用 <500 TPS
- **計畫**：高併發場景遷移至 Redis Lua Script

### 6.3 Stock Alert (待實作)
- 庫存低於閾值觸發通知

---

## 7. Shipping & Logistics

### 7.1 Current Design
- 固定運費：60 元
- 免運門檻：`settings.free_shipping_threshold` (1000 元)

### 7.2 Planned Enhancement
- **區域運費**：僅配送 3 個縣市
- **階梯運費**：基於金額或重量

---

## 8. Frontend Architecture

### 8.1 State Management (Pinia)
```
stores/
├── auth.js      # 認證狀態、Token 管理
├── cart.js      # 購物車、Badge 計數
└── ...
```

### 8.2 Routing Strategy
- 單一進入點
- 動態路由懶加載 (Code Splitting)
- Admin/Customer 路由分離

### 8.3 Component Design
- Tailwind CSS Utility Classes
- Modal 元件：Focus Trap + ARIA (計畫)
- Toast 通知：`useNotification` 封裝層 (計畫)

---

## 9. Security Architecture

### 9.1 Authentication Flow
```
┌─────────┐     ┌─────────┐     ┌─────────┐
│ Client  │────>│  Login  │────>│ Sanctum │
└─────────┘     └─────────┘     └─────────┘
     │                               │
     └───── Bearer Token ◄───────────┘
```

### 9.2 Planned Improvements
| Aspect | Current | Target |
|--------|---------|--------|
| Token Storage | localStorage | HttpOnly Cookie |
| Token TTL | Default | 24h + Sliding Expiration |
| Admin Routes | Basic Auth | Rate Limiting + IP Whitelist |
| Logs | Raw | Sanitized (敏感資料脫敏) |
| CSRF | Sanctum Cookie-based | - |

---

## 10. Deployment Architecture

### 10.1 Production Stack
```yaml
services:
  nginx-prod:    # Static Vue + API Proxy
  app:           # Laravel + PHP-FPM
  db:            # MySQL 8.0
  redis:         # Cache + Session + Queue
```

### 10.2 Removed from Production
- Meilisearch（改用 SQL LIKE）

### 10.3 Future Consideration
- Kubernetes (架構預留，尚未使用)

---

## 11. Architecture Decision Records (ADR)

> 📁 **完整 ADR 文件**：[/memory-bank/adr/](./adr/README.md)

| ID | Decision | Date | Status |
|----|----------|------|--------|
| [ADR-001](./adr/ADR-001-integer-cents-storage.md) | 整數金額儲存 (Cents) | 2025-12 | Implemented |
| [ADR-002](./adr/ADR-002-order-snapshot-tables.md) | 訂單快照拆表 | 2025-12-22 | Implemented |
| [ADR-003](./adr/ADR-003-member-level-database.md) | 會員等級資料庫化 | 2025-12-22 | Implemented |
| [ADR-004](./adr/ADR-004-sequence-logistics-number.md) | Sequence 物流編號 | 2025-12-22 | Implemented |
| [ADR-005](./adr/ADR-005-discount-rate-bps.md) | 折扣率採萬分比 (BPS) | 2025-12 | Implemented |
| [ADR-006](./adr/ADR-006-vuex-to-pinia.md) | Vuex → Pinia 遷移 | 2025-12 | In Progress |
| [ADR-007](./adr/ADR-007-httponly-cookie.md) | HttpOnly Cookie | - | Planned |
| [ADR-008](./adr/ADR-008-wallet-audit-log.md) | Wallet Audit Log Table | - | Planned |
| [ADR-009](./adr/ADR-009-order-state-machine.md) | Order State Machine | - | Planned |
| [ADR-010](./adr/ADR-010-openapi-documentation.md) | OpenAPI 文檔自動生成 | - | Planned |
