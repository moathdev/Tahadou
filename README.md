# 🎁 تهادوا تحابوا — Eid Gift Exchange Platform

> منصة متكاملة وآمنة لتنظيم تبادل الهدايا في مناسبات العيد.
> An automated, secure, and user-friendly platform for organizing Eid gift exchanges.

---

## ✨ المميزات / Features

- 🌐 **ثنائي اللغة** — عربي (RTL) وإنجليزي مع زر تبديل في كل صفحة
- 🔗 **رابط تسجيل قابل للمشاركة** — ينشأ تلقائياً عند إنشاء المجموعة
- 🔑 **كود مشرف خاص** — يُعرض مرة واحدة فقط عند الإنشاء
- 👥 **لوحة تحكم المشرف** — عرض المشتركين مع اهتماماتهم وأرقامهم، إزالة مشترك، قفل التسجيل، تنفيذ القرعة
- 💰 **حد أقصى لسعر الهدية** — اختياري، يظهر للمشتركين عند التسجيل وفي رسالة الواتساب
- 📝 **تسجيل المشتركين** — الاسم، رقم الواتساب، واختيار حتى 3 اهتمامات من 10 فئات
- 🎯 **خوارزمية القرعة** — Circular Permutation تضمن ألا يهدي أحد نفسه
- 📱 **إرسال واتساب مباشر** — بعد القرعة، زر لكل مشترك يفتح الواتساب مع رسالة جاهزة
- ✅ **تتبع الإرسال** — الزر يتحول لـ "تم الإرسال" مع إمكانية إعادة الإرسال
- 🛡️ **تحقق صارم** — رقم جوال سعودي فقط، اسم لا يقل عن 3 أحرف، لا تكرار في نفس المجموعة

---

## 🛠 التقنيات / Tech Stack

| Layer        | Technology                          |
|--------------|--------------------------------------|
| Backend      | PHP 8.2+ / Laravel 11                |
| Database     | MySQL 8                              |
| Frontend     | Blade + Tailwind CSS (CDN)           |
| Localisation | Arabic (RTL) + English (LTR)         |
| Containers   | Docker + Supervisor (nginx + php-fpm)|
| Orchestration| k3s (Kubernetes)                     |
| Edge / CDN   | Cloudflare (WAF ready)               |

---

## 🚀 التشغيل المحلي / Local Setup

### المتطلبات / Requirements
- PHP 8.2+
- Composer
- MySQL 8
- Node.js (for assets, optional)

### الخطوات / Steps

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

App: **http://localhost:8000**

---

## 🗄 قاعدة البيانات / Database Schema

### `groups`
| Column           | Type      | Notes                                    |
|------------------|-----------|------------------------------------------|
| id               | bigint PK |                                          |
| uuid             | string    | Unique — used in shareable URL           |
| name             | string    | Group display name                       |
| max_participants | int       | Maximum allowed participants             |
| max_gift_price   | int?      | Optional max gift budget (SAR)           |
| admin_code       | string    | Bcrypt hashed admin password             |
| is_locked        | boolean   | Locks new registrations                  |
| is_drawn         | boolean   | True after draw is executed              |
| created_at/updated_at | timestamp |                                     |

### `participants`
| Column          | Type      | Notes                                         |
|-----------------|-----------|-----------------------------------------------|
| id              | bigint PK |                                               |
| group_id        | bigint FK | → groups.id                                   |
| name            | string    | Full name                                     |
| phone_number    | string    | Saudi mobile (unique per group)               |
| interests       | JSON      | Up to 3 selected interest keys                |
| assigned_to_id  | bigint FK | → participants.id (null until draw)           |
| created_at/updated_at | timestamp |                                         |

---

## 🎯 خوارزمية القرعة / Draw Algorithm

**Circular Permutation (Derangement):**

1. يتم خلط المشتركين عشوائياً
2. كل مشترك `[i]` يهدي المشترك `[i+1]`، والأخير يهدي الأول
3. ✅ لا أحد يهدي نفسه
4. ✅ لا حلقات مغلقة بين شخصين فقط (للمجموعات > 2)

---

## 📱 رسالة الواتساب / WhatsApp Message

بعد تنفيذ القرعة، يظهر زر لكل مشترك في لوحة المشرف يفتح الواتساب مع هذه الرسالة:

```
مرحباً [اسم المهدي]،
أنت ضمن قرعة "[اسم المجموعة]" لتبادل الهدايا 🎁

الشخص الذي ستهديه:
[اسم المهدى إليه]

اهتماماته:
- [اهتمام 1]
- [اهتمام 2]

⚠️ الحد الأقصى لسعر الهدية: [المبلغ] ريال   ← يظهر فقط إذا حُدد

جهّز له هدية قبل العيد 🌙
```

---

## 🎁 فئات الاهتمامات / Gift Interest Categories

| Key          | عربي                    | English                  |
|--------------|--------------------------|--------------------------|
| books        | 📚 الكتب                 | 📚 Books                 |
| electronics  | 📱 الإلكترونيات والأجهزة | 📱 Electronics & Gadgets |
| sports       | 🏋️ الرياضة واللياقة      | 🏋️ Sports & Fitness      |
| fashion      | 👗 الموضة والإكسسوارات   | 👗 Fashion & Accessories |
| home         | 🏠 المنزل والمطبخ        | 🏠 Home & Kitchen        |
| games        | 🎮 الألعاب والترفيه      | 🎮 Games & Entertainment |
| beauty       | 💄 التجميل والعناية      | 💄 Beauty & Skincare     |
| travel       | ✈️ السفر والمغامرة       | ✈️ Travel & Outdoor      |
| art          | 🎨 الفن والأشغال اليدوية | 🎨 Art & Crafts          |
| food         | 🍫 الطعام والحلويات      | 🍫 Food & Sweets         |

---

## ⚙️ متغيرات البيئة / Environment Variables

```env
APP_NAME=Tahadou
APP_ENV=production
APP_KEY=                    # php artisan key:generate
APP_DEBUG=false
APP_URL=https://tahadou.nit.sa
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

## ☸️ النشر على k3s / Deploying on k3s

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

## 🏗 هيكل المشروع / Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── GroupController.php        # Group creation
│   │   ├── AdminController.php        # Dashboard, draw, WhatsApp
│   │   └── ParticipantController.php  # Registration
│   ├── Middleware/
│   │   └── SetLocale.php              # ar/en session-based locale
│   └── Requests/                      # Validated form requests
├── Models/
│   ├── Group.php
│   └── Participant.php
└── Services/
    └── DrawService.php                # Circular permutation algorithm

lang/
├── ar/app.php                         # Arabic translations
├── ar/validation.php
├── en/app.php                         # English translations
└── en/validation.php

resources/views/
├── layouts/app.blade.php              # RTL/LTR layout + lang switcher
├── home.blade.php                     # Landing page
├── group/created.blade.php            # Post-creation (link + admin code)
├── admin/
│   ├── login.blade.php
│   └── dashboard.blade.php            # Full admin panel
└── participant/
    ├── register.blade.php
    ├── success.blade.php
    └── closed.blade.php

k8s/
├── deployment.yaml                    # App + queue worker (2 replicas)
├── service.yaml
└── ingress.yaml                       # Cloudflare-ready
```

---

## 📄 License

Private project — Built with ❤️ by [Muath Aljohani](https://moath.co)
