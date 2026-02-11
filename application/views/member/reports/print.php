<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $reportType; ?> Financial Report - IKIMINA</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @media print {
            body {
                background: white;
                color: #333;
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                line-height: 1.5;
            }

            .no-print {
                display: none !important;
            }

            .page {
                page-break-after: always;
                padding: 20mm;
            }

            .page:last-child {
                page-break-after: avoid;
            }

            table {
                page-break-inside: avoid;
            }

            .section {
                page-break-inside: avoid;
            }
        }

        @media screen {
            body {
                background: #f5f5f5;
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                padding: 20px;
            }

            .print-container {
                background: white;
                max-width: 900px;
                margin: 0 auto;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            }

            .print-toolbar {
                padding: 15px;
                background: #667eea;
                color: white;
                display: flex;
                gap: 10px;
                align-items: center;
                justify-content: space-between;
            }

            .print-toolbar button {
                padding: 8px 16px;
                background: white;
                color: #667eea;
                border: none;
                border-radius: 4px;
                cursor: pointer;
                font-weight: 600;
                transition: all 0.3s ease;
            }

            .print-toolbar button:hover {
                background: #f0f0f0;
                transform: translateY(-2px);
            }

            .page {
                padding: 40px;
                min-height: 100vh;
            }
        }

        /* Report Styling */
        .report-header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #667eea;
            padding-bottom: 20px;
        }

        .report-header h1 {
            font-size: 28px;
            color: #333;
            margin-bottom: 5px;
        }

        .report-header .subtitle {
            color: #667eea;
            font-size: 18px;
            margin-bottom: 10px;
        }

        .report-header .period {
            color: #666;
            font-size: 13px;
        }

        .user-info {
            background: #f9f9f9;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin-bottom: 30px;
            border-radius: 4px;
        }

        .user-info p {
            margin: 5px 0;
            font-size: 13px;
        }

        .user-info strong {
            color: #333;
        }

        .section {
            margin-bottom: 30px;
        }

        .section-title {
            font-size: 16px;
            font-weight: 600;
            color: #333;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }

        .summary-box {
            background: #f9f9f9;
            border: 1px solid #e0e0e0;
            padding: 15px;
            border-radius: 4px;
            border-left: 4px solid #667eea;
        }

        .summary-box h4 {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 8px;
            letter-spacing: 0.5px;
        }

        .summary-box .value {
            font-size: 18px;
            font-weight: 700;
            color: #667eea;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 12px;
        }

        table thead {
            background: #f0f0f0;
            border-bottom: 2px solid #667eea;
        }

        table th {
            padding: 10px;
            text-align: left;
            font-weight: 600;
            color: #333;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.5px;
        }

        table td {
            padding: 10px;
            border-bottom: 1px solid #e0e0e0;
        }

        table tbody tr:nth-child(even) {
            background: #f9f9f9;
        }

        table tbody tr:hover {
            background: #f0f0f0;
        }

        .no-data {
            text-align: center;
            padding: 30px;
            color: #999;
            font-style: italic;
        }

        .report-footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
            text-align: center;
            color: #666;
            font-size: 11px;
        }

        .footer-text {
            margin: 5px 0;
        }

        @media (max-width: 768px) {
            .summary-grid {
                grid-template-columns: 1fr;
            }

            .page {
                padding: 20px;
            }

            table {
                font-size: 11px;
            }

            table th,
            table td {
                padding: 8px;
            }
        }
    </style>
