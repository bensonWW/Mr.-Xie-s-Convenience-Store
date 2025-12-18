<?php
$mermaid = <<<'EOD'
erDiagram
    users ||--o{ orders : "places"
    users ||--o{ carts : "has"
    users ||--o{ wallet_transactions : "has"
    users ||--o{ favorites : "has"
    stores ||--o{ products : "sells"
    stores ||--o{ users : "employs/members"
    products ||--o{ cart_items : "in"
    products ||--o{ order_items : "in"
    products ||--o{ favorites : "in"
    products ||--o{ sales : "has"
    carts ||--o{ cart_items : "contains"
    orders ||--o{ order_items : "contains"

    users {
        bigint id PK
        string name
        string email UK
        string role
        decimal balance
    }
    stores {
        bigint id PK
        string name
        string email UK
    }
    products {
        bigint id PK
        string name
        decimal price
        bigint store_id FK
    }
    carts {
        bigint id PK
        bigint user_id FK
    }
    cart_items {
        bigint id PK
        bigint cart_id FK
        bigint product_id FK
        integer quantity
    }
    orders {
        bigint id PK
        bigint user_id FK
        string status
        decimal total_amount
    }
    order_items {
        bigint id PK
        bigint order_id FK
        bigint product_id FK
        integer quantity
        decimal price
    }
    coupons {
        bigint id PK
        string code UK
        decimal discount_amount
    }
    special_events {
        bigint id PK
        string name
        decimal discount_rate
    }
    sales {
        bigint id PK
        bigint product_id FK
        decimal amount
        timestamp sold_at
    }
    wallet_transactions {
        bigint id PK
        bigint user_id FK
        string type
        decimal amount
    }
    favorites {
        bigint id PK
        bigint user_id FK
        bigint product_id FK
    }
EOD;

$base64 = base64_encode($mermaid);
$url = "https://mermaid.ink/img/" . $base64;

$html = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <style>body { margin: 0; padding: 20px; background: white; }</style>
</head>
<body>
    <h1>ER Diagram</h1>
    <img src="$url" alt="ER Diagram" style="max-width: 100%;">
</body>
</html>
HTML;

file_put_contents('C:\Users\User\.gemini\antigravity\brain\9fdf4130-d908-4ec7-8942-e4c8c8b00ede\er_diagram_image.html', $html);
echo "HTML file generated.\n";
