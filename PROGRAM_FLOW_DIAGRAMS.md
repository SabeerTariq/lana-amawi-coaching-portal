# Program Workflow Diagrams

## 1. Complete Program Flow Overview

```
CLIENT SIDE                    ADMIN SIDE
=============                  ===========

1. Browse Programs             1. Monitor Applications
   ↓                              ↓
2. Select Program             2. Review Application
   ↓ (status: pending)            ↓
3. Wait for Review            3. Send Agreement
   ↓                              ↓ (status: agreement_sent)
4. Receive Agreement          4. Monitor Upload
   ↓                              ↓
5. Download & Sign            5. Review Uploaded Agreement
   ↓                              ↓
6. Upload Signed Agreement    6. Approve Application
   ↓ (status: agreement_uploaded)  ↓ (status: approved)
7. Wait for Approval          7. Request Payment
   ↓                              ↓ (status: payment_requested)
8. Complete Payment           8. Mark Payment Complete
   ↓                              ↓ (status: payment_completed)
9. Program Active             9. Activate Program
   ↓ (status: active)             ↓
10. Book Sessions             10. Monitor Active Programs
```

## 2. Status Transition Flow

```
START
  ↓
[PENDING] ← Client selects program
  ↓
  ├─ Admin sends agreement → [AGREEMENT_SENT]
  │                         ↓
  │                         Client uploads signed agreement → [AGREEMENT_UPLOADED]
  │                         ↓
  │                         Admin approves → [APPROVED]
  │                         ↓
  │                         Admin requests payment → [PAYMENT_REQUESTED]
  │                         ↓
  │                         Admin marks payment complete → [PAYMENT_COMPLETED]
  │                         ↓
  │                         Admin activates program → [ACTIVE]
  │
  └─ Admin rejects → [REJECTED]
```

## 3. Admin Dashboard Flow

```
Admin Login
    ↓
Dashboard Overview
    ↓
Programs → Applications
    ↓
View by Status Tabs:
├─ Pending (Review & Send Agreement)
├─ Agreement Sent (Monitor Upload)
├─ Agreement Uploaded (Review & Approve)
├─ Approved (Request Payment)
├─ Payment Requested (Mark Complete)
├─ Payment Completed (Activate Program)
├─ Active (Monitor)
└─ Rejected (View Details)
```

## 4. Client Dashboard Flow

```
Client Login
    ↓
Dashboard Overview
    ↓
Programs Section
    ↓
├─ My Applications (Status Tracking)
│  ├─ Pending: "Waiting for review"
│  ├─ Agreement Sent: "Download & Upload"
│  ├─ Agreement Uploaded: "Waiting for approval"
│  ├─ Approved: "Payment will be requested"
│  ├─ Payment Requested: "Complete payment"
│  ├─ Active: "Book sessions"
│  └─ Rejected: "View reason"
│
└─ Available Programs (Browse & Select)
   ├─ View Details
   └─ Select Program
```

## 5. Email Notification Flow

```
Client Actions → Email Notifications
===================

Select Program → No immediate email
    ↓
Admin sends agreement → Agreement sent email
    ↓
Client uploads agreement → No immediate email
    ↓
Admin approves → Approval notification email
    ↓
Admin requests payment → Payment request email
    ↓
Admin marks payment complete → No immediate email
    ↓
Admin activates program → Program activated email
```

## 6. File Management Flow

```
Agreement Generation & Management
================================

Admin sends agreement:
├─ Generate PDF from template
├─ Store in storage/app/public/agreements/
├─ Send email with download link
└─ Update status to agreement_sent

Client uploads signed agreement:
├─ Validate PDF format & size
├─ Store in storage/app/public/signed-agreements/
├─ Update status to agreement_uploaded
└─ Generate unique filename

Admin views agreement:
├─ Retrieve from storage
├─ Display in browser
└─ Allow download
```

## 7. Error Handling Flow

```
Error Scenarios & Handling
==========================

Client Side:
├─ Upload fails → Show error message with requirements
├─ Invalid file format → Show format requirements
├─ File too large → Show size limit message
└─ Network error → Retry option

Admin Side:
├─ PDF generation fails → Log error, show message
├─ Email sending fails → Log error, manual retry
├─ File not found → Show error, regenerate
└─ Database error → Log error, show message
```

## 8. Status Badge Colors & Icons

```
Status Visual Indicators
========================

PENDING:           🟡 Warning (fa-clock)
AGREEMENT_SENT:    🔵 Info (fa-paper-plane)
AGREEMENT_UPLOADED: 🔵 Primary (fa-upload)
APPROVED:          🟢 Success (fa-check)
PAYMENT_REQUESTED: 🟡 Warning (fa-credit-card)
PAYMENT_COMPLETED: 🟢 Success (fa-check-circle)
ACTIVE:            🟢 Success (fa-play)
REJECTED:          🔴 Danger (fa-times)
CANCELLED:         ⚫ Secondary (fa-ban)
```

## 9. Database State Management

```
UserProgram Model States
========================

Initial State:
- user_id, program_id, status: 'pending'
- All other fields: null

After Agreement Sent:
- agreement_path: 'agreements/agreement_X_timestamp.pdf'
- agreement_sent_at: timestamp
- status: 'agreement_sent'

After Agreement Uploaded:
- signed_agreement_path: 'signed-agreements/signed_agreement_X_timestamp.pdf'
- signed_agreement_name: 'original_filename.pdf'
- agreement_uploaded_at: timestamp
- status: 'agreement_uploaded'

After Approval:
- approved_at: timestamp
- status: 'approved'

After Payment Request:
- payment_requested_at: timestamp
- status: 'payment_requested'

After Payment Complete:
- payment_completed_at: timestamp
- amount_paid: decimal
- payment_reference: string
- status: 'payment_completed'

After Activation:
- status: 'active'

After Rejection:
- admin_notes: string (rejection reason)
- status: 'rejected'
```

## 10. Security & Validation

```
Security Measures
=================

Client Actions:
├─ Verify user owns UserProgram
├─ Validate file uploads (type, size)
├─ Sanitize input data
└─ CSRF protection on forms

Admin Actions:
├─ Admin middleware protection
├─ Validate all inputs
├─ Secure file storage
└─ Audit logging

File Handling:
├─ PDF validation
├─ Size limits (10MB)
├─ Secure storage paths
└─ Access control
```

---

## Quick Reference Cards

### Admin Quick Actions by Status:
- **Pending**: Send Agreement | Reject | Add Notes
- **Agreement Sent**: Monitor | Add Notes
- **Agreement Uploaded**: View Agreement | Approve | Add Notes
- **Approved**: Request Payment | Add Notes
- **Payment Requested**: Mark Paid | Add Notes
- **Payment Completed**: Activate Program | Add Notes
- **Active**: Monitor | Add Notes
- **Rejected**: View Details | Add Notes

### Client Quick Actions by Status:
- **Pending**: Wait | View Status
- **Agreement Sent**: Download Agreement | Upload Signed
- **Agreement Uploaded**: Wait | View Status
- **Approved**: Wait | View Status
- **Payment Requested**: Complete Payment | View Status
- **Active**: Book Sessions | View Program
- **Rejected**: View Reason | Select Different Program

### Status Transition Rules:
- Only admin can change status (except client upload)
- Client can only upload signed agreement
- Each status has specific allowed transitions
- All transitions are logged with timestamps
- Email notifications sent for key transitions
