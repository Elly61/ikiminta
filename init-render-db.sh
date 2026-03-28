#!/bin/bash
# Initialize Render PostgreSQL Database with IKIMINTA Schema

RENDER_DB_URL="postgresql://ikimina_db_49wr_user:Dfzd9G7gq207xC7FPfWxQag6jJUdcelx@dpg-d743c7dm5p6s73f1ffq0-a.oregon-postgres.render.com/ikimina_db_49wr"

echo "=== INITIALIZING RENDER DATABASE ==="
echo "Database: ikimina_db_49wr"
echo ""

# Check if psql is installed
if ! command -v psql &> /dev/null; then
    echo "✗ ERROR: psql is not installed"
    echo "Please install PostgreSQL client tools and try again"
    exit 1
fi

echo "Connecting to Render database..."
echo ""

# Run the schema SQL file
psql "$RENDER_DB_URL" << 'EOSQL'
-- IKIMINTA PostgreSQL Schema for Render

-- Table: users
CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    legal_id VARCHAR(16) NOT NULL UNIQUE,
    phone_number VARCHAR(20) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    user_type VARCHAR(10) DEFAULT 'member' CHECK (user_type IN ('member', 'super')),
    balance DECIMAL(15,2) DEFAULT 0.00,
    status VARCHAR(10) DEFAULT 'active' CHECK (status IN ('active', 'inactive', 'suspended')),
    profile_picture VARCHAR(255) DEFAULT NULL,
    date_created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL DEFAULT NULL
);

