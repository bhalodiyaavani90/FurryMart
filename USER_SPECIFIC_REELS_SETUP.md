# FURRYMART - User-Specific Reel Likes and Saves Setup Guide

## Overview
The Mood Reels system has been updated to show user-specific likes and saved reels. Each user will now only see their own liked and saved reels when they log in.

## What Changed?

### 1. Database Storage
- **Before**: Likes and saves were stored in browser's localStorage (shared across all users on the same browser)
- **After**: Likes and saves are stored in the database with user_id association (each user has their own likes and saves)

### 2. User-Specific Display
- When **Khushi** logs in, she will only see her liked and saved reels
- When **Avani** logs in, she will only see her liked and saved reels
- The counts (6 likes, 6 saves) are now specific to each user

## Setup Instructions

### Step 1: Create Database Tables

1. Open **phpMyAdmin** (usually at http://localhost/phpmyadmin/)
2. Select your **furrymart** database
3. Click on the **SQL** tab
4. Copy all the content from the file: `reel_likes_saves.sql`
5. Paste it into the SQL query box
6. Click **Go** to execute

This will create two new tables:
- `reel_likes` - stores which user liked which reel
- `reel_saves` - stores which user saved which reel

### Step 2: Clear Browser Cache (Important!)

Since we're moving from localStorage to database storage, you need to clear the browser cache:

**Option 1: Clear Site Data (Recommended)**
1. Press `Ctrl + Shift + I` (Windows) or `Cmd + Option + I` (Mac) to open Developer Tools
2. Go to **Application** tab (Chrome) or **Storage** tab (Firefox)
3. Find **Local Storage** in the left sidebar
4. Click on your site URL (http://localhost/FURRYMART/)
5. Right-click and select **Clear** or click the ❌ icon
6. Refresh the page with `Ctrl + F5`

**Option 2: Hard Refresh**
- Press `Ctrl + Shift + R` (Windows) or `Cmd + Shift + R` (Mac)
- Or press `Ctrl + F5`

### Step 3: Test the Feature

1. **Login as first user (e.g., Khushi)**:
   - Go to http://localhost/FURRYMART/login.php
   - Login with Khushi's credentials
   - Go to Pet Feelings page
   - Like some reels (click heart icon)
   - Save some reels (click bookmark icon)
   - Check the counts at the top (should show your likes and saves)
   - Click on the "❤️" pill to filter only liked reels
   - Click on the "🔖" pill to filter only saved reels

2. **Logout and login as second user (e.g., Avani)**:
   - Logout from Khushi's account
   - Login with Avani's credentials
   - Go to Pet Feelings page
   - Notice that the liked and saved counts are 0 (or different from Khushi's)
   - Like and save different reels
   - Check that only Avani's likes and saves are shown

3. **Switch back to first user**:
   - Logout and login as Khushi again
   - Verify that Khushi's original likes and saves are still there
   - The counts should match what Khushi had before

## Features

✅ **User-Specific Likes**: Each user has their own list of liked reels
✅ **User-Specific Saves**: Each user has their own list of saved reels
✅ **Persistent Storage**: Likes and saves are stored in database (not lost on browser cache clear)
✅ **Real-time Filtering**: Click on the pills to filter reels by likes or saves
✅ **Accurate Counts**: The counts at the top show only the logged-in user's data
✅ **Cross-Device Sync**: Login from any device/browser and see your same likes and saves

## Files Modified

1. **reel_likes_saves.sql** (NEW)
   - SQL script to create database tables

2. **reel_likes_saves_handler.php** (NEW)
   - Backend handler for managing likes and saves
   - Handles: get_user_data, toggle_like, toggle_save

3. **pet_feelings.php** (MODIFIED)
   - Updated JavaScript to fetch user-specific data from database
   - Removed localStorage dependency
   - Added AJAX calls to backend handler

## Troubleshooting

### Issue: Counts not updating
**Solution**: Clear browser cache and hard refresh (Ctrl + F5)

### Issue: "User not logged in" error
**Solution**: Make sure you're logged in to FurryMart before using the feature

### Issue: Database errors
**Solution**: 
1. Check if tables are created by running in phpMyAdmin:
   ```sql
   SHOW TABLES LIKE 'reel_%';
   ```
   You should see: `reel_likes`, `reel_saves`, `reel_comments`, etc.

2. Verify the tables have correct structure:
   ```sql
   DESCRIBE reel_likes;
   DESCRIBE reel_saves;
   ```

### Issue: Old likes/saves from localStorage still showing
**Solution**: 
1. Clear Local Storage (see Step 2 above)
2. Hard refresh the page
3. The system will now fetch data from database only

## Technical Details

### Database Schema

**reel_likes table**:
- `id` (INT, PRIMARY KEY, AUTO_INCREMENT)
- `reel_id` (INT) - the mood reel ID
- `user_id` (INT) - the logged-in user's ID
- `created_at` (TIMESTAMP)
- UNIQUE constraint on (reel_id, user_id)

**reel_saves table**:
- `id` (INT, PRIMARY KEY, AUTO_INCREMENT)
- `reel_id` (INT) - the mood reel ID
- `user_id` (INT) - the logged-in user's ID
- `created_at` (TIMESTAMP)
- UNIQUE constraint on (reel_id, user_id)

### API Endpoints (reel_likes_saves_handler.php)

**get_user_data**
- Returns: Array of liked reel IDs and saved reel IDs for current user
- Response: `{success: true, likes: [1,3,5], saves: [2,4,6], like_count: 3, save_count: 3}`

**toggle_like**
- Params: `reel_id`
- Adds or removes like for current user
- Response: `{success: true, action: 'added'|'removed', message: '...'}`

**toggle_save**
- Params: `reel_id`
- Adds or removes save for current user
- Response: `{success: true, action: 'added'|'removed', message: '...'}`

## Notes

- All interactions are now tied to the logged-in user's ID
- Data persists across sessions and devices
- No interference between different users' preferences
- Optimistic UI updates for better user experience

---
**Created**: February 14, 2026
**Status**: ✅ Implemented and Ready for Use
