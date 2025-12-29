<?php

namespace App\Logging;

use Monolog\LogRecord;
use Monolog\Processor\ProcessorInterface;

class SanitizeLogProcessor implements ProcessorInterface
{
    /**
     * 敏感字段列表
     */
    protected array $sensitiveFields = [
        'password',
        'password_confirmation',
        'token',
        'access_token',
        'refresh_token',
        'credit_card',
        'cvv',
        'secret',
        'authorization', // Header
    ];

    public function __invoke(LogRecord $record): LogRecord
    {
        // 遞歸處理 context
        return $record->with(context: $this->sanitize($record->context));
    }

    protected function sanitize(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->sanitize($value);
            } elseif (is_string($key) && $this->isSensitive($key)) {
                $data[$key] = '[REDACTED]';
            }
        }
        return $data;
    }

    protected function isSensitive(string $key): bool
    {
        $key = strtolower($key);
        foreach ($this->sensitiveFields as $field) {
            if (str_contains($key, $field)) {
                return true;
            }
        }
        return false;
    }
}
