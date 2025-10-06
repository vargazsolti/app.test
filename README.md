# 🧱 Laravel 12 Sanctum SPA API Starter

This repository is a **Laravel 12** backend API boilerplate featuring **Laravel Sanctum** token-based authentication, a ready-to-use **User seeder**, and an included **Postman collection** for rapid testing and development.

---

## 🚀 Features

- ✅ Clean **Laravel 12** base install  
- ✅ **Sanctum** token authentication (SPA / API ready)  
- ✅ Default `User` model, factory & seeder  
- ✅ Pre-seeded test user  
- ✅ Consistent JSON API responses  
- ✅ Included **Postman v2.1 collection** for quick testing  

---

## 🔗 API Endpoints

| Method | Endpoint | Description |
|--------|-----------|-------------|
| `POST` | `/api/v1/auth/token` | Login – creates a new personal access token |
| `DELETE` | `/api/v1/auth/token` | Logout – revokes the current token |
| `GET` | `/api/v1/me` | Retrieve the authenticated user's profile |
| `GET` | `/api/v1/auth/tokens` | List the user's active tokens |
| `GET` | `/api/v1/auth/tokens/{id}` | Get details of a specific token |
| `PUT` | `/api/v1/auth/tokens/{id}` | Update a token name |

---

## 🧩 Installation Guide

### 1️⃣ Clone the repository
```bash
git clone https://github.com/<your-username>/<repo-name>.git
cd <repo-name>
```

### 2️⃣ Install dependencies
```bash
composer install
```

### 3️⃣ Environment setup
```bash
cp .env.example .env
php artisan key:generate
```

Then update your `.env` file with your local database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=app_sanctum
DB_USERNAME=root
DB_PASSWORD=
```

---

## ⚙️ Database Migration & Seed

Run the following commands:

```bash
php artisan migrate --seed
```

This will create a default **test user**:

| Field | Value |
|--------|--------|
| Email | `test@example.com` |
| Password | `password` |

---

## 🔐 Authentication (Sanctum)

Laravel Sanctum is already configured for **SPA and API token authentication**.

After successful login via:
```
POST /api/v1/auth/token
```
you will receive a token in the response.  
Use it for subsequent authenticated requests by setting the header:

```
Authorization: Bearer <your_token_here>
```

All protected routes use the `auth:sanctum` middleware.

---

## 📬 Postman Collection

A complete **Postman Collection** (`Sanctum SPA API v1.postman_collection.json`) is included.

### 🧱 Structure
- **Collection Variables**
  - `base_url` → your API root (e.g., `http://app-sanctum.test`)
  - `token` → automatically set after login
- **Automatic Token Handling**
  - The login request runs a test script that saves the received token into the collection variables.
- **Inherited Auth**
  - All requests inherit the Bearer token from the collection-level auth.

### 🧪 Usage Steps
1. Import the `.postman_collection.json` file into Postman.  
2. Set `base_url` to your local or deployed API (without trailing slash).  
3. Run the **Login** request to authenticate and automatically save your token.  
4. Test all other endpoints using the saved token.

---

## 📦 Example JSON Response

```json
{
  "success": true,
  "data": {
    "user": {
      "id": 1,
      "name": "Test User",
      "email": "test@example.com"
    }
  },
  "message": "Login successful."
}
```

---

## 🧰 Useful Artisan Commands

| Purpose | Command |
|----------|----------|
| Serve locally | `php artisan serve` |
| Clear cache | `php artisan optimize:clear` |
| List routes | `php artisan route:list` |
| Run migrations | `php artisan migrate` |
| Seed users | `php artisan db:seed --class=UserSeeder` |

---

## 🧠 Notes

- Sanctum middleware setup included.  
- Works seamlessly with **WAMP/XAMPP** or `php artisan serve`.  
- Perfect base for building SPA backends using **Vue**, **React**, or **Inertia**.  
- Clean and consistent API response structure for all endpoints.

---

## 🧑‍💻 Contributing & License

This project is open-source and free to use under the **MIT License**.  
Feel free to **fork**, **modify**, and **build** your own Laravel-based projects on top of it.

> Built with ❤️ using Laravel 12 and Sanctum.
