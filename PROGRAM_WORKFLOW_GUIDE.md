# Program Workflow Guide - Lana Amawi Coaching Portal

## Overview
This document outlines the complete workflow for both Admin and Client users in the Program Management system. The workflow ensures a smooth, step-by-step process from program selection to program activation.

## Program Status Flow

### Status Definitions
1. **`pending`** - Initial status when client selects a program
2. **`agreement_sent`** - Admin has sent the program agreement to client
3. **`agreement_uploaded`** - Client has uploaded the signed agreement
4. **`approved`** - Admin has approved the application
5. **`payment_requested`** - Admin has requested payment from client
6. **`payment_completed`** - Payment has been completed
7. **`active`** - Program is active and client can book sessions
8. **`rejected`** - Application has been rejected by admin
9. **`cancelled`** - Application has been cancelled

---

## 🔧 ADMIN WORKFLOW

### Step 1: Review Applications
**Location:** Admin Dashboard → Programs → Applications
**Status:** `pending`

**Actions Available:**
- View all pending applications
- Review client details and program selection
- Add admin notes
- Send agreement to client
- Reject application

**Admin Actions:**
1. **Send Agreement**
   - Click "Send Agreement" button
   - System generates PDF agreement
   - Email sent to client with agreement
   - Status changes to `agreement_sent`

2. **Reject Application**
   - Click "Reject" button
   - Provide rejection reason in modal
   - Status changes to `rejected`
   - Client receives notification

### Step 2: Monitor Agreement Process
**Status:** `agreement_sent`

**What Happens:**
- Client receives email with agreement
- Client downloads and signs agreement
- Client uploads signed agreement
- Status automatically changes to `agreement_uploaded`

**Admin Actions:**
- Monitor progress
- Add notes if needed
- No direct action required (automatic transition)

### Step 3: Review Uploaded Agreement
**Status:** `agreement_uploaded`

**Actions Available:**
- View uploaded agreement
- Approve application
- Add admin notes

**Admin Actions:**
1. **View Agreement**
   - Click "View Agreement" to open PDF
   - Verify signature and completeness

2. **Approve Application**
   - Click "Approve" button
   - Status changes to `approved`
   - Client receives approval notification

### Step 4: Request Payment
**Status:** `approved`

**Actions Available:**
- Request payment from client
- Add admin notes

**Admin Actions:**
1. **Request Payment**
   - Click "Request Payment" button
   - Status changes to `payment_requested`
   - Client receives payment request notification

### Step 5: Process Payment
**Status:** `payment_requested`

**Actions Available:**
- Mark payment as completed
- Add admin notes

**Admin Actions:**
1. **Mark Payment Completed**
   - Click "Mark Paid" button
   - Enter amount paid and payment reference
   - Status changes to `payment_completed`

### Step 6: Activate Program
**Status:** `payment_completed`

**Actions Available:**
- Activate program
- Add admin notes

**Admin Actions:**
1. **Activate Program**
   - Click "Activate Program" button
   - Status changes to `active`
   - Client can now book sessions

### Step 7: Monitor Active Programs
**Status:** `active`

**What Happens:**
- Client can book coaching sessions
- Program is fully operational
- Admin can monitor progress

---

## 👤 CLIENT WORKFLOW

### Step 1: Browse Programs
**Location:** Client Dashboard → Programs

**What Client Sees:**
- List of available programs
- Program details, pricing, and features
- "My Program Applications" section (if any)

**Client Actions:**
1. **View Program Details**
   - Click "View Details" for more information
   - See full program description and features

2. **Select Program**
   - Click "Select Program" button
   - Application created with `pending` status
   - Confirmation message displayed

### Step 2: Wait for Admin Review
**Status:** `pending`

**What Client Sees:**
- Application status: "Pending Review"
- Message: "Your application will be reviewed by our team"

**Client Actions:**
- Wait for admin action
- Can view application status
- No direct action required

### Step 3: Receive and Sign Agreement
**Status:** `agreement_sent`

**What Client Receives:**
- Email notification with agreement PDF
- Download link for agreement

**Client Actions:**
1. **Download Agreement**
   - Click "Download Agreement" button
   - Save PDF to device

