# Program Payment System Guide

## Overview
The program payment system now supports **3-month contracts** with flexible payment options and additional session charges.

## Key Features

### 1. Contract Duration
- All programs are set to **3-month contracts** by default
- Contract start and end dates are automatically calculated
- Contract status is tracked in the `user_programs` table

### 2. Payment Types

#### Monthly Payments
- Client pays **monthly** for 3 months
- Each payment is tracked separately
- Next payment date is automatically calculated
- Payment records are created for each monthly payment

#### One-Time Payment
- Client pays **full 3 months upfront**
- Single payment covers entire contract period
- Amount = Monthly Price × 3

### 3. Monthly Booking Limits
- Each program has a **monthly booking limit** (`monthly_sessions`)
- Clients can book sessions up to their monthly limit
- System tracks remaining bookings for the current month

### 4. Additional Session Charges
- Clients can book **additional 60-minute sessions** beyond their monthly limit
- Each program has its own **additional booking charge** (`additional_booking_charge`)
- Additional sessions require separate payment
- Payment records are created for each additional session booking

## Database Structure

### user_programs Table (New Fields)
- `contract_duration_months` (default: 3)
- `payment_type` (enum: 'monthly', 'one_time')
- `contract_start_date`
- `contract_end_date`
- `next_payment_date` (for monthly payments)
- `total_payments_due` (default: 3)
- `payments_completed` (default: 0)
- `one_time_payment_amount` (calculated for one-time payments)

### programs Table (New Field)
- `additional_booking_charge` (decimal) - Charge for additional 60-minute sessions

### payments Table (New)
- Tracks all payment transactions
- Links to `user_programs` and optionally `appointments`
- Payment types: `contract_monthly`, `contract_one_time`, `additional_session`
- Status: `pending`, `completed`, `failed`, `refunded`

## Workflow

### Admin Workflow

1. **Approve Application**
   - Application status: `approved`

2. **Request Payment**
   - Admin selects payment type: Monthly or One-Time
   - Status changes to: `payment_requested`
   - Contract duration is set to 3 months

3. **Mark Payment Completed**
   - Admin enters amount and payment reference
   - System creates Payment record
   - For monthly: Updates `next_payment_date` and `payments_completed`
   - For one-time: Marks all payments as completed
   - Status changes to: `payment_completed` when all payments are done

4. **Activate Program**
   - System initializes contract (sets start/end dates)
   - Status changes to: `active`
   - Client can now book sessions

5. **Handle Additional Sessions**
   - When client books beyond monthly limit
   - Admin marks additional session payment
   - Creates separate payment record for additional session

### Client Workflow

1. **Select Program** → Application created
2. **Wait for Agreement** → Upload signed agreement
3. **Wait for Approval** → Admin approves
4. **Complete Payment** → Monthly or One-Time
5. **Program Active** → Book sessions (up to monthly limit)
6. **Book Additional Sessions** → Pay additional charge if needed

## Payment Tracking

### Monthly Payments
- Payment 1: Month 1 (due at contract start)
- Payment 2: Month 2 (due 1 month after start)
- Payment 3: Month 3 (due 2 months after start)

### One-Time Payment
- Single payment covering all 3 months
- Amount = Monthly Price × 3

### Additional Session Payments
- Separate payment for each additional 60-minute session
- Amount = Program's `additional_booking_charge`
- Linked to specific appointment

## API/Controller Methods

### ProgramController Methods

1. **requestPayment()**
   - Accepts `payment_type` (monthly/one_time)
   - Sets payment type and requests payment

2. **markPaymentCompleted()**
   - Creates Payment record
   - Updates UserProgram payment tracking
   - Handles both monthly and one-time payments

3. **activateProgram()**
   - Initializes contract (3 months)
   - Sets contract start/end dates
   - Calculates payment schedule

4. **markAdditionalSessionPayment()**
   - Creates payment for additional session
   - Links to specific appointment

## Model Methods

### UserProgram Model

- `initializeContract($paymentType)` - Sets up 3-month contract
- `isContractActive()` - Checks if contract is within date range
- `areAllPaymentsCompleted()` - Checks payment completion
- `getRemainingPaymentsAttribute()` - Returns remaining payments
- `getTotalContractAmountAttribute()` - Calculates total contract cost
- `hasReachedMonthlyLimit()` - Checks booking limit
- `getRemainingBookingsAttribute()` - Returns remaining bookings
- `getAdditionalBookingChargeAttribute()` - Gets program's additional charge

### Payment Model

- `isCompleted()` - Checks if payment is completed
- `getFormattedAmountAttribute()` - Formats amount for display
- `getPaymentTypeDisplayAttribute()` - Gets payment type text
- `getStatusBadgeColorAttribute()` - Gets status badge color

## Views to Update

1. **Admin Applications View** - Show payment type selection, contract details
2. **Payment Modal** - Allow selecting payment type and month number
3. **Program Create/Edit** - Include additional booking charge field
4. **Client Dashboard** - Show contract details, remaining bookings, payment status

## Next Steps

1. Update admin applications view to show payment type selection
2. Update payment modal to handle monthly vs one-time payments
3. Add contract details display in client dashboard
4. Add booking limit checking when clients book sessions
5. Add additional session payment flow in booking process

