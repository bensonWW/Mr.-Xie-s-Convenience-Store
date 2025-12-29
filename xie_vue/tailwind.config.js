/** @type {import('tailwindcss').Config} */
module.exports = {
    darkMode: 'class',
    content: ['./src/**/*.{vue,js,ts}', './public/index.html'],
    theme: {
        extend: {
            colors: {
                // 品牌色
                xieOrange: '#E79460',
                // 保留舊的 xieBlue 以便漸進式遷移
                xieBlue: '#334155',
                // 木質色調
                wood: {
                    50: '#d9cdb4',   // 麥稈色 (木質感米色)
                    100: '#DCCDB8',  // 次要區塊
                    200: '#CEBFA6',  // 邊框
                    300: '#BFB094',  // 深米色
                    400: '#A89880',  // 木質色
                    500: '#8C7A64',  // 深木色
                },
            },

            zIndex: {
                'dropdown': '40',
                'sticky': '50',
                'modal': '60',
                'toast': '70',
            },
            fontFamily: {
                sans: ['"Noto Sans TC"', 'Inter', 'system-ui', 'sans-serif'],
            },
        },
    },
    plugins: [],
}