2. **Sign Agreement**
   - Print and sign the agreement
   - Scan or take photo of signed agreement

3. **Upload Signed Agreement**
   - Click "Upload Signed Agreement" button
   - Select signed PDF file
   - Upload file
   - Status changes to `agreement_uploaded`

### Step 4: Wait for Approval
**Status:** `agreement_uploaded`

**What Client Sees:**
- Status: "Agreement Uploaded"
- Message: "Agreement uploaded. Waiting for admin approval"

**Client Actions:**
- Wait for admin approval
- No direct action required

### Step 5: Receive Approval
**Status:** `approved`

**What Client Sees:**
- Status: "Approved"
- Message: "Program approved! Payment will be requested soon"

**Client Actions:**
- Wait for payment request
- No direct action required

### Step 6: Process Payment
**Status:** `payment_requested`

**What Client Sees:**
- Status: "Payment Requested"
- Message: "Payment requested. Please complete payment to activate your program"

**Client Actions:**
- Complete payment as instructed
- Wait for admin to mark payment as completed

### Step 7: Program Activation
**Status:** `active`

**What Client Sees:**
- Status: "Active"
- Message: "Program active! You can now book sessions"

**Client Actions:**
- Book coaching sessions
- Access program features
- Full program participation

---

## 🚫 REJECTION WORKFLOW

### Admin Side
1. **Reject Application**
   - Click "Reject" button on pending application
   - Provide detailed rejection reason
   - Status changes to `rejected`

### Client Side
1. **Receive Rejection**
   - Application status shows "Rejected"
   - Can view rejection reason
   - Can select a different program if desired

---

## 📊 DASHBOARD FEATURES

### Admin Dashboard
- **Applications Overview**: All applications grouped by status
- **Status Counts**: Badge counts for each status
- **Quick Actions**: Status-specific action buttons
- **Notes System**: Add notes to any application
- **File Management**: View and download agreements

### Client Dashboard
- **My Applications**: Current program applications
- **Status Tracking**: Clear status indicators
- **Action Buttons**: Status-appropriate actions
- **Program Catalog**: Browse available programs

---

## 🔄 AUTOMATIC TRANSITIONS

1. **Client Uploads Agreement**: `agreement_sent` → `agreement_uploaded`
2. **Admin Sends Agreement**: `pending` → `agreement_sent`
3. **Admin Approves**: `agreement_uploaded` → `approved`
4. **Admin Requests Payment**: `approved` → `payment_requested`
5. **Admin Marks Payment Complete**: `payment_requested` → `payment_completed`
6. **Admin Activates Program**: `payment_completed` → `active`

---

## 📧 EMAIL NOTIFICATIONS

### Client Receives:
- Agreement sent notification
- Application approved notification
- Payment requested notification
- Program activated notification

### Admin Receives:
- New application notification (optional)
- Agreement uploaded notification (optional)

---

## 🛠️ TECHNICAL IMPLEMENTATION

### Key Files:
- **Models**: `UserProgram.php`, `Program.php`
- **Controllers**: `Admin\ProgramController.php`, `ProgramController.php`
- **Views**: `admin/programs/applications.blade.php`, `client/programs.blade.php`
- **Routes**: Program management routes in `web.php`

### Database Tables:
- `programs`: Available programs
- `user_programs`: Client program applications
- `users`: Client and admin users

---

## ✅ BEST PRACTICES

### For Admins:
1. Review applications promptly
2. Provide clear rejection reasons
3. Keep detailed notes
4. Process payments quickly
5. Communicate clearly with clients

### For Clients:
1. Read program details carefully
2. Sign agreements promptly
3. Upload clear, readable documents
4. Complete payments on time
5. Check status regularly

---

## 🚨 TROUBLESHOOTING

### Common Issues:
1. **Agreement not received**: Check email spam folder
2. **Upload fails**: Ensure PDF format and size limits
3. **Status not updating**: Refresh page or contact admin
4. **Payment issues**: Contact admin for assistance

### Support:
- Admin can add notes to any application
- Email notifications provide status updates
- Clear error messages guide users
- Status badges show current state

---

This workflow ensures a smooth, professional experience for both administrators and clients while maintaining clear communication and status tracking throughout the entire program application process.