-- Table: settings
CREATE TABLE IF NOT EXISTS settings (
    id SERIAL PRIMARY KEY,
    transfer_fee DECIMAL(5,2) DEFAULT 2.50,
    withdraw_fee DECIMAL(5,2) DEFAULT 2.50,
    signup_bonus DECIMAL(15,2) DEFAULT 50.00,
    loan_interest_rate DECIMAL(5,2) DEFAULT 1.50,
    savings_interest_rate DECIMAL(5,2) DEFAULT 1.50,
    updated_by INTEGER DEFAULT NULL REFERENCES users(id),
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Insert default settings
INSERT INTO settings (signup_bonus) VALUES (50.00) ON CONFLICT DO NOTHING;

-- Table: deposits
CREATE TABLE IF NOT EXISTS deposits (
    id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL REFERENCES users(id),
    amount DECIMAL(15,2) NOT NULL,
    payment_method VARCHAR(20) NOT NULL CHECK (payment_method IN ('credit_card', 'momo')),
    transaction_reference VARCHAR(255) UNIQUE DEFAULT NULL,
    status VARCHAR(15) DEFAULT 'pending' CHECK (status IN ('pending', 'completed', 'failed', 'cancelled')),
    description TEXT DEFAULT NULL,
    proof VARCHAR(500) DEFAULT NULL,
    proof_payment VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    completed_at TIMESTAMP NULL DEFAULT NULL
);

-- Table: loan_requests
CREATE TABLE IF NOT EXISTS loan_requests (
    id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL REFERENCES users(id),
    amount_requested DECIMAL(15,2) NOT NULL,
    loan_purpose VARCHAR(255) DEFAULT NULL,
    status VARCHAR(15) DEFAULT 'pending' CHECK (status IN ('pending', 'approved', 'rejected', 'disbursed', 'completed')),
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    reviewed_at TIMESTAMP NULL DEFAULT NULL,
    reviewed_by INTEGER DEFAULT NULL REFERENCES users(id)
);

-- Table: loans
CREATE TABLE IF NOT EXISTS loans (
    id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL REFERENCES users(id),
    loan_request_id INTEGER DEFAULT NULL REFERENCES loan_requests(id),
    amount DECIMAL(15,2) NOT NULL,
    interest_rate DECIMAL(5,2) NOT NULL,
    monthly_payment DECIMAL(15,2) NOT NULL,
    total_amount DECIMAL(15,2) NOT NULL,
    remaining_balance DECIMAL(15,2) NOT NULL,
    total_paid DECIMAL(15,2) DEFAULT 0.00,
    status VARCHAR(15) DEFAULT 'active' CHECK (status IN ('active', 'completed', 'defaulted')),
    start_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    end_date TIMESTAMP DEFAULT NULL,
    last_payment_date TIMESTAMP DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Table: loan_payments
CREATE TABLE IF NOT EXISTS loan_payments (
    id SERIAL PRIMARY KEY,
    loan_id INTEGER NOT NULL REFERENCES loans(id),
    amount DECIMAL(15,2) NOT NULL,
    payment_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(15) DEFAULT 'completed' CHECK (status IN ('pending', 'completed', 'failed')),
    description TEXT DEFAULT NULL
);

-- Table: transactions
CREATE TABLE IF NOT EXISTS transactions (
    id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL REFERENCES users(id),
    transaction_type VARCHAR(20) NOT NULL CHECK (transaction_type IN ('deposit', 'withdrawal', 'transfer', 'loan', 'payment', 'interest')),
    amount DECIMAL(15,2) NOT NULL,
    description TEXT DEFAULT NULL,
    status VARCHAR(15) DEFAULT 'completed' CHECK (status IN ('pending', 'completed', 'failed', 'cancelled')),
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Table: savings
CREATE TABLE IF NOT EXISTS savings (
    id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL REFERENCES users(id),
    amount DECIMAL(15,2) NOT NULL,
    interest_rate DECIMAL(5,2) NOT NULL,
    status VARCHAR(15) DEFAULT 'active' CHECK (status IN ('active', 'closed')),
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    closed_at TIMESTAMP DEFAULT NULL
);

-- Table: transfers
CREATE TABLE IF NOT EXISTS transfers (
    id SERIAL PRIMARY KEY,
    sender_id INTEGER NOT NULL REFERENCES users(id),
    receiver_id INTEGER NOT NULL REFERENCES users(id),
    amount DECIMAL(15,2) NOT NULL,
    fee DECIMAL(5,2) DEFAULT 2.50,
    description TEXT DEFAULT NULL,
    status VARCHAR(15) DEFAULT 'completed' CHECK (status IN ('pending', 'completed', 'failed', 'cancelled')),
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    completed_at TIMESTAMP DEFAULT NULL
);

-- Table: withdrawals
CREATE TABLE IF NOT EXISTS withdrawals (
    id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL REFERENCES users(id),
    amount DECIMAL(15,2) NOT NULL,
    method VARCHAR(20) NOT NULL CHECK (method IN ('bank_transfer', 'momo', 'cash')),
    account_number VARCHAR(100) DEFAULT NULL,
    status VARCHAR(15) DEFAULT 'pending' CHECK (status IN ('pending', 'completed', 'failed', 'cancelled')),
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    completed_at TIMESTAMP DEFAULT NULL
);

-- Table: reports
CREATE TABLE IF NOT EXISTS reports (
    id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL REFERENCES users(id),
    report_type VARCHAR(20) NOT NULL,
    description TEXT DEFAULT NULL,
    data JSON DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Table: admin_settings
CREATE TABLE IF NOT EXISTS admin_settings (
    id SERIAL PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT NOT NULL,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Table: audit_logs
CREATE TABLE IF NOT EXISTS audit_logs (
    id SERIAL PRIMARY KEY,
    user_id INTEGER DEFAULT NULL REFERENCES users(id),
    action VARCHAR(100) NOT NULL,
    entity_type VARCHAR(50) DEFAULT NULL,
    entity_id INTEGER DEFAULT NULL,
    details TEXT DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Create indexes for better performance
CREATE INDEX IF NOT EXISTS idx_users_email ON users(email);
CREATE INDEX IF NOT EXISTS idx_users_legal_id ON users(legal_id);
CREATE INDEX IF NOT EXISTS idx_users_status ON users(status);
CREATE INDEX IF NOT EXISTS idx_deposits_user_id ON deposits(user_id);
CREATE INDEX IF NOT EXISTS idx_loans_user_id ON loans(user_id);
CREATE INDEX IF NOT EXISTS idx_transactions_user_id ON transactions(user_id);
CREATE INDEX IF NOT EXISTS idx_transfers_sender ON transfers(sender_id);
CREATE INDEX IF NOT EXISTS idx_transfers_receiver ON transfers(receiver_id);
CREATE INDEX IF NOT EXISTS idx_withdrawals_user_id ON withdrawals(user_id);

\dt
\echo 'Tables created successfully!'
EOSQL

echo ""
echo "✓ Database initialization complete!"
