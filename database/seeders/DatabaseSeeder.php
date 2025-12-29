<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\MemberLevel;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Member Levels first (required for normalization)
        $this->seedMemberLevels();

        // Create Admin
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Create Store
        $store = Store::create([
            'name' => 'Mr. Xie\'s Convenience Store',
            'email' => 'store@example.com',
            'address' => '123 Main St',
            'phone' => '123-456-7890',
        ]);

        // Create Staff
        User::create([
            'name' => 'Staff User',
            'email' => 'staff@example.com',
            'password' => Hash::make('password'),
            'role' => 'staff',
            'store_id' => $store->id,
        ]);

        // Create Categories first
        $categories = $this->seedCategories();

        $products = [
            [
                "name" => "Apple iPhone 15 Pro Max",
                "price" => 48900,
                "image" => "123.png",
                "category" => "手機",
                "information" => "Apple 最新旗艦機，搭載 A17 晶片、ProMotion 顯示器與專業級相機模組。",
                "stock" => 100
            ],
            [
                "name" => "Samsung Galaxy S24 Ultra",
                "price" => 42900,
                "image" => "123.png",
                "category" => "手機",
                "information" => "三星旗艦，擁有頂級相機與大電池，適合攝影與重度使用者。",
                "stock" => 100
            ],
            [
                "name" => "Xiaomi 14 Ultra",
                "price" => 35999,
                "image" => "123.png",
                "category" => "手機",
                "information" => "小米高階機種，性價比高，具備大容量記憶體與快速充電。",
                "stock" => 100
            ],
            [
                "name" => "Google Pixel 8",
                "price" => 26900,
                "image" => "123.png",
                "category" => "手機",
                "information" => "Google 原生 Android 體驗，AI 功能與拍照表現優異。",
                "stock" => 100
            ],
            [
                "name" => "OnePlus 12",
                "price" => 29900,
                "image" => "123.png",
                "category" => "手機",
                "information" => "旗艦級效能並重視系統流暢度，充電速度快。",
                "stock" => 100
            ],
            [
                "name" => "Dyson V12 無線吸塵器",
                "price" => 18900,
                "image" => "123.png",
                "category" => "家電",
                "information" => "Dyson 技術的小型無線吸塵器，吸力強、方便收納。",
                "stock" => 100
            ],
            [
                "name" => "Panasonic 國際牌 變頻冷氣",
                "price" => 28900,
                "image" => "123.png",
                "category" => "家電",
                "information" => "節能變頻冷氣，適合居家長時間使用並降低電費。",
                "stock" => 100
            ],
            [
                "name" => "SK-II 青春露 230ml",
                "price" => 3999,
                "image" => "123.png",
                "category" => "美妝",
                "information" => "經典護膚精華，幫助改善肌膚質感與光澤。",
                "stock" => 100
            ],
            [
                "name" => "資生堂紅妍肌活露",
                "price" => 2999,
                "image" => "123.png",
                "category" => "美妝",
                "information" => "資生堂高效保濕精華，適合乾燥肌膚。",
                "stock" => 100
            ],
            [
                "name" => "義美小泡芙",
                "price" => 39,
                "image" => "123.png",
                "category" => "食品",
                "information" => "酥脆外皮搭配香甜內餡，零食首選。",
                "stock" => 100
            ],
            [
                "name" => "統一肉燥麵",
                "price" => 15,
                "image" => "123.png",
                "category" => "食品",
                "information" => "經典速食麵，方便又有家常風味。",
                "stock" => 100
            ],
            [
                "name" => "五月花抽取式衛生紙",
                "price" => 199,
                "image" => "123.png",
                "category" => "日用品",
                "information" => "高品質抽取式衛生紙，柔軟且不易掉屑。",
                "stock" => 100
            ],
            [
                "name" => "舒潔棉柔衛生紙",
                "price" => 189,
                "image" => "123.png",
                "category" => "日用品",
                "information" => "舒潔品牌，親膚且吸水性佳。",
                "stock" => 100
            ],
            [
                "name" => "LEGO 樂高城市系列",
                "price" => 1299,
                "image" => "123.png",
                "category" => "玩具",
                "information" => "經典拼砌玩具，適合親子共玩與創意發揮。",
                "stock" => 100
            ],
            [
                "name" => "NERF 火力發射槍",
                "price" => 799,
                "image" => "123.png",
                "category" => "玩具",
                "information" => "戶外競賽玩具，安全發射軟彈。",
                "stock" => 100
            ],
            [
                "name" => "UNIQLO 極暖發熱衣",
                "price" => 499,
                "image" => "123.png",
                "category" => "服飾",
                "information" => "UNIQLO 熱賣款，貼身保暖且透氣。",
                "stock" => 100
            ],
            [
                "name" => "Nike Air Max 270",
                "price" => 3990,
                "image" => "123.png",
                "category" => "服飾",
                "information" => "潮流跑鞋，兼顧緩震與時尚外型。",
                "stock" => 100
            ],
            [
                "name" => "解憂雜貨店",
                "price" => 320,
                "image" => "123.png",
                "category" => "書籍",
                "information" => "治癒系小說，溫暖人心的故事集。",
                "stock" => 100
            ],
            [
                "name" => "原子習慣",
                "price" => 380,
                "image" => "123.png",
                "category" => "書籍",
                "information" => "暢銷書，提供可實行的小習慣改變方法。",
                "stock" => 100
            ]
        ];

        foreach ($products as $productData) {
            $categoryName = $productData['category'];
            unset($productData['category']);

            // Get category_id from the created categories
            $categoryId = $categories[$categoryName] ?? null;

            Product::create(array_merge($productData, [
                'store_id' => $store->id,
                'category_id' => $categoryId,
            ]));
        }

        // Create Coupons
        \App\Models\Coupon::create([
            'code' => 'SAVE100',
            'discount_amount' => 100,
            'type' => 'fixed',
            'limit_price' => 1000,
            'starts_at' => now(),
            'ends_at' => now()->addMonth(),
        ]);

        \App\Models\Coupon::create([
            'code' => 'OFF10',
            'discount_amount' => 10,
            'type' => 'percentage',
            'limit_price' => 500,
            'starts_at' => now(),
            'ends_at' => now()->addMonth(),
        ]);
    }

    /**
     * Seed member levels for normalization.
     */
    private function seedMemberLevels(): void
    {
        $levels = [
            ['slug' => 'normal', 'name' => '一般會員', 'threshold' => 0, 'discount' => 0.00],
            ['slug' => 'vip', 'name' => 'VIP 會員', 'threshold' => 100000, 'discount' => 0.05],
            ['slug' => 'gold', 'name' => '黃金會員', 'threshold' => 300000, 'discount' => 0.08],
            ['slug' => 'platinum', 'name' => '白金會員', 'threshold' => 500000, 'discount' => 0.10],
            ['slug' => 'diamond', 'name' => '鑽石會員', 'threshold' => 1000000, 'discount' => 0.15],
        ];

        foreach ($levels as $level) {
            MemberLevel::firstOrCreate(['slug' => $level['slug']], $level);
        }
    }

    /**
     * Seed categories and return a mapping of name => id.
     */
    private function seedCategories(): array
    {
        // Chinese name => English slug mapping
        $categoryData = [
            '手機' => 'phones',
            '家電' => 'appliances',
            '美妝' => 'beauty',
            '食品' => 'food',
            '日用品' => 'daily',
            '玩具' => 'toys',
            '服飾' => 'clothing',
            '書籍' => 'books',
        ];

        $categories = [];

        foreach ($categoryData as $name => $slug) {
            $category = Category::firstOrCreate(
                ['name' => $name],
                ['slug' => $slug, 'description' => $name . '類商品']
            );
            $categories[$name] = $category->id;
        }

        return $categories;
    }
}
