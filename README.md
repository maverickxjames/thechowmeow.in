# PetWear — Premium Pet Clothing Store

**PetWear** is a robust, high-performance eCommerce platform dedicated to premium clothing for dogs and cats. Built with Laravel and modern frontend technologies, it offers a seamless shopping experience for pet lovers worldwide.

## 🚀 Live Demo
Website: [thechowmeow.in](https://thechowmeow.in)

## ✨ Features

### 🛒 Shopping Experience
- **Interactive Product Catalog:** Dynamic filtering, search, and sorting.
- **Advanced Variants:** Full support for various sizes (XS to 3XL) and colors.
- **Product Zoom:** High-quality image hover zoom for detailed inspection.
- **Size Guides:** Integrated size charts tailored for both dogs and cats.
- **User Reviews:** Cryptographically verified rating and review system.

### 💳 Payments & Internationalization
- **Razorpay Integration:** Secure checkout flow with support for UPI, Cards, and Net Banking.
- **Live/Test Modes:** Easily switchable via the Admin Settings panel.
- **Multi-Currency Support:** Support for **INR (₹)** and **USD ($)** with a session-based switcher.
- **Manual Exchange Rates:** Admin-controlled exchange rates to ensure precise international pricing.

### 🛡️ Admin Dashboard
- **Inventory Control:** Comprehensive stock management with low-stock and out-of-stock highlights.
- **Data Export:** Export inventory and order data to **Excel, CSV, and PDF**.
- **Dynamic Menus & Banners:** Manage your storefront's navigation and hero sliders in real-time.
- **Coupon System:** Create and manage discount codes.
- **Order Management:** Real-time order tracking, status updates, and automated stock restoration on cancellation.

### ⚙️ System Configuration
- **Global Settings:** Modify App Name, Currency, and Logo directly from the UI.
- **SMTP Management:** Custom mail server configuration via the admin panel.
- **Payment Toggles:** Enable or disable COD and Online Payment methods instantly.

## 🛠️ Tech Stack

- **Backend:** [Laravel 11](https://laravel.com)
- **Frontend:** [Tailwind CSS](https://tailwindcss.com), [Alpine.js](https://alpinejs.dev)
- **Database:** MySQL
- **Caching & Queues:** Redis (via Predis)
- **Payment Gateway:** [Razorpay](https://razorpay.com)
- **Assets:** Vite

## 📥 Installation & Setup

### Prerequisites
- PHP 8.2+
- Composer
- Node.js & NPM
- MySQL
- Redis

### Step-by-step Setup

1. **Clone the repository:**
   ```bash
   git clone https://github.com/maverickxjames/thechowmeow.in.git
   cd thechowmeow.in
   ```

2. **Install dependencies:**
   ```bash
   composer install
   npm install
   ```

3. **Configure Environment:**
   Copy `.env.example` to `.env` and update your database, redis, and mail credentials.
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Initialize Database:**
   ```bash
   php artisan migrate --seed
   ```

5. **Symlink Storage:**
   ```bash
   php artisan storage:link
   ```

6. **Build Assets:**
   ```bash
   npm run build
   ```

7. **Run the application:**
   ```bash
   php artisan serve
   ```

## 📜 License
This project is open-sourced software licensed under the [MIT license](LICENSE).
