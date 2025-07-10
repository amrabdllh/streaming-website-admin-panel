# Streaming Website Development Plan

## Project Structure
```
/
├── index.php (Frontend streaming site)
├── assets/
│   ├── css/
│   │   ├── style.css
│   │   └── admin.css
│   ├── js/
│   │   ├── main.js
│   │   └── admin.js
│   └── images/
├── web/
│   └── crotah/
│       ├── index.php (Admin login)
│       ├── dashboard.php
│       ├── pages/
│       │   ├── home.php
│       │   ├── videos.php
│       │   ├── settings.php
│       │   ├── profile.php
│       │   └── logout.php
│       └── includes/
│           ├── config.php
│           ├── functions.php
│           └── auth.php
└── database/
    └── streaming_db.sql

## Database Tables
1. videos (id, iframe_url, thumbnail, title, genre, views, created_at)
2. settings (id, setting_name, setting_value)
3. admin_users (id, username, password, created_at)

## Frontend Features
- Video grid layout like Javmama
- Search functionality
- Genre filtering
- Video player with iframe embedding
- Responsive design

## Admin Panel Features
- Login authentication
- Dashboard with statistics
- Video CRUD operations
- Settings management (General, SEO, Appearance)
- Profile management
- Session management

## Implementation Steps
1. Create database structure
2. Build frontend streaming site
3. Create admin authentication system
4. Build admin dashboard
5. Implement CRUD operations
6. Add settings management
7. Test all functionality
