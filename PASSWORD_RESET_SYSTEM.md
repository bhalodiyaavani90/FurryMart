# 🔐 Password Reset System - FurryMart

## Complete Implementation Guide

---

## ✨ Features

### User-Friendly Features
- ✅ **Forgot Password Link** on login page
- ✅ **Email-based verification** (shows link instead of sending email in demo mode)
- ✅ **Secure token generation** using cryptographically secure random bytes
- ✅ **1-hour token expiry** for security
- ✅ **Password strength indicator** during reset
- ✅ **Real-time password match validation**
- ✅ **Auto-redirect** after successful reset

### Security Features
- 🔒 **SHA-256 token hashing** - Tokens stored as hashes, not plain text
- 🔒 **Time-limited tokens** - Automatically expire after 60 minutes
- 🔒 **Password hashing** - Uses PHP's password_hash() with bcrypt
- 🔒 **One-time use tokens** - Deleted immediately after use
- 🔒 **SQL injection protection** - All inputs sanitized
- 🔒 **HTTPS ready** - Works with secure connections

---

## 📁 Files Created

### 1. **forgot_password.php**
**Purpose:** Request password reset link  
**Features:**
- Email validation
- User existence check
- Token generation (64-character hex string)
- Displays reset link (production: sends via email)
- Auto-creates database table if not exists

**Location:** `http://localhost/FURRYMART/forgot_password.php`

---

### 2. **reset_password.php**
**Purpose:** Reset password using token  
**Features:**
- Token validation
- Expiry checking
- Password strength indicator
- Confirm password matching
- Updates user password
- Deletes used token
- Auto-redirects to login

**Location:** `http://localhost/FURRYMART/reset_password.php?token=XXXXX`

---

### 3. **setup_password_reset.php**
**Purpose:** One-click database setup  
**Features:**
- Creates `password_resets` table
- Checks if already set up
- Visual status indicators
- Technical information display

**Location:** `http://localhost/FURRYMART/setup_password_reset.php`

---

### 4. **password_reset_setup.sql**
**Purpose:** Manual SQL setup option  
**Usage:** Import via phpMyAdmin or MySQL CLI

---

### 5. **login.php** (Modified)
**Changes Made:**
- Added "Forgot Password?" link above register link
- Styled with red accent color
- Links to forgot_password.php

---

## 🗄️ Database Structure

### Table: `password_resets`

```sql
CREATE TABLE `password_resets` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `token_hash` VARCHAR(64) NOT NULL,
  `expires_at` DATETIME NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX(email),
  INDEX(token_hash),
  INDEX(expires_at)
);
```

**Field Descriptions:**
- `id` - Auto-increment primary key
- `user_id` - Links to users.id
- `email` - User's email (for quick lookup)
- `token_hash` - SHA-256 hash of reset token (64 chars)
- `expires_at` - Token expiration time (NOW + 1 hour)
- `created_at` - Timestamp of token generation

---

## 🚀 Setup Instructions

### Method 1: Automatic Setup (Recommended)
1. Visit: `http://localhost/FURRYMART/setup_password_reset.php`
2. Click "Run Setup Now" button
3. Done! ✅

### Method 2: Manual SQL Import
1. Open phpMyAdmin
2. Select `furrymart` database
3. Go to "Import" tab
4. Choose `password_reset_setup.sql`
5. Click "Go"

### Method 3: SQL Query
1. Open phpMyAdmin > SQL tab
2. Copy contents from `password_reset_setup.sql`
3. Execute query

---

## 📖 User Flow

### Step 1: Request Reset
1. User goes to login page
2. Clicks "Forgot Password?" link
3. Enters registered email address
4. System generates secure token
5. Reset link displayed (or sent via email)

### Step 2: Reset Password
1. User clicks reset link
2. Token validated (checks expiry)
3. User enters new password
4. Password strength shown
5. Confirms password (match validation)
6. Submits form

### Step 3: Completion
1. Password updated in database
2. Token deleted (one-time use)
3. User redirected to login
4. Success message shown
5. User can login with new password

---

## 🔧 How It Works Technically

### Token Generation
```php
$token = bin2hex(random_bytes(32)); // 64-char hex string
$token_hash = hash('sha256', $token); // SHA-256 hash
$expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
```

### Token Validation
```php
$token_hash = hash('sha256', $_GET['token']);
$query = "SELECT * FROM password_resets 
          WHERE token_hash = '$token_hash' 
          AND expires_at > NOW()";
```

### Password Update
```php
$hashed = password_hash($new_password, PASSWORD_DEFAULT);
$update = "UPDATE users SET password = '$hashed' WHERE email = '$email'";
// Delete token after use
mysqli_query($conn, "DELETE FROM password_resets WHERE token_hash = '$token_hash'");
```

---

## 🎨 UI/UX Features

