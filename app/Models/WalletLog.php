<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * WalletLog Model
 * 
 * 錢包交易審計日誌，記錄所有金融交易的完整軌跡
 * 符合 ADR-008 規範
 */
class WalletLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'wallet_transaction_id',
        'user_id',
        'operator_id',
        'operator_type',
        'action',
        'amount',
        'balance_before',
        'balance_after',
        'ip_address',
        'user_agent',
        'reason',
        'checksum',
        'created_at',
    ];

    protected $casts = [
        'amount' => 'integer',
        'balance_before' => 'integer',
        'balance_after' => 'integer',
        'created_at' => 'datetime',
    ];

    /**
     * 關聯的錢包交易
     */
    public function walletTransaction(): BelongsTo
    {
        return $this->belongsTo(WalletTransaction::class);
    }

    /**
     * 被操作的用戶
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 操作人員
     */
    public function operator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    /**
     * 生成防竄改校驗碼
     */
    public static function generateChecksum(
        int $transactionId,
        int $amount,
        int $balanceBefore,
        int $balanceAfter
    ): string {
        $secret = config('app.key');
        $data = "{$transactionId}|{$amount}|{$balanceBefore}|{$balanceAfter}|{$secret}";
        return hash('sha256', $data);
    }

    /**
     * 驗證校驗碼
     */
    public function verifyChecksum(): bool
    {
        $expected = self::generateChecksum(
            $this->wallet_transaction_id,
            $this->amount,
            $this->balance_before,
            $this->balance_after
        );
        return hash_equals($expected, $this->checksum ?? '');
    }
}
