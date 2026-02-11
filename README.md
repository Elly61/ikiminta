# IKIMINTA - Financial Management System

A comprehensive financial management web application for deposits, money transfers, loans, withdrawals, and currency conversion.

## Features

### Member Features
- **User Registration & Login** - Create account with username, email, legal ID, and phone number
- **Dashboard** - View comprehensive financial overview with balance, credits, debits
- **Deposits** - Deposit funds via credit card or Mobile Money (MOMO)
- **Transfer Funds** - Send money to other members with automatic fee calculation
- **Loans** - Request loans with admin approval and track loan payments
- **Withdrawals** - Request withdrawals with blockchain integration
- **Savings** - Create savings accounts with interest rates and maturity dates
- **Transaction History** - Track all transactions with filtering options
- **Currency Converter** - Convert between multiple currencies

### Admin Features
- **Dashboard** - Overview of pending requests and system activity
- **Deposit Management** - Approve/reject member deposits
- **Loan Approval** - Review and approve/reject loan requests
- **Withdrawal Management** - Process withdrawals with blockchain recording
- **User Management** - Manage member accounts and suspend/activate users
- **Settings** - Configure transfer fees, withdrawal fees, and signup bonuses

## Technology Stack

- **Frontend**: HTML5, CSS3, JavaScript
- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Payment Gateway**: Mobile Money (MOMO) API
- **Blockchain**: SHA-256 hashing for transaction records
- **Architecture**: MVC (Model-View-Controller)

## Installation

### Requirements
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache with mod_rewrite enabled
- XAMPP or similar local server

### Steps

1. **Clone/Copy Project**
   ```bash
   Copy the project to C:\xampp\htdocs\ikiminta\
   ```

2. **Import Database**
   - Open phpMyAdmin
   - Create a new database named `ikiminta`
   - Import `database.sql` file
   - Default admin credentials: admin@ikiminta.com / Admin@123

3. **Configure Settings**
   - Edit `application/config/config.php`
   - Update database credentials if needed
   - Add your MOMO API keys:
     ```php
     define('MOMO_API_KEY', 'your_key');
     define('MOMO_API_SECRET', 'your_secret');
     define('MOMO_MERCHANT_ID', 'your_merchant_id');
     ```

4. **Start Application**
   - Start Apache and MySQL in XAMPP
   - Navigate to `http://localhost/ikiminta/`

## Project Structure

```
ikiminta/
├── application/
│   ├── config/           # Configuration files
│   ├── controller/       # Application controllers
│   ├── model/            # Database models
│   ├── views/            # HTML templates
│   ├── libraries/        # Custom libraries (Currency Converter)
│   └── third_party/      # External integrations (MOMO, Blockchain)
├── public/
│   ├── css/              # Stylesheets
│   ├── js/               # JavaScript files
│   └── images/           # Image assets
├── database.sql          # Database schema
├── index.php             # Main entry point
└── .htaccess             # URL rewriting rules
```

## URL Routing

The application uses a simple routing system based on URL patterns:

```
/member/dashboard           - Member dashboard
/member/deposits           - View deposits
/member/transfer           - Transfer funds
/member/loans              - Manage loans
/member/withdraw           - Manage withdrawals
/member/savings            - Manage savings
/member/transactions       - View transactions
/admin/dashboard           - Admin dashboard
/admin/deposits            - Manage deposits
/admin/loans               - Manage loan requests
/admin/withdrawals         - Manage withdrawals
/admin/users               - Manage users
/admin/settings            - System settings
```

## Database Schema

### Key Tables
- `users` - User accounts and profiles
- `deposits` - Deposit transactions
- `transfer_funds` - Fund transfers between users
- `loan_requests` - Loan request applications
- `loans` - Active loan records
- `withdraw_requests` - Withdrawal requests
- `withdrawals` - Completed withdrawals
- `transactions` - Transaction history
- `savings` - Savings accounts
- `currency_rates` - Currency exchange rates
- `blockchain_records` - Blockchain transaction records
- `settings` - System configuration

## Security Features

- Password hashing using bcrypt
- SQL prepared statements to prevent injection
- Session-based authentication
- CSRF protection
- Input validation and sanitization
- HTTPS support ready

## API Integration

### MOMO Payment Gateway
- Initiate payments
- Check payment status
- Phone number validation

### Blockchain Integration
- Transaction recording on blockchain
- SHA-256 hash generation
- Transaction verification
- Transaction history retrieval

## Default Credentials

**Admin Account:**
- Email: admin@ikiminta.com
- Password: Admin@123

**Note:** Change these credentials immediately after first login.

## File Upload

Supports file uploads in:
- `/public/uploads/` - General uploads
- `/public/uploads/profile/` - Profile pictures

Maximum file size: 5MB
Allowed formats: jpg, jpeg, png, pdf, doc, docx

## Configuration Options

Edit `application/config/config.php` to customize:

```php
// App Environment
define('APP_ENV', 'development'); // or 'production'

// Session timeout (in seconds)
define('SESSION_TIMEOUT', 3600); // 1 hour

// Password requirements
define('PASSWORD_MIN_LENGTH', 8);

// Pagination
define('ITEMS_PER_PAGE', 10);

// Email settings
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);

// MOMO Payment
define('MOMO_SANDBOX', true); // true for testing

// Blockchain
define('BLOCKCHAIN_ENABLED', true);
```

## Troubleshooting

**Issue: Page not found (404)**
- Ensure mod_rewrite is enabled
- Check .htaccess file permissions
- Verify URL routing in index.php

**Issue: Database connection error**
- Check database credentials in config/database.php
- Verify MySQL is running
- Ensure database is imported

**Issue: Sessions not working**
- Check session.save_path permissions
- Verify PHP session settings
- Clear browser cookies

## Support & Documentation

For detailed documentation and API references, check individual file comments.

## License

Copyright © 2025 IKIMINTA. All rights reserved.

## Version

Current Version: 1.0.0
Last Updated: January 26, 2025
