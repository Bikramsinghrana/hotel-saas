# Multi-Tenant & Theme/Sub-Theme Workflow Guide

This document explains how **Multi-Tenancy**, **Authentication/Login**, and the **Theme & Sub-Theme Engine** work in this Hotel SaaS application.

---

## 1. System Architecture Overview

The application uses a **Single Database, Column-Scoped Multi-Tenancy** architecture with dynamic **Domain & Session-based Tenant Resolution** and a **Hierarchical Theme Engine** (Industry Theme &rarr; Layout Sub-Theme).

```
                      ┌────────────────────────────────────────┐
                      │             Incoming Request           │
                      └──────────────────┬─────────────────────┘
                                         │
                                         ▼
                      ┌────────────────────────────────────────┐
                      │    TenantMiddleware (Domain Match)     │
                      │   - Looks up domain in `tenants` table │
                      │   - Sets tenant_id & themes in session │
                      │   - Binds $tenant to Service Container │
                      └──────────────────┬─────────────────────┘
                                         │
            ┌────────────────────────────┴────────────────────────────┐
            ▼                                                         ▼
┌───────────────────────┐                                 ┌───────────────────────┐
│     Customer Side     │                                 │   Admin / Seller Side │
│  (Public / Guest /    │                                 │   (/seller/login &    │
│   Booking Pages)      │                                 │    /admin/* routes)   │
└───────────┬───────────┘                                 └───────────┬───────────┘
            │                                                         │
            ▼                                                         ▼
┌───────────────────────┐                                 ┌───────────────────────┐
│ Dynamic Theme & Layout│                                 │ Manage Hotel/Rooms &  │
│ Scoped by SubTheme:   │                                 │ Switch Themes/Layouts │
│ - Single Hotel Mode   │                                 │ in Admin Settings     │
│ - Multi Hotel Mode    │                                 └───────────────────────┘
└───────────────────────┘
```

---

## 2. Database Models & Relationships

### Core Entities

```
 ┌──────────────┐         1:N         ┌──────────────────┐
 │    Theme     ├────────────────────►│    SubTheme      │
 │ (e.g. Hotel, │                     │ (e.g. Luxury,    │
 │  Restaurant) │                     │  Budget, Resort) │
 └──────┬───────┘                     └────────┬─────────┘
        │ 1:N                                  │ 1:N
        │                                      │
        ▼                                      ▼
 ┌───────────────────────────────────────────────────────┐
 │                        Tenant                         │
 │  - id                                                 │
 │  - name                                               │
 │  - domain (e.g. hotel1.com, demo.localhost)           │
 │  - theme_id (FK -> themes)                            │
 │  - sub_theme_id (FK -> sub_themes)                    │
 │  - settings (JSON)                                    │
 └──────────────────────────┬────────────────────────────┘
                            │ 1:N
        ┌───────────────────┼────────────────────┐
        ▼                   ▼                    ▼
 ┌──────────────┐    ┌──────────────┐    ┌──────────────┐
 │    Users     │    │    Hotels    │    │ Navigations, │
 │  (tenant_id) │    │  (tenant_id) │    │ Coupons, etc.│
 └──────────────┘    └──────┬───────┘    └──────────────┘
                            │ 1:N
                            ▼
                     ┌──────────────┐
                     │    Rooms     │
                     └──────────────┘
```

1. **`Theme`** (`app/Models/Theme.php`):
   - Represents the primary industry/vertical (e.g. `hotel`, `restaurant`, `temple`).
   - Fields: `key`, `name`, `status`, `description`.

2. **`SubTheme`** (`app/Models/SubTheme.php`):
   - Represents the layout and operational model for a theme.
   - Fields: `theme_id`, `key`, `name`, `type` (`single_hotel`, `multi_hotel`, `restaurant`), `description`.
   - Examples:
     - `hotel` &rarr; `luxury` (Type: `single_hotel` - single property focus).
     - `hotel` &rarr; `budget` (Type: `multi_hotel` - multi-property directory).
     - `hotel` &rarr; `boutique` (Type: `single_hotel`).

3. **`Tenant`** (`app/Models/Tenant.php`):
   - Represents the hotel business / account.
   - Fields: `name`, `domain`, `theme_id`, `sub_theme_id`, `settings` (JSON), `address` (JSON).

4. **`User`** (`app/Models/User.php`):
   - Linked to tenant via `tenant_id`.
   - Uses Spatie roles: `super_admin`, `seller` / `hotel_admin`, `staff`, `customer`.

---

## 3. How Tenant Resolution Works

When any HTTP request reaches the server:

### Step 1: `TenantMiddleware` Execution
- Located in: `app/Http/Middleware/TenantMiddleware.php`
- Registered globally in `bootstrap/app.php` on the `web` middleware group.
- **Workflow**:
  1. Reads `$request->getHost()` or `$request->getHttpHost()`.
  2. Queries `Tenant::where('domain', ...)` in the database.
  3. If tenant exists:
     ```php
     session([
         'tenant_id' => $tenant->id,
         'theme'     => $tenant->theme?->key,
         'sub_theme' => $tenant->subTheme?->key
     ]);
     app()->instance('tenant', $tenant);
     ```

### Step 2: Global `tenant()` Helper Resolution
- Located in: `app/Helpers/helpers.php`
- Whenever code calls `tenant()`, it resolves using 3 fallback priorities:
  1. **Session**: `session('tenant_id')` (fastest and available across requests).
  2. **Auth User**: `auth()->user()->tenant_id` (backfills session if previously unset).
  3. **Container**: `app('tenant')` (set by `TenantMiddleware`).

---

## 4. Authentication & Login Workflows

