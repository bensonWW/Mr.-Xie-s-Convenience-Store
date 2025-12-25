# ADR-008: Wallet Audit Log Table

- **Status**: Planned
- **Date**: -
- **Deciders**: Development Team

## Context

錢包系統處理金融交易，需符合審計與合規要求。

## Problem

當前 `wallet_transactions` 表缺少完整的審計資訊：

1. **操作人員不明**：無法追蹤「誰」觸發了交易
2. **缺少 IP 記錄**：無法定位異常請求來源
3. **餘額變化不可見**：需額外計算才能得知交易前後餘額
4. **無防竄改機制**：記錄可被修改

## Decision

**建立獨立的 `wallet_logs` 審計表**

### 表結構
```sql
CREATE TABLE wallet_logs (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    wallet_transaction_id INT NOT NULL,
    user_id INT NOT NULL,
    operator_id INT NULL,           -- 操作人員（管理員或 NULL 表示用戶本人）
    operator_type VARCHAR(50),      -- 'user', 'admin', 'system'
    action VARCHAR(50) NOT NULL,    -- 'deposit', 'payment', 'refund', 'adjustment'
    amount INT NOT NULL,            -- cents
    balance_before INT NOT NULL,    -- 交易前餘額
    balance_after INT NOT NULL,     -- 交易後餘額
    ip_address VARCHAR(45),         -- IPv4/IPv6
    user_agent TEXT,
    reason TEXT,                    -- 交易備註/原因
    created_at TIMESTAMP NOT NULL,
    
    -- 防竄改
    checksum VARCHAR(64),           -- SHA256(id + amount + balance_before + balance_after + secret)
    
    FOREIGN KEY (wallet_transaction_id) REFERENCES wallet_transactions(id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    INDEX idx_user_created (user_id, created_at),
    INDEX idx_operator (operator_id)
);
```

### 寫入時機
```php
// WalletService::processTransaction()
$transaction = WalletTransaction::create([...]);

WalletLog::create([
    'wallet_transaction_id' => $transaction->id,
    'user_id' => $user->id,
    'operator_id' => auth()->id(),
    'operator_type' => auth()->user()?->isAdmin() ? 'admin' : 'user',
    'action' => $type,
    'amount' => $amount,
    'balance_before' => $lockedUser->balance - $amount,
    'balance_after' => $lockedUser->balance,
    'ip_address' => request()->ip(),
    'user_agent' => request()->userAgent(),
    'reason' => $description,
    'checksum' => $this->generateChecksum(...),
]);
```

## Consequences

### Positive
- 完整審計軌跡，符合財務合規
- 可追蹤異常交易來源
- Checksum 提供防竄改保護

### Negative
- 每筆交易額外寫入開銷
- 需定期歸檔舊日誌

## Related
- ADR-001: 金額整數化 - `amount`, `balance_*` 欄位
- TD-001: 優先級 P0
