# Generic Admin Template

A clean, reusable CodeIgniter 3 admin template with built-in Role-Based Access Control (RBAC).

## Features

- **User Authentication** - Login/logout with session management
- **Role-Based Access Control** - Roles, permissions, and permission groups
- **Clean Dashboard** - Ready to customize for your project
- **Responsive Design** - Bootstrap 4 based admin layout
- **Generic Structure** - Easy to extend for any project type

## Directory Structure

```
custom-bootstrap-admin/
├── application/
│   ├── config/          # Configuration files
│   ├── controllers/     # Auth, Dashboard, Users, Roles, Permissions
│   ├── helpers/         # Admin helper functions
│   ├── libraries/       # Auth library
│   ├── models/          # Database models
│   └── views/           # Layouts and views
│       ├── admin/
│       │   ├── layouts/  # Header, sidebar, footer
│       │   ├── dashboard/
│       │   ├── users/
│       │   ├── roles/
│       │   └── permissions/
│       └── auth/
├── assets/
│   ├── css/             # Custom styles
│   ├── js/              # Custom JavaScript
│   ├── icons/           # Favicon and icons
│   └── images/           # Images
├── database.sql         # Clean database schema
└── index.php            # Entry point
```

## Quick Setup

### 1. Database Setup

1. Create a new database:
   ```sql
   CREATE DATABASE admin_template;
   ```

2. Import the clean schema:
   ```sql
   mysql -u root -p admin_template < database.sql
   ```

### 2. Configure Database

Edit `application/config/database.php`:
```php
$db['default'] = array(
    'hostname' => 'localhost',
    'username' => 'your_username',
    'password' => 'your_password',
    'database' => 'admin_template',
    'dbdriver' => 'mysqli',
);
```

### 3. Configure Base URL

Edit `application/config/config.php`:
```php
$config['base_url'] = 'http://localhost/your-project-folder/';
```

### 4. Run the Application

Visit `http://localhost/your-project-folder/`

**Default Login:**
- Email: `admin@example.com`
- Password: `password123`

## Database Schema

### Core Tables

| Table | Description |
|-------|-------------|
| `admin_users` | System users (admins, managers) |
| `roles` | User roles (Admin, Manager, etc.) |
| `permission_groups` | Groups for organizing permissions |
| `permissions` | Individual permission items |
| `role_permissions` | Links roles to permissions |

### Default Roles

1. **Admin** - Full access to all features
2. **Manager** - Limited access (view users, settings)

### Default Permissions

**Dashboard:**
- `dashboard.view`

**Users:**
- `users.view`, `users.create`, `users.edit`, `users.delete`

**Roles:**
- `roles.view`, `roles.create`, `roles.edit`, `roles.delete`

**Permissions:**
- `permissions.view`, `permissions.manage`

**Settings:**
- `settings.view`, `settings.edit`

## Extending the Template

### Adding New Modules

1. **Create Controller** in `application/controllers/`
2. **Create Model** in `application/models/` (if needed)
3. **Create Views** in `application/views/admin/`
4. **Add Routes** in `application/config/routes.php`

Example:
```php
// routes.php
$route['products'] = 'products/index';
$route['products/create'] = 'products/create';
```

### Adding New Permissions

1. Add to `permission_groups` table (if new group)
2. Add to `permissions` table:
   ```sql
   INSERT INTO permissions (permission_group_id, perm_name, perm_slug) 
   VALUES (1, 'View Products', 'products.view');
   ```
3. Assign to roles in `role_permissions` table

### Customizing the Dashboard

Edit `application/views/admin/dashboard/index.php`:
```php
// Add your custom stats
$data['stats'] = [
    'total_orders' => $this->Order_model->count_all(),
    'revenue' => $this->Order_model->get_total_revenue(),
];
```

### Branding

Update branding in:
- `application/views/admin/layouts/header.php` - Title
- `application/views/admin/layouts/sidebar.php` - Logo and brand name
- `application/views/admin/layouts/footer.php` - Copyright
- `application/views/auth/login.php` - Login page branding
- `assets/css/custom.css` - Custom colors/styles

## Security Notes

1. **Change encryption key** in `application/config/config.php`:
   ```php
   $config['encryption_key'] = 'your-32-character-secret-key';
   ```

2. **Update default passwords** in production

3. **Configure email** in `application/config/email.php` for production

## License

MIT License - Feel free to use this template for your projects.