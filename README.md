
# 💘 Laravel 12 + Sanctum Dating API & Blade UI

Ez a projekt egy **Laravel 12 + Sanctum** alapú társkereső alkalmazás backend és frontend kombinációja,  
amely JSON API-n és Blade alapú Lightwave UI-n keresztül működik.

## 🚀 Fő funkciók

### 🔐 Autentikáció
- **Laravel Sanctum** token alapú hitelesítés.
- `POST /api/v1/auth/token` – Bejelentkezés és Bearer token generálás.
- `DELETE /api/v1/auth/token` – Kijelentkezés (token érvénytelenítés).
- `GET /api/v1/me` – Saját profil lekérdezése.

### ❤️ Társkereső profilok (CRUD)
- `GET /api/v1/dating-profiles` – Profilok listázása (szűrés ország, város szerint).
- `POST /api/v1/dating-profiles` – Profil létrehozása (egy felhasználónak csak egy lehet).
- `GET /api/v1/dating-profiles/{id}` – Profil megtekintése.
- `PUT /api/v1/dating-profiles/{id}` – Profil frissítése.
- `DELETE /api/v1/dating-profiles/{id}` – Profil törlése.

### 🧑‍💼 Admin jogosultságok
- Az `users` táblában található `is_admin` mező alapján.
- Az admin más felhasználók profilját is létrehozhatja, frissítheti, törölheti.
- Admin felhasználó seeder: `AdminUserSeeder` (`admin@example.com / password`).

### 🧠 Adatszerkezet
A társkereső profil adatai segédtáblában (`dating_profiles`) tárolódnak,  
a `users` tábla csak a bejelentkezéshez szükséges adatokat tartalmazza.

Fő mezők:
- Becenév, magasság, testsúly, testalkat, hajszín
- Szexuális beállítottság, családi állapot, végzettség
- Foglalkozás, beszélt nyelvek, ország, megye, város
- Regisztráció célja

### 🗃️ Seeder & Factory
- Minden entitás rendelkezik factory-val és seederrel.
- A seeder truncate-olja a táblákat, majd 10 mintaprofil generálódik.

### 🧩 JSON válaszstruktúra
Minden API egységes formátumban ad vissza adatot:
```json
{
  "success": true,
  "data": { ... },
  "message": "Human readable üzenet."
}
```

---

## 🧭 Blade alapú Lightwave UI

A projekt tartalmaz egy **minimalista Blade UI-t**, amely közvetlenül a Sanctum API-t hívja JavaScriptből.

### 🔑 Auth oldalak
- `/auth/login` → Bejelentkezés (`POST /api/v1/auth/token`)
- `/me` → Saját profil oldal (`GET /api/v1/me`)
- Logout gomb → `DELETE /api/v1/auth/token`

### 💌 Dating Profiles UI
- `/profiles` → Profil lista (`GET /api/v1/dating-profiles`)
- `/profiles/{id}` → Profil adatlap (`GET /api/v1/dating-profiles/{id}`)
- `/profiles/{id}/edit` → Profil szerkesztés (`PUT /api/v1/dating-profiles/{id}`)
- Automatikus token kezelés `localStorage` segítségével.

### ⚙️ Technológia
- Blade template engine (Laravel 12)
- Vanilla JavaScript + fetch API
- Lightwave layout (minimalista stílus)
- Tailwind nélkül, könnyű és gyors UI

---

## 💾 Telepítés

```bash
git clone https://github.com/vargazsolti/app-sanctum.test.git
cd app-sanctum.test
composer install
cp .env.example .env
php artisan key:generate

# Adatbázis beállítás az .env-ben, majd migrációk futtatása
php artisan migrate --seed

# Admin user létrehozás
php artisan db:seed --class=AdminUserSeeder

# Fejlesztői szerver indítása
php artisan serve
```

Alapértelmezett elérési út:
```
http://localhost:8000/auth/login
```

---

## 🧪 Postman Collection

A projekt tartalmaz egy teljes Postman gyűjteményt:

- **DatingProfiles_updated.postman_collection.json**  
  Minden CRUD endpoint előre kitöltve  
  Bearer token örökléssel (`{{token}}`), `{{base_url}}` környezeti változóval.

Importáld a Postman-be, állítsd be a változókat:
```
base_url = http://localhost/
token = <a saját Sanctum tokened>
```

---

## 🧰 Hasznos parancsok

```bash
# Cache törlés
php artisan optimize:clear

# Factory újra generálás
php artisan db:seed --class=DatingProfileSeeder

# Admin újra seedelés
php artisan db:seed --class=AdminUserSeeder
```

---

## 📚 Fejlesztői információk

- Laravel 12 + PHP 8.2
- Sanctum middleware minden privát API endpointon
- PSR-12 kódstílus
- JSON alapú API + Blade front kombináció
- Könnyen integrálható Vue/Inertia frontendre

---


