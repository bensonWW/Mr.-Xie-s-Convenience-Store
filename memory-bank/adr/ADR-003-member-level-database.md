# ADR-003: 會員等級資料庫化

- **Status**: Implemented
- **Date**: 2025-12-22
- **Deciders**: Development Team

## Context

會員等級系統需定義等級名稱、升級門檻、折扣率等配置。

## Problem

原設計將等級定義寫死於設定檔 (`config/shop.php`)，存在以下問題：

1. **變更需部署**：修改等級門檻需重新部署應用
2. **缺乏歷史記錄**：無法追蹤等級配置變更
3. **外鍵約束困難**：用戶的 `member_level` 字串無法與設定檔建立關聯

## Decision

**建立 `member_levels` 資料表儲存等級配置**

### 表結構
```sql
CREATE TABLE member_levels (
    id INT PRIMARY KEY,
    slug VARCHAR(50) UNIQUE NOT NULL,  -- 'normal', 'vip', 'platinum'
    name VARCHAR(100) NOT NULL,         -- 顯示名稱
    min_spent INT NOT NULL,             -- 最低消費門檻 (cents)
    discount_rate INT NOT NULL,         -- 折扣率 (BPS, 萬分比)
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### 用戶關聯
```sql
ALTER TABLE users ADD COLUMN member_level_id INT;
ALTER TABLE users ADD FOREIGN KEY (member_level_id) REFERENCES member_levels(id);
```

### 折扣率單位
採用萬分比 (Basis Points, BPS)：
- 95 折 = 9500 BPS
- 計算：`(price * discount_rate) / 10000`

## Consequences

### Positive
- 可透過後台動態調整等級配置
- 支援外鍵約束，確保資料完整性
- 便於擴展新等級

### Negative
- 需資料庫遷移與資料初始化
- 查詢時需 JOIN `member_levels` 表

## Related
- ADR-001: 折扣率同樣採用整數化原則 (BPS)
- `MemberLevelService` 負責升級邏輯
