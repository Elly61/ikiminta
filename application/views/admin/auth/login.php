<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - IKIMINA Financial Management System</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/auth.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #0f172a;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow-x: hidden;
            overflow-y: auto;
        }

        body::before {
            content: '';
            position: absolute;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, #f59e0b 0%, transparent 70%);
            top: -250px;
            right: -150px;
            opacity: 0.25;
            animation: pulse 8s ease-in-out infinite;
        }

        body::after {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, #ef4444 0%, transparent 70%);
            bottom: -200px;
            left: -100px;
            opacity: 0.25;
            animation: pulse 8s ease-in-out infinite reverse;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.25; }
            50% { transform: scale(1.1); opacity: 0.4; }
        }

        .auth-container {
            width: 100%;
            max-width: 450px;
            position: relative;
            z-index: 1;
            animation: slideUp 0.8s cubic-bezier(0.16, 1, 0.3, 1);
            margin: auto;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .auth-box {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            overflow: visible;
            box-shadow: 0 30px 90px rgba(0, 0, 0, 0.5),
                        0 0 0 1px rgba(255, 255, 255, 0.1);
            max-height: 95vh;
            overflow-y: auto;
        }

        .brand-section {
            background: linear-gradient(135deg, #f59e0b 0%, #ef4444 100%);
            padding: 40px 35px 35px;
            text-align: center;
            position: relative;
            overflow: hidden;
            border-radius: 24px 24px 0 0;
        }

        .brand-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><circle cx="50" cy="50" r="1" fill="white" opacity="0.1"/></svg>');
            opacity: 0.4;
        }

        .logo-container {
            position: relative;
            z-index: 1;
            margin-bottom: 15px;
        }

        .logo-icon {
            width: 70px;
            height: 70px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            color: white;
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.3);
            margin-bottom: 15px;
        }

        .brand-section h1 {
            font-size: 34px;
            font-weight: 800;
            color: white;
            letter-spacing: 1px;
            margin-bottom: 8px;
            position: relative;
            z-index: 1;
        }

        .brand-section p {
            color: rgba(255, 255, 255, 0.95);
            font-size: 14px;
            font-weight: 400;
            position: relative;
            z-index: 1;
        }

        .security-badge {
            background: rgba(255, 255, 255, 0.15);
            padding: 10px 16px;
            border-radius: 10px;
            margin-top: 15px;
            font-size: 12px;
            color: white;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            position: relative;
            z-index: 1;
            backdrop-filter: blur(10px);
        }

        .auth-form {
            padding: 45px 40px;
            background: white;
        }

        .form-header {
            margin-bottom: 35px;
        }

        .form-header h2 {
            font-size: 26px;
            color: #0f172a;
            margin-bottom: 8px;
            font-weight: 700;
        }

        .form-header p {
            color: #64748b;
            font-size: 14px;
            font-weight: 400;
        }

        .form-group {
            margin-bottom: 22px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 10px;
            letter-spacing: 0.3px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 18px;
            transition: color 0.3s ease;
        }

        .form-group input {
            width: 100%;
            padding: 15px 18px 15px 50px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 15px;
            transition: all 0.3s ease;
            font-family: inherit;
            background-color: #f8fafc;
            color: #0f172a;
            font-weight: 500;
        }

        .form-group input:focus {
            outline: none;
            border-color: #f59e0b;
            background-color: #ffffff;
            box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.1);
        }

        .form-group input:focus ~ .input-icon {
            color: #f59e0b;
        }

        .form-group input::placeholder {
            color: #94a3b8;
            font-weight: 400;
        }

        .forgot-password {
            text-align: right;
            margin-bottom: 25px;
        }

        .forgot-password a {
            color: #f59e0b;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .forgot-password a:hover {
            color: #d97706;
        }

        .btn-primary {
            width: 100%;
            padding: 16px 24px;
            background: linear-gradient(135deg, #f59e0b 0%, #ef4444 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            letter-spacing: 0.5px;
            box-shadow: 0 10px 30px rgba(245, 158, 11, 0.3);
            position: relative;
            overflow: hidden;
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }

        .btn-primary:hover::before {
            left: 100%;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 40px rgba(245, 158, 11, 0.4);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .btn-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .divider {
            display: flex;
            align-items: center;
            margin: 30px 0 25px;
            color: #94a3b8;
            font-size: 13px;
            font-weight: 500;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e2e8f0;
        }

        .divider span {
            padding: 0 15px;
        }

        .auth-footer {
            text-align: center;
        }

        .auth-footer p {
            color: #64748b;
            font-size: 14px;
            margin-bottom: 12px;
        }

        .auth-footer a {
            color: #f59e0b;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .auth-footer a:hover {
            color: #d97706;
            text-decoration: underline;
        }

        .member-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 10px 20px;
            background: #f1f5f9;
            border-radius: 8px;
            color: #475569;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-top: 8px;
        }

        .member-link:hover {
            background: #e2e8f0;
            color: #334155;
            text-decoration: none;
        }

        /* Tablet Landscape & Small Desktops */
        @media (max-width: 1024px) {
            .auth-container {
                max-width: 420px;
            }
        }

        /* Tablet Portrait */
        @media (max-width: 768px) {
            body {
                padding: 15px;
            }

            .auth-container {
                max-width: 100%;
            }

            .brand-section {
                padding: 35px 30px 30px;
            }

            .logo-icon {
                width: 65px;
                height: 65px;
                font-size: 32px;
            }

            .brand-section h1 {
                font-size: 30px;
            }

            .brand-section p {
                font-size: 13px;
            }

            .auth-form {
                padding: 40px 35px;
            }

            .form-header h2 {
                font-size: 24px;
            }

            .form-group input {
                padding: 14px 16px 14px 48px;
                font-size: 14px;
            }

            .input-icon {
                left: 14px;
                font-size: 16px;
            }

            .btn-primary {
                padding: 15px 20px;
                font-size: 15px;
            }
        }

        /* Mobile Landscape */
        @media (max-width: 640px) {
            body::before {
                width: 350px;
                height: 350px;
                top: -180px;
                right: -100px;
            }

            body::after {
                width: 300px;
                height: 300px;
                bottom: -150px;
                left: -80px;
            }

            .auth-form {
                padding: 35px 28px;
            }

            .brand-section {
                padding: 30px 25px 25px;
            }

            .divider {
                margin: 25px 0 20px;
            }
        }

        /* Mobile Portrait */
        @media (max-width: 480px) {
            body {
                padding: 10px;
                align-items: flex-start;
                padding-top: 20px;
                padding-bottom: 20px;
            }

            .auth-container {
                max-width: 100%;
                margin: 0 auto;
            }

            .auth-box {
                border-radius: 20px;
                max-height: none;
            }

            .brand-section {
                padding: 28px 22px 24px;
            }

            .logo-icon {
                width: 60px;
                height: 60px;
                font-size: 28px;
                border-radius: 16px;
                margin-bottom: 12px;
            }

            .brand-section h1 {
                font-size: 26px;
                letter-spacing: 0.5px;
            }

            .brand-section p {
                font-size: 12px;
            }

            .security-badge {
                font-size: 11px;
                padding: 8px 14px;
            }

            .auth-form {
                padding: 32px 24px;
            }

            .form-header {
                margin-bottom: 28px;
            }

            .form-header h2 {
                font-size: 22px;
            }

            .form-header p {
                font-size: 13px;
            }

            .form-group {
                margin-bottom: 20px;
            }

            .form-group label {
                font-size: 12px;
                margin-bottom: 8px;
            }

            .form-group input {
                padding: 13px 15px 13px 45px;
                font-size: 14px;
                border-radius: 10px;
            }

            .input-icon {
                left: 13px;
                font-size: 15px;
            }

            .forgot-password {
                margin-bottom: 20px;
            }

            .forgot-password a {
                font-size: 12px;
            }

            .btn-primary {
                padding: 14px 20px;
                font-size: 14px;
                border-radius: 10px;
            }

            .divider {
                margin: 22px 0 18px;
                font-size: 12px;
            }

            .auth-footer p {
                font-size: 13px;
                margin-bottom: 10px;
            }

            .member-link {
                font-size: 12px;
                padding: 9px 18px;
            }
        }

        /* Small Mobile Devices */
        @media (max-width: 375px) {
            body {
                padding: 8px;
            }

            .brand-section h1 {
                font-size: 24px;
            }

            .logo-icon {
                width: 55px;
                height: 55px;
                font-size: 26px;
            }

            .auth-form {
                padding: 28px 20px;
            }

            .form-header h2 {
                font-size: 20px;
            }

            .form-group input {
                padding: 12px 14px 12px 42px;
                font-size: 13px;
            }

            .input-icon {
                font-size: 14px;
            }

            .btn-primary {
                padding: 13px 18px;
                font-size: 13px;
            }

            .auth-footer p,
            .member-link {
                font-size: 11px;
            }
        }

        /* Landscape Orientation Fix */
        @media (max-height: 600px) and (orientation: landscape) {
            body {
                padding: 10px;
                align-items: flex-start;
                min-height: auto;
            }

            .auth-container {
                margin: 10px auto;
            }

            .auth-box {
                max-height: none;
            }

            .brand-section {
                padding: 25px 30px 20px;
            }

            .logo-icon {
                width: 50px;
                height: 50px;
                font-size: 24px;
                margin-bottom: 8px;
            }

            .brand-section h1 {
                font-size: 22px;
                margin-bottom: 4px;
            }

            .brand-section p {
                font-size: 11px;
            }

            .security-badge {
                font-size: 10px;
                padding: 6px 12px;
                margin-top: 10px;
            }

            .auth-form {
                padding: 25px 30px;
            }

            .form-header {
                margin-bottom: 20px;
            }

            .form-header h2 {
                font-size: 20px;
                margin-bottom: 4px;
            }

            .form-header p {
                font-size: 12px;
            }

            .form-group {
                margin-bottom: 16px;
            }

            .forgot-password {
                margin-bottom: 16px;
            }

            .divider {
                margin: 20px 0 16px;
            }

            body::before,
            body::after {
                display: none;
            }
        }
    </style>
</head>
<body>
<div class="auth-container">
    <div class="auth-box">
        <div class="brand-section">
            <div class="logo-container">
                <div class="logo-icon">
                    <i class="fas fa-shield-halved"></i>
                </div>
                <h1>IKIMINA</h1>
                <p>Administrator Portal</p>
            </div>
            <div class="security-badge">
                <i class="fas fa-lock"></i> Secure Admin Access
            </div>
        </div>

        <div class="auth-form">
            <div class="form-header">
                <h2>Admin Login</h2>
                <p>Access administrative dashboard</p>
            </div>

            <form id="adminLoginForm" method="POST" action="<?php echo BASE_URL; ?>admin/auth/login">
                <div class="form-group">
                    <label for="email">Admin Email Address</label>
                    <div class="input-wrapper">
                        <input type="email" id="email" name="email" required placeholder="Enter admin email">
                        <i class="fas fa-envelope input-icon"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrapper">
                        <input type="password" id="password" name="password" required placeholder="Enter password">
                        <i class="fas fa-lock input-icon"></i>
                    </div>
                </div>

                <div class="forgot-password">
                    <a href="#">Forgot Password?</a>
                </div>

                <button type="submit" class="btn-primary">Access Admin Panel</button>
            </form>

            <div class="divider">
                <span>OR</span>
            </div>

            <div class="auth-footer">
                <p>Not an administrator?</p>
                <a href="<?php echo BASE_URL; ?>member/auth/login" class="member-link">
                    <i class="fas fa-user"></i> Member Login
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Company Branding -->
<div class="company-footer">
    <p><?php echo COMPANY_POWERED_BY; ?></p>
    <p>CEO: <?php echo COMPANY_CEO; ?> | Founder: <?php echo COMPANY_FOUNDER; ?></p>
    <p>&copy; <?php echo COMPANY_YEAR; ?> <?php echo COMPANY_NAME; ?>. All rights reserved.</p>
</div>

<script src="<?php echo BASE_URL; ?>public/js/auth.js"></script>
</body>
</html>
