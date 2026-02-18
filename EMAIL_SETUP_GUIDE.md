# FurryMart Customer Care Email Setup Guide

## 📧 Recommended Email Configuration

### Primary Customer Care Email
**Email Address:** `customercare@furrymart.com`

---

## 🔧 How to Set Up Your Customer Care Email

### Option 1: Using Your Domain Email (Professional - RECOMMENDED)

If you own the domain `furrymart.com`, follow these steps:

1. **Login to your domain hosting provider** (e.g., GoDaddy, Namecheap, Bluehost)
2. **Navigate to Email section** in cPanel or your hosting control panel
3. **Create new email account:**
   - Email: `customercare@furrymart.com`
   - Password: Create a strong password (at least 12 characters)
   - Suggested Password: `FurryMart@Care2026#Secure`

4. **Configure Email Forwarding (Optional):**
   - Forward all emails from `customercare@furrymart.com` to your personal email
   - This way you'll receive notifications instantly

5. **Access Methods:**
   - Webmail: Usually at `https://mail.furrymart.com` or `https://webmail.furrymart.com`
   - Email Client: Use Outlook, Gmail, or Thunderbird with IMAP/SMTP settings

---

### Option 2: Using Gmail with Custom Domain (Free & Easy)

1. **Create Gmail Account:**
   - Go to Gmail.com
   - Create: `furrymartcustomercare@gmail.com`
   - Suggested Password: `FurryMart#Care2026!Support`

2. **Update in contact.php:**
   - Replace `customercare@furrymart.com` with `furrymartcustomercare@gmail.com`

3. **Enable Gmail Forwarding:**
   - Go to Settings → Forwarding and POP/IMAP
   - Add your personal email for notifications

---

### Option 3: Business Email Service (Most Professional)

#### Using Google Workspace (Paid)
- Cost: ₹125/user/month
- Email: `customercare@furrymart.com`
- Benefits: Professional, 30GB storage, 24/7 support

#### Using Microsoft 365 (Paid)
- Cost: ₹420/user/month
- Email: `customercare@furrymart.com`
- Benefits: Outlook integration, 50GB storage

---

## 🔐 IMPORTANT SECURITY NOTES

### ⚠️ Never Share These Credentials:
- Do NOT share email password with anyone
- Do NOT include password in your code
- Use environment variables for sensitive data

### 🛡️ Security Best Practices:
1. **Enable Two-Factor Authentication (2FA)**
2. **Use strong passwords** (mix of uppercase, lowercase, numbers, symbols)
3. **Change password every 3-6 months**
4. **Monitor login activity regularly**
5. **Use email encryption** for sensitive communications

---

## 📱 Updated Contact Information in Your Website

### Current Configuration:
```
WhatsApp: +91 99257 08043
Phone: +91 99257 08043
Email: customercare@furrymart.com
```

### Features Implemented:
✅ WhatsApp - Direct chat with FurryMart bot
✅ Phone - Confirmation popup before calling
✅ Email - Pre-filled template for customer inquiries
✅ Location - Interactive Google Maps modal

---

## 🎯 Email Template Configuration

The contact page now sends emails with this template:

**Subject:** Customer Inquiry - FurryMart

**Body:**
```
Dear FurryMart Customer Care Team,

I am reaching out regarding:

[Customer describes their query here]

Thank you!
```

---

## 📧 Accessing Customer Care Emails

### For Domain Email:
1. Go to your webmail provider
2. Login with:
   - Email: `customercare@furrymart.com`
   - Password: [Your chosen password]

### For Gmail:
1. Go to gmail.com
2. Login with:
   - Email: `furrymartcustomercare@gmail.com`
   - Password: [Your chosen password]

### Configure Email Client (Outlook/Thunderbird):
**Incoming Server (IMAP):**
- Server: `mail.furrymart.com` or `imap.gmail.com`
- Port: 993
- Security: SSL/TLS

**Outgoing Server (SMTP):**
- Server: `mail.furrymart.com` or `smtp.gmail.com`
- Port: 465 or 587
- Security: SSL/TLS
- Authentication: Required

---

## 🔄 Auto-Reply Setup (Optional)

Set up an auto-reply for customer emails:

**Sample Auto-Reply Message:**
```
Dear Valued Customer,

Thank you for contacting FurryMart! 🐾

We have received your inquiry and our customer care team will respond within 24 hours.

For immediate assistance:
📱 WhatsApp: +91 99257 08043
📞 Call: +91 99257 08043 (Mon-Sat, 9:30 AM - 6:30 PM)

Best regards,
FurryMart Customer Care Team
```

---

## 📞 Complete Contact Configuration

All contact cards are now fully functional:

### 1. WhatsApp Card
- **Number:** +91 99257 08043
- **Action:** Opens WhatsApp chat with FurryMart
- **Pre-filled Message:** "Hi FurryMart! I would like to know more about your products and services."

### 2. Email Card
- **Email:** customercare@furrymart.com
- **Action:** Opens email client with pre-filled template
- **Subject:** Customer Inquiry - FurryMart

### 3. Phone Card
- **Number:** +91 99257 08043
- **Action:** Shows confirmation popup, then initiates call
- **Popup Message:** "Do you want to call us for any query?"

### 4. Location Card
- **Action:** Opens interactive Google Maps modal
- **Location:** FurryMart HQ, Pastel Plaza, Nehru Place, New Delhi

---

## 🎨 Additional Features

- ✨ Hover animations on all cards
- 🎭 Floating effect when cards become visible
- 💫 Pulse animation on icons
- 🌟 Shine effect on hover
- 📱 Fully responsive design
- ⌨️ Keyboard shortcuts (ESC to close map)

---

## 📝 Next Steps

1. **Choose an email option** from above
2. **Set up the email account** with a strong password
3. **Test all contact methods** to ensure they work
4. **Keep credentials secure** (don't share with anyone)
5. **Monitor inbox regularly** for customer inquiries

---

## 🆘 Support

If you need help setting up:
- Contact your hosting provider for domain email setup
- Visit Google Workspace Help for Gmail setup
- Check your cPanel documentation for webmail access

---

**Last Updated:** February 11, 2026
**Version:** 1.0
**Maintained By:** FurryMart Development Team
