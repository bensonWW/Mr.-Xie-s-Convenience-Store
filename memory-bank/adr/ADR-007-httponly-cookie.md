# ADR-007: HttpOnly Cookie 認證

- **Status**: Planned
- **Date**: -
- **Deciders**: Development Team

## Context

SPA 應用需安全儲存認證 Token。

## Problem

當前使用 `localStorage` 儲存 Sanctum Token，存在安全風險：

1. **XSS 攻擊**：惡意腳本可讀取 `localStorage`
2. **無法自動過期**：需手動實作 Token 過期邏輯
3. **跨標籤頁問題**：Token 更新需手動同步

## Decision

**遷移至 HttpOnly Cookie 儲存認證狀態**

### 實作方案

#### 後端 (Laravel Sanctum)
```php
// config/sanctum.php
'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', 'localhost')),

// 啟用 Cookie 認證
'guard' => ['web'],
```

#### 前端 (Axios)
```javascript
// api/index.js
axios.defaults.withCredentials = true;

// 登入時不再手動儲存 Token
```

### Cookie 設定
```php
// config/session.php
'http_only' => true,
'same_site' => 'lax',
'secure' => env('SESSION_SECURE_COOKIE', true),
```

### Token TTL
- 24 小時過期
- 採滑動過期 (Sliding Expiration)：活動時自動延長

## Consequences

### Positive
- XSS 無法竊取 Token
- 瀏覽器自動管理 Cookie 生命週期
- CSRF 保護由 Sanctum 自動處理

### Negative
- 需配置 CORS 與 CSRF
- 跨域部署需額外設定
- 本地開發需使用相同域名

## Related
- ADR-008: Wallet Audit Log - 安全性相關
- Sanctum 文檔：https://laravel.com/docs/sanctum
