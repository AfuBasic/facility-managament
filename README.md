# 🏢 Optima FM - Facility Management System

A comprehensive web-based facility management platform built with Laravel 12, Livewire 3, and Flux UI. Designed to manage multiple facilities, work orders, assets, events, and maintenance operations across organizations with multi-tenant architecture.

---

## ✨ Features

### 📋 Work Order Management

- Complete lifecycle management: Report → Approve → Assign → Start → Complete → Close
- Priority levels (low, medium, high, critical) with SLA tracking
- Pause/resume functionality with reason tracking
- Asset allocation for required materials
- Comprehensive audit trail with full history
- Export to Excel and PDF

### 🏗️ Facility & Space Management

- Multi-facility support per organization
- Spaces (rooms, zones, areas) within facilities
- Store/inventory points management
- User assignments with designations

### 📦 Asset Management

- Complete asset/inventory tracking with serial numbers
- Checkout/check-in system with user tracking
- Asset condition history and audit trail
- Image gallery support via Cloudinary
- Supplier/contact references

### ⏱️ SLA Policy Management

- Define response and resolution times by priority
- Business hours calculation support
- Automatic policy application to new work orders
- Breach detection and tracking

### 📊 Reporting & Analytics

- Work order status distribution
- SLA compliance reports
- Technician performance metrics
- Cost summary reports
- Interactive charts with ApexCharts

### 💬 Communication

- Direct messaging between users
- Real-time notifications via Laravel Reverb
- Unread message tracking

### 📅 Events & Scheduling

- Virtual events with auto-generated Jitsi meeting links
- Physical event management
- Attendee invitations with RSVP tracking

### 👥 Contacts Management

- Vendor and supplier contact management
- Contact grouping and categorization
- Hierarchical relationships

### 🔐 User & Role Management

- Role-based access control (RBAC)
- Facility user assignments
- User invitation system

### 🛡️ Admin Module

- Platform-wide oversight dashboard
- User and client account management
- User impersonation for support
- Admin notifications

---

## 🛠️ Technology Stack

| Category          | Technology                       |
| ----------------- | -------------------------------- |
| 🔧 Backend        | Laravel 12, PHP 8.5              |
| 🎨 Frontend       | Livewire 3, Flux UI v2           |
| 🎭 Styling        | Tailwind CSS v4                  |
| ⚡ Real-time      | Laravel Reverb, Laravel Echo     |
| 🔑 Authentication | Laravel Socialite (Google OAuth) |
| 🛡️ Authorization  | Spatie Laravel Permission        |
| 📁 File Storage   | Cloudinary                       |
| 📄 Export/Reports | Maatwebsite Excel, DomPDF        |
| 🧪 Testing        | Pest v4                          |
| ✅ Code Quality   | Laravel Pint                     |

---

## 🧑‍💻 Development

**Run the development server with hot reload:**

```bash
composer run dev
```

Or run services separately:

```bash
npm run dev              # Vite dev server
php artisan serve        # Laravel server
php artisan reverb:start # WebSocket server
```

---

## 🧪 Testing

Run the test suite:

```bash
php artisan test
```

Run specific tests:

```bash
php artisan test --filter=WorkOrderTest
php artisan test tests/Feature/Auth/
```

Run with coverage:

```bash
php artisan test --coverage
```

---

## ✅ Code Quality

Format code with Laravel Pint:

```bash
vendor/bin/pint
```

Check formatting without fixing:

```bash
vendor/bin/pint --test
```

---

## 📁 Project Structure

```
app/
├── Http/
│   ├── Controllers/      # HTTP controllers
│   └── Middleware/       # Custom middleware
├── Livewire/
│   ├── Admin/            # Admin panel components
│   ├── App/              # Main application components
│   ├── Auth/             # Authentication components
│   └── Public/           # Public page components
├── Models/               # Eloquent models
├── Services/             # Business logic services
└── Concerns/             # Shared traits

resources/
├── views/
│   ├── components/       # Blade components
│   ├── layouts/          # Application layouts
│   └── livewire/         # Livewire component views

tests/
├── Feature/              # Feature tests
└── Unit/                 # Unit tests
```

---

## 🗺️ Key Routes

| Route                 | Description                 |
| --------------------- | --------------------------- |
| `/`                   | 🏠 Public home page         |
| `/login`              | 🔑 User authentication      |
| `/app/dashboard`      | 📊 Main dashboard           |
| `/app/facilities`     | 🏗️ Facility management      |
| `/app/work-orders`    | 📋 Work order management    |
| `/app/assets/{asset}` | 📦 Asset details            |
| `/app/sla-policy`     | ⏱️ SLA policy configuration |
| `/app/reports/*`      | 📈 Reporting module         |
| `/admin`              | 🛡️ Admin panel              |

---

## 🏢 Multi-Tenancy

The application supports multiple client accounts with complete data isolation:

- 🔹 Users can belong to multiple organizations
- 🔹 Client context is maintained via session
- 🔹 All data is scoped to the current client account
- 🔹 Users can switch between client accounts

---

## 📜 License

This project is proprietary software. All rights reserved.
