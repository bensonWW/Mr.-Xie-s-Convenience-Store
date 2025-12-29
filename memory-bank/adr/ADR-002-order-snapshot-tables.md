# ADR-002: 訂單快照拆表

- **Status**: Implemented
- **Date**: 2025-12-22
- **Deciders**: Development Team

## Context

訂單系統需要保留下單當時的買家資訊（姓名、地址、會員等級），即使原始資料後續被修改，訂單歷史仍需準確。

## Problem

原設計使用 `orders.snapshot_data` JSON 欄位儲存快照，存在以下問題：

1. **查詢效能**：JSON 欄位難以建立有效索引
2. **報表生成**：跨訂單的會員等級統計需解析 JSON
3. **3NF 違反**：JSON 內含多個獨立實體（地址、會員等級）
4. **型別安全**：JSON Schema 難以強制約束

## Decision

**拆分為兩個獨立正規化表**

### order_addresses 表
```sql
CREATE TABLE order_addresses (
    id INT PRIMARY KEY,
    order_id INT UNIQUE NOT NULL,
    name VARCHAR(255),
    phone VARCHAR(50),
    address TEXT,
    FOREIGN KEY (order_id) REFERENCES orders(id)
);
```

### order_snapshots 表
```sql
CREATE TABLE order_snapshots (
    id INT PRIMARY KEY,
    order_id INT UNIQUE NOT NULL,
    buyer_email VARCHAR(255),
    member_level_name VARCHAR(50),
    user_name VARCHAR(255),
    FOREIGN KEY (order_id) REFERENCES orders(id)
);
```

## Consequences

### Positive
- 符合 3NF 正規化要求
- 可建立索引，提升查詢效能
- 支援 `member_level_name` 的統計分析
- 財務審計時資料結構清晰

### Negative
- 訂單建立時需額外 INSERT 操作
- 查詢時需 JOIN 多表

## Related
- 原 `orders.snapshot_data` JSON 欄位已移除
- `OrderCreationService` 已更新以建立關聯記錄
