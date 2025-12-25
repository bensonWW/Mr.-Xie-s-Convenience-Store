# ADR-005: 折扣率採萬分比 (BPS)

- **Status**: Implemented
- **Date**: 2025-12
- **Deciders**: Development Team

## Context

會員等級與優惠券系統需儲存折扣率（如 95 折、9 折）。

## Problem

折扣率儲存方式的選擇：

| 方案 | 95 折表示 | 計算方式 | 風險 |
|------|-----------|----------|------|
| 百分比整數 | 95 | `price * 95 / 100` | 無法表示 0.5% 等精細折扣 |
| 小數比例 | 0.95 | `price * 0.95` | 浮點數精度問題 |
| 萬分比 (BPS) | 9500 | `price * 9500 / 10000` | 需額外說明單位 |

## Decision

**統一採用萬分比 (Basis Points, BPS)**

### 轉換規則
- 95 折 = 9500 BPS
- 85 折 = 8500 BPS
- 99.5 折 = 9950 BPS

### 計算公式
```php
// 計算折後價（無條件捨去）
$discountedPrice = (int) floor($originalPrice * $discountBps / 10000);

// 計算折扣金額
$discountAmount = $originalPrice - $discountedPrice;
```

> [!NOTE]
> **取整規則**：採用「無條件捨去 (Floor)」而非四捨五入，避免超出用戶預算或產生微小差額。
> 這在電商領域是常見做法，對消費者較友善。

### 欄位影響
- `member_levels.discount_rate` - INT (BPS)
- `coupons.discount_value` - INT (固定金額為 cents，百分比為 BPS)

## Consequences

### Positive
- 與金額整數化 (ADR-001) 原則一致
- 完全消除浮點數精度問題
- 支援精細到 0.01% 的折扣表示

### Negative
- 開發人員需理解 BPS 概念
- 後台 UI 需進行單位轉換顯示

## Related
- ADR-001: 整數金額儲存 (Cents) - 同樣整數化原則
- ADR-003: 會員等級資料庫化 - 使用此折扣率格式
