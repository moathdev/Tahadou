# 🎁 Tahadou — Eid Gift Exchange Platform

> An automated, secure, and user-friendly platform for organizing Eid gift exchange among groups of friends and family.

---

## ✨ Features

- **Create a group** with a custom name and max participant limit
- **Shareable registration link** — share with participants via any channel
- **Admin Dashboard** — protected by a private admin code, view/remove participants, lock registration, and execute the draw
- **Participant registration** — full name, WhatsApp number, and up to 3 gift interests from 10 predefined categories
- **Smart draw algorithm** — Circular permutation (Derangement) ensuring no self-assignment and no two-person loops
- **WhatsApp notifications** — each participant receives a private message with their assigned person's name and interests, dispatched via a background queue
- **Bot protection** — Cloudflare WAF-ready
- **Horizontally scalable** — Dockerized and deployable on k3s

---

## 🛠 Tech Stack

| Layer        | Technology                         |
|--------------|-------------------------------------|
| Backend      | PHP 8.3 + Laravel 11                |
| Database     | MySQL 8 (PostgreSQL compatible)     |
| Queue        | Laravel Queue (database / Redis)    |
| Frontend     | Blade + Vite + TailwindCSS          |
| Containers   | Docker + docker-compose             |
| Orchestration| k3s (Kubernetes)                    |
| Edge / CDN   | Cloudflare (WAF + Web Analytics)    |
| Storage      | Cloudflare R2 (S3-compatible)       |

---

## 🚀 Local Development (Docker)

### Prerequisites
- Docker & docker-compose installed

### Setup

```bash
git clone https://github.com/moathdev/Tahadou.git
cd Tahadou

# Copy environment file
cp .env.example .env

# Start all services
docker-compose up -d

# Install PHP dependencies
docker-compose exec app composer install

# Generate app key
docker-compose exec app php artisan key:generate

# Run migrations
docker-compose exec app php artisan migrate

# Build frontend assets
docker-compose exec app npm install && npm run build
```

App will be available at: **http://localhost:8080**

---

## 🗄 Database Schema

### `groups`
| Column           | Type      | Notes                              |
|------------------|-----------|------------------------------------|
| id               | bigint PK |                                    |
| uuid             | string    | Unique — used in shareable URL     |
| name             | string    | Group display name                 |
| max_participants | int       | Maximum allowed participants       |
| admin_code       | string    | Bcrypt hashed admin password       |
| is_locked        | boolean   | Locks new registrations            |
| is_drawn         | boolean   | True after draw is executed        |
| created_at       | timestamp |                                    |
| updated_at       | timestamp |                                    |

### `participants`
| Column          | Type      | Notes                                        |
|-----------------|-----------|----------------------------------------------|
| id              | bigint PK |                                              |
| group_id        | bigint FK | References groups.id                         |
| name            | string    | Full name                                    |
| phone_number    | string    | WhatsApp number (unique per group)           |
| interests       | JSON      | Up to 3 selected interests                   |
| assigned_to_id  | bigint FK | References participants.id (null until draw) |
| created_at      | timestamp |                                              |
| updated_at      | timestamp |                                              |

---

## 🎯 Draw Algorithm

The draw uses a **circular permutation (Derangement)** approach:

1. Participants array is shuffled randomly
2. Shifted by one position: A→B, B→C, ..., Last→A
3. This guarantees:
   - ✅ No one draws themselves
   - ✅ No two-person closed loops
   - ✅ Every person gives exactly once and receives exactly once

After the draw, Laravel dispatches a **Job** per participant to the queue, sending a WhatsApp message with their assigned person's name and gift interests — without blocking the admin's request.

---

## ⚙️ Environment Variables

```env
APP_NAME=Tahadou
APP_ENV=production
APP_KEY=                         # Generated via artisan key:generate
APP_DEBUG=false
APP_URL=https://tahadou.nit.sa

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=tahadou
DB_USERNAME=tahadou
DB_PASSWORD=secret

QUEUE_CONNECTION=database        # Switch to redis for production

WHATSAPP_API_URL=                # Your WhatsApp API endpoint
WHATSAPP_API_TOKEN=              # Your API token

AWS_ACCESS_KEY_ID=               # Cloudflare R2
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=auto
AWS_BUCKET=tahadou
AWS_ENDPOINT=https://<account>.r2.cloudflarestorage.com
AWS_USE_PATH_STYLE_ENDPOINT=true
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
  --from-literal=WHATSAPP_API_TOKEN=... \
  -n tahadou

# Apply manifests
kubectl apply -f k8s/ -n tahadou

# Check rollout
kubectl rollout status deployment/tahadou -n tahadou
```

---

## 🎁 Gift Interest Categories

1. 📚 Books
2. 📱 Electronics & Gadgets
3. 🏋️ Sports & Fitness
4. 👗 Fashion & Accessories
5. 🏠 Home & Kitchen
6. 🎮 Games & Entertainment
7. 💄 Beauty & Skincare
8. ✈️ Travel & Outdoor
9. 🎨 Art & Crafts
10. 🍫 Food & Sweets

---

## 📄 License

Private project — NIT © 2025
