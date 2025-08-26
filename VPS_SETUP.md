# 🚀 VPS Setup Guide - Fix Admin Login Issue

## ❌ **Problem Fixed:**
- AdminUserSeeder was not being called during database seeding
- Duplicate admin user creation was removed from UserSeeder

## ✅ **Solution Applied:**
1. **Updated DatabaseSeeder.php** - Now includes AdminUserSeeder
2. **Cleaned UserSeeder.php** - Removed duplicate admin user creation

## 🔧 **Steps to Fix on Your VPS:**

### **Step 1: Pull Latest Changes**
```bash
git pull origin main
```

### **Step 2: Clear All Caches**
```bash
php artisan optimize:clear
```

### **Step 3: Run Migrations (if needed)**
```bash
php artisan migrate
```

### **Step 4: Run Seeders (This will create the admin user)**
```bash
php artisan db:seed
```

### **Step 5: Verify Admin User Created**
```bash
php artisan tinker --execute="echo 'Admin users: ' . \App\Models\User::where('is_admin', true)->count();"
```

## 🔑 **Admin Login Credentials:**
- **Email**: `admin@example.com`
- **Password**: `password`

## 📋 **What Gets Created:**
- ✅ **Admin User**: `admin@example.com` (password: `password`)
- ✅ **Demo User**: `demo@example.com` (password: `password`)
- ✅ **Test User**: `test@example.com` (password: `password`)

## 🚨 **If Still Having Issues:**

### **Manual Admin User Creation:**
```bash
php artisan tinker
```

```php
\App\Models\User::create([
    'name' => 'Admin User',
    'email' => 'admin@example.com',
    'password' => \Illuminate\Support\Facades\Hash::make('password'),
    'is_admin' => true,
]);
```

### **Check Database Connection:**
```bash
php artisan db:show
```

### **Verify File Permissions:**
```bash
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
chown -R www-data:www-data storage/
chown -R www-data:www-data bootstrap/cache/
```

## 🎯 **Expected Result:**
After running `php artisan db:seed`, you should see:
```
Admin user created successfully!
Email: admin@example.com
Password: password
```

Then you can login with these credentials at your admin login page.
