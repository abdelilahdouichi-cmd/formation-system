# Authentication & Authorization Map – Example App

**Date:** 2026-02-15  
**Framework:** Laravel 12  
**Guard:** Session-based (web) · **Provider:** Eloquent User model  
**Authorization:** Custom middleware + Policies  
**Role System:** 3-tier enum (SuperAdmin, Admin, User)

---

## 1. Authentication Setup

### Guards & Providers

| Component | Config | Details |
|-----------|--------|---------|
| **Default Guard** | `web` (session-based) | `config/auth.php` line 19 |
| **User Provider** | `eloquent` (Eloquent User model) | `config/auth.php` line 56 |
| **User Model** | `App\Models\User` | Authenticatable; has `role` (UserRole enum) and `is_active` (bool) |
| **Sessions Table** | `sessions` | Stores session data; user_id indexed |

### Password Reset

| Setting | Value | Details |
|---------|-------|---------|
| **Token Table** | `password_reset_tokens` | Created in `0001_01_01_000000_create_users_table.php` |
| **Token Expiry** | 60 minutes | `config/auth.php` line 99 |
| **Reset Throttle** | 60 seconds | `config/auth.php` line 100 |
| **Password Broker** | `users` | Default broker tied to users provider |

### Session & Cookie Config

| Setting | Value | Details |
|---------|-------|---------|
| **Session Driver** | `database` | `.env` SESSION_DRIVER |
| **Lifetime** | 120 minutes | `.env` SESSION_LIFETIME |
| **Encrypt Sessions** | `false` | `.env` SESSION_ENCRYPT |
| **Cookie Path** | `/` | `.env` SESSION_PATH |
| **Cookie Domain** | `null` | Default (current domain) |

---

## 2. Authentication Endpoints

### Login / Logout / Password Reset

| Method | Path | Controller@Method | Middleware | Auth Required | Purpose |
|--------|------|-------------------|------------|---------------|---------|
| GET | `/login` | `AuthenticatedSessionController@create` | `guest` | No | Show login form (`auth.login`) |
| POST | `/login` | `AuthenticatedSessionController@store` | `guest` | No | Submit login (validates `is_active`, redirects admin users → `/admin`, else → `/dashboard`) |
| POST | `/logout` | `AuthenticatedSessionController@destroy` | `auth` | **Yes** | Invalidate session & tokens |
| GET | `/forgot-password` | `PasswordResetLinkController@create` | `guest` | No | Show password reset request form |
| POST | `/forgot-password` | `PasswordResetLinkController@store` | `guest` | No | Send password reset email |
| GET | `/reset-password/{token}` | `NewPasswordController@create` | `guest` | No | Show password reset form (with token) |
| POST | `/reset-password` | `NewPasswordController@store` | `guest` | No | Confirm new password (token validated) |

### Notes on Auth Controllers
- **Login Logic:** `AuthenticatedSessionController@store`
  - Validates email + password + active status (custom check: `'is_active' => true`)
  - Supports "remember me" checkbox
  - Session regeneration on success
  - Smart redirect based on `canAccessAdmin()` method
- **Password Reset:** Uses Laravel's default `PasswordResetLinkController` & `NewPasswordController` (not shown in detail but standard flow)

---

## 3. Authorization Structure

### Role Enum (UserRole)

```php
// app/Enums/UserRole.php
enum UserRole: string {
    SuperAdmin = 'super_admin'   // Full access
    Admin = 'admin'              // Restricted admin access
    User = 'user'                // Regular user (no admin)
}
```

### User Model Helper Methods

| Method | Returns | Purpose |
|--------|---------|---------|
| `isSuperAdmin()` | bool | True if role === SuperAdmin |
| `isAdmin()` | bool | True if role === Admin |
| `canAccessAdmin()` | bool | True if SuperAdmin OR Admin (used for redirect after login) |

### Authorization Middleware

#### EnsureRole Middleware

**File:** `app/Http/Middleware/EnsureRole.php`

