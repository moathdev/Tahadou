# 🎁 Tahadou — Eid Gift Exchange Platform

> A clean, bilingual (Arabic/English) web platform for organizing Eid gift exchanges among friends and family. No app downloads, no sign-ups — just a link.

---

## ✨ Features

- 🌐 **Bilingual** — Arabic (RTL) default + English with a language switcher on every page; all UI text, WhatsApp messages, and the footer switch automatically with the selected language
- 🕌 **Hadith section** — homepage includes an explanation of the Prophetic hadith *"Tahadou Tahabbu"* in both Arabic and English
- 🔗 **Shareable registration link** — auto-generated when a group is created
- 🔑 **Code-only admin access** — enter your 8-character admin code at `/admin` to reach your dashboard (no UUID needed)
- 👥 **Admin dashboard** — view participants, remove them, lock registration, execute the draw, send WhatsApp messages
- 💰 **Max gift price** — optional budget cap shown to participants at registration and included in the WhatsApp message
- 📝 **Participant registration** — full name, WhatsApp number, up to 3 interests from 10 categories
- 🎯 **Smart draw algorithm** — Circular Permutation (no self-assignment, no two-person loops)
- 📱 **Direct WhatsApp send** — after the draw, each row has a button that opens WhatsApp with a pre-written message in the admin's active language (Arabic or English)
- ✅ **Send tracking** — button turns to "Sent ✓" with an option to resend (stored in `localStorage`)
- 🛡️ **Strict validation** — Saudi mobile format only (`05XXXXXXXXX`), name ≥ 3 characters, no duplicate phone per group

---

## 🛠 Tech Stack

| Layer         | Technology                           |
|---------------|--------------------------------------|
| Backend       | PHP 8.2+ / Laravel 11                |
| Database      | MySQL 8                              |
| Frontend      | Blade + Tailwind CSS (CDN)           |
| Localisation  | Arabic (RTL) + English (LTR)         |
| Containers    | Docker + Supervisor (nginx + php-fpm)|
| Orchestration | k3s (Kubernetes)                     |
| Edge / CDN    | Cloudflare (WAF ready)               |

---

## 🚀 Local Setup

### Requirements
- PHP 8.2+
- Composer
- MySQL 8

### Steps

```bash
git clone https://github.com/moathdev/Tahadou.git
cd Tahadou

# Copy env file
cp .env.example .env

# Edit DB credentials in .env
# DB_HOST=127.0.0.1 | DB_DATABASE=tahadou | DB_USERNAME=... | DB_PASSWORD=...

# Install dependencies
composer install

# Generate app key
php artisan key:generate

# Run migrations
php artisan migrate

# Start server
php artisan serve
```

App available at: **http://localhost:8000**

---

## 🔐 Admin Access

Admins **do not need the group UUID** — just the 8-character code shown once at group creation.

1. Click **🔐 المشرف / Admin** in the navbar (or go to `/admin`)
2. Enter the admin code
3. You're taken directly to the dashboard

The admin code is stored as bcrypt in the DB. A SHA-256 lookup hash (`admin_lookup`) enables code-only lookup without exposing the raw code.

---

## 🗄 Database Schema

### `groups`
| Column           | Type      | Notes                                         |
|------------------|-----------|-----------------------------------------------|
| id               | bigint PK |                                               |
| uuid             | string    | Unique — used in participant registration URL |
| name             | string    | Group display name                            |
| max_participants | int       | Maximum allowed participants                  |
| max_gift_price   | int?      | Optional max gift budget (SAR)                |
| admin_code       | string    | Bcrypt-hashed admin password                  |
| admin_lookup     | string    | SHA-256 of raw admin code (for code-only login)|
| is_locked        | boolean   | Locks new registrations when true             |
| is_drawn         | boolean   | True after draw is executed                   |
| created_at/updated_at | timestamp |                                          |

### `participants`
| Column          | Type      | Notes                                    |
|-----------------|-----------|------------------------------------------|
| id              | bigint PK |                                          |
| group_id        | bigint FK | → groups.id                              |
| name            | string    | Full name                                |
| phone_number    | string    | Saudi mobile (unique per group)          |
| interests       | JSON      | Up to 3 selected interest keys           |
| assigned_to_id  | bigint FK | → participants.id (null until draw)      |
| created_at/updated_at | timestamp |                                    |

---

## 🎯 Draw Algorithm

**Circular Permutation (Derangement):**

1. Participants array is shuffled randomly
2. Each participant `[i]` gives to `[i+1]`; the last gives to `[0]`
3. ✅ No one draws themselves
4. ✅ No two-person closed loops (for groups > 2)
5. Results saved directly to DB — no external services

