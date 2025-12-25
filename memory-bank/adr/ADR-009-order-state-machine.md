# ADR-009: Order State Machine

- **Status**: Planned
- **Date**: -
- **Deciders**: Development Team

## Context

訂單系統需管理複雜的狀態轉換流程。

## Problem

當前狀態管理存在以下問題：

1. **轉換邏輯分散**：狀態檢查散落於多個 Controller 方法
2. **缺乏強制約束**：非法轉換（如 delivered → pending）未被阻擋
3. **無自動動作**：Processing → Cancelled 應自動觸發退款
4. **難以追蹤**：狀態變更歷史未記錄

## Decision

**引入正式狀態機 (State Machine) 實作**

### 狀態定義
```php
enum OrderStatus: string {
    case PENDING_PAYMENT = 'pending_payment';
    case PROCESSING = 'processing';
    case SHIPPED = 'shipped';
    case DELIVERED = 'delivered';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
    case REFUNDED = 'refunded';
}
```

### 轉換圖
```
pending_payment ──┬──→ processing ──┬──→ shipped ──→ delivered ──→ completed
                  │                 │
                  ↓                 ↓
              cancelled ◄───── cancelled
                  │                 │
                  ↓                 ↓
              refunded          refunded
```

### 轉換規則
| From | To | Condition | Auto Action |
|------|-----|-----------|-------------|
| pending_payment | processing | 付款成功 | - |
| pending_payment | cancelled | 用戶取消/逾時 | - |
| processing | shipped | 管理員發貨 | - |
| processing | cancelled | 用戶/管理員取消 | **自動退款** |
| cancelled | refunded | 系統觸發 | 錢包入帳 |
| shipped | delivered | 物流確認 | - |
| delivered | completed | 用戶確認/自動 | - |

### 實作選項
1. **Laravel 套件**：`spatie/laravel-model-states`
2. **自定義 Trait**：`HasStateMachine`
3. **Service 封裝**：`OrderStateMachine`

### 推薦方案：自定義 Service
```php
class OrderStateMachine
{
    private array $transitions = [
        'pending_payment' => ['processing', 'cancelled'],
        'processing' => ['shipped', 'cancelled'],
        'cancelled' => ['refunded'],
        'shipped' => ['delivered'],
        'delivered' => ['completed'],
    ];
    
    public function canTransition(Order $order, string $newStatus): bool
    {
        return in_array($newStatus, $this->transitions[$order->status] ?? []);
    }
    
    public function transition(Order $order, string $newStatus): void
    {
        if (!$this->canTransition($order, $newStatus)) {
            throw new InvalidStateTransitionException();
        }
        
        $order->status = $newStatus;
        $order->save();
        
        // 觸發自動動作
        $this->handleAutoActions($order, $newStatus);
    }
    
    private function handleAutoActions(Order $order, string $newStatus): void
    {
        if ($newStatus === 'cancelled' && $order->getOriginal('status') === 'processing') {
            // 自動觸發退款
            dispatch(new RefundOrderJob($order));
        }
    }
}
```

## Consequences

### Positive
- 狀態轉換集中管理
- 非法轉換被強制阻擋
- 自動動作可定義觸發
- 便於新增狀態與轉換

### Negative
- 需重構現有 Controller 邏輯
- 增加學習曲線

## Related
- ADR-008: Wallet Audit Log - 退款動作記錄
- `OrderService` 現有實作需遷移
