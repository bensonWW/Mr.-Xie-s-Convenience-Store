# Project Progress

> **Last Updated**: 2025-12-25
> **Version**: 2.0 (Cold Start Revision)

---

## Status Legend
- [ ] Todo
- [/] In Progress
- [x] Done

---

## Current Status

| Metric | Value |
|--------|-------|
| **Current Phase** | Phase 12 - Cold Start & Documentation |
| **Overall Progress** | ~85% MVP Complete |
| **Technical Debt Items** | 16 identified |
| **Test Coverage** | Partial (21 Feature Tests, no coverage report) |

---

## Milestones

### Phase 1-8: Foundation & Initial Features
- [x] Create Memory Bank
- [x] Update to Laravel 12.0
- [x] Stabilize Docker Environment
- [x] Wallet Database Migrations
- [x] WalletService Implementation
- [x] WalletController & Routes
- [x] Automated Tests (Wallet)
- [x] Gap Analysis
- [x] User Profile - Wallet View
- [x] Top-up Modal (Mock)
- [x] Checkout with Wallet Balance
- [x] Full E2E Test (Register → Topup → Shop)

### Phase 9: Major Refactoring & Features Integration
- [x] Admin & Security Hardening
  - Removed `VITE_BYPASS_AUTH_DEV`
  - Logout clears `localStorage`
  - Added `is_level_locked` to users
- [x] Database Schema & Settings
  - Created `settings` table
  - Created `addresses` table
  - 3NF normalization audit
- [x] Frontend Experience Refinement
  - City/District selectors
  - Guest cart badge hidden
  - Server-side order filtering
  - Original/Sale price display
- [x] Smart Payment & Logistics
  - Auto-generate logistics number
  - Wallet-only payment enforcement
- [x] Admin Dashboard & Stats
  - Revenue chart (Last 7 days)
  - Order status modal confirmation
- [x] Comprehensive Database Audit
  - Performance indexes added
  - Soft deletes enforced

### Phase 10: Final Polish & Launch Readiness
- [x] Free Shipping & Add-on Logic
- [x] System-wide Verification (SystemSmokeTest)

### Phase 11: Deployment Preparation
- [x] Frontend Production Build (`npm run build`)
- [x] Nginx Production Config
- [x] Docker Production Configuration
- [x] Final Launch Verification (Dry Run)

### Phase 12: Cold Start & Documentation (Current)
- [x] Clarification Phase (72 questions answered)
- [x] PRD.md updated
- [x] tech-stack.md updated
- [x] architecture.md updated
- [x] implementation-plan.md updated
- [x] progress.md updated
- [x] ADR directory creation (`/memory-bank/adr/`)
- [x] 10 individual ADR files created
- [x] OpenAPI/Scribe 整合（ADR-010）

### Phase 13: Security Hardening (Complete)
- [x] 13.1 Wallet Audit System (P0)
  - Created `wallet_logs` migration
  - Created `WalletLog` model with checksum verification
  - Integrated audit logging into `WalletService`
  - Created `WalletAuditTest` (7 tests passing)
- [/] 13.2 Authentication Enhancement (P1)
  - Token Expiration set to 24h (ADR-007)
  - Implemented Sliding Expiration via `RefreshTokenExpiration` middleware
  - *HttpOnly Cookie migration deferred to Phase 14*
- [x] 13.3 Admin Protection (P1)
  - Implemented `throttle:admin` (60 req/min)
- [x] 13.4 Log Sanitization (P1)
  - Created `SanitizeLogProcessor` to redact sensitive fields
  - Configured `logging.php` to use sanitizer on `single` and `daily` channels
- [x] 13.5 Verification Enhancement (P2)
  - 60s Resend Limit & 15-min TTL
- [x] 13.6 CI/CD Security (P2)
  - Added Github Actions Security Scan & Makefile audit targets

---

## Recent Changes (2025-12-25)

### Memory Bank Cold Start
基於 72 個澄清問題的回覆，完整更新 Memory Bank：

1. **PRD.md**
   - 新增商業模式定義（單店鋪 B2C）
   - 新增使用者角色詳細權限
   - 新增核心功能規格（錢包、訂單、會員等級）
   - 新增 Known Gaps & Technical Debt 清單

2. **tech-stack.md**
   - 新增技術選型理由
   - 新增版本約束
   - 新增 Security & Compliance 規劃
   - 新增 ADR Index

