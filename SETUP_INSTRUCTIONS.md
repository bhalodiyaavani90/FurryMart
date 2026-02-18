# 📋 Quick Setup Instructions - Support System

## Step 1: Create Database Tables ⚡

### Option A: Using phpMyAdmin (Recommended)
1. Open your browser and go to: `http://localhost/phpmyadmin`
2. Click on your database `furrymart` in the left sidebar
3. Click on the **SQL** tab at the top
4. Copy and paste the following SQL code:

```sql
-- ========================================
-- FurryMart Support System Tables
-- ========================================

-- Table for Callback Requests
CREATE TABLE IF NOT EXISTS `callback_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `status` enum('pending','in_progress','completed','resolved','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `status` (`status`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table for Quick Queries (Email Support)
CREATE TABLE IF NOT EXISTS `quick_queries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `status` enum('unread','read','pending','in_process','resolved') DEFAULT 'unread',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `status` (`status`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

5. Click **Go** button at the bottom right
6. You should see: "2 tables created successfully" ✅

---

## Step 2: Test the System 🧪

### A. Test Frontend (User Side)
1. Open: `http://localhost/FURRYMART/contact.php`
2. Scroll down to the contact cards section
3. **Test Callback Request:**
   - Click on "Call Us Now" card (pink)
   - Fill in name and 10-digit phone number
   - Click "Request FREE Callback"
   - Should see success message ✅

4. **Test Quick Query:**
   - Click on "Email Support" card (blue)
   - Fill in name, email, and message
   - Click "Send Email"
   - Should see success message ✅

5. **Test Contact Form:**
   - Fill the main contact form at the top
   - Submit
   - Should see success message ✅

### B. Test Admin Panel
1. Login to admin: `http://localhost/FURRYMART/admin/login.php`
2. Go to: `http://localhost/FURRYMART/admin/admin_queries.php`
3. You should see:
   - Four filter buttons at the top (All, Call Requests, Quick Queries, Contact Forms)
   - All submitted requests in a table
   - Type badges (colored labels) for each request
   - Status dropdown for each request
   - Delete button for each request

4. **Test Filtering:**
   - Click each filter button
   - Verify correct requests show for each filter ✅

5. **Test Status Update:**
   - Select a new status from dropdown
   - Click save icon (💾)
   - Should see "Updated!" message ✅

6. **Test Resolved Status:**
   - Change a status to "Resolved" or "Completed"
   - Save it
   - Try to update again - should be disabled (grayed out) ✅

7. **Test Delete:**
   - Click trash icon (🗑️)
   - Confirm deletion
   - Should see "Deleted!" message ✅

---

## Step 3: Verify Everything Works ✨

### Checklist:
- ✅ Two new tables created in database
- ✅ Callback requests submitting from frontend
- ✅ Quick queries submitting from frontend
- ✅ All three request types showing in admin panel
- ✅ Filter buttons working correctly
- ✅ Status updates working
- ✅ Resolved status disables further updates
- ✅ Delete functionality working
- ✅ Success notifications appearing

---

## 🎉 You're All Set!

Your support system is now fully functional with:
- 📞 Phone callback requests
- ⚡ Quick email queries
- 📝 Full contact forms
- 🎛️ Admin management panel with filters
- 🔒 Security features built-in

---

## Common Issues & Solutions

### Issue: "Table already exists" error
**Solution**: Tables are already created, you're good to go! Skip to Step 2.

### Issue: AJAX submissions not working
**Solution**: 
1. Check browser console (F12) for errors
2. Verify files exist:
   - `submit_callback_request.php`
   - `submit_quick_query.php`
3. Clear browser cache (Ctrl + Shift + Delete)

### Issue: Admin page shows no data
**Solution**: 
1. Submit test requests from frontend first
2. Refresh admin page
3. Click "All Requests" filter button

### Issue: Status dropdown not updating
**Solution**:
1. Make sure you click the save button (💾) after selecting status
2. Refresh the page after update

---

## Need Help?
- Check `SUPPORT_SYSTEM_GUIDE.md` for detailed documentation
- Review code comments in the PHP files
- Check database tables in phpMyAdmin to verify data

---

**Last Updated**: February 14, 2026
**Status**: Production Ready ✅