The application distinguishes between standard customers and hotel managers/sellers.

```
                  ┌───────────────────────────────┐
                  │      User Enters Site         │
                  └──────────────┬────────────────┘
                                 │
                 Is it /login or /seller/login?
                                 │
           ┌─────────────────────┴─────────────────────┐
           ▼                                           ▼
   ┌───────────────┐                           ┌───────────────┐
   │    /login     │                           │ /seller/login │
   │ (Customer)    │                           │ (Admin/Seller)│
   └───────┬───────┘                           └───────┬───────┘
           │                                           │
           ▼                                           ▼
   Authenticate via                            Authenticate via
     AuthService                                 AuthService
           │                                           │
           ▼                                           ▼
 Redirect to Customer Dashboard               1. Role verification
     (/customer/dashboard)                    2. session(['tenant_id' => $user->tenant_id])
                                              3. Redirect to Admin Dashboard (/admin/dashboard)
```

### 1. Customer Login (`/login`)
- **Controller**: `App\Http\Controllers\Auth\LoginController::login()`
- **Flow**:
  1. Validates `email` and `password`.
  2. Authenticates through `AuthService`.
  3. Redirects to `/customer/dashboard`.

### 2. Seller / Hotel Admin Login (`/seller/login`)
- **Controller**: `App\Http\Controllers\Auth\LoginController::sellerLogin()`
- **Flow**:
  1. Validates `email` and `password`.
  2. Authenticates via `AuthService`.
  3. **Role Check**: Ensures the user is an admin/seller (redirects pure customers to customer dashboard).
  4. **Tenant Session Link**:
     ```php
     if ($user && $user->tenant_id) {
         session(['tenant_id' => $user->tenant_id]);
     }
     ```
  5. Redirects to `/admin/dashboard`.

---

## 5. Theme & Sub-Theme Workflow

### A. Theme Management in Admin Panel
- **Controller**: `App\Http\Controllers\Admin\SettingController.php`
- **Route**: `admin/settings`

1. **Switching Primary Industry / Main Theme** (`POST admin/settings/theme/main/activate`):
   - Sets `$tenant->theme_id = $theme->id`.
   - Automatically selects the first available `SubTheme` for that theme.
   - Sets `$tenant->sub_theme_id = $subTheme->id`.
   - Saves to DB and refreshes session (`theme`, `sub_theme`).

2. **Switching Sub-Theme / Layout** (`POST admin/settings/theme/activate`):
   - Updates `$tenant->sub_theme_id` to the chosen layout.
   - Refreshes session variables (`theme`, `sub_theme`).

### B. Single Hotel vs Multi Hotel Mode
The system uses the `isSingleHotel()` helper (`app/Helpers/helpers.php`):
```php
function isSingleHotel(): bool
{
    $t = tenant();
    return $t && $t->subTheme && $t->subTheme->type === 'single_hotel';
}
```
- **`single_hotel`**: UI simplifies room selection, highlights direct booking for one property.
- **`multi_hotel`**: UI presents hotel listing search filters and multi-hotel discovery.

### C. Theme View File Structure

Views are organized inside `resources/views/themes/`:

```
resources/views/themes/
├── hotel/                              <-- Theme Key
│   ├── admin/                          <-- Admin views specific to hotel vertical
│   │   ├── coupons/
│   │   ├── hotel/
│   │   ├── room/
│   │   └── terms/
│   ├── layouts/
│   │   └── admin.blade.php
│   ├── luxury/                         <-- Sub-Theme Key (Single Hotel)
│   │   ├── assets/
│   │   ├── components/
│   │   ├── config/
│   │   └── views/
│   │       ├── booking/
│   │       ├── hotel/
│   │       ├── layouts/
│   │       ├── pages/
│   │       └── room/
│   └── budget/                         <-- Sub-Theme Key (Multi Hotel)
│       ├── assets/
│       ├── components/
│       ├── config/
│       └── views/
├── resto/                              <-- Restaurant Theme Key
└── temple/                             <-- Temple Theme Key
```

### D. Frontend Page Rendering Flow (`PageController.php`)
When a visitor visits the homepage (`/`):
1. Resolve tenant via domain or session.
2. If the tenant has no active theme (`$tenant->theme_id == null`), render `coming-soon.blade.php`.
3. Load hotels, rooms, navigation, and coupons scoped to `where('tenant_id', $tenant->id)`.
4. Render theme views or fallback to default views (`welcome.blade.php`, `rooms/index.blade.php`).

---

## 6. Key Code Reference Summary

| Purpose | File Path | Key Functions / Logic |
| :--- | :--- | :--- |
| **Tenant Domain Middleware** | `app/Http/Middleware/TenantMiddleware.php` | Resolves domain, populates session & container |
| **Global Tenancy Helpers** | `app/Helpers/helpers.php` | `tenant()`, `isSingleHotel()`, `themeView()` |
| **Theme Path Helper** | `app/Helpers/hotel.php` | `HotelPath::view()`, `HotelPath::asset()` |
| **Login Controller** | `app/Http/Controllers/Auth/LoginController.php` | `login()`, `sellerLogin()`, tenant session assignment |
| **Theme Switch Controller** | `app/Http/Controllers/Admin/SettingController.php` | `activateMainTheme()`, `activateTheme()` |
| **Frontend Root Controller** | `app/Http/Controllers/PageController.php` | Scopes data per tenant & handles theme fallback |
| **Room & Booking Controller** | `app/Http/Controllers/RoomController.php` | Scoped room search, coupon validation, checkout |
| **Database Seeders** | `database/seeders/ThemeSeeder.php` | Seeds `hotel`, `restaurant` themes and sub-themes |
