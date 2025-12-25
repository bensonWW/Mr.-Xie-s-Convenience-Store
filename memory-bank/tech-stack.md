# Technology Stack

> **Last Updated**: 2025-12-25
> **Version**: 2.0 (Cold Start Revision)

---

## 1. Backend

| Technology | Version | Purpose | Rationale |
|------------|---------|---------|-----------|
| **Laravel** | 12.x | Web Framework | 成熟的 PHP 框架，強大的 ORM、Migration、Queue 支持 |
| **PHP** | 8.2+ | Runtime | 支援聯合類型、屬性、Enum 等現代語言特性 |
| **MySQL** | 8.0 | Primary Database | ACID 保證、JSON 支援、Window Functions |
| **Redis** | Latest | Cache/Session/Queue | 三合一架構，降低基礎設施複雜度 |
| **Laravel Sanctum** | Latest | API Authentication | Token-based 認證，SPA 友好 |
| **PHPUnit** | Latest | Testing | Laravel 內建測試框架 |

### 1.1 Key Design Decisions

#### Database Normalization (3NF)
- **決策**：核心表 (Users, Products, Orders) 嚴格遵循第三正規化形式
- **理由**：消除資料冗餘、確保參照完整性、支援財務審計需求

#### Monetary Storage (Integer Cents)
- **決策**：所有金額以整數「分 (Cents)」儲存
- **理由**：避免浮點數精度問題，確保財務計算準確性

#### Pessimistic Locking Strategy
- **決策**：庫存與錢包餘額使用 `SELECT ... FOR UPDATE`
- **理由**：防止超賣與餘額透支的競態條件
- **限制**：500+ TPS 場景需遷移至 Redis Lua Script

---

## 2. Frontend

| Technology | Version | Purpose | Rationale |
|------------|---------|---------|-----------|
| **Vue.js** | 3.x | UI Framework | Composition API、優秀的響應式系統 |
| **Pinia** | Latest | State Management | Vue 3 官方推薦，取代 Vuex |
| **Vue Router** | 4.x | Routing | 動態路由懶加載，Code Splitting |
| **Axios** | Latest | HTTP Client | Promise-based，Interceptor 支持 |
| **Tailwind CSS** | 3.x | Styling | Utility-first，快速原型開發 |
| **Chart.js** | Latest | Charts | 後台儀表板營收圖表 |
| **vue-toastification** | Latest | Notifications | Toast 訊息反饋 |

### 2.1 Key Design Decisions

#### State Management Migration (Vuex → Pinia)
- **決策**：從 Vuex 遷移至 Pinia
- **理由**：更好的 TypeScript 支持、更簡潔的 API、Vue 3 官方推薦

#### Authentication Strategy
- **現狀**：`localStorage` 儲存 Token
- **計畫**：遷移至 HttpOnly Cookie 增強安全性

#### Currency Display
- **計畫**：實作全域 `v-currency` Directive 統一價格格式化

#### Accessibility
- **計畫**：Modal 元件落實 Focus Trap 與 ARIA 屬性

---

## 3. DevOps & Infrastructure

| Technology | Purpose | Environment |
|------------|---------|-------------|
| **Docker** | Containerization | Dev + Prod |
| **Docker Compose** | Orchestration | Dev + Prod |
| **Nginx** | Reverse Proxy + Static Files | Prod |
| **Kubernetes** | Orchestration | 架構預留 (未使用) |
| **Meilisearch** | Full-text Search | Dev only (Prod 移除) |

### 3.1 Docker Architecture

```
docker-compose.yml (Dev)
├── app (Laravel + PHP-FPM)
├── nginx
├── db (MySQL 8.0)
├── redis
└── meilisearch (Dev only)

docker-compose.prod.yml (Prod)
├── app (Laravel + PHP-FPM)
├── nginx-prod (Static Vue + API Proxy)
├── db
└── redis (Cache/Session/Queue)
```

### 3.2 Key Design Decisions

#### Redis Multi-purpose
- **決策**：Redis 同時作為 Cache、Session、Queue 後端
- **理由**：降低基礎設施複雜度，適合現階段規模

#### Meilisearch Removal (Prod)
- **決策**：生產環境移除 Meilisearch，改用 SQL LIKE 搜索
- **理由**：減少維運複雜度，現有資料量不需全文搜索引擎

---

## 4. Development Tools

| Tool | Purpose |
|------|---------|
| **Composer** | PHP 套件管理 |
| **NPM** | JavaScript 套件管理 |
| **Git** | 版本控制 |
| **Laravel Pint** | PHP 代碼格式化 (PSR-12) |
| **ESLint** | JavaScript 代碼檢查 |
| **Makefile** | 常用命令封裝 |

---

## 5. Testing Stack

| Layer | Tool | Coverage |
|-------|------|----------|
| **Backend Feature Tests** | PHPUnit | 錢包、訂單、會員等級、優惠券、運費、Schema |
| **Backend Unit Tests** | PHPUnit | 待補齊 (PriceCalculator 等) |
| **Frontend Unit Tests** | - | 缺失 (計畫引入 Vitest) |
| **E2E Tests** | - | 缺失 (計畫引入 Playwright) |
| **Concurrency Tests** | - | 缺失 (需補齊) |

---

## 6. Security & Compliance

| Aspect | Current | Planned |
|--------|---------|---------|
| **Token TTL** | 預設 | 24h + Sliding Expiration |
| **Token Storage** | localStorage | HttpOnly Cookie |
| **CSRF Protection** | Sanctum Cookie-based | - |
| **Rate Limiting** | 未實作 | API Token Bucket |
| **Admin Protection** | 基本 Auth | Rate Limiting + IP 白名單 (選配) |
| **Log Sanitization** | 未實作 | Laravel Log Sanitizer |
| **Dependency Scanning** | 未實作 | CI/CD composer audit |
| **Error Monitoring** | 未實作 | Sentry + Error-level Only |

---

## 7. Version Constraints

```json
{
  "php": "^8.2",
  "laravel/framework": "^12.0",
  "mysql": "8.0",
  "vue": "^3.0",
  "pinia": "^2.0",
  "tailwindcss": "^3.0",
  "redis": "latest"
}
```

---

## 8. Technology Decisions Log (ADR Index)

> 待建立 `/docs/adr/` 目錄

| ID | Decision | Status |
|----|----------|--------|
| ADR-001 | 採用整數儲存金額 (Cents) | Implemented |
| ADR-002 | 訂單快照拆表 (order_snapshots + order_addresses) | Implemented |
| ADR-003 | 會員等級儲存於資料庫表而非設定檔 | Implemented |
| ADR-004 | 物流編號採 Sequence 表而非 UUID | Implemented |
| ADR-005 | 折扣率採萬分比 BPS (9500 = 95折) | Implemented |
| ADR-006 | Vuex → Pinia 遷移 | In Progress |
| ADR-007 | localStorage → HttpOnly Cookie | Planned |
