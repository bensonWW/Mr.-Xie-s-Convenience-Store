# ADR-006: Vuex → Pinia 遷移

- **Status**: In Progress
- **Date**: 2025-12
- **Deciders**: Development Team

## Context

前端 Vue 3 應用需要狀態管理解決方案。

## Problem

原專案使用 Vuex 4.x，但面臨以下問題：

1. **Boilerplate 冗長**：mutations、actions、getters 分離導致程式碼量大
2. **TypeScript 支援弱**：型別推斷不完善
3. **官方推薦變更**：Vue 3 官方已推薦 Pinia 取代 Vuex

## Decision

**從 Vuex 遷移至 Pinia**

### 主要變更

#### Vuex 結構
```javascript
// store/modules/cart.js
export default {
  namespaced: true,
  state: () => ({ count: 0 }),
  mutations: {
    SET_COUNT(state, count) { state.count = count; }
  },
  actions: {
    async fetchCount({ commit }) {
      const { data } = await api.getCartCount();
      commit('SET_COUNT', data.count);
    }
  }
};
```

#### Pinia 結構
```javascript
// stores/cart.js
export const useCartStore = defineStore('cart', {
  state: () => ({ count: 0 }),
  actions: {
    async fetchCount() {
      const { data } = await api.getCartCount();
      this.count = data.count;
    }
  }
});
```

### 遷移計畫
1. 安裝 Pinia
2. 逐模組遷移（auth → cart → ...）
3. 更新組件引用
4. 移除 Vuex 依賴

## Consequences

### Positive
- 程式碼更簡潔（無 mutations）
- 更好的 TypeScript 支援
- Vue DevTools 整合更佳
- 官方長期支援

### Negative
- 需逐步遷移現有程式碼
- 團隊需學習新 API

## Related
- 現有模組：`auth`, `cart`
- 遷移進度追蹤於 `progress.md`