3. **architecture.md**
   - 新增系統架構圖
   - 新增 Service Layer 職責說明
   - 新增資料庫設計細節
   - 新增訂單狀態機草案
   - 新增 9 個 ADR 條目

4. **implementation-plan.md**
   - 新增 Phase 13-16 未來規劃
   - 新增 16 項 Technical Debt Backlog
   - 新增依賴與阻擋項目

---

## Known Issues & Technical Debt

### P0 - Critical
| Issue | Description | Status |
|-------|-------------|--------|
| Wallet Audit | 缺少 `wallet_logs` 審計表 | Planned (Phase 13) |

### P1 - High Priority
| Issue | Description | Status |
|-------|-------------|--------|
| HttpOnly Cookie | Token 儲存於 localStorage | Planned (Phase 13) |
| Admin Rate Limiting | 無 API 限流保護 | Planned (Phase 13) |
| Log Sanitizer | 日誌未脫敏 | Planned (Phase 13) |
| PriceCalculator Tests | 缺少單元測試 | Planned (Phase 14) |
| Concurrency Tests | 缺少並發測試 | Planned (Phase 14) |

### P2 - Medium Priority
| Issue | Description | Status |
|-------|-------------|--------|
| OrderCreationService | 付款邏輯待重構 | Planned (Phase 15) |
| Order State Machine | 狀態轉換未正規化 | Planned (Phase 15) |
| ADJUSTMENT Type | 缺少調整交易類型 | Planned (Phase 15) |
| Regional Shipping | 區域運費未實作 | Planned (Phase 15) |
| Inventory Alert | 庫存預警未實作 | Planned (Phase 15) |
| Sentry Integration | 無錯誤監控 | Planned (Phase 15) |
| Vuex Cleanup | 殘留 Vuex 模組清理 | Planned (Phase 14) |

### P3 - Low Priority
| Issue | Description | Status |
|-------|-------------|--------|
| ~~OpenAPI Docs~~ | ~~API 文檔未同步~~ | ✅ Done (Scribe) |
| ~~ADR Directory~~ | ~~ADR 目錄未建立~~ | ✅ Done |
| Frontend Tests | 無 Vitest 測試 | Planned (Phase 14) |
| E2E Tests | 無 Playwright 測試 | Planned (Phase 14) |

---

## Test Results Summary

### Feature Tests (21 total)
```
✓ AdminAuthTest
✓ AdminUserUpdateTest
✓ AdminWalletTest
✓ CouponTest
✓ DashboardStatsTest
✓ MemberLevelSeedingTest
✓ MemberLevelTest
✓ MemberUpgradeTest
✓ OrderCreationServiceTest
✓ OrderRefundTest
✓ OrderSequenceGeneratorTest
✓ OrderSnapshotTest
✓ Phase9Step3Test
✓ Phase9Step4Test
✓ SchemaAuditTest
✓ SchemaIntegrityTest
✓ ShippingFeeTest
✓ SystemSmokeTest
✓ WalletE2ETest
✓ WalletTest
✓ ExampleTest
```

### Coverage Gaps
- [ ] PriceCalculator unit tests
- [ ] Concurrency scenarios
- [ ] Frontend component tests
- [ ] E2E browser tests

---

## Next Steps

### Immediate (Phase 12 Completion)
1. [ ] 提交 Memory Bank 更新供審核
2. [ ] 建立 `/docs/adr/` 目錄
3. [ ] 初始化 ADR 模板

### Short-term (Phase 13)
1. [ ] 建立 `wallet_logs` 審計表
2. [ ] 實作 HttpOnly Cookie
3. [ ] 實作 Admin Rate Limiting

### Medium-term (Phase 14-15)
1. [ ] 補齊單元測試
2. [ ] 建立 CI/CD Pipeline
3. [ ] 新增 `adjustment` 交易類型
4. [ ] 實作訂單狀態機

---

## Deployment Status

| Environment | Status | URL |
|-------------|--------|-----|
| Development | ✅ Active | localhost:8080 (Frontend), localhost:8000 (API) |
| Production | ✅ Ready | localhost (Nginx) |

### Production Containers
- `nginx-prod` - Static Vue + API Proxy
- `app` - Laravel + PHP-FPM
- `db` - MySQL 8.0
- `redis` - Cache/Session/Queue