---

## 📱 WhatsApp Message Template

After the draw, the admin clicks a button per participant to open WhatsApp with a pre-filled message. The message is **automatically generated in the admin's active language** (Arabic or English).

**English:**
```
Hello [Giver Name],
You're part of the "[Group Name]" gift exchange 🎁

You're gifting:
[Receiver Name]

Their interests:
- [Interest 1]
- [Interest 2]

⚠️ Max gift price: [Amount] SAR   ← only shown if set

Prepare a thoughtful gift before Eid! 🌙
```

**Arabic:**
```
مرحباً [اسم المهدي]،
أنت ضمن قرعة "[اسم المجموعة]" لتبادل الهدايا 🎁

الشخص الذي ستهديه:
[اسم المُهدى إليه]

اهتماماته:
- [اهتمام 1]
- [اهتمام 2]

⚠️ الحد الأقصى لسعر الهدية: [المبلغ] ريال   ← يظهر فقط إذا تم تعيينه

جهّز له هدية قبل العيد 🌙
```

The button state ("Send" → "Sent ✓") is tracked per-participant in `localStorage` so it persists across page refreshes.

---

## 🎁 Gift Interest Categories

| Key          | Arabic              | English                  |
|--------------|---------------------|--------------------------|
| books        | 📚 الكتب            | 📚 Books                 |
| electronics  | 📱 الإلكترونيات     | 📱 Electronics & Gadgets |
| sports       | 🏋️ الرياضة          | 🏋️ Sports & Fitness      |
| fashion      | 👗 الموضة           | 👗 Fashion & Accessories |
| home         | 🏠 المنزل والمطبخ   | 🏠 Home & Kitchen        |
| games        | 🎮 الألعاب          | 🎮 Games & Entertainment |
| beauty       | 💄 التجميل          | 💄 Beauty & Skincare     |
| travel       | ✈️ السفر            | ✈️ Travel & Outdoor      |
| art          | 🎨 الفن             | 🎨 Art & Crafts          |
| food         | 🍫 الطعام والحلويات | 🍫 Food & Sweets         |

---

## ⚙️ Environment Variables

```env
APP_NAME=Tahadou
APP_ENV=production
APP_KEY=                    # php artisan key:generate
APP_DEBUG=false
APP_URL=https://tahadou.example.com
APP_LOCALE=ar               # ar | en

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tahadou
DB_USERNAME=tahadou
DB_PASSWORD=secret

SESSION_DRIVER=database
QUEUE_CONNECTION=database
```

---

## ☸️ Deploying on k3s

```bash
# Create namespace
kubectl create namespace tahadou

# Create secrets
kubectl create secret generic tahadou-secrets \
  --from-literal=APP_KEY=base64:... \
  --from-literal=DB_PASSWORD=... \
  -n tahadou

# Apply manifests
kubectl apply -f k8s/ -n tahadou

# Check rollout
kubectl rollout status deployment/tahadou -n tahadou
```

---

## 🏗 Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── GroupController.php        # Group creation
│   │   ├── AdminController.php        # Find group, dashboard, draw, WhatsApp
│   │   └── ParticipantController.php  # Registration
│   ├── Middleware/
│   │   └── SetLocale.php              # Session-based ar/en locale
│   └── Requests/                      # Validated form requests
├── Models/
│   ├── Group.php
│   └── Participant.php
└── Services/
    ├── DrawService.php                # Circular permutation algorithm
    └── XlsxExporter.php               # SpreadsheetML export (internal use)

lang/
├── ar/app.php + validation.php        # Arabic translations
└── en/app.php + validation.php        # English translations

resources/views/
├── layouts/app.blade.php              # RTL/LTR layout + lang switcher + admin btn
├── home.blade.php                     # Landing page — create group
├── group/created.blade.php            # Post-creation — shows code + share link
├── admin/
│   ├── find.blade.php                 # Code-only login entry (/admin)
│   ├── login.blade.php                # UUID-based login (direct URL access)
│   └── dashboard.blade.php           # Group management + WhatsApp buttons
└── participant/
    ├── register.blade.php
    ├── success.blade.php
    └── closed.blade.php

database/migrations/
├── 2025_01_01_000001_create_groups_table.php
├── 2025_01_01_000002_create_participants_table.php
├── 2025_01_02_000001_add_max_gift_price_to_groups_table.php
└── 2025_01_03_000001_add_admin_lookup_to_groups_table.php

k8s/
├── deployment.yaml                    # App + queue worker
├── service.yaml
└── ingress.yaml                       # Cloudflare-ready
```

---

## 📄 License

MIT — Built with ❤️ by [Muath Aljohani](https://moath.co)
