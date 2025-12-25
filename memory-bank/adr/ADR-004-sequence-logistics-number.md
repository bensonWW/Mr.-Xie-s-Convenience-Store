# ADR-004: Sequence 物流編號 vs UUID

- **Status**: Implemented
- **Date**: 2025-12-22
- **Deciders**: Development Team

## Context

訂單系統需為每筆訂單生成唯一的物流追蹤編號。

## Problem

需選擇物流編號的生成策略：

| 方案 | 格式範例 | 優點 | 缺點 |
|------|----------|------|------|
| UUID | `550e8400-e29b-41d4-...` | 全域唯一、無需中央協調 | 難以閱讀、物流商不友善 |
| Sequence | `LOGI-20251225-0001` | 易讀、符合業務習慣 | 需中央序號管理 |
| Snowflake | `1234567890123456789` | 分散式友好、時序性 | 對物流商仍不夠直觀 |

## Decision

**採用 Sequence 表配合日期前綴**

### 格式
```
LOGI-{YYYYMMDD}-{4位序號}
```
範例：`LOGI-20251225-0042`

### 實作
```sql
CREATE TABLE sequences (
    id INT PRIMARY KEY,
    name VARCHAR(50) UNIQUE NOT NULL,
    current_value INT DEFAULT 0,
    prefix_date DATE
);
```

### 生成邏輯 (OrderSequenceGenerator)
```php
public function generateLogisticsNumber(): string
{
    $today = now()->format('Ymd');
    
    DB::transaction(function () use ($today, &$sequence) {
        $seq = Sequence::where('name', 'logistics')
            ->lockForUpdate()
            ->first();
            
        if ($seq->prefix_date !== $today) {
            $seq->current_value = 0;
            $seq->prefix_date = $today;
        }
        
        $seq->current_value++;
        $seq->save();
        
        $sequence = $seq->current_value;
    });
    
    return sprintf('LOGI-%s-%04d', $today, $sequence);
}
```

### 循環設計
當序號達到 INT 上限時，自動循環重用（實務上每日歸零已足夠）。

## Consequences

### Positive
- 物流商與客服人員易於辨識
- 日期前綴便於按時間範圍查詢
- 序號長度可控（4-6 位）

### Negative
- 需資料庫行鎖，高併發時可能成為瓶頸
- 分散式部署需額外考量序號同步

## Related
- `OrderSequenceGenerator` 服務實作
- 高併發優化可考慮 Redis INCR
