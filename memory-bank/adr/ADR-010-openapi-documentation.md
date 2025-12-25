# ADR-010: OpenAPI 文檔自動生成

- **Status**: Planned
- **Date**: -
- **Deciders**: Development Team

## Context

API 文檔需與程式碼保持同步，並提供互動式測試介面。

## Problem

當前 `API_REFERENCE.md` 為手動維護，存在以下問題：

1. **同步困難**：API 變更後文檔易過時
2. **無法測試**：Markdown 文檔不支援直接呼叫
3. **缺乏規範**：參數、回應格式描述不統一

## Decision

**導入 OpenAPI 自動生成工具**

### 候選方案比較

| 工具 | 優點 | 缺點 |
|------|------|------|
| **Scribe** | Laravel 專屬、註解簡潔、支援 Try It | 需額外學習註解語法 |
| **L5-Swagger** | OpenAPI 標準、生態豐富 | 註解較冗長 |
| **Scramble** | 零配置、自動推斷 | 較新，社群較小 |

### 推薦方案：Scribe

#### 安裝
```bash
composer require --dev knuckleswtf/scribe
php artisan vendor:publish --tag=scribe-config
```

#### 註解範例
```php
/**
 * Get product details
 *
 * @urlParam id integer required The product ID. Example: 1
 *
 * @response 200 {
 *   "id": 1,
 *   "name": "Example Product",
 *   "price": 9900,
 *   "stock": 50
 * }
 *
 * @response 404 {
 *   "message": "Product not found"
 * }
 */
public function show(int $id): JsonResponse
```

#### 生成文檔
```bash
php artisan scribe:generate
```

### 輸出位置
- **靜態文檔**：`public/docs/`
- **OpenAPI JSON**：`public/docs/openapi.yaml`

### CI 整合
```yaml
# .github/workflows/docs.yml
- name: Generate API Docs
  run: php artisan scribe:generate --force
- name: Commit docs
  uses: stefanzweifel/git-auto-commit-action@v4
```

## Consequences

### Positive
- 文檔與程式碼同步
- 提供互動式 "Try It" 測試
- 可匯出 Postman Collection
- 支援 CI 自動更新

### Negative
- 需為每個 endpoint 添加註解
- 學習曲線（但較低）

## Related
- `API_REFERENCE.md` 將逐步廢棄
- TD-013: OpenAPI 文檔（Phase 12）