```php
public function handle(Request $request, Closure $next, string ...$roles): Response {
    $user = $request->user();
    if (!$user || $roles === []) abort(403);
    $role = $user->role instanceof UserRole ? $user->role->value : (string) $user->role;
    if (!in_array($role, $roles, true)) abort(403);
    return $next($request);
}
```

**Behavior:**
- Checks if user is authenticated
- Validates user's role against provided roles (variadic)
- Aborts with 403 if role not in allowed list
- Strict comparison (`in_array(..., true)`)
- Role converted to string safely (handles both enum and string)

**Registration:** `bootstrap/app.php` (alias: `role`)

---

### Policies

#### UserPolicy

**File:** `app/Policies/UserPolicy.php`  
**Registration:** `AppServiceProvider@boot` line 19

| Method | Parameters | Logic | Notes |
|--------|-----------|-------|-------|
| `viewAny` | `(User $user)` | `canAccessAdmin()` | List users (admins only) |
| `view` | `(User $user, User $model)` | `canAccessAdmin()` | View user details (admins only) |
| `create` | `(User $user)` | `canAccessAdmin()` | Create user form (admins only) |
| `update` | `(User $user, User $model)` | ✓ Must `canAccessAdmin()`; ✓ Cannot edit SuperAdmin unless SuperAdmin; ✓ Cannot edit Admin unless SuperAdmin | Edit user (strict hierarchy) |
| `delete` | `(User $user, User $model)` | ✓ Must `canAccessAdmin()`; ✓ Cannot delete SuperAdmin; ✓ Cannot delete Admin unless SuperAdmin; ✓ Cannot delete self | Delete user (prevents self-deletion) |
| `updateRole` | `(User $user, User $model)` | Only SuperAdmin; must not be self | Change user's role (SuperAdmin only) |
| `updateStatus` | `(User $user, User $model)` | ✓ Must `canAccessAdmin()`; ✓ Cannot change SuperAdmin status; ✓ Cannot change Admin status unless SuperAdmin; ✓ Cannot change self | Toggle is_active |
| `updatePassword` | `(User $user, User $model)` | ✓ Must `canAccessAdmin()`; ✓ Cannot change SuperAdmin password unless SuperAdmin; ✓ Cannot change Admin password unless SuperAdmin; ✓ Can change own password OR be SuperAdmin | Reset user password |

**Key Security Patterns:**
- Role hierarchy enforced: SuperAdmin > Admin > User
- Self-modification guards (cannot delete/disable self)
- SuperAdmin bypass for editing Admin-role users

---

## 4. Protected Routes Map

### All Routes Summary

| Method | Path | Controller@Method | Middleware | Required Role | Policy Check | Purpose |
|--------|------|-------------------|------------|---------------|--------------|-|--------|
| GET | `/` | Anonymous closure | — | — | — | Root: redirect to dashboard if auth, else welcome |
| GET | `/login` | Auth\AuthenticatedSessionController@create | `guest` | — | — | Show login form |
| POST | `/login` | Auth\AuthenticatedSessionController@store | `guest` | — | — | Process login |
| GET | `/forgot-password` | Auth\PasswordResetLinkController@create | `guest` | — | — | Password reset request form |
| POST | `/forgot-password` | Auth\PasswordResetLinkController@store | `guest` | — | — | Send reset email |
| GET | `/reset-password/{token}` | Auth\NewPasswordController@create | `guest` | — | — | Password reset form |
| POST | `/reset-password` | Auth\NewPasswordController@store | `guest` | — | — | Confirm new password |
| POST | `/logout` | Auth\AuthenticatedSessionController@destroy | `auth` | Any (must be logged in) | — | Logout |
| GET | `/dashboard` | Anonymous closure | `auth` | Any (must be logged in) | — | User dashboard |
| **GET** | **/admin** | Admin\DashboardController@index | `auth`,`role:admin,super_admin` | Admin or SuperAdmin | — | Admin panel homepage |
| **GET** | **/admin/users** | Admin\UserController@index | `auth`,`role:admin,super_admin` | Admin or SuperAdmin | `viewAny` | List users (paginated, filterable) |
| **GET** | **/admin/users/create** | Admin\UserController@create | `auth`,`role:admin,super_admin` | Admin or SuperAdmin | `create` | Show create user form |
| **POST** | **/admin/users** | Admin\UserController@store | `auth`,`role:admin,super_admin` | Admin or SuperAdmin | `create` (implicit) | Create new user |
| **GET** | **/admin/users/{user}/edit** | Admin\UserController@edit | `auth`,`role:admin,super_admin` | Admin or SuperAdmin | `update` | Show edit user form |
| **PATCH/PUT** | **/admin/users/{user}** | Admin\UserController@update | `auth`,`role:admin,super_admin` | Admin or SuperAdmin | `update` | Update user |
| **DELETE** | **/admin/users/{user}** | Admin\UserController@destroy | `auth`,`role:admin,super_admin` | Admin or SuperAdmin | `delete` | Delete user |

