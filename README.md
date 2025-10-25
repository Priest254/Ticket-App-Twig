# TicketApp - Twig Version

A PHP/Twig implementation of the TicketApp project, providing the same functionality as the React version but using server-side rendering with Twig templates.

## Features

- **User Authentication**: Login and signup functionality with session management
- **Ticket Management**: Full CRUD operations for tickets
- **Dashboard**: Overview with ticket statistics
- **Responsive Design**: Mobile-friendly interface with Tailwind CSS
- **Modern UI**: Beautiful gradient backgrounds and animations

## Project Structure

```
ticketapp-twig/
├── index.php              # Main entry point with routing
├── .htaccess             # URL rewriting rules
├── composer.json         # PHP dependencies
├── templates/           # Twig templates
│   ├── base.html.twig   # Base template
│   ├── components/       # Reusable components
│   │   ├── navbar.html.twig
│   │   └── footer.html.twig
│   └── pages/           # Page templates
│       ├── landing.html.twig
│       ├── auth/
│       │   ├── login.html.twig
│       │   └── signup.html.twig
│       ├── dashboard.html.twig
│       └── tickets.html.twig
└── data/                # JSON data storage
    ├── users.json       # User data
    └── tickets.json     # Ticket data
```

## Installation

1. **Install PHP dependencies:**
   ```bash
   composer install
   ```

2. **Set up web server:**
   - Ensure your web server supports PHP
   - Point document root to the project directory
   - Enable URL rewriting (mod_rewrite for Apache)

3. **Set permissions:**
   ```bash
   chmod 755 data/
   chmod 644 data/*.json
   ```

## Usage

1. **Start the application:**
   - Navigate to your web server URL
   - The application will automatically create necessary data files

2. **Create an account:**
   - Click "Get Started" on the landing page
   - Fill out the signup form
   - Login with your credentials

3. **Manage tickets:**
   - View dashboard for statistics
   - Create, edit, and delete tickets
   - Track ticket status (Open, In Progress, Closed)

## Features Comparison with React Version

| Feature | React Version | Twig Version |
|--------|---------------|--------------|
| Authentication | ✅ Context API | ✅ Session-based |
| Ticket CRUD | ✅ Context API | ✅ Server-side |
| Dashboard Stats | ✅ Real-time | ✅ Server-side |
| Responsive Design | ✅ Tailwind | ✅ Tailwind |
| Animations | ✅ Framer Motion | ✅ CSS Animations |
| Data Persistence | ✅ localStorage | ✅ JSON files |

## Technical Details

- **Backend**: PHP with session management
- **Templating**: Twig for server-side rendering
- **Styling**: Tailwind CSS via CDN
- **Data Storage**: JSON files (easily replaceable with database)
- **Routing**: Simple PHP-based routing
- **Authentication**: Session-based with password storage

## Development

The application uses a simple MVC-like structure:
- **Model**: JSON file operations in `index.php`
- **View**: Twig templates in `templates/`
- **Controller**: Routing and business logic in `index.php`

## Security Notes

- This is a demo application
- In production, use proper password hashing
- Implement CSRF protection
- Use a proper database instead of JSON files
- Add input validation and sanitization

## Browser Support

- Modern browsers with ES6 support
- Responsive design for mobile devices
- Progressive enhancement for JavaScript features
