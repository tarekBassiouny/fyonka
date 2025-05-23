# Fyonka Financial Tracking

A Laravel-based financial tracking system for managing store transactions, income, outcomes, and generating insightful reports.

---

## 🚀 Features

- Secure Admin Login  
- Store Management  
- CSV Upload for Bank Transactions  
- Income/Outcome Tagging  
- Manual Transaction Entry  
- API Support for External Transaction Input  
- PDF Financial Reporting  
- Responsive UI (Bootstrap / Tailwind)  
- Full CI/CD Pipeline via GitHub Actions  

---

## ⚙️ Setup

### Requirements

- PHP 8.2  
- Composer  
- MySQL 8+  
- Node.js  
- Laravel 10+  

### Installation

```
git clone https://github.com/your-username/fyonka.git
cd fyonka

cp .env.example .env
composer install
php artisan key:generate

npm install && npm run build

php artisan migrate
php artisan serve
```

---

## ✅ Testing

Run unit and feature tests:

```
php artisan test
```

---

## 🔁 CI/CD (GitHub Actions)

This project uses GitHub Actions to:

- Install dependencies  
- Build frontend assets  
- Run automated tests  
- Deploy to production via SSH on `main` branch push  

Workflow location: `.github/workflows/deploy.yml`

---

## 🔐 GitHub Secrets Required

To enable deployment, the following secrets must be configured in your repo settings:

- `DEPLOY_KEY`: SSH private key  
- `APP_KEY`: Laravel app key  
- `APP_URL`: Production URL  
- `DB_DATABASE`: DB name  
- `DB_USERNAME`: DB user  
- `DB_PASSWORD`: DB password  
- `DEFAULT_ADMIN_PASSWORD`: Default login password  
- `DEFAULT_API_PASSWORD`: API access password  

---

## 📂 Project Structure

- `app/` – Laravel backend logic  
- `resources/views/` – Blade templates  
- `resources/js/` – Modular JS (dashboard, charts, etc.)  
- `tests/Feature/` – Feature test cases  
- `tests/Unit/` – Unit tests  

---

## 📄 License

This project is open-source and available under the [MIT License](LICENSE).
