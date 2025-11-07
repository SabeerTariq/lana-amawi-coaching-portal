# Programs & Subscription Management Module

## 🎯 **Overview**

A comprehensive programs and subscription management system that allows administrators to create, manage, and track coaching programs with subscription-based models and monthly booking limits.

## 🏗️ **Architecture**

### **Database Structure**

#### **Programs Table (Enhanced)**
```sql
- id (Primary Key)
- name (Program Name)
- description (Program Description)
- price (One-time Price)
- duration_months (Program Duration)
- sessions_included (Sessions in One-time Purchase)
- is_active (Active Status)
- features (JSON - Program Features)
- subscription_type (Student/Resident/Medical/Concierge/Relationship)
- monthly_price (Monthly Subscription Price)
- monthly_sessions (Sessions/Bookings per Month)
- is_subscription_based (Subscription Model Enabled)
- subscription_features (JSON - Subscription Features)
- created_at, updated_at
```

#### **Subscriptions Table (New)**
```sql
- id (Primary Key)
- user_id (Foreign Key to Users)
- program_id (Foreign Key to Programs)
- subscription_type (Type of Subscription)
- monthly_price (Monthly Cost)
- monthly_sessions (Sessions/Bookings per Month)
- is_active (Active Status)
- starts_at (Subscription Start Date)
- ends_at (Subscription End Date - Optional)
- next_billing_date (Next Billing Date)
- last_billing_date (Last Billing Date)
- total_bookings_this_month (Current Month Bookings)
- subscription_features (JSON - Features)
- notes (Admin Notes)
- created_at, updated_at
```

## 🎛️ **Admin Features**

### **Program Management**
- **Create Programs**: Full CRUD operations for programs
- **Subscription Settings**: Configure monthly pricing and limits
- **Program Types**: Support for different subscription types
- **Feature Management**: Dynamic feature lists for programs and subscriptions
- **Status Management**: Activate/deactivate programs
- **Application Tracking**: View and manage program applications

### **Subscription Management**
- **Create Subscriptions**: Assign users to programs with custom settings
- **Booking Limits**: Enforce monthly booking limits per subscription
- **Status Tracking**: Monitor subscription status and usage
- **Billing Management**: Track billing dates and cycles
- **Usage Analytics**: View booking usage and remaining limits
- **Extension Tools**: Extend subscriptions and reset monthly counts

## 📊 **Key Features**

### **Subscription Types Supported**
1. **Student Program** - $249/month (2 sessions + text support)
2. **Resident/Fellow Program** - $299/month (2 sessions + text support)
3. **Medical Program** - $379/month (2 sessions + text support)
4. **Medical Concierge** - $499/month (3 sessions + on-call + text support)
5. **Relationship Program** - $399/month (2 sessions + text support)

### **Booking Limit Enforcement**
- **Monthly Limits**: Each subscription has a configurable monthly booking limit
- **Real-time Checking**: Middleware prevents over-booking
- **Usage Tracking**: Visual progress bars show booking usage
- **Automatic Reset**: Monthly limits reset automatically

### **Program Features**
- **One-time Programs**: Traditional programs with fixed pricing
- **Subscription Programs**: Monthly recurring programs
- **Hybrid Support**: Programs can support both models
- **Feature Lists**: Customizable features for each program type
- **Duration Tracking**: Program duration and session management

## 🔧 **Technical Implementation**

### **Models**

#### **Program Model (Enhanced)**
```php
// New Fields Added
protected $fillable = [
    // ... existing fields
    'subscription_type',
    'monthly_price',
    'monthly_sessions',
    'is_subscription_based',
    'subscription_features',
];

// New Relationships
public function subscriptions()
public function activeSubscriptions()
```

#### **Subscription Model (New)**
```php
// Key Methods
public function hasReachedBookingLimit()
public function getRemainingBookingsAttribute()
public function isActive()
public function currentMonthBookings()
```

### **Controllers**

#### **ProgramController (Enhanced)**
- `index()` - List all programs with counts
- `create()` - Show program creation form
- `store()` - Create new program
- `edit()` - Show program edit form
- `update()` - Update program
- `destroy()` - Delete program (with safety checks)
- `toggleStatus()` - Activate/deactivate program

#### **SubscriptionController (New)**
- `index()` - List all subscriptions with usage stats
- `create()` - Show subscription creation form
- `store()` - Create new subscription
- `show()` - View subscription details
- `edit()` - Edit subscription
- `update()` - Update subscription
- `destroy()` - Delete subscription
- `toggleStatus()` - Activate/deactivate subscription
- `resetMonthlyCount()` - Reset monthly booking count
- `extend()` - Extend subscription duration

