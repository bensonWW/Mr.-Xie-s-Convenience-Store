# API Reference

Base URL: `https://mr-xie-s-convenience-store-ch-back-icvbux.laravel.cloud/api`

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

## Admin (Requires Auth & Admin Role)

| Method | Endpoint | Description |
| :--- | :--- | :--- |
| `GET` | `/admin/stats` | Get dashboard statistics |
| `GET` | `/admin/users` | List all users |
| `GET` | `/admin/orders` | List all orders (paginated) |
| `GET` | `/admin/orders/{id}` | Get items for a single order |
| `PUT` | `/admin/orders/{id}/status` | Update order status |
| `GET` | `/admin/products` | List all products (paginated) |
| `POST` | `/admin/products` | Create product |
| `PUT` | `/admin/products/{id}` | Update product |
| `DELETE` | `/admin/products/{id}` | Delete product |

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
