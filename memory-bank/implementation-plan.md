# Implementation Plan

> **Last Updated**: 2025-12-25
> **Version**: 2.0 (Cold Start Revision)

---

## Overview

本文件記錄專案的實作計畫，包含已完成階段回顧與未來待辦項目。基於冷啟動分析，識別出多項技術債與功能缺口，已納入後續階段規劃。

---

## Phase Summary

| Phase | Name | Status |
|-------|------|--------|
| 1-8 | Foundation & Initial Features | ✅ Completed |
| 9 | Major Refactoring & Integration | ✅ Completed |
| 10 | Final Polish & Launch Readiness | ✅ Completed |
| 11 | Deployment Preparation | ✅ Completed |
| 12 | Cold Start & Documentation | 🔄 In Progress |
| 13 | Security Hardening | 📋 Planned |
| 14 | Testing Enhancement | 📋 Planned |
| 15 | Feature Expansion | 📋 Planned |

---

## Completed Phases (1-11)

> 詳見 `progress.md` 完整歷史記錄

### Key Accomplishments
- ✅ Laravel 12 + Vue 3 基礎架構
- ✅ 儲值金錢包系統 (Wallet)
- ✅ 訂單系統 + 購物車
- ✅ 會員等級制度（資料庫化）
- ✅ 訂單快照正規化 (3NF)
- ✅ 品類正規化 (`categories` 表)
- ✅ Admin Dashboard + 營收報表
- ✅ Docker 開發/生產環境
- ✅ 21 個 Feature Tests

---

## Phase 12: Cold Start & Documentation (Current)

> **Goal**: 建立完整的 Memory Bank，作為專案單一事實來源

### 12.1 Memory Bank Update
- [x] PRD.md - 產品需求文檔
- [x] tech-stack.md - 技術棧與選型理由
- [x] architecture.md - 系統架構與 ADR
- [/] implementation-plan.md - 實作計畫
- [ ] progress.md - 進度狀態更新

### 12.2 Documentation Infrastructure
- [ ] 建立 `/docs/adr/` 目錄
- [ ] 初始化 ADR 模板
- [ ] API 文檔同步 (OpenAPI/Swagger)

---

## Phase 13: Security Hardening (Planned)

> **Priority**: P0-P1
> **Goal**: 強化金融交易安全性與合規性

### 13.1 Wallet Audit System (P0)
- [x] 建立 `wallet_logs` 表
  - `operator_id` - 操作人員
  - `ip_address` - 請求 IP
  - `balance_before` / `balance_after`
  - `reason` - 交易備註
- [x] 整合至 `WalletService`
- [x] 建立測試案例

### 13.2 Authentication Enhancement (P1)
- [x] Token TTL 設定 24 小時
- [x] 實作 Sliding Expiration
- [ ] 遷移至 HttpOnly Cookie (Deferred to Phase 14)

### 13.3 Admin Protection (P1)
- [x] 實作 API Rate Limiting (Token Bucket)
- [x] Admin 路由 Rate Limiting
- [ ] IP 白名單（選配）

### 13.4 Log Sanitization (P1)
- [x] 實作 Laravel Log Sanitizer
- [x] 敏感資料脫敏（密碼、Token）

### 13.5 Verification Enhancement (P2)
- [ ] 驗證碼 60 秒重發限制
- [ ] 驗證碼 15 分鐘有效期確認

### 13.6 CI/CD Security (P2)
- [ ] 整合 `composer audit`
- [ ] 整合 `npm audit`
- [ ] 自動化掃描流程

---

## Phase 14: Testing Enhancement (Planned)

> **Priority**: P1-P2
> **Goal**: 提升測試覆蓋率與品質保證

### 14.1 Backend Unit Tests (P1)
- [ ] `PriceCalculator` 單元測試
- [ ] `MemberLevelService` 單元測試
- [ ] `OrderSequenceGenerator` 邊界測試

### 14.2 Concurrency Tests (P1)
- [ ] 錢包並發交易測試
- [ ] 庫存並發扣減測試
- [ ] 訂單並發建立測試

### 14.3 Frontend Tests (P2)
- [ ] 引入 Vitest
- [ ] Cart Store 單元測試
- [ ] Auth Store 單元測試