### Admin Routes Details

**Prefix:** `/admin`  
**Name Prefix:** `admin.`  
**Middleware Stack:** `['auth', 'role:admin,super_admin']`

**Resource Mapping:**

```
Route::resource('users', UserController::class)->except('show');
```

This generates 7 routes (show excluded):

| HTTP Method | URI | Name | Action |
|-------------|-----|------|--------|
| GET | `/admin/users` | `admin.users.index` | index |
| GET | `/admin/users/create` | `admin.users.create` | create |
| POST | `/admin/users` | `admin.users.store` | store |
| GET | `/admin/users/{user}` | — | (show - excluded) |
| GET | `/admin/users/{user}/edit` | `admin.users.edit` | edit |
| PATCH/PUT | `/admin/users/{user}` | `admin.users.update` | update |
| DELETE | `/admin/users/{user}` | `admin.users.destroy` | destroy |

---

## 5. Key Files Reference

| File | Purpose | Key Lines/Methods |
|------|---------|-------------------|
| `config/auth.php` | Auth config (guard, provider, password reset) | Line 17–116 |
| `bootstrap/app.php` | Middleware registration | Line 15 (`role` middleware alias) |
| `app/Enums/UserRole.php` | 3-tier role enum | SuperAdmin, Admin, User |
| `app/Models/User.php` | User model; role casting; helper methods | `isSuperAdmin()`, `isAdmin()`, `canAccessAdmin()` |
| `app/Policies/UserPolicy.php` | Authorization policy for User CRUD | 8 methods (viewAny, view, create, update, delete, updateRole, updateStatus, updatePassword) |
| `app/Http/Middleware/EnsureRole.php` | Custom role-checking middleware | Validates user role against variadic roles param |
| `app/Providers/AppServiceProvider.php` | Service provider; policy registration | Line 19 |
| `app/Http/Controllers/Auth/AuthenticatedSessionController.php` | Login/Logout | `create()`, `store()`, `destroy()` |
| `app/Http/Controllers/Admin/DashboardController.php` | Admin home | Counts users by role |
| `app/Http/Controllers/Admin/UserController.php` | User CRUD (with authorizeResource) | index, create, store, edit, update, destroy |
| `routes/web.php` | All web routes | 41 lines total |
| `database/migrations/0001_01_01_000000_create_users_table.php` | Users, password_reset_tokens, sessions tables | email_verified_at, remember_token |
| `database/migrations/2026_02_15_010610_add_role_and_status_to_users_table.php` | Add phone, role, is_active, last_login_at | Added after initial setup |

---

## 6. Authorization Checks & Usage Patterns

### In Controllers

#### UserController (Admin Resource)

```php
public function __construct() {
    $this->authorizeResource(User::class, 'user');
}
```

**Effect:** Automatically authorizes each action against the `UserPolicy`:
- `index()` → `viewAny()`
- `show()` — N/A (excluded from routes)
- `create()` → `create()`
- `store()` → `create()` (implicit)
- `edit()` → `update()`
- `update()` → `update()`
- `destroy()` → `delete()`

#### UserController Store Method

```php
public function store(StoreUserRequest $request): RedirectResponse {
    $data = $request->validated();
    
    // Only SuperAdmin can assign admin roles
    if (!$request->user()->isSuperAdmin()) {
        $data['role'] = UserRole::User->value;
    }
    // ... create user
}
```

#### UserController Update Method

