
# AI Master Context & Instructions: Local Offers & Live Commerce Platform

> **Notice to AI Assistant:** You are acting as the lead software architect and full-stack developer for the **Local Offers & Live Commerce Platform**. When building code, answering questions, or generating components for this project, you must strictly follow the tech stack, architectural rules, and design guidelines outlined in this document.

---
## 0. Language
- Just use Arabic Language
- RTL


## 1. Project Identity & Purpose

- **Project Name:** Local Offers & Live Commerce Platform

- **Core Concept:** A hyper-local mobile and web platform that connects local retail stores with shoppers through **time-sensitive discount offers** and **real-time video live-streaming demonstrations**.

- **Key Constraint (Crucial):** **No login is required for end-users (shoppers).** Shoppers can browse categories, view offers, and watch live streams anonymously to eliminate user friction. Store owners and admins require authentication (JWT).

---

## 2. Mandatory Tech Stack

You must adhere strictly to the following technology stack for any code generation:

- **Mobile App Frontend:** **Ionic Framework** (Angular) wrapped with Capacitor for Android compatibility.

- **Web Dashboards (Admin & Store Owner):** Laravel 12 + Blade

- **Styling UI Framework:** **Tailwind CSS** (Utility-first styling, mobile-first design, clean spacing, modern color palette).

- **Backend API:** **laravel 12 + sanctum (PHP)** with a RESTful architecture. (COMPLETED ✅)

- **Database:** **MySQL** relational database. (COMPLETED ✅)

- **Live Streaming SDK:** **Agora RTC SDK** for sub-second latency video broadcasting. (COMPLETED ✅ — AgoraTokenService implemented)

- **AI Integration:** **Google Gemini API** (Multimodal) for automated product description generation from images. (COMPLETED ✅ — GeminiAIService implemented)

---

## 3. Core Modules & Specifications

### A. Customer Experience (Mobile App)

- **Anonymous Browsing:** Zero authentication required for shoppers. (COMPLETED ✅ — Backend API ready)

- **Category & Offer Exploration:** Hierarchical navigation through main categories and subcategories. Active discounts display original price, discounted price, and expiration countdown. (COMPLETED ✅ — Backend API ready)

- **Live Commerce Feed:** A dedicated "Live Now" section showing active store broadcasts. Tapping a stream immediately connects the user as a viewer via Agora RTC. (COMPLETED ✅ — Backend API ready)

### B. Store Owner Panel (Web Dashboard)

- **Store Profile & Management:** Manage store metadata, location, and operating status. (COMPLETED ✅ — Backend API ready)

- **Offer CRUD:** Create, read, update, and delete promotional offers. (COMPLETED ✅ — Backend API ready)

- **AI Product Description Generator:** When uploading a product image and entering basic price/name details, send a request to the backend which interfaces with the **Google Gemini API** to output a professional marketing description. (COMPLETED ✅ — Backend API ready)

- **Live Broadcast Control:** Generate secure Agora tokens and initiate live video broadcasts using device cameras. (COMPLETED ✅ — Backend API ready)

### C. Admin Control Panel (Web Dashboard)

- **Store Verification:** Approve, suspend, or activate local store accounts. (COMPLETED ✅ — Backend API ready)

- **Taxonomy Management:** Create and manage global categories and subcategories. (COMPLETED ✅ — Backend API ready)

- **System Oversight:** Monitor active live streams and platform metrics. (COMPLETED ✅ — Backend API ready)

---

## 4. UI/UX & Tailwind CSS Guidelines

- **Design Aesthetic:** Clean, modern, high-contrast, professional, and mobile-first , RTL.

- **Color Scheme:**
  - Primary Action: Vibrant Orange (`#F97316` / `bg-orange-600`) or Modern Blue.
  - Status Indicators: Emerald Green (`bg-emerald-500`) for active live streams, Rose Red (`bg-rose-600`) for expired/offline items.
  - Backgrounds: Neutral slate/gray (`bg-slate-50` to `bg-slate-900`).

- **Typography:** Sans-serif (`Inter` or `Roboto`), clean font weights.

- **Layouts:** Use Tailwind CSS Grid and Flexbox for responsive card layouts, floating action buttons (FAB) for store live streaming, and skeleton loaders for asynchronous data fetching.

---

## 6. How to Respond in Tasks

When asked to write code, design database schemas, or implement features for this project:

1. Always implement styling using **Tailwind CSS** for mobile don't use **Tailwind CSS**

1. Respect the role boundaries (Shopper = No Auth, Store Owner = Authenticated CRUD + AI + Live, Admin = Global Control).

1. Provide clean, production-ready code with concise explanations.

1. always use CDN if exist 

# AI Master Context & Instructions: Local Offers & Live Commerce Platform

> **Notice to AI Assistant:** You are acting as the lead software architect and full-stack developer for the **Local Offers & Live Commerce Platform**. When building code, answering questions, or generating components for this project, you must strictly follow the tech stack, architectural rules, and design guidelines outlined in this document.

