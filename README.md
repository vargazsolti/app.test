# 💘 Laravel 12 + Sanctum Dating API & Blade UI

Ez a projekt egy **Laravel 12 + Sanctum** alapú társkereső alkalmazás backend és frontend kombinációja,  
amely JSON API-n és Blade alapú Lightwave UI-n keresztül működik.

---

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

---

## 📸 Profilképek modul

A `ProfileImage` modul biztosítja a profilképek kezelését, jogosultságokat és megosztásokat.

### 🧩 API végpontok

| Módszer | URL | Leírás |
|----------|-----|--------|
| `GET` | `/api/v1/profile-images` | Profilképek listázása (profil ID alapján is szűrhető) |
| `POST` | `/api/v1/profile-images` | Új kép feltöltése (csak a tulajdonos vagy admin) |
| `GET` | `/api/v1/profile-images/{id}` | Egy kép megtekintése (redaktálva, ha privát és nem jogosult) |
| `PUT` | `/api/v1/profile-images/{id}` | Kép frissítése (caption, visibility, primary stb.) |
| `DELETE` | `/api/v1/profile-images/{id}` | Kép törlése (csak a tulajdonos vagy admin) |

### 🧱 Adatszerkezet

**Táblák:**
- `profile_images`
  - `dating_profile_id`
  - `path`
  - `caption`
  - `visibility` (`public` / `private`)
  - `is_primary` (bool)
  - `sort_order`
- `profile_image_shares`
  - `profile_image_id`
  - `shared_with_user_id`

### 🧠 Működés

- Minden képhez megadható **publikus** vagy **privát** láthatóság.
- **Privát kép** esetén:
  - a nem jogosult felhasználó csak egy *placeholder* képet lát (`/img/locked-placeholder.png`),
  - a válaszban `is_redacted: true` szerepel.
- **Tulajdonos** és **admin** mindig látja az eredeti képet.
- **Megosztás**: a tulajdonos más felhasználóknak is engedélyezheti a privát kép megtekintését.

### 🧰 Megosztás API

| Módszer | URL | Leírás |
|----------|-----|--------|
| `GET` | `/api/v1/profile-image-shares?profile_image_id=ID` | Egy kép megosztásainak listája |
| `POST` | `/api/v1/profile-image-shares` | Megosztás létrehozása (owner vagy admin) |
| `DELETE` | `/api/v1/profile-image-shares/{id}` | Megosztás visszavonása |

**Példa:**
```json
POST /api/v1/profile-image-shares
{
  "profile_image_id": 45,
  "shared_with_user_id": 123
}
```

**Válasz:**
```json
{
  "success": true,
  "message": "Profile image shared."
}
```

---

## 🔒 Jogosultsági szabályok

| Szerep | Megtekintés | Feltöltés | Törlés / Frissítés | Megosztás |
|--------|--------------|------------|---------------------|------------|
| Tulajdonos | Saját képei, privát képei | ✔️ | ✔️ | ✔️ |
| Megosztott user | Csak megosztott privát képei | ❌ | ❌ | ❌ |
| Más user | Csak publikus képek | ❌ | ❌ | ❌ |
| Admin | Minden kép | ✔️ | ✔️ | ✔️ |

---

## 🧭 Blade alapú Lightwave UI

### 💡 Profilképek kezelése a Blade nézetben

`resources/views/profiles/show.blade.php`

Funkciók:
- Képfeltöltés (multipart/form-data)
- Láthatóság választó (`Publikus` / `Privát`)
- Képek listázása, törlése, elsődleges beállítása
- Privát képek **placeholder**-rel jelennek meg, ha a user nem jogosult
- Tulajdonos/admin esetén „Megosztás” blokk:
  - user ID megadása
  - megosztás API-hívás (`POST /profile-image-shares`)
- Privát badge (`PRIVÁT`) és redaktált (`REDAKTÁLT`) jelölés

---

## 🧑‍💼 Admin jogosultságok

- Az `users` táblában `is_admin` boolean flag.
- Az admin:
  - minden profilhoz tölthet fel képet,
  - lát minden privát képet,
  - kezelheti a megosztásokat is.

Seeder:
```bash
php artisan db:seed --class=AdminUserSeeder
# admin@example.com / password
```

---

## 📸 Redaktálási logika

A redaktálás minden olyan API-válaszban megtörténik, ahol `images` kapcsolat szerepel.  
Ha a user nem jogosult a privát képre, a `url` mező a placeholderre mutat:

```json
{
  "id": 12,
  "visibility": "private",
  "is_redacted": true,
  "url": "http://localhost/img/locked-placeholder.png"
}
```

---

## 🧪 Tesztelési forgatókönyvek

1️⃣ **Publikus kép** → bárki láthatja  
2️⃣ **Privát kép (tulaj)** → látszik  
3️⃣ **Privát kép (más user)** → placeholder  
4️⃣ **Privát kép megosztva más userrel** → teljes kép látszik  
5️⃣ **Admin** → mindig látja az eredeti képet  
6️⃣ **Nem tulaj feltöltése** → 403 hiba  
7️⃣ **Megosztás nem tulajtól** → 403 hiba  
8️⃣ **Megosztás admin felől** → engedélyezett  

---

## 🧰 Hasznos artisan parancsok

```bash
# Migráció + seed
php artisan migrate --seed

# Cache törlés
php artisan optimize:clear

# Storage link létrehozása
php artisan storage:link

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

## 🏁 Összefoglaló

Ez a verzió már tartalmazza:
- ✅ profilképek feltöltését (`ProfileImageController`)
- ✅ privát/publikus képek kezelését és placeholder megjelenítést
- ✅ képek megosztását más felhasználókkal (`ProfileImageShareController`)
- ✅ admin bypass jogosultságot
- ✅ tulajdonos-ellenőrzést feltöltés/frissítés/törlés során
- ✅ teljes Lightwave UI integrációt (`profiles/show.blade.php`)

---