```php
public function update(UpdateUserRequest $request, User $user): RedirectResponse {
    $data = $request->validated();
    
    // Policy prevents unauthorized role changes
    if (!$request->user()->can('updateRole', $user)) {
        $data['role'] = $user->role->value;  // Preserve existing role
    }
    
    // Prevent self-deactivation
    if ($request->user()->id === $user->id) {
        $data['is_active'] = true;
    }
    // ... update user
}
```

### Using Policies Elsewhere

```php
// In a controller or view:
if ($user->can('update', $targetUser)) {
    // Allow edit
}

if ($user->can('delete', $targetUser)) {
    // Allow delete
}

// Check multiple permission:
if ($user->can('updatePassword', $targetUser)) {
    // Allow password reset
}
```

---

## 7. Session & Cookie Security

| Setting | Value | Security Note |
|---------|-------|----------------|
| **Session Driver** | `database` | Persisted to `sessions` table; secure across distributed servers |
| **Session Lifetime** | 120 min | Moderate; balance between convenience & security |
| **Session Encrypt** | `false` | Payload stored plaintext in DB; OK for database-backed sessions |
| **CSRF Token** | Regenerated on login | Session ID regenerated in `AuthenticatedSessionController@store` |
| **Remember Token** | Generated on user creation | Can be used for "Remember Me" (optional in login) |
| **Last Login Tracking** | Event listener | `Login` event updates `last_login_at` in real-time |

---

## 8. Risks & Observations

### ✅ Strengths
1. **Role-based access control (RBAC)** with 3-tier hierarchy prevents privilege escalation
2. **Policy-based authorization** ensures consistent checks across all CRUD operations
3. **Hierarchical enforcement:** SuperAdmin > Admin > User (strict, testable)
4. **Self-modification guards:** Cannot delete/disable self (prevents locking out all admins)
5. **Session-based auth** with database persistence is transparent and secure
6. **Password reset** uses standard Laravel flow (tokens, expiry, throttling)
7. **Login activity tracking** with `last_login_at` (audit trail)
8. **Active user check** at login (`is_active` flag prevents access for disabled accounts)

### ⚠️ Potential Risks & Recommendations

| Risk | Severity | Notes | Recommendation |
|------|----------|-------|-----------------|
| **No API Routes (no routes/api.php)** | Low | If API is added later, ensure Sanctum/JWT is configured | Use Laravel Sanctum for token-based API auth; add `config/sanctum.php` |
| **Hardcoded role strings in middleware** | Low | Role names are string literals; refactor to use enum | Use `EnsureRole::class` with enum values: `'role:' . UserRole::Admin->value . ',' . UserRole::SuperAdmin->value` |
| **No email verification flow** | Medium | `email_verified_at` exists but no verification gate | Add `MustVerifyEmail` trait to User model if critical; enforce in register flow |
| **Password reset emails not configured** | Medium | Uses `log` mailer (`.env` MAIL_MAILER=log); no actual emails sent in local | Configure SMTP/SendGrid in production; test reset flow |
| **No Two-Factor Authentication (2FA)** | Low | Not implemented; can be added with Laravel Fortify/custom | Consider `laravel/fortify` for optional 2FA if high-security requirement |
| **No audit logging** | Medium | Policy checks happen silently; no record of authorization denials | Implement middleware to log denied access attempts; track policy failures |
| **Session fixation not explicitly mitigated** | Low | Laravel default mitigation is in place | Ensure `SESSION_SECURE=true` & `SESSION_HTTP_ONLY=true` in production `.env` |
| **CORS not configured** | Low | No CORS headers; OK for single-domain app | If adding SPA frontend, configure `config/cors.php` |
| **No permission-based access beyond Role** | Low | Only role check; no granular permissions per resource | If needed, add Spatie's `laravel-permission` package with Roles + Permissions model |
| **Admin user creation via seeder only** | Low | No UI to create initial admin; depends on seeder | Add artisan command for safe admin creation in production |

### Hardcoded Role Strings

**Locations:**
- `routes/web.php` line 37: `'role:admin,super_admin'`
- `app/Http/Controllers/Admin/UserController.php` line 74: `$data['role'] = UserRole::User->value;`

