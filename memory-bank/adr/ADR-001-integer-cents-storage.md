# ADR-001: 整數金額儲存 (Cents)

- **Status**: Implemented
- **Date**: 2025-12
- **Deciders**: Development Team

## Context

電商平台涉及大量金融計算（商品價格、運費、折扣、錢包餘額），需要確保計算精確度。

## Problem

使用浮點數 (FLOAT/DOUBLE) 或 DECIMAL 儲存金額時，可能在以下場景產生精度問題：
- 多次運算累積誤差（如 0.1 + 0.2 ≠ 0.3）
- 不同程式語言/資料庫間的精度處理差異
- 折扣計算時的四捨五入問題

## Decision

**所有金額以整數「分 (Cents)」儲存**

### 影響欄位
- `users.balance`
- `orders.total_amount`
- `orders.discount_amount`
- `orders.shipping_fee`
- `products.price`
- `products.original_price`
- `wallet_transactions.amount`
- `coupons.discount_value` (固定金額類型)

### 計算規則
```php
// 儲存：元 → 分
$cents = (int) ($dollars * 100);

// 顯示：分 → 元
$dollars = $cents / 100;
```

## Consequences

### Positive
- 消除浮點數精度問題
- 資料庫索引效能更佳
- 簡化跨語言一致性（PHP、JavaScript、MySQL）

### Negative
- 前端需統一處理顯示轉換
- 匯入舊資料需進行轉換

## Related
- ADR-005: 折扣率採萬分比 (BPS) - 同樣原則應用於比例計算
