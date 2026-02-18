# FurryMart Support System Implementation Guide

## Overview
A comprehensive support management system with three types of customer requests:
1. **Callback Requests** - Phone callback requests
2. **Quick Queries** - Email support queries  
3. **Contact Forms** - Full contact form submissions

---

## Installation Steps

### Step 1: Import Database Tables
Run the SQL file to create the new tables:

```bash
# In phpMyAdmin or MySQL command line
mysql -u root -p furrymart < support_tables.sql
```

Or manually execute the queries in `support_tables.sql` through phpMyAdmin.

### Step 2: Verify Database Structure
After importing, you should have these three tables:
- `callback_requests` - Stores phone callback requests
- `quick_queries` - Stores email support queries
- `contact_queries` - Existing contact form submissions (updated with indexes)

---

## Features Implemented

### 1. Frontend (contact.php)

#### Call Request Card
- Click on "Call Us Now" card opens a modal
- User enters name and 10-digit phone number
- AJAX submission to `submit_callback_request.php`
- Data stored in `callback_requests` table
- Beautiful success message with callback confirmation

#### Email Support Card
- Click on "Email Support" card opens a modal
- User enters name, email, and message
- AJAX submission to `submit_quick_query.php`
- Data stored in `quick_queries` table
- Success notification

#### Contact Form
- Existing full contact form (unchanged)
- Stores in `contact_queries` table

### 2. Admin Panel (admin_queries.php)

#### Filter Toggle Buttons
Four filter options with real-time counts:
- **All Requests** - Shows all three types combined
- **Call Requests** - Only callback requests
- **Quick Queries** - Only email queries
- **Contact Forms** - Only contact form submissions

#### Features:
- **Type Badges**: Visual color-coded badges for each request type
  - 🔴 Callback (Red)
  - 🔵 Quick Query (Blue)
  - 🟣 Contact Form (Purple)

- **Status Management**:
  - Callback requests: Pending → In Progress → Completed/Cancelled
  - Quick queries & Contact forms: Unread → Read → Pending → In Progress → Resolved

- **Disabled Updates**: Once status is "Resolved" or "Completed", the update button becomes disabled
- **Delete Functionality**: Delete requests with confirmation
- **SweetAlert Notifications**: Success messages for updates/deletes

---

## File Structure

```
FURRYMART/
├── contact.php (Updated with AJAX handlers)
├── submit_callback_request.php (NEW - Handles callback submissions)
├── submit_quick_query.php (NEW - Handles quick query submissions)
├── support_tables.sql (NEW - Database schema)
└── admin/
    └── admin_queries.php (Updated - All three request types with filters)
```

---

## Database Schema

### callback_requests
```sql
id, user_id, name, phone, status, created_at
Status values: pending, in_progress, completed, cancelled
```

### quick_queries
```sql
id, user_id, name, email, message, status, created_at
Status values: unread, read, pending, in_process, resolved
```

### contact_queries (Existing)
```sql
id, user_id, first_name, last_name, mobile, email, subject, message, status, created_at
Status values: unread, read, pending, in_process, resolved
```

---

## Usage Instructions

### For Users (Frontend):
1. Visit the Contact page
2. Choose support method:
   - **Quick call?** Click "Call Us Now" → Enter details → Get callback in 2-3 minutes
   - **Email query?** Click "Email Support" → Write message → Get email response
   - **Detailed query?** Fill contact form at top

### For Admin:
1. Go to `admin/admin_queries.php`
2. Use filter buttons to view specific request types
3. Update status as you process requests
4. Once resolved/completed, status updates are locked
5. Delete handled requests if needed

---

## Security Features
- SQL injection protection with `mysqli_real_escape_string()`
- Phone number validation (exactly 10 digits)
- Email validation with `filter_var()`
- Session-based user tracking
- CSRF protection through POST methods

---

## Customization Tips

### Change Phone Number:
Edit in [contact.php](contact.php#L930) lines 930-935

### Modify Status Options:
Edit dropdown options in [admin_queries.php](admin/admin_queries.php#L230-250)

### Update Colors:
Modify CSS variables in [admin_queries.php](admin/admin_queries.php#L90-95)

---

## Testing Checklist

- [ ] Import SQL tables successfully
- [ ] Submit callback request from frontend
- [ ] Submit quick query from frontend
- [ ] Submit contact form
- [ ] View all requests in admin panel
- [ ] Test each filter button
- [ ] Update status of requests
- [ ] Verify resolved status disables updates
- [ ] Test delete functionality
- [ ] Check SweetAlert notifications

---

## Troubleshooting

**Problem**: Tables not created
- **Solution**: Manually run SQL queries in phpMyAdmin

**Problem**: AJAX not working
- **Solution**: Check browser console for errors, verify file paths

**Problem**: Status not updating
- **Solution**: Check `query_type` hidden input in forms

**Problem**: Filter counts wrong
- **Solution**: Clear browser cache and refresh page

---

## Support
For issues or questions, check the code comments or contact the development team.

---

**Version**: 1.0  
**Last Updated**: February 14, 2026  
**Author**: FurryMart Dev Team
