# Streaming Website with Admin Panel

A complete streaming website with admin panel built with PHP, MySQL, HTML, CSS, and JavaScript.

## Features

### Frontend Website
- **Modern Design**: Dark theme with responsive layout similar to popular streaming sites
- **Video Grid Layout**: Clean grid display of videos with thumbnails
- **Search Functionality**: Real-time search through video titles
- **Genre Filtering**: Filter videos by genre categories
- **Video Player**: Iframe-based video embedding system
- **Responsive Design**: Works on desktop, tablet, and mobile devices

### Admin Panel
- **Secure Login**: Password-protected admin access
- **Dashboard**: Statistics and overview of video performance
- **Video Management**: Full CRUD operations for videos
- **Settings Management**: 
  - General settings (Site name, Telegram URL, Base URL)
  - SEO settings (Title, Description, Keywords)
  - Appearance settings (Coming soon)
- **Profile Management**: Update admin password
- **Session Management**: Secure logout functionality

## Installation

### Prerequisites
- Web server (Apache/Nginx)
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Modern web browser

### Setup Instructions

1. **Clone/Download** the project files to your web server directory

2. **Database Setup**:
   - Create a MySQL database named `streaming_website`
   - Import the SQL file: `database/streaming_db.sql`
   - Or run the SQL commands manually

3. **Configuration**:
   - Edit `web/crotah/includes/config.php`
   - Update database credentials if needed:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_USER', 'your_username');
     define('DB_PASS', 'your_password');
     define('DB_NAME', 'streaming_website');
     ```

4. **Initialize Data**:
   - Visit `setup.php` in your browser
   - This will create default admin user and sample data

5. **Access the Website**:
   - **Frontend**: `http://yourdomain.com/index.php`
   - **Admin Panel**: `http://yourdomain.com/web/crotah/index.php`

## Default Login Credentials

- **Username**: `admin`
- **Password**: `admin123`

⚠️ **Important**: Change the default password after first login!

## File Structure

```
/
├── index.php                 # Frontend homepage
├── setup.php                 # Initial setup script
├── README.md                 # This file
├── assets/
│   ├── css/
│   │   ├── style.css         # Frontend styles
│   │   └── admin.css         # Admin panel styles
│   └── js/
│       ├── main.js           # Frontend JavaScript
│       └── admin.js          # Admin panel JavaScript
├── database/
│   └── streaming_db.sql      # Database structure
└── web/
    └── crotah/               # Admin panel directory
        ├── index.php         # Admin login page
        ├── dashboard.php     # Admin dashboard
        ├── includes/
        │   ├── config.php    # Database configuration
        │   └── auth.php      # Authentication functions
        └── pages/
            ├── home.php      # Dashboard home
            ├── videos.php    # Video management
            ├── settings.php  # Settings management
            └── profile.php   # Profile management
```

## Usage

### Adding Videos

1. Login to admin panel
2. Go to "Videos" section
3. Fill in the form:
   - **Iframe Embed Code**: Use the template provided
   - **Video Title**: Enter descriptive title
   - **Genre**: Specify video category
   - **Thumbnail URL**: Optional image URL

### Iframe Template

```html
<IFRAME SRC="LINK_EMBED" FRAMEBORDER=0 MARGINWIDTH=0 MARGINHEIGHT=0 SCROLLING=NO WIDTH=640 HEIGHT=360 allowfullscreen></IFRAME>
```

**Example**:
```html
<IFRAME SRC="https://movearnpre.com/embed/68e7zzyb52dc" FRAMEBORDER=0 MARGINWIDTH=0 MARGINHEIGHT=0 SCROLLING=NO WIDTH=640 HEIGHT=360 allowfullscreen></IFRAME>
```

### Customizing Settings

1. **General Settings**:
   - Site Name: Appears as logo on frontend
   - Telegram URL: Your channel/group link
   - Base URL: Root URL for logo clicks

2. **SEO Settings**:
   - SEO Title: Browser title and search results
   - SEO Description: Meta description for search engines
   - SEO Keywords: Comma-separated keywords

## Security Features

- Password hashing with PHP's `password_hash()`
- SQL injection prevention with prepared statements
- Session-based authentication
- CSRF protection on forms
- Input validation and sanitization

## Browser Compatibility

- Chrome 60+
- Firefox 55+
- Safari 12+
- Edge 79+
- Mobile browsers (iOS Safari, Chrome Mobile)

## Customization

### Changing Colors/Theme
Edit `assets/css/style.css` and `assets/css/admin.css` to modify:
- Color schemes
- Layout styles
- Typography
- Responsive breakpoints

### Adding New Features
- Database tables can be extended
- New admin pages can be added to `web/crotah/pages/`
- Frontend functionality can be enhanced in `assets/js/main.js`

## Troubleshooting

### Common Issues

1. **Database Connection Error**:
   - Check database credentials in `config.php`
   - Ensure MySQL service is running
   - Verify database exists

2. **Admin Login Not Working**:
   - Run `setup.php` to create default user
   - Check if `admin_users` table exists
   - Verify password hashing

3. **Videos Not Displaying**:
   - Check if `videos` table has data
   - Verify iframe URLs are valid
   - Check for JavaScript errors in browser console

4. **Styling Issues**:
   - Clear browser cache
   - Check if CSS files are loading
   - Verify file paths are correct

## Support

For issues and questions:
1. Check the troubleshooting section
2. Verify all files are uploaded correctly
3. Check browser console for JavaScript errors
4. Ensure proper file permissions

## License

This project is open source and available under the MIT License.

## Version

Current Version: 1.0.0
Last Updated: 2024
