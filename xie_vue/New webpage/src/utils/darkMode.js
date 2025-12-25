/**
 * Dark Mode Utility
 * 深色模式工具函式
 */

import { ref, onMounted, onUnmounted } from 'vue';

// 響應式的深色模式狀態
export const isDark = ref(false);

/**
 * 初始化深色模式
 * 優先使用 localStorage，其次跟隨系統偏好
 */
export function initDarkMode() {
    const saved = localStorage.getItem('theme');

    if (saved === 'dark') {
        isDark.value = true;
        document.documentElement.classList.add('dark');
    } else if (saved === 'light') {
        isDark.value = false;
        document.documentElement.classList.remove('dark');
    } else {
        // 跟隨系統偏好
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        isDark.value = prefersDark;
        if (prefersDark) {
            document.documentElement.classList.add('dark');
        }
    }
}

/**
 * 切換深色模式
 */
export function toggleDarkMode() {
    isDark.value = !isDark.value;

    if (isDark.value) {
        document.documentElement.classList.add('dark');
        localStorage.setItem('theme', 'dark');
    } else {
        document.documentElement.classList.remove('dark');
        localStorage.setItem('theme', 'light');
    }

    return isDark.value;
}

/**
 * 設定特定主題
 * @param {'dark' | 'light' | 'system'} theme
 */
export function setTheme(theme) {
    if (theme === 'system') {
        localStorage.removeItem('theme');
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        isDark.value = prefersDark;
        document.documentElement.classList.toggle('dark', prefersDark);
    } else {
        localStorage.setItem('theme', theme);
        isDark.value = theme === 'dark';
        document.documentElement.classList.toggle('dark', isDark.value);
    }
}

/**
 * Composable: 使用深色模式
 * 使用方式: const { isDark, toggle } = useDarkMode();
 */
export function useDarkMode() {
    let mediaQuery = null;

    const handleChange = (e) => {
        // 只有在沒有手動設定時才跟隨系統
        if (!localStorage.getItem('theme')) {
            isDark.value = e.matches;
            document.documentElement.classList.toggle('dark', e.matches);
        }
    };

    onMounted(() => {
        initDarkMode();
        mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
        mediaQuery.addEventListener('change', handleChange);
    });

    onUnmounted(() => {
        if (mediaQuery) {
            mediaQuery.removeEventListener('change', handleChange);
        }
    });

    return {
        isDark,
        toggle: toggleDarkMode,
        setTheme
    };
}
