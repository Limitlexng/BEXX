# Cartlex Fleet Partner Portal

The official fleet investment, asset management, and rider verification platform for **Cartlex** delivery partners — built with Laravel 13, Tailwind CSS v4, and Alpine.js.

---

## 🚀 Deploy to Render (Free — get a live URL)

1. Go to **[render.com](https://render.com)** and create a free account
2. Click **New → Web Service**
3. Connect your GitHub account and select the **`Limitlexng/BEXX`** repository
4. Render auto-detects the `render.yaml` — click **Apply**
5. Wait ~5 minutes for the build
6. Your live URL will be: `https://cartlex-fleet-portal.onrender.com`

> **Note:** On the free tier Render spins down after 15 min of inactivity. First request may take ~30s to wake up.

---

## Demo Credentials

| Role | Email | Password |
|---|---|---|
| **Super Admin** | `admin@gocartlex.com` | `Cartlex@2025!` |
| **Finance Admin** | `finance@gocartlex.com` | `Cartlex@2025!` |
| **Demo Partner** | `demo@partner.com` | `Demo@2025!` |

---

## Local Development

```bash
# Clone and install
composer install
npm install

# Environment
cp .env.example .env
php artisan key:generate
touch database/database.sqlite

# Migrate and seed
php artisan migrate --seed

# Build assets and serve
npm run build
php artisan serve
```

Open [http://localhost:8000](http://localhost:8000)

### With Docker

```bash
docker-compose up
```

Open [http://localhost:8080](http://localhost:8080)

---

## Tech Stack

- **Laravel 13** (PHP 8.4)
- **Tailwind CSS v4** + Alpine.js
- **SQLite** (zero-config, swappable to MySQL/PostgreSQL)
- **Spatie Permission** — role-based access control
- **Chart.js** — earnings trend charts
- **QR Code** — rider ID card verification

---

## Portal Modules

| Module | Description |
|---|---|
| Fleet Registry | Register motorcycles, track health, insurance, and ROI |
| Rider Management | Create riders, generate QR ID cards, track performance |
| Rider Verification | Public page at `/verify/rider/{cardNumber}` |
| Earnings Dashboard | Daily/weekly/monthly/yearly revenue per asset & rider |
| Maintenance | Service logs, cost tracking, upcoming schedules |
| Compliance | License, insurance, road worthiness monitoring |
| Wallet & Withdrawals | Balance, transactions, payout requests |
| Admin Portal | Partner approval, earnings upload, withdrawal management |
| Documents | Upload and manage fleet documents |
| Reports | Printable fleet and earnings reports |

---

## Contact

- 📞 07052004934
- 📧 help@gocartlex.com
- 🌐 gocartlex.com