**Risk:** If role enum values change, string literals become stale.

**Fix:**
```php
// In routes/web.php
Route::middleware(['auth', 'role:' . UserRole::Admin->value . ',' . UserRole::SuperAdmin->value])
    ->group(...);

// Or use helper:
// Route::middleware(['auth', 'role:admin_or_super_admin'])
//   ->group(...);
```

---

## 9. Testing Checklist

### Auth Flow Tests
- [ ] Login with valid credentials → redirects to `/admin` if admin, else `/dashboard`
- [ ] Login with invalid email → shows error message
- [ ] Login with incorrect password → shows error message
- [ ] Login with inactive user → shows error message
- [ ] Logout → invalidates session and redirects to login
- [ ] "Remember me" → generates remember token and extends session
- [ ] Password reset request → sends email with token
- [ ] Password reset with valid token → updates password and logs in
- [ ] Password reset with expired token → shows error

### Authorization Policy Tests
- [ ] SuperAdmin can create users with any role
- [ ] Admin cannot create users with Admin role (only User)
- [ ] SuperAdmin can edit Admin users; Admin cannot
- [ ] Admin cannot delete SuperAdmin
- [ ] User cannot access `/admin` routes (403 Forbidden)
- [ ] Cannot delete self (policy prevents)
- [ ] Cannot downgrade SuperAdmin role (if edit attempted)
- [ ] `updatePassword` policy enforced
- [ ] `updateStatus` policy enforced (SuperAdmin cannot be deactivated)

### Route Access Tests
- [ ] GET `/admin` with Admin role → 200 OK
- [ ] GET `/admin` with User role → 403 Forbidden
- [ ] GET `/admin/users` without auth → redirects to login
- [ ] POST `/admin/users` (create) with Admin role → 200 (form) or redirect (success)
- [ ] DELETE `/admin/users/{self}` → 403 Forbidden (policy)

---

## 10. Summary Table: Auth vs Authz

| Aspect | Implementation | Technology |
|--------|----------------|-----------|
| **Identify User** | Email + Password | Session guard (web) |
| **Persist Auth** | Database sessions (`sessions` table) | Session middleware |
| **Remember User** | `remember_token` column | Optional in login |
| **Reset Password** | Email token + expiry | `password_reset_tokens` table |
| **Define Roles** | `UserRole` enum (SuperAdmin, Admin, User) | PHP enum + `role` column (String cast) |
| **Check Role** | `EnsureRole` middleware | Variadic middleware param |
| **Check Action** | `UserPolicy` with hierarchy | Policy + `authorizeResource()` |
| **Prevent Self-Harm** | Policy gates + controller logic | `update()`, `delete()`, `updateStatus()` methods |

---

## 11. Configuration Checklist for Production

Before deploying to production:

- [ ] **`. env` Production Values**
  - [ ] `APP_DEBUG=false` (disable debug mode)
  - [ ] `APP_ENV=production`
  - [ ] `SESSION_SECURE=true` (HTTPS only)
  - [ ] `SESSION_HTTP_ONLY=true` (prevent XSS theft)
  - [ ] `MAIL_MAILER=*` (configure SMTP: SendGrid, AWS SES, etc.)
  - [ ] `AUTH_PASSWORD_RESET_TOKEN_TABLE` (ensure migration exists)

- [ ] **Database**
  - [ ] All migrations applied (`php artisan migrate --force`)
  - [ ] `users`, `password_reset_tokens`, `sessions` tables exist
  - [ ] Indexes on `email`, `user_id (sessions)` for performance

- [ ] **Security Headers**
  - [ ] Add `X-Frame-Options: sameorigin` (clickjacking protection)
  - [ ] Add `X-Content-Type-Options: nosniff`
  - [ ] Add `Strict-Transport-Security` (HSTS) header

- [ ] **Monitoring**
  - [ ] Log failed login attempts
  - [ ] Monitor unauthorized access attempts (403 responses)
  - [ ] Alert on unusual password reset requests

---

**Doc Version:** 1.0 | **Generated:** 2026-02-15
