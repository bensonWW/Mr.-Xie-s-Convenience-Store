# 前端頁面重設與標註 (Frontend Pages Redesign - Japanese Style)

> **設計理念**: "Xie Conbini" - 結合好市多 (Costco) 的量販精神與日式便利店 (Konbini) 的簡約美學。
> **風格關鍵字**: 極簡 (Minimalist)、自然 (Natural)、溫暖 (Warm).
> **更新日期**: 2025-12-26

---

## 1. 設計系統 (Design System)

### 1.1 色票 (Color Palette)
採用「低飽和自然色調」基調，搭配洗柿與淺藏青。**完整支援深色模式 (Dark Mode)**。

#### Light Mode
| 角色 | 顏色 | Tailwind Class | Hex | 說明 |
|------|------|----------------|-----|------|
| **背景** | 米白/紙白 | `bg-stone-50` | `#FAFAF9` | 全站背景，營造溫暖質感 |
| **卡片** | 純白 | `bg-white` | `#FFFFFF` | 內容區塊，搭配極細邊框 |
| **文字 (主)** | 淺藏青 | `text-slate-700` | `#334155` | 標題、主要內文 |
| **文字 (次)** | 暖灰 | `text-stone-500` | `#78716c` | 說明文字、Meta 資訊 |
| **主色 (Brand)** | 洗柿 | `bg-[#E79460]` | `#E79460` | 按鈕、活動標籤、價格 |
| **輔色 (Accent)**| 淺藏青 | `bg-slate-700` | `#334155` | 導航、主要背景區塊 |
| **邊框** | 木質灰 | `border-stone-200`| `#e7e5e4` | 區塊分隔 |

#### Dark Mode
| 角色 | 顏色 | Tailwind Class | Hex | 說明 |
|------|------|----------------|-----|------|
| **背景** | 深石板 | `dark:bg-slate-900` | `#0f172a` | 全站背景 |
| **卡片** | 石板灰 | `dark:bg-slate-800` | `#1e293b` | 內容區塊 |
| **文字 (主)** | 淺灰白 | `dark:text-stone-100` | `#f5f5f4` | 標題、主要內文 |
| **文字 (次)** | 淡灰 | `dark:text-stone-400` | `#a8a29e` | 說明文字、Meta 資訊 |
| **主色 (Brand)** | 洗柿 | `bg-[#E79460]` | `#E79460` | **保持不變** |
| **邊框** | 深石板 | `dark:border-slate-700` | `#334155` | 區塊分隔 |

### 1.2 排版 (Typography)
*   **字體**: Noto Sans TC/JP, Roboto, system-ui.
*   **導航**: 14px/16px, Medium weight.
*   **標題**: Serif (可選) 或 Bold Sans.
*   **留白 (Spacing)**: 寬鬆的 Padding，使用 `p-6` 或 `p-8` 區隔內容。

### 1.3 元件風格 (Component Style)
*   **按鈕**: `rounded-md` (微圓角), Flat Design (扁平化), 無漸層。
*   **陰影**: `shadow-sm` (極淺陰影) 或 無陰影僅邊框 (`border`).
*   **圖片**: `aspect-square` 或 `aspect-[4/3]`, `object-cover`.
*   **過渡動畫**: `transition-colors duration-300` (主題切換平滑過渡).

---

## 2. 深色模式 (Dark Mode)

### 2.1 實作方式
使用 **Tailwind CSS `dark:` variant** 搭配 **class-based** 切換。

```javascript
// tailwind.config.js
module.exports = {
  darkMode: 'class',
  // ...
}
```

### 2.2 工具函式
**路徑**: `src/utils/darkMode.js`

| 函式 | 說明 |
|------|------|
| `initDarkMode()` | 初始化深色模式，檢查 localStorage 和系統偏好 |
| `toggleDarkMode()` | 切換深色/淺色模式 |
| `setTheme(theme)` | 設定特定主題 (`'dark'`, `'light'`, `'system'`) |
| `isDark` | 響應式狀態 (`ref<boolean>`) |
| `useDarkMode()` | Vue Composable，包含完整功能 |

### 2.3 切換按鈕
位於 **NavBar** 右側，圖示會根據當前主題變化：
- 淺色模式顯示 🌙 **月亮** (fa-moon)
- 深色模式顯示 ☀️ **太陽** (fa-sun)

### 2.4 持久化
使用 `localStorage.setItem('theme', 'dark' | 'light')` 儲存使用者偏好。

---

## 3. 頁面結構 (Page Structure)

### 3.1 主要頁面 (Views)