---


## 4. Enhanced Database Schema (MySQL) (COMPLETED ✅ — 6 migrations created & seeded)

### Tables (6 Main Entities with Enhancements)

1. **users**
  - `id` (PK, INT, AUTO_INCREMENT)
  - `username` (VARCHAR 50, UNIQUE)
  - `password` (VARCHAR 255, hashed)
  - `role` (ENUM: 'admin', 'store_owner')
  - `email` (VARCHAR 100)
  - `phone` (VARCHAR 20)
  - `is_active` (TINYINT 1, DEFAULT 1)
  - `created_at` (TIMESTAMP)

1. **categories**
  - `id` (PK, INT, AUTO_INCREMENT)
  - `name` (VARCHAR 100)
  - `icon` (VARCHAR 255, nullable)
  - `sort_order` (INT, DEFAULT 0)
  - `is_active` (TINYINT 1, DEFAULT 1)

1. **subcategories**
  - `id` (PK, INT, AUTO_INCREMENT)
  - `category_id` (FK -> categories.id, ON DELETE CASCADE)
  - `name` (VARCHAR 100)
  - `is_active` (TINYINT 1, DEFAULT 1)

1. **stores**
  - `id` (PK, INT, AUTO_INCREMENT)
  - `owner_id` (FK -> users.id)
  - `name` (VARCHAR 150)
  - `description` (TEXT, nullable)
  - `logo` (VARCHAR 255, nullable)
  - `address` (VARCHAR 255)
  - `latitude` (DECIMAL 10,8, nullable) -- Added for local geo-sorting
  - `longitude` (DECIMAL 11,8, nullable) -- Added for local geo-sorting
  - `phone` (VARCHAR 20)
  - `whatsapp_number` (VARCHAR 20, nullable) -- Added for direct communication
  - `is_active` (TINYINT 1, DEFAULT 0) -- Admin must approve
  - `created_at` (TIMESTAMP)

1. **offers**
  - `id` (PK, INT, AUTO_INCREMENT)
  - `store_id` (FK -> stores.id, ON DELETE CASCADE)
  - `category_id` (FK -> categories.id)
  - `subcategory_id` (FK -> subcategories.id, nullable)
  - `title` (VARCHAR 200)
  - `description` (TEXT)
  - `original_price` (DECIMAL 10,2)
  - `offer_price` (DECIMAL 10,2)
  - `image` (VARCHAR 255)
  - `is_active` (TINYINT 1, DEFAULT 1)
  - `is_featured` (TINYINT 1, DEFAULT 0) -- Added for homepage spotlight
  - `is_ai_generated` (TINYINT 1, DEFAULT 0) -- Added to track AI descriptions
  - `view_count` (INT, DEFAULT 0) -- Added for analytics
  - `start_date` (DATETIME)
  - `end_date` (DATETIME)
  - `created_at` (TIMESTAMP)

1. **live_streams**
  - `id` (PK, INT, AUTO_INCREMENT)
  - `store_id` (FK -> stores.id, ON DELETE CASCADE)
  - `channel_name` (VARCHAR 100)
  - `agora_token` (TEXT)
  - `preview_image` (VARCHAR 255, nullable) -- Added for live feed thumbnail
  - `max_viewers` (INT, DEFAULT 0) -- Added for stream analytics
  - `is_active` (TINYINT 1, DEFAULT 1)
  - `started_at` (TIMESTAMP)
  - `ended_at` (TIMESTAMP, nullable)

---


## 6. Security & Architectural Standards (COMPLETED ✅)

- **Password Security:** All store owner and admin passwords must be hashed securely using standard hashing (`bcrypt` / `password_hash()`). (COMPLETED ✅)

- **Authentication:** Use **JWT (JSON Web Tokens)** for securing API endpoints between the frontend dashboards/mobile app and the backend. (COMPLETED ✅ — Sanctum tokens implemented)

- **Agora Security:** Never expose Agora credentials client-side for token creation; tokens must be dynamically generated server-side. (COMPLETED ✅ — AgoraTokenService generates tokens server-side)

- **Database Integrity:** Enforce foreign key constraints with cascading deletes where appropriate (e.g., deleting a store cascades to its offers). (COMPLETED ✅)

---

## 7. Backend Implementation Status (COMPLETED ✅)

The Laravel 12 + Sanctum backend is fully implemented, seeded, and tested. All 36 API routes are live and verified.

### 7.1 Project Structure

