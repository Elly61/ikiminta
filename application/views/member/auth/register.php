<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Registration - IKIMINA</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/auth.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .auth-container {
            width: 100%;
            max-width: 600px;
        }

        .auth-box {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            overflow: hidden;
        }

        .auth-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px;
            text-align: center;
        }

        .auth-header h1 {
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 8px;
            letter-spacing: 1px;
        }

        .auth-header p {
            font-size: 15px;
            opacity: 0.95;
            font-weight: 300;
        }

        .auth-form {
            padding: 40px;
        }

        .auth-form h2 {
            font-size: 26px;
            color: #333;
            margin-bottom: 28px;
            font-weight: 600;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
            font-family: inherit;
        }

        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            background-color: #f8f9ff;
        }

        .form-group input::placeholder {
            color: #aaa;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .btn-primary {
            width: 100%;
            padding: 14px 24px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 10px;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .auth-footer {
            text-align: center;
            border-top: 1px solid #f0f0f0;
            padding-top: 20px;
            margin-top: 20px;
        }

        .auth-footer p {
            font-size: 14px;
            color: #666;
        }

        .auth-footer a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .auth-footer a:hover {
            color: #764ba2;
            text-decoration: underline;
        }

        .info-box {
            background: #f0f4ff;
            border-left: 4px solid #667eea;
            padding: 14px 16px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 13px;
            color: #333;
            line-height: 1.6;
        }

        .info-box strong {
            color: #667eea;
        }

        @media (max-width: 768px) {
            .auth-form {
                padding: 30px 20px;
            }

            .auth-header {
                padding: 30px 20px;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .auth-form h2 {
                font-size: 22px;
            }
        }
    </style>
</head>
<body>
<div class="auth-container">
    <div class="auth-box">
        <div class="auth-header">
            <h1>IKIMINTA</h1>
            <p>Join Our Financial Community</p>
        </div>

        <div class="auth-form">
            <h2>Create Your Account</h2>
            
            <div class="info-box">
                <strong>ⓘ Account Setup:</strong> Fill in your details to create your account. All fields are required for compliance.
            </div>

            <form id="registerForm" method="POST" action="<?php echo BASE_URL; ?>member/auth/register">
                <div class="form-group">
                    <label for="username">👤 Username</label>
                    <input type="text" id="username" name="username" required placeholder="Choose a unique username">
                </div>

                <div class="form-group">
                    <label for="email">📧 Email Address</label>
                    <input type="email" id="email" name="email" required placeholder="your@email.com">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name">First Name</label>
                        <input type="text" id="first_name" name="first_name" required placeholder="John">
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last Name</label>
                        <input type="text" id="last_name" name="last_name" required placeholder="Doe">
                    </div>
                </div>

                <div class="form-group">
                    <label for="legal_id">🆔 Legal ID (16 characters)</label>
                    <input type="text" id="legal_id" name="legal_id" required placeholder="Your national ID" maxlength="16">
                </div>

                <div class="form-group">
                    <label for="phone_number">📱 Phone Number</label>
                    <input type="tel" id="phone_number" name="phone_number" required placeholder="+250 700 000 000">
                </div>

                <div class="form-group">
                    <label for="password">🔐 Password (Minimum 8 characters)</label>
                    <input type="password" id="password" name="password" required placeholder="Create a strong password">
                </div>

                <div class="form-group">
                    <label for="confirm_password">🔐 Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required placeholder="Repeat your password">
                </div>

                <button type="submit" class="btn-primary">Create Account</button>
            </form>

            <div class="auth-footer">
                <p>Already have an account? <a href="<?php echo BASE_URL; ?>member/auth/login">Sign in here</a></p>
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