### 14.4 E2E Tests (P2)
- [ ] 引入 Playwright
- [ ] 關鍵購物流程測試
- [ ] Admin 操作流程測試

### 14.5 CI Integration (P2)
- [ ] GitHub Actions 設定
- [ ] 測試覆蓋率報告
- [ ] PR 自動測試

---

## Phase 15: Feature Expansion (Planned)

> **Priority**: P2-P3
> **Goal**: 功能擴展與使用者體驗提升

### 15.1 Transaction Type Enhancement (P2)
- [ ] 新增 `adjustment` 交易類型
- [ ] Admin 手動調整餘額 UI
- [ ] 調整原因記錄

### 15.2 Order State Machine (P2)
- [ ] 定義正式狀態轉換圖
- [ ] **Processing → Cancelled 自動觸發退款**
- [ ] 實作 State Machine Library
- [ ] 非法轉換阻擋

### 15.3 Shipping Enhancement (P2)
- [ ] 區域運費配置
- [ ] 階梯運費計算
- [ ] 3 縣市配送區域設定

### 15.4 Inventory Features (P2)
- [ ] 庫存預警機制
- [ ] 低庫存通知
- [ ] 庫存報表

### 15.5 Wishlist Enhancement (P3)
- [ ] 商品降價通知
- [ ] 庫存補足通知
- [ ] 精準行銷基礎

### 15.6 Error Monitoring (P2)
- [ ] Sentry 整合
- [ ] Error-level Only 配置
- [ ] 即時警報設定

### 15.7 Database Maintenance (P3)
- [ ] 資料保留策略定義
- [ ] Soft Delete 清理排程
- [ ] 備份策略建立

---

## Phase 16: Architecture Optimization (Future)

> **Priority**: P3
> **Goal**: 效能優化與可擴展性

### 16.1 High-Concurrency Inventory
- [ ] Redis Lua Script 庫存扣減
- [ ] 效能測試 (>500 TPS)

### 16.2 Caching Strategy
- [ ] Settings Write-through Cache
- [ ] 產品列表 Cache

### 16.3 Multi-tenant Preparation
- [ ] `stores` 表完整實作
- [ ] 資料隔離策略

---

## Technical Debt Backlog

> 從冷啟動分析識別之技術債

| ID | Item | Priority | Phase |
|----|------|----------|-------|
| TD-001 | `wallet_logs` 審計表 | P0 | 13 |
| TD-002 | HttpOnly Cookie 遷移 | P1 | 13 |
| TD-003 | Admin Rate Limiting | P1 | 13 |
| TD-004 | Log Sanitizer | P1 | 13 |
| TD-005 | PriceCalculator 測試 | P1 | 14 |
| TD-006 | 並發交易測試 | P1 | 14 |
| TD-007 | OrderCreationService 重構（引入 CheckoutCoordinator） | P2 | 15 |
| TD-008 | 訂單狀態機 | P2 | 15 |
| TD-009 | `adjustment` 交易類型 | P2 | 15 |
| TD-010 | 區域運費 | P2 | 15 |
| TD-011 | 庫存預警 | P2 | 15 |
| TD-012 | Sentry 整合 | P2 | 15 |
| TD-013 | OpenAPI 文檔 | P3 | 12 |
| TD-014 | ADR 目錄 | P3 | 12 |
| TD-015 | Vitest 前端測試 | P2 | 14 |
| TD-016 | Playwright E2E | P2 | 14 |
| TD-017 | 殘留 Vuex 模組清理（遷移至 Pinia） | P2 | 14 |

---

## Verification Plan

### Automated Tests
```bash
# 執行所有測試
php artisan test

# 執行特定測試
php artisan test --filter Wallet
php artisan test --filter Order
```

### Manual Verification
- **Admin**：登入 → 鎖定會員等級 → 驗證自動升級跳過
- **User**：儲值 → 購買 → 驗證營收圖表更新
- **Guest**：嘗試加入購物車 → 驗證跳轉登入

---

## Dependencies & Blockers

| Blocker | Impact | Resolution |
|---------|--------|------------|
| 無 CI/CD | 無法自動化測試 | Phase 14 建立 |
| 無安全掃描 | 潛在漏洞風險 | Phase 13 整合 |
| 文檔過時 | 知識斷層 | Phase 12 更新 |