```
backend/
├── app/
│   ├── Http/
│   │   ├── Controllers/Api/
│   │   │   ├── AuthController.php          # register, login, logout, profile
│   │   │   ├── PublicController.php        # anonymous browsing (no auth)
│   │   │   ├── StoreOwnerController.php    # store/offer CRUD, AI, live streams
│   │   │   └── AdminController.php         # store approval, taxonomy, metrics
│   │   └── Middleware/
│   │       ├── EnsureUserIsAdmin.php
│   │       └── EnsureUserIsStoreOwner.php
│   ├── Models/
│   │   ├── User.php, Category.php, Subcategory.php
│   │   ├── Store.php, Offer.php, LiveStream.php
│   └── Services/
│       ├── GeminiAIService.php             # Google Gemini API integration
│       └── AgoraTokenService.php           # Server-side Agora token generation
├── database/
│   ├── migrations/  (6 migration files)
│   └── seeders/DatabaseSeeder.php          # Arabic sample data
├── routes/api.php                           # 36 API routes
├── config/services.php                      # Gemini & Agora config keys
└── bootstrap/app.php                        # Middleware aliases
```

### 7.2 API Endpoints (36 routes, all tested ✅)

#### Public (No Auth — for mobile app shoppers)
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/public/categories` | All categories with subcategories |
| GET | `/api/public/offers` | Paginated active offers (12/page) with store+category relations |
| GET | `/api/public/offers/{id}` | Single offer detail (increments view_count) |
| GET | `/api/public/stores` | All active stores |
| GET | `/api/public/stores/{id}` | Single store detail |
| GET | `/api/public/live-streams` | All active live streams with store info |
| GET | `/api/public/live-streams/{id}` | Single live stream detail |

#### Auth (No Auth required)
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/auth/register` | Register new store_owner |
| POST | `/api/auth/login` | Login → returns Sanctum bearer token |
| GET | `/api/auth/profile` | Get authenticated user (requires token) |
| POST | `/api/auth/logout` | Revoke current token (requires token) |

#### Store Owner (Auth: Sanctum token + role:store_owner)
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET/POST/PUT | `/api/store-owner/store` | Get/Create/Update own store |
| GET | `/api/store-owner/offers` | List own offers |
| POST | `/api/store-owner/offers` | Create offer |
| PUT | `/api/store-owner/offers/{id}` | Update own offer |
| DELETE | `/api/store-owner/offers/{id}` | Delete own offer |
| POST | `/api/store-owner/ai/generate-description` | AI description via Gemini (image upload) |
| POST | `/api/store-owner/live-streams/start` | Start broadcast → generates Agora token |
| POST | `/api/store-owner/live-streams/{id}/end` | End broadcast |

#### Admin (Auth: Sanctum token + role:admin)
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/admin/metrics` | Platform stats (stores, offers, streams, users) |
| GET | `/api/admin/users` | List all users |
| POST | `/api/admin/users/{id}/toggle-status` | Activate/suspend user |
| GET | `/api/admin/stores` | List all stores |
| POST | `/api/admin/stores/{id}/approve` | Approve store |
| POST | `/api/admin/stores/{id}/suspend` | Suspend store |
| GET/POST | `/api/admin/categories` | List/Create categories |
| PUT/DELETE | `/api/admin/categories/{id}` | Update/Delete category |
| POST | `/api/admin/subcategories` | Create subcategory |
| PUT/DELETE | `/api/admin/subcategories/{id}` | Update/Delete subcategory |
| GET | `/api/admin/live-streams` | Monitor all streams |
| POST | `/api/admin/live-streams/{id}/end` | Force-end a stream |

### 7.3 Test Credentials (from seeder)

| Role | Username | Password |
|------|----------|----------|
| Admin | `admin` | `admin123` |
| Store Owner | `storeowner` | `store123` |

### 7.4 Configuration Keys (in `.env`)

```env
# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=live_offers
DB_USERNAME=root
DB_PASSWORD=

# Sanctum
SANCTUM_STATEFUL_DOMAINS=localhost,127.0.0.1

# Google Gemini AI
GEMINI_API_KEY=your_gemini_key

# Agora RTC
AGORA_APP_ID=your_app_id
AGORA_APP_CERTIFICATE=your_app_certificate
```

### 7.5 Seeded Sample Data (Arabic)

- **4 Categories:** إلكترونيات, أزياء, طعام ومشروبات, منزل ومطبخ
- **10 Subcategories** (هواتف ذكية, ملابس رجالية, etc.)
- **2 Users:** admin + storeowner
- **1 Store:** متجر العروض الذهبية (Amman coordinates)
- **5 Offers:** هاتف ذكي, سماعات, قميص, وجبة عائلية, طقم أواني طبخ
- **1 Live Stream:** Active broadcast with demo token

### 7.6 How to Run

```bash
cd backend
php artisan migrate:fresh --seed   # Reset DB with seed data
php artisan serve                   # Start at http://127.0.0.1:8000
```

### 7.7 Next Steps (Frontend)

- [ ] **Mobile App (Ionic Angular):** Consume `/api/public/*` endpoints, integrate Agora RTC SDK for viewers — NOT STARTED
- [ ] **Store Owner Dashboard (Laravel Blade):** Consume `/api/store-owner/*` endpoints, Agora broadcaster, AI image upload — NOT STARTED
- [ ] **Admin Dashboard (Laravel Blade):** Consume `/api/admin/*` endpoints, manage taxonomy and stores — NOT STARTED