### **Middleware**

#### **CheckSubscriptionLimits**
- Checks user's active subscriptions
- Prevents booking when limits are reached
- Provides user-friendly error messages

### **Views**

#### **Program Management**
- `admin/programs/index.blade.php` - Program listing with stats
- `admin/programs/create.blade.php` - Program creation form
- `admin/programs/edit.blade.php` - Program editing form

#### **Subscription Management**
- `admin/subscriptions/index.blade.php` - Subscription listing with usage
- `admin/subscriptions/create.blade.php` - Subscription creation form
- `admin/subscriptions/edit.blade.php` - Subscription editing form

## 🚀 **Usage Examples**

### **Creating a New Program**
1. Navigate to Admin → Programs → Create New Program
2. Fill in program details (name, description, price)
3. Enable subscription model if needed
4. Set monthly pricing and limits
5. Add program features
6. Save program

### **Creating a Subscription**
1. Navigate to Admin → Subscriptions → Create New Subscription
2. Select client and program
3. Set subscription type and pricing
4. Configure booking limits
5. Set start/end dates
6. Add subscription features
7. Save subscription

### **Monitoring Usage**
1. View subscription dashboard
2. Check booking usage progress bars
3. Monitor remaining bookings
4. Reset monthly counts when needed
5. Extend subscriptions as required

## 📈 **Benefits**

### **For Administrators**
- **Complete Control**: Full CRUD operations for programs and subscriptions
- **Usage Tracking**: Real-time monitoring of subscription usage
- **Flexible Pricing**: Support for both one-time and subscription models
- **Client Management**: Easy assignment of programs to clients
- **Analytics**: Clear visibility into program performance and usage

### **For Clients**
- **Clear Limits**: Transparent booking limits and usage tracking
- **Flexible Options**: Multiple program types to choose from
- **Progress Tracking**: Visual indicators of remaining bookings
- **Consistent Experience**: Reliable booking system with proper limits

### **For Business**
- **Revenue Management**: Predictable monthly recurring revenue
- **Resource Planning**: Better capacity planning with booking limits
- **Scalability**: Easy addition of new programs and subscription types
- **Data Insights**: Comprehensive tracking for business decisions

## 🔒 **Security & Validation**

### **Data Validation**
- Required field validation for all forms
- Numeric validation for prices and limits
- Date validation for subscription periods
- Array validation for features

### **Business Logic**
- Prevents duplicate active subscriptions
- Enforces booking limits
- Validates subscription periods
- Protects against data inconsistencies

### **User Experience**
- Clear error messages
- Confirmation dialogs for destructive actions
- Real-time form validation
- Intuitive progress indicators

## 🎯 **Next Steps (Optional Enhancements)**

### **Potential Features**
1. **Automated Billing**: Integration with payment processors
2. **Email Notifications**: Automated subscription reminders
3. **Usage Reports**: Detailed analytics and reporting
4. **Bulk Operations**: Mass subscription management
5. **API Integration**: RESTful API for external integrations
6. **Mobile Support**: Mobile-optimized interfaces
7. **Advanced Analytics**: Business intelligence dashboards

## 📋 **File Structure**

```
app/
├── Models/
│   ├── Program.php (Enhanced)
│   └── Subscription.php (New)
├── Http/
│   ├── Controllers/Admin/
│   │   ├── ProgramController.php (Enhanced)
│   │   └── SubscriptionController.php (New)
│   └── Middleware/
│       └── CheckSubscriptionLimits.php (New)
database/
├── migrations/
│   ├── add_subscription_fields_to_programs_table.php
│   └── create_subscriptions_table.php
resources/views/admin/
├── programs/
│   ├── index.blade.php
│   ├── create.blade.php
│   └── edit.blade.php
└── subscriptions/
    ├── index.blade.php
    ├── create.blade.php
    └── edit.blade.php
```

## ✅ **Implementation Status**

- ✅ Program model with subscription fields
- ✅ Subscription model with booking tracking
- ✅ Database migrations
- ✅ Program CRUD operations
- ✅ Subscription CRUD operations
- ✅ Admin views for program management
- ✅ Admin views for subscription management
- ✅ Booking limit enforcement middleware
- ✅ Routes configuration
- ✅ Form validation and error handling

The Programs & Subscription Management Module is now fully implemented and ready for use! 🎉
