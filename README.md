# 🎁 Tahadou — Eid Gift Exchange Platform

> An automated, secure, and user-friendly platform for organizing Eid gift exchanges among friends and family.

---

## ✨ Features

- 🌐 **Bilingual** — Arabic (RTL) and English with a language switcher on every page
- 🔗 **Shareable registration link** — auto-generated when a group is created
- 🔑 **Private admin code** — shown only once at creation
- 👥 **Admin dashboard** — view participants with interests and phone numbers, remove participants, lock registration, execute draw
- 💰 **Max gift price** — optional budget cap shown to participants on registration and included in the WhatsApp message
- 📝 **Participant registration** — full name, WhatsApp number, up to 3 interests from 10 categories
- 🎯 **Smart draw algorithm** — Circular Permutation ensuring no self-assignment and no two-person loops
- 📱 **Direct WhatsApp send** — after the draw, each participant row has a button that opens WhatsApp with a pre-written message
- ✅ **Send tracking** — button turns to "Sent" with an option to resend
- 🛡️ **Strict validation** — Saudi mobile format only, name minimum 3 characters, no duplicate phone per group

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

## 🗄 Database Schema

### `groups`
| Column            | Type       | Notes                               |
|-------------------|------------|-------------------------------------|
| id                | bigint PK  |                                     |
| uuid              | string     | Unique — used in the shareable URL  |
| name              | string     | Group display name                  |
| max_participants  | int        | Maximum allowed participants        |
| max_gift_price    | int?       | Optional max gift budget (SAR)      |
| admin_code        | string     | Bcrypt hashed admin password        |
| is_locked         | boolean    | Locks new registrations             |
| is_drawn          | boolean    | True after draw is executed         |
| created_at/updated_at | timestamp |                                 |

### `participants`
| Column          | Type       | Notes                                      |
|-----------------|------------|--------------------------------------------|
| id              | bigint PK  |                                            |
| group_id        | bigint FK  | → groups.id                                |
| name            | string     | Full name                                  |
| phone_number    | string     | Saudi mobile (unique per group)            |
| interests       | JSON       | Up to 3 selected interest keys             |
| assigned_to_id  | bigint FK  | → participants.id (null until draw)        |
| created_at/updated_at | timestamp |                                      |

---

## 🎯 Draw Algorithm

**Circular Permutation (Derangement):**

1. Participants array is shuffled randomly
2. Each participant `[i]` gives to `[i+1]`; the last gives to `[0]`
3. ✅ No one draws themselves
4. ✅ No two-person closed loops (for groups > 2)

---

## 📱 WhatsApp Message Template

After the draw, the admin clicks a button per participant to open WhatsApp with this pre-filled message:

```
Hello [Giver Name],
You are part of the "[Group Name]" gift exchange 🎁

The person you'll be gifting:
[Receiver Name]

Their interests:
- [Interest 1]
- [Interest 2]

⚠️ Max gift price: [Amount] SAR   ← only shown if set

Prepare their gift before Eid! 🌙
```

---

## 🎁 Gift Interest Categories

| Key          | English                  |
|--------------|--------------------------|
| books        | 📚 Books                 |
| electronics  | 📱 Electronics & Gadgets |
| sports       | 🏋️ Sports & Fitness      |
| fashion      | 👗 Fashion & Accessories |
| home         | 🏠 Home & Kitchen        |
| games        | 🎮 Games & Entertainment |
| beauty       | 💄 Beauty & Skincare     |
| travel       | ✈️ Travel & Outdoor      |
| art          | 🎨 Art & Crafts          |
| food         | 🍫 Food & Sweets         |

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
│   │   ├── AdminController.php        # Dashboard, draw, WhatsApp
│   │   └── ParticipantController.php  # Registration
│   ├── Middleware/
│   │   └── SetLocale.php              # Session-based ar/en locale
│   └── Requests/                      # Validated form requests
├── Models/
│   ├── Group.php
│   └── Participant.php
└── Services/
    └── DrawService.php                # Circular permutation algorithm

lang/
├── ar/app.php + validation.php        # Arabic translations
└── en/app.php + validation.php        # English translations

resources/views/
├── layouts/app.blade.php              # RTL/LTR layout + lang switcher
├── home.blade.php                     # Landing page
├── group/created.blade.php            # Post-creation page
├── admin/
│   ├── login.blade.php
│   └── dashboard.blade.php
└── participant/
    ├── register.blade.php
    ├── success.blade.php
    └── closed.blade.php

k8s/
├── deployment.yaml                    # App + queue worker
├── service.yaml
└── ingress.yaml                       # Cloudflare-ready
```

---

## 📄 License

Private — Built with ❤️ by [Muath Aljohani](https://moath.co)
