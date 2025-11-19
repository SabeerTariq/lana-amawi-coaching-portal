# Mail Setup Documentation

## Overview

The application uses a **hybrid mail configuration system** that allows SMTP settings to be managed from the admin dashboard and stored in the database, with fallback to `.env` file configuration.

## How It Works

### 1. **Configuration Storage**

Mail settings can be stored in two places:

#### A. Database (Primary - Managed via Admin Dashboard)
- **Table**: `settings`
- **Group**: `smtp`
- **Keys**:
  - `mail_mailer` - Mail driver (smtp, mailgun, ses)
  - `mail_host` - SMTP host address
  - `mail_port` - SMTP port number
  - `mail_username` - SMTP username
  - `mail_password` - SMTP password
  - `mail_encryption` - Encryption type (tls, ssl, or empty)
  - `mail_from_address` - Default "from" email address
  - `mail_from_name` - Default "from" name

#### B. Environment File (Fallback)
- **File**: `.env`
- **Variables**:
  - `MAIL_MAILER=smtp`
  - `MAIL_HOST=sandbox.smtp.mailtrap.io`
  - `MAIL_PORT=2525`
  - `MAIL_USERNAME=your_username`
  - `MAIL_PASSWORD=your_password`
  - `MAIL_ENCRYPTION=tls`
  - `MAIL_FROM_ADDRESS=noreply@lana-amawi.com`
  - `MAIL_FROM_NAME="Lana Amawi Coaching"`

### 2. **Dynamic Configuration Loading**

The `AppServiceProvider` (located at `app/Providers/AppServiceProvider.php`) dynamically loads mail settings from the database during application boot:

```php
protected function configureMailFromDatabase(): void
{
    // Gets settings from database
    $mailHost = Setting::get('mail_host');
    $mailPort = Setting::get('mail_port');
    // ... etc
    
    // Updates Laravel's config dynamically
    Config::set('mail.mailers.smtp.host', $mailHost);
    Config::set('mail.mailers.smtp.port', $mailPort);
    // ... etc
}
```

**Priority Order:**
1. **Database settings** (if exists) - Used first
2. **`.env` file** - Used as fallback if database setting is empty/null

### 3. **Admin Dashboard Management**

Admins can manage SMTP settings through:
- **Route**: `/admin/settings`
- **View**: `resources/views/admin/settings.blade.php`
- **Controller Method**: `AdminController::updateSmtpSettings()`

**Features:**
- Update SMTP host, port, username, password
- Change encryption (TLS/SSL)
- Set "from" address and name
- Switch between mail drivers (SMTP, Mailgun, SES)
- Settings are saved to database immediately

### 4. **Mail Usage in Application**

The application sends emails in several places:

#### A. Program Agreement Sent
- **File**: `app/Mail/ProgramAgreementSent.php`
- **Usage**: When agreement is sent to client
- **Location**: `app/Http/Controllers/ProgramController.php` (line 847)

#### B. Client Credentials
- **File**: `app/Mail/ClientCredentials.php`
- **Usage**: When admin creates/resets client password
- **Location**: `app/Http/Controllers/BookingController.php` (lines 116, 143)

**Example Usage:**
```php
Mail::to($user->email)->send(new ClientCredentials($user, $password));
```

Laravel automatically uses the configured mail settings (from database or .env) when sending.

## Configuration Flow

```
┌─────────────────────────────────────────────────────────┐
│  Admin Updates SMTP Settings in Dashboard               │
└────────────────────┬────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────┐
│  Settings Saved to Database (settings table)            │
└────────────────────┬────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────┐
│  AppServiceProvider::boot()                              │
│  - Reads settings from database                          │
│  - Updates Laravel config dynamically                    │
└────────────────────┬────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────┐
│  Mail::send() uses configured settings                  │
│  - Uses database settings if available                  │
│  - Falls back to .env if database setting is null       │
└─────────────────────────────────────────────────────────┘
```

## Setting Up SMTP

### Option 1: Via Admin Dashboard (Recommended)

1. Log in as admin
2. Navigate to **Settings** → **SMTP Email Configuration**
3. Fill in your SMTP details:
   - **Mail Driver**: SMTP
   - **SMTP Host**: e.g., `smtp.gmail.com`
   - **SMTP Port**: e.g., `587` (TLS) or `465` (SSL)
   - **Encryption**: TLS or SSL
   - **SMTP Username**: Your email address
   - **SMTP Password**: Your email password or app password
   - **From Email**: The email address to send from
   - **From Name**: Display name for emails
4. Click **Save SMTP Settings**

### Option 2: Via .env File

Edit `.env` file:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@lana-amawi.com
MAIL_FROM_NAME="Lana Amawi Coaching"
```

## Common SMTP Providers

### Gmail
- **Host**: `smtp.gmail.com`
- **Port**: `587` (TLS) or `465` (SSL)
- **Username**: Your Gmail address
- **Password**: App-specific password (not your regular password)
- **Encryption**: TLS or SSL

### Outlook/Office 365
- **Host**: `smtp.office365.com`
- **Port**: `587`
- **Encryption**: TLS

### Mailtrap (Testing)
- **Host**: `sandbox.smtp.mailtrap.io`
- **Port**: `2525`
- **Encryption**: None or TLS

### SendGrid
- **Host**: `smtp.sendgrid.net`
- **Port**: `587`
- **Username**: `apikey`
- **Password**: Your SendGrid API key
- **Encryption**: TLS

## Testing Mail Configuration

After updating SMTP settings:

1. **Clear config cache** (if using config caching):
   ```bash
   php artisan config:clear
   ```

2. **Test email sending**:
   - Create a test booking or send an agreement
   - Check if email is received
   - Check Laravel logs: `storage/logs/laravel.log`

3. **Verify settings are loaded**:
   ```php
   // In tinker or controller
   dd(config('mail.mailers.smtp'));
   ```

## Troubleshooting

### Emails Not Sending

1. **Check database settings**:
   ```sql
   SELECT * FROM settings WHERE `group` = 'smtp';
   ```

2. **Verify .env fallback**:
   - Check if `.env` has correct SMTP credentials
   - Run `php artisan config:clear`

3. **Check Laravel logs**:
   ```bash
   tail -f storage/logs/laravel.log
   ```

4. **Test SMTP connection**:
   - Use a tool like Mailtrap for testing
   - Verify credentials are correct

### Settings Not Updating

1. **Clear config cache**:
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

2. **Check if settings table exists**:
   ```bash
   php artisan migrate:status
   ```

3. **Verify database connection**:
   - Ensure database is accessible
   - Check if settings are being saved

## Security Notes

⚠️ **Important Security Considerations:**

1. **SMTP Passwords**: Stored in database as plain text. Consider encryption for production.
2. **Environment Variables**: `.env` file should never be committed to git.
3. **App Passwords**: Use app-specific passwords for Gmail/Google accounts, not regular passwords.
4. **Access Control**: Only admins should access SMTP settings page.

## Code References

- **Service Provider**: `app/Providers/AppServiceProvider.php`
- **Settings Model**: `app/Models/Setting.php`
- **Admin Controller**: `app/Http/Controllers/AdminController.php`
- **Mail Classes**: `app/Mail/`
- **Config File**: `config/mail.php`
- **Settings View**: `resources/views/admin/settings.blade.php`