| 頁面 | 路徑 | 功能 | Dark Mode |
|------|------|------|-----------|
| `HomeView.vue` | `/` | 首頁、Hero、分類、新品 | ✅ |
| `ItemsView.vue` | `/items` | 商品列表、篩選、分頁 | ✅ |
| `ProductDetail.vue` | `/items/:id` | 商品詳情、加入購物車 | ✅ |
| `CarView.vue` | `/car` | 購物車、訂單摘要 | ✅ |
| `ProfileView.vue` | `/profile` | 會員中心、訂單歷史 | ✅ |
| `AdminView.vue` | `/admin` | 後台管理框架 | ✅ |
| `LoginView.vue` | `/login` | 登入/註冊表單 | ✅ |

### 3.2 共用元件 (Components)

| 元件 | 路徑 | 功能 | Dark Mode |
|------|------|------|-----------|
| `MainLayout.vue` | `layouts/` | 頁面佈局框架 | ✅ |
| `NavBar.vue` | `components/` | 導航列、搜尋、主題切換 | ✅ |
| `FooterBar.vue` | `components/` | 頁尾資訊 | ✅ |
| `ProductCard.vue` | `components/` | 商品卡片 | ✅ |

### 3.3 工具函式 (Utils)

| 檔案 | 功能 |
|------|------|
| `darkMode.js` | 深色模式狀態管理與切換 |

---

## 4. Tailwind CSS 深色模式類別模式

### 4.1 背景

```html
<!-- 淺色/深色背景 -->
<div class="bg-stone-50 dark:bg-slate-900">
<div class="bg-white dark:bg-slate-800">
```

### 4.2 文字

```html
<!-- 主要文字 -->
<span class="text-slate-700 dark:text-stone-100">
<!-- 次要文字 -->
<span class="text-stone-500 dark:text-stone-400">
```

### 4.3 邊框

```html
<div class="border border-stone-200 dark:border-slate-700">
```

### 4.4 輸入框

```html
<input class="bg-stone-50 dark:bg-slate-700 border-stone-200 dark:border-slate-600 text-slate-700 dark:text-stone-200 placeholder:text-stone-400 dark:placeholder:text-stone-500">
```

### 4.5 按鈕

```html
<!-- Primary (主色保持不變) -->
<button class="bg-[#E79460] text-white hover:bg-[#cf8354]">

<!-- Secondary (深色模式調整) -->
<button class="bg-white dark:bg-transparent border-2 border-slate-700 dark:border-stone-300 text-slate-700 dark:text-stone-200 hover:bg-slate-50 dark:hover:bg-slate-700">
```

---

## 5. 使用方式

### 5.1 在元件中使用深色模式狀態

```javascript
import { isDark, toggleDarkMode } from '../utils/darkMode.js';

// 切換
toggleDarkMode();

// 讀取狀態
console.log(isDark.value); // true 或 false
```

### 5.2 使用 Composable

```javascript
import { useDarkMode } from '../utils/darkMode.js';

const { isDark, toggle, setTheme } = useDarkMode();
```

---

## 6. 檔案結構

> **說明**: 所有元件都同時支援 **淺色模式 (Light)** 和 **深色模式 (Dark)**。
> 使用 Tailwind CSS 的 `dark:` variant 在同一檔案內定義兩種主題樣式。

```
New webpage/
├── Frontend_Pages.md          # 本文件
└── src/
    ├── layouts/
    │   └── MainLayout.vue     # ☀️🌙 淺色 + 深色模式
    ├── components/
    │   ├── NavBar.vue         # ☀️🌙 淺色 + 深色模式 + 切換按鈕
    │   ├── FooterBar.vue      # ☀️🌙 淺色 + 深色模式
    │   └── ProductCard.vue    # ☀️🌙 淺色 + 深色模式
    ├── views/
    │   ├── HomeView.vue       # ☀️🌙 淺色 + 深色模式
    │   ├── ItemsView.vue      # ☀️🌙 淺色 + 深色模式
    │   ├── ProductDetail.vue  # ☀️🌙 淺色 + 深色模式
    │   ├── CarView.vue        # ☀️🌙 淺色 + 深色模式
    │   ├── ProfileView.vue    # ☀️🌙 淺色 + 深色模式
    │   ├── AdminView.vue      # ☀️🌙 淺色 + 深色模式
    │   └── LoginView.vue      # ☀️🌙 淺色 + 深色模式
    └── utils/
        └── darkMode.js        # 主題切換工具函式
```

### 6.1 主題切換原理

每個元件內的樣式都包含兩種主題：

```html
<!-- 範例：一個元素同時定義淺色和深色樣式 -->
<div class="bg-white dark:bg-slate-800 text-slate-700 dark:text-stone-100">
  <!--      ↑淺色背景  ↑深色背景       ↑淺色文字      ↑深色文字 -->
</div>
```

- **預設**: 套用淺色樣式 (`bg-white`, `text-slate-700`)
- **當 `<html class="dark">` 時**: 套用深色樣式 (`dark:bg-slate-800`, `dark:text-stone-100`)
