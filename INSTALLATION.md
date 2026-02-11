# IKIMINTA Installation & Setup Guide

## Quick Start Guide

### 1. Database Setup

1. **Open phpMyAdmin**
   - Go to http://localhost/phpmyadmin/
   - Log in with default credentials (root, no password)

2. **Create Database**
   - Click "New" on the left
   - Enter database name: `ikiminta`
   - Click "Create"

3. **Import SQL Schema**
   - Click on the `ikiminta` database
   - Go to "Import" tab
   - Click "Choose File" and select `database.sql` from the project root
   - Click "Import"

4. **Verify Tables**
   - You should see 14 tables created:
     - users
     - settings
     - deposits
     - savings
     - transfer_funds
     - loan_requests
     - loans
     - loan_payments
     - withdraw_requests
     - withdrawals
     - transactions
     - currency_rates
     - blockchain_records
     - audit_logs

### 2. PHP Configuration

1. **Update Database Connection** (if needed)
   - File: `application/config/database.php`
   - Default:
     ```php
     'host'     => 'localhost',
     'username' => 'root',
     'password' => '',
     'database' => 'ikiminta',
     ```

2. **Configure Application Settings**
   - File: `application/config/config.php`
   - Update MOMO API credentials (optional for testing):
     ```php
     define('MOMO_API_KEY', 'your_api_key');
     define('MOMO_API_SECRET', 'your_api_secret');
     define('MOMO_MERCHANT_ID', 'your_merchant_id');
     define('MOMO_SANDBOX', true); // true for testing
     ```

### 3. Directory Permissions

Ensure writable directories:
```bash
chmod 755 public/uploads/
chmod 755 public/uploads/profile/
chmod 755 logs/
```

### 4. Apache Configuration

1. **Enable Mod Rewrite**
   - In XAMPP Control Panel, click "Apache" → "Config" → "httpd.conf"
   - Find and uncomment: `LoadModule rewrite_module modules/mod_rewrite.so`
   - Restart Apache

2. **Virtual Host Setup** (Optional)
   - Add to `httpd.conf`:
   ```apache
   <VirtualHost *:80>
       ServerName ikiminta.local
       DocumentRoot "C:/xampp/htdocs/ikiminta"
       <Directory "C:/xampp/htdocs/ikiminta">
           AllowOverride All
       </Directory>
   </VirtualHost>
   ```

### 5. Start Application

1. **Start Services**
   - Start Apache in XAMPP Control Panel
   - Start MySQL in XAMPP Control Panel

2. **Access Application**
   - Member Portal: http://localhost/ikiminta/
   - Admin Portal: http://localhost/ikiminta/admin/auth/login

## Login Credentials

### Default Admin Account
- **Email**: admin@ikiminta.com
- **Password**: Admin@123
- **Type**: Super Admin

### Test Member Account (After Registration)
- Create a new account via registration page
- Email used for login
- Password must be at least 8 characters

## Feature Testing

### Member Features to Test

1. **Registration**
   - Go to http://localhost/ikiminta/
   - Click "Register"
   - Fill in all fields (Legal ID must be exactly 16 characters)
   - Submit

2. **Login**
   - Use registered email and password
   - Should redirect to dashboard

3. **Dashboard**
   - View balance and statistics
   - Check recent transactions

4. **Deposits**
   - Click "Deposits" from sidebar
   - Click "+ New Deposit"
   - Enter amount and select payment method
   - Submit (Admin must approve)

5. **Transfers**
   - Click "Transfer Funds"
   - Enter receiver user ID
   - Enter amount
   - Submit transfer

6. **Loans**
   - Click "Loans"
   - Click "+ Request Loan"
   - Fill in amount and duration
   - Admin must approve

7. **Withdrawals**
   - Click "Withdrawals"
   - Click "+ Request Withdrawal"
   - Select method (bank/momo/cash)
   - Admin must approve

8. **Savings**
   - Click "Savings"
   - Create savings account with interest rate
   - Account will mature automatically

9. **Transactions**
   - Click "Transactions"
   - View all transaction history
   - Filter by type

### Admin Features to Test

1. **Admin Login**
   - Go to http://localhost/ikiminta/admin/auth/login
   - Use admin credentials

2. **Dashboard**
   - View pending requests count
   - Quick action buttons

3. **Manage Deposits**
   - Click "Pending Deposits"
   - Approve or reject deposits
   - Check user balance updates

4. **Manage Loans**
   - Click "Loan Requests"
   - Enter interest rate to approve
   - Loan amount should be added to user balance

5. **Manage Withdrawals**
   - Click "Withdrawals"
   - Approve withdrawal (generates blockchain hash)
   - Amount deducted from user balance

6. **Manage Users**
   - Click "Manage Users"
   - View all registered users
   - Suspend/activate users

7. **Settings**
   - Click "Settings"
   - Adjust transfer fee percentage
   - Adjust withdrawal fee percentage
   - Set signup bonus amount

## Troubleshooting

### Common Issues

**1. "Page not found" Error**
```
Solution:
- Check that .htaccess file exists in ikiminta folder
- Enable mod_rewrite in Apache
- Clear browser cache and restart Apache
```

**2. "Database connection failed"**
```
Solution:
- Verify MySQL is running in XAMPP
- Check database name is "ikiminta"
- Verify credentials in config/database.php
- Ensure database is imported
```

**3. "Session not working"**
```
Solution:
- Check PHP session settings in php.ini
- Verify session.save_path has write permissions
- Clear browser cookies
- Restart PHP/Apache
```

**4. "File upload fails"**
```
Solution:
- Verify public/uploads/ folder permissions (755)
- Check max_upload_size in php.ini
- Ensure file format is allowed
```

**5. "Blank pages"**
```
Solution:
- Check application/config/config.php exists
- Verify all database tables are created
- Check PHP error logs
- Enable error reporting in config.php for development
```

## Performance Tips

1. **Optimize Database**
   ```sql
   -- Run these in phpMyAdmin
   ANALYZE TABLE users;
   OPTIMIZE TABLE users;
   -- Repeat for other tables
   ```

2. **Enable Query Caching**
   - Update MySQL my.ini:
   ```ini
   query_cache_type = 1
   query_cache_size = 32M
   ```

3. **Set Proper Indexes**
   - Already included in database.sql

## Security Checklist

- [ ] Change default admin password
- [ ] Update MOMO API credentials
- [ ] Set APP_ENV to 'production' when live
- [ ] Configure HTTPS
- [ ] Set proper file permissions (755 for dirs, 644 for files)
- [ ] Disable debug mode in production
- [ ] Set strong SESSION_TIMEOUT value
- [ ] Regular database backups

## Backup & Recovery

### Backup Database
```bash
mysqldump -u root ikiminta > ikiminta_backup.sql
```

### Restore Database
```bash
mysql -u root ikiminta < ikiminta_backup.sql
```

### Backup Files
- Copy entire `ikiminta` folder to safe location
- Include `database.sql` file

## Additional Resources

- [PHP Documentation](https://www.php.net/docs.php)
- [MySQL Documentation](https://dev.mysql.com/doc/)
- [HTML/CSS/JS References](https://developer.mozilla.org/)

## Support

For issues or questions:
1. Check README.md for general information
2. Review this installation guide
3. Check error logs in `logs/` directory
4. Verify all files are in correct locations

## Version Information

- **Application Version**: 1.0.0
- **PHP Required**: 7.4+
- **MySQL Required**: 5.7+
- **Last Updated**: January 26, 2025