### Visual Elements
- **Forgot Password Page:**
  - Red accent color scheme
  - Key icon branding
  - Info box with instructions
  - Success/error message display
  - Responsive design

- **Reset Password Page:**
  - Green accent for success
  - Lock icon branding
  - User badge showing name
  - Password requirements list
  - Real-time validation feedback
  - Strength meter (Weak/Medium/Strong)

### Interactive Features
- Password strength indicator
- Real-time match validation
- Form validation before submit
- Auto-redirect after success
- Back to login/homepage links

---

## 🔐 Security Best Practices Implemented

1. **Token Storage:**
   - ✅ Tokens never stored in plain text
   - ✅ SHA-256 hashing before database storage
   - ✅ Cryptographically secure random generation

2. **Token Expiry:**
   - ✅ 60-minute automatic expiration
   - ✅ Database-level expiry checking
   - ✅ Old tokens auto-deleted on new request

3. **Password Security:**
   - ✅ Minimum 6 characters required
   - ✅ bcrypt hashing (password_hash)
   - ✅ No plaintext password storage

4. **SQL Injection Prevention:**
   - ✅ mysqli_real_escape_string() used
   - ✅ Prepared statements ready (optional upgrade)

5. **Session Security:**
   - ✅ No sensitive data in URLs (except token)
   - ✅ Token one-time use only
   - ✅ Immediate token deletion after use

---

## 📧 Email Integration (Future Enhancement)

### Current Implementation (Demo Mode)
- Reset link displayed on screen
- Suitable for local testing
- Shows production email in message

### Production Implementation (To Add)
Replace in `forgot_password.php`:

```php
// Instead of displaying link, send email:
$reset_link = "https://yoursite.com/reset_password.php?token=" . $token;

// Use PHPMailer or similar
$mail = new PHPMailer();
$mail->setFrom('noreply@furrymart.com', 'FurryMart');
$mail->addAddress($email, $user['first_name']);
$mail->Subject = 'Password Reset Request';
$mail->Body = "Click here to reset: $reset_link";
$mail->send();
```

---

## 🧪 Testing Guide

### Test Scenario 1: Valid Reset
1. Go to login page
2. Click "Forgot Password?"
3. Enter: `ishu123@gmail.com`
4. Copy reset link
5. Paste in browser
6. Enter new password: `newpass123`
7. Confirm password: `newpass123`
8. Submit
9. Verify redirect to login
10. Login with new password ✅

### Test Scenario 2: Expired Token
1. Request password reset
2. Wait 61 minutes (or manually set expiry in DB)
3. Try to use link
4. Should show "expired" error ❌

### Test Scenario 3: Invalid Token
1. Visit: `reset_password.php?token=invalid123`
2. Should show "invalid" error ❌

### Test Scenario 4: Password Mismatch
1. Valid reset link
2. Password: `test123`
3. Confirm: `test456`
4. Should show error ❌

---

## 🐛 Troubleshooting

### Issue: Table doesn't exist
**Solution:** Run `setup_password_reset.php` or import SQL file

### Issue: Token expired immediately
**Solution:** Check server timezone matches database timezone

### Issue: Password not updating
**Solution:** Check users table has 'password' column and proper permissions

### Issue: Link not working
**Solution:** Verify `mod_rewrite` enabled and `.htaccess` configured

---

## 📊 Database Maintenance

### Clean Expired Tokens (Run Periodically)
```sql
DELETE FROM password_resets WHERE expires_at < NOW();
```

### View Active Tokens
```sql
SELECT email, created_at, expires_at 
FROM password_resets 
WHERE expires_at > NOW();
```

### Check Reset History
```sql
SELECT COUNT(*) as total_resets 
FROM password_resets 
GROUP BY email;
```

---

## ✅ Checklist for Production

- [ ] Set up email sending (PHPMailer/SMTP)
- [ ] Change link to use domain name instead of localhost
- [ ] Enable HTTPS for secure token transmission
- [ ] Set up cron job to clean expired tokens
- [ ] Add rate limiting (max 3 requests per hour per email)
- [ ] Implement CAPTCHA on forgot password form
- [ ] Log all password reset attempts
- [ ] Add notification to user's email when password changed
- [ ] Test on production server
- [ ] Update documentation with production URLs

---

## 🆘 Support

For issues or questions:
1. Check troubleshooting section
2. Verify database setup
3. Check PHP error logs
4. Test with different browsers

---

## 📝 Notes

- Token length: 64 characters (hex)
- Token hash: 64 characters (SHA-256)
- Expiry time: 60 minutes
- Password min length: 6 characters
- Database engine: InnoDB
- Character set: utf8mb4

---

## 🎉 Success!

Your password reset system is now fully functional! Users can:
- ✅ Request password resets
- ✅ Receive secure links
- ✅ Set new passwords
- ✅ Login with updated credentials

**Enjoy secure password recovery in FurryMart!** 🐾
