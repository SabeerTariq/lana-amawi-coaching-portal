# Lana Amawi Coaching Portal

A comprehensive coaching portal built with Laravel, Bootstrap, and MySQL for managing client appointments, messaging, and administrative tasks.

## Features

### Public Features
- **Booking Form**: Public booking form for new clients to schedule sessions
- **Authentication**: Login and registration system

### Client Portal
- **Dashboard**: Overview with next appointment, recent messages, and statistics
- **Appointments**: View upcoming and past appointments with reschedule/cancel options
- **Messages**: Secure 1-on-1 chat interface with Lana
- **Profile**: Personal information and booking history

### Admin Portal
- **Dashboard**: Statistics, today's appointments, and quick actions
- **Client Management**: View all clients and their profiles
- **Messages**: Chat interface to communicate with clients
- **Calendar**: Monthly calendar view of all appointments
- **Settings**: Admin profile and system settings

## Technology Stack

- **Backend**: Laravel 11
- **Frontend**: HTML, Bootstrap 5, JavaScript
- **Database**: MySQL
- **Authentication**: Laravel's built-in authentication
- **Styling**: Custom CSS with Bootstrap components

## Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd lana-amawi
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies**
   ```bash
   npm install
   ```

4. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configure database**
   - Update `.env` file with your database credentials
   - Run migrations: `php artisan migrate`
   - Seed admin user: `php artisan db:seed --class=AdminUserSeeder`

6. **Start development server**
   ```bash
   php artisan serve
   npm run dev
   ```

## Default Admin Credentials

- **Email**: admin@lana-amawi.com
- **Password**: password123

## Project Structure

```
resources/
├── views/
│   ├── layouts/
│   │   ├── app.blade.php          # Main layout
│   │   ├── client.blade.php       # Client portal layout
│   │   └── admin.blade.php        # Admin portal layout
│   ├── auth/
│   │   ├── login.blade.php        # Login page
│   │   └── register.blade.php     # Registration page
│   ├── client/
│   │   ├── dashboard.blade.php    # Client dashboard
│   │   ├── appointments.blade.php # Client appointments
│   │   └── messages.blade.php     # Client messages
│   ├── admin/
│   │   ├── dashboard.blade.php    # Admin dashboard
│   │   ├── clients.blade.php      # Client management
│   │   ├── messages.blade.php     # Admin messages
│   │   ├── calendar.blade.php     # Calendar view
│   │   └── settings.blade.php     # Admin settings
│   └── booking.blade.php          # Public booking form

app/
├── Http/Controllers/
│   ├── BookingController.php      # Handle booking form
│   ├── AuthController.php         # Authentication
│   ├── ClientController.php       # Client portal logic
│   └── AdminController.php        # Admin portal logic
├── Models/
│   ├── User.php                   # User model
│   ├── Booking.php                # Booking model
│   ├── Appointment.php            # Appointment model
│   └── Message.php                # Message model
└── Http/Middleware/
    └── AdminMiddleware.php        # Admin route protection
```

## Database Schema

### Users Table
- `id`, `name`, `email`, `password`, `is_admin`, `timestamps`

### Bookings Table
- `id`, `full_name`, `email`, `phone`, `program`, `preferred_date`, `preferred_time`, `message`, `status`, `timestamps`

### Appointments Table
- `id`, `user_id`, `program`, `appointment_date`, `appointment_time`, `message`, `status`, `timestamps`

### Messages Table
- `id`, `user_id`, `message`, `sender_type`, `is_read`, `timestamps`

## Routes

### Public Routes
- `GET /` - Booking form
- `GET /login` - Login page
- `GET /register` - Registration page

### Client Routes (Authenticated)
- `GET /client/dashboard` - Client dashboard
- `GET /client/appointments` - Client appointments
- `GET /client/messages` - Client messages
- `GET /client/profile` - Client profile

### Admin Routes (Authenticated + Admin)
- `GET /admin/dashboard` - Admin dashboard
- `GET /admin/clients` - Client management
- `GET /admin/messages` - Admin messages
- `GET /admin/calendar` - Calendar view
- `GET /admin/settings` - Admin settings

## Features Implemented

✅ **Booking Form**: Complete booking form with validation
✅ **Authentication**: Login/register system with role-based access
✅ **Client Dashboard**: Overview with statistics and next appointment
✅ **Client Appointments**: View and manage appointments
✅ **Client Messages**: Chat interface with Lana
✅ **Admin Dashboard**: Statistics and quick actions
✅ **Admin Client Management**: View and manage clients
✅ **Admin Messages**: Chat interface for client communication
✅ **Database**: Complete database schema with relationships
✅ **Middleware**: Admin route protection
✅ **Responsive Design**: Mobile-friendly Bootstrap layout

## Next Steps

1. **Email Notifications**: Implement email notifications for bookings and messages
2. **Calendar Integration**: Add calendar integration for appointments
3. **Payment Processing**: Integrate payment processing for sessions
4. **File Uploads**: Add file sharing in messages
5. **Advanced Analytics**: Add detailed reporting and analytics
6. **Multi-admin Support**: Support for multiple admin users
7. **API Development**: Create REST API for mobile app integration

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Submit a pull request

## License

This project is licensed under the MIT License.
