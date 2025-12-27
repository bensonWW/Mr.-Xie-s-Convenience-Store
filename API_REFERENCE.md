# API Reference

Base URL: 
- **Production / Cloud**: `https://mr-xie-s-convenience-store-main-d3awzd.laravel.cloud/api`
- **Local Development**: `http://localhost:8000/api`

> **Note**: For local Docker development, the frontend is configured to use `http://localhost:8000/api` via the `VUE_APP_API_URL` build argument in `docker-compose.yml`.

## Authentication

| Method | Endpoint | Description |
| :--- | :--- | :--- |
| `POST` | `/register` | Register a new user |
| `POST` | `/login` | Login and get a token |
| `POST` | `/logout` | Logout (Requires Auth) |
| `GET` | `/user` | Get current user info (Requires Auth) |
| `PUT` | `/user/profile` | Update user profile (Requires Auth) |

## Products & Stores

| Method | Endpoint | Description |
| :--- | :--- | :--- |
| `GET` | `/products` | List all products |
| `GET` | `/products/{id}` | Get product details |
| `GET` | `/stores` | List all stores |
| `GET` | `/stores/{id}` | Get store details |
| `PUT` | `/stores/{id}` | Update store (Requires Auth/Admin) |

## Cart (Requires Auth)

| Method | Endpoint | Description |
| :--- | :--- | :--- |
| `GET` | `/cart` | Get current user's cart |
| `POST` | `/cart/items` | Add item to cart |
| `PUT` | `/cart/items/{itemId}` | Update cart item quantity |
| `DELETE` | `/cart/items/{itemId}` | Remove item from cart |

## Orders (Requires Auth)

| Method | Endpoint | Description |
| :--- | :--- | :--- |
| `GET` | `/orders` | List user's orders |
| `POST` | `/orders` | Create a new order |
| `GET` | `/orders/{order}` | Get order details |
| `POST` | `/coupons/check` | Check coupon validity |
| `POST` | `/orders/{id}/pay` | Pay for an order |

## Wallet (Requires Auth)

| Method | Endpoint | Description |
| :--- | :--- | :--- |
| `GET` | `/user/wallet` | Get wallet balance and transactions |
| `POST` | `/user/wallet/deposit` | Deposit funds (Mock/Request) |

## Admin (Requires Auth & Admin Role)

| Method | Endpoint | Description |
| :--- | :--- | :--- |
| `GET` | `/admin/stats` | Get dashboard statistics |
| `GET` | `/admin/inventory-report` | Get inventory report |
| `GET` | `/admin/users` | List all users |
| `POST` | `/admin/users` | Create a new user |
| `GET` | `/admin/users/{id}` | Get user details |
| `PUT` | `/admin/users/{id}` | Update user details |
| `POST` | `/admin/users/{id}/wallet/transaction` | Admin Modifies Wallet (Deposit/Withdraw) |
| `GET` | `/admin/orders` | List all orders (paginated) |
| `GET` | `/admin/orders/{id}` | Get items for a single order |
| `POST` | `/admin/orders/{order}/refund` | Refund an order |
| `PUT` | `/admin/orders/{order}/status` | Update order status |
| `PUT` | `/admin/orders/{order}/logistics` | Update logistics number |
| `GET` | `/admin/products` | List all products (paginated) |
| `POST` | `/admin/products` | Create product |
| `PUT` | `/admin/products/{id}` | Update product |
| `DELETE` | `/admin/products/{id}` | Delete product |
| `GET` | `/admin/categories` | List all categories with product counts |
| `POST` | `/admin/categories` | Create category |
| `PUT` | `/admin/categories/{id}` | Update category |
| `DELETE` | `/admin/categories/{id}` | Delete category (products become uncategorized) |
| `POST` | `/admin/categories/{id}/reassign` | Move products to another category then delete |
| `GET` | `/admin/coupons` | List all coupons |
| `POST` | `/admin/coupons` | Create coupon |
| `PUT` | `/admin/coupons/{id}` | Update coupon |
| `DELETE` | `/admin/coupons/{id}` | Delete coupon |
| `PUT` | `/admin/stores/{id}` | Update store settings |

## How to Test

You can use tools like **Postman**, **Insomnia**, or `curl` to test these endpoints.

**Example Request (Get Products):**
```bash
curl -X GET https://<your-laravel-cloud-url>/api/products
```

**Example Request (Login):**
```bash
curl -X POST https://<your-laravel-cloud-url>/api/login \
  -H "Content-Type: application/json" \
  -d '{"email": "user@example.com", "password": "password"}'
```

**Authenticated Requests:**
Include the token returned from login in the `Authorization` header:
`Authorization: Bearer <your-token>`
