# Architecture Decision Records (ADR)

> **Location**: `/memory-bank/adr/`
> **Last Updated**: 2025-12-25

## Overview

本目錄記錄專案的重要架構決策 (Architecture Decision Records)。每個 ADR 文件描述一個特定的技術決策，包含背景、問題、決策內容與後果分析。

## ADR 格式

每個 ADR 檔案遵循以下結構：

```markdown
# ADR-XXX: Title

- **Status**: Proposed | Implemented | Deprecated | Superseded
- **Date**: YYYY-MM-DD
- **Deciders**: Who made the decision

## Context
What is the issue that we're seeing that is motivating this decision?

## Problem
What is the specific problem we're trying to solve?

## Decision
What is the change that we're proposing and/or doing?

## Consequences
What becomes easier or more difficult to do because of this change?

## Related
Links to related ADRs or documentation.
```

---

## ADR Index

### Implemented

| ID | Title | Date | File |
|----|-------|------|------|
| ADR-001 | [整數金額儲存 (Cents)](./ADR-001-integer-cents-storage.md) | 2025-12 | `ADR-001-integer-cents-storage.md` |
| ADR-002 | [訂單快照拆表](./ADR-002-order-snapshot-tables.md) | 2025-12-22 | `ADR-002-order-snapshot-tables.md` |
| ADR-003 | [會員等級資料庫化](./ADR-003-member-level-database.md) | 2025-12-22 | `ADR-003-member-level-database.md` |
| ADR-004 | [Sequence 物流編號](./ADR-004-sequence-logistics-number.md) | 2025-12-22 | `ADR-004-sequence-logistics-number.md` |
| ADR-005 | [折扣率採萬分比 (BPS)](./ADR-005-discount-rate-bps.md) | 2025-12 | `ADR-005-discount-rate-bps.md` |

### In Progress

| ID | Title | Date | File |
|----|-------|------|------|
| ADR-006 | [Vuex → Pinia 遷移](./ADR-006-vuex-to-pinia.md) | 2025-12 | `ADR-006-vuex-to-pinia.md` |

### Planned

| ID | Title | File |
|----|-------|------|
| ADR-007 | [HttpOnly Cookie 認證](./ADR-007-httponly-cookie.md) | `ADR-007-httponly-cookie.md` |
| ADR-008 | [Wallet Audit Log Table](./ADR-008-wallet-audit-log.md) | `ADR-008-wallet-audit-log.md` |
| ADR-009 | [Order State Machine](./ADR-009-order-state-machine.md) | `ADR-009-order-state-machine.md` |
| ADR-010 | [OpenAPI 文檔自動生成](./ADR-010-openapi-documentation.md) | `ADR-010-openapi-documentation.md` |

---

## Quick Reference

### 金額與計算
- **金額單位**：整數「分 (Cents)」 → [ADR-001](./ADR-001-integer-cents-storage.md)
- **折扣率單位**：萬分比 (BPS) → [ADR-005](./ADR-005-discount-rate-bps.md)

### 資料庫設計
- **訂單快照**：拆分為 `order_addresses` + `order_snapshots` → [ADR-002](./ADR-002-order-snapshot-tables.md)
- **會員等級**：`member_levels` 表取代 config → [ADR-003](./ADR-003-member-level-database.md)
- **物流編號**：Sequence 表 + 日期前綴 → [ADR-004](./ADR-004-sequence-logistics-number.md)

### 前端架構
- **狀態管理**：Vuex → Pinia 遷移中 → [ADR-006](./ADR-006-vuex-to-pinia.md)

### 安全性
- **認證 Token**：計畫遷移至 HttpOnly Cookie → [ADR-007](./ADR-007-httponly-cookie.md)
- **審計日誌**：計畫建立 `wallet_logs` 表 → [ADR-008](./ADR-008-wallet-audit-log.md)

### 系統設計
- **訂單狀態機**：計畫正規化 → [ADR-009](./ADR-009-order-state-machine.md)
- **API 文檔**：計畫導入 Scribe/OpenAPI → [ADR-010](./ADR-010-openapi-documentation.md)