</head>
<body>
    <div class="print-container">
        <div class="no-print print-toolbar">
            <div style="font-weight: 600;">Financial Report - <?php echo $reportType; ?></div>
            <div style="display: flex; gap: 10px;">
                <button onclick="window.print()">🖨️ Print to PDF</button>
                <button onclick="window.location.href='<?php echo BASE_URL; ?>member/dashboard'">✕ Back to Dashboard</button>
            </div>
        </div>

        <div class="page">
            <!-- Header -->
            <div class="report-header">
                <div class="subtitle">IKIMINTA Financial System</div>
                <h1><?php echo $reportType; ?> Financial Report</h1>
                <div class="period">
                    Report Period: <?php echo date('M d, Y', strtotime($report['start_date'])); ?> - <?php echo date('M d, Y', strtotime($report['end_date'])); ?>
                </div>
            </div>

            <!-- User Information -->
            <div class="user-info">
                <p><strong>Member Name:</strong> <?php echo htmlspecialchars($report['user']['first_name'] . ' ' . $report['user']['last_name']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($report['user']['email']); ?></p>
                <p><strong>Username:</strong> <?php echo htmlspecialchars($report['user']['username']); ?></p>
                <p><strong>Current Balance:</strong> RWF <?php echo number_format($report['user']['balance'], 2); ?></p>
            </div>

            <!-- Summary Section -->
            <div class="section">
                <h3 class="section-title">Financial Summary</h3>
                <div class="summary-grid">
                    <div class="summary-box">
                        <h4>Total Deposits</h4>
                        <div class="value">RWF <?php echo number_format($report['summary']['total_deposits'], 2); ?></div>
                    </div>
                    <div class="summary-box">
                        <h4>Total Withdrawals</h4>
                        <div class="value">RWF <?php echo number_format($report['summary']['total_withdrawals'], 2); ?></div>
                    </div>
                    <div class="summary-box">
                        <h4>Total Transfers</h4>
                        <div class="value">RWF <?php echo number_format($report['summary']['total_transfers_sent'] + $report['summary']['total_transfers_received'], 2); ?></div>
                    </div>
                    <div class="summary-box">
                        <h4>Transfers Sent</h4>
                        <div class="value">RWF <?php echo number_format($report['summary']['total_transfers_sent'], 2); ?></div>
                    </div>
                    <div class="summary-box">
                        <h4>Transfers Received</h4>
                        <div class="value">RWF <?php echo number_format($report['summary']['total_transfers_received'], 2); ?></div>
                    </div>
                    <div class="summary-box">
                        <h4>Net Change</h4>
                        <div class="value" style="color: <?php echo $report['summary']['net_change'] >= 0 ? '#10b981' : '#ef4444'; ?>;">
                            RWF <?php echo number_format($report['summary']['net_change'], 2); ?>
                        </div>
                    </div>
                </div>
                <p style="margin-top: 10px; font-size: 12px; color: #666;">
                    <strong>Total Transactions:</strong> <?php echo $report['summary']['transaction_count']; ?> | 
                    <strong>Average Transaction:</strong> RWF <?php echo number_format($report['summary']['average_transaction'], 2); ?>
                </p>
            </div>

            <!-- Loans Summary -->
            <div class="section">
                <h3 class="section-title">Loan Summary</h3>
                <div class="summary-grid">
                    <div class="summary-box">
                        <h4>Total Loans</h4>
                        <div class="value">RWF <?php echo number_format($report['summary']['total_loans'], 2); ?></div>
                    </div>
                    <div class="summary-box">
                        <h4>Loan Payments</h4>
                        <div class="value">RWF <?php echo number_format($report['summary']['total_loan_payments'], 2); ?></div>
                    </div>
                    <div class="summary-box">
                        <h4>Total Savings</h4>
                        <div class="value">RWF <?php echo number_format($report['summary']['total_savings'], 2); ?></div>
                    </div>
                </div>
            </div>

            <!-- Recent Transactions -->
            <?php if (!empty($report['transactions'])): ?>
            <div class="section">
                <h3 class="section-title">Recent Transactions</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th style="text-align: right;">Amount (RWF)</th>
                            <th style="text-align: right;">Balance After (RWF)</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $txCount = 0;
                        foreach ($report['transactions'] as $transaction): 
                            if ($txCount >= 30) break;
                            $txCount++;
                        ?>
                        <tr>
                            <td><?php echo date('M d, Y H:i', strtotime($transaction['created_at'])); ?></td>
                            <td><?php echo ucfirst(str_replace('_', ' ', $transaction['transaction_type'])); ?></td>
                            <td style="text-align: right;"><?php echo number_format($transaction['amount'], 2); ?></td>
                            <td style="text-align: right;"><?php echo number_format($transaction['balance_after'], 2); ?></td>
                            <td><?php echo ucfirst($transaction['status']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>

            <!-- Deposits -->
            <?php if (!empty($report['deposits'])): ?>
            <div class="section">
                <h3 class="section-title">Deposits</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th style="text-align: right;">Amount (RWF)</th>
                            <th>Method</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($report['deposits'] as $deposit): ?>
                        <tr>
                            <td><?php echo date('M d, Y', strtotime($deposit['created_at'])); ?></td>
                            <td style="text-align: right;"><?php echo number_format($deposit['amount'], 2); ?></td>
                            <td><?php echo ucfirst(str_replace('_', ' ', $deposit['payment_method'])); ?></td>
                            <td><?php echo ucfirst($deposit['status']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>

            <!-- Loans -->
            <?php if (!empty($report['loans'])): ?>
            <div class="section">
                <h3 class="section-title">Loans</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th style="text-align: right;">Principal (RWF)</th>
                            <th>Interest (%)</th>
                            <th>Duration</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($report['loans'] as $loan): ?>
                        <tr>
                            <td><?php echo date('M d, Y', strtotime($loan['created_at'])); ?></td>
                            <td style="text-align: right;"><?php echo number_format($loan['principal_amount'], 2); ?></td>
                            <td><?php echo $loan['interest_rate']; ?>%</td>
                            <td><?php echo $loan['duration_months']; ?> months</td>
                            <td><?php echo ucfirst($loan['status']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>

            <!-- Savings -->
            <?php if (!empty($report['savings'])): ?>
            <div class="section">
                <h3 class="section-title">Savings</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th style="text-align: right;">Amount (RWF)</th>
                            <th>Interest (%)</th>
                            <th>Maturity Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($report['savings'] as $saving): ?>
                        <tr>
                            <td><?php echo date('M d, Y', strtotime($saving['created_at'])); ?></td>
                            <td style="text-align: right;"><?php echo number_format($saving['amount'], 2); ?></td>
                            <td><?php echo $saving['interest_rate']; ?>%</td>
                            <td><?php echo date('M d, Y', strtotime($saving['maturity_date'])); ?></td>
                            <td><?php echo ucfirst($saving['status']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>

            <!-- Footer -->
            <div class="report-footer">
                <div class="footer-text">Generated on: <?php echo date('M d, Y H:i:s'); ?></div>
                <div class="footer-text">IKIMINTA Financial System - Confidential Member Report</div>
                <div class="footer-text">This document is intended only for the member and should be kept confidential.</div>
            </div>
        </div>
    </div>

    <script>
        // Auto-focus print dialog on load
        window.addEventListener('load', function() {
            // Uncomment below if you want auto-print (optional)
            // window.print();
        });

        // Handle print start
        window.addEventListener('beforeprint', function() {
            // Hide toolbar when printing
            document.querySelector('.print-toolbar').style.display = 'none';
        });

        // Handle print end
        window.addEventListener('afterprint', function() {
            // Show toolbar after printing
            document.querySelector('.print-toolbar').style.display = 'flex';
        });
    </script>
</body>
</html>
