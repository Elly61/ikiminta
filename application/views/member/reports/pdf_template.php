<?php
/**
 * PDF Report Template
 */
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; }
        body { 
            font-family: 'Arial', sans-serif; 
            color: #333;
            line-height: 1.6;
        }
        .container { 
            max-width: 800px; 
            margin: 0 auto; 
            padding: 20px; 
        }
        .header { 
            border-bottom: 3px solid #667eea; 
            padding-bottom: 20px; 
            margin-bottom: 30px; 
            text-align: center;
        }
        .header h1 { 
            font-size: 28px; 
            color: #667eea;
            margin-bottom: 5px;
        }
        .header p { 
            color: #666; 
            font-size: 13px; 
        }
        .title { 
            font-size: 24px; 
            font-weight: bold; 
            margin-bottom: 10px; 
        }
        .date-range { 
            color: #666; 
            font-size: 12px; 
            margin-bottom: 20px;
        }
        .section { 
            margin-bottom: 30px; 
            page-break-inside: avoid;
        }
        .section-title { 
            font-size: 16px; 
            font-weight: bold; 
            border-bottom: 2px solid #667eea; 
            padding-bottom: 10px; 
            margin-bottom: 15px; 
            color: #333;
        }
        .user-info { 
            background-color: #f0f4ff; 
            padding: 15px; 
            border-radius: 5px; 
            margin-bottom: 20px; 
            border-left: 4px solid #667eea;
        }
        .user-info p { 
            margin: 5px 0; 
            font-size: 13px; 
        }
        .summary-boxes { 
            display: grid; 
            grid-template-columns: 1fr 1fr 1fr; 
            gap: 10px; 
            margin: 20px 0; 
        }
        .summary-box { 
            border: 1px solid #e0e0e0; 
            padding: 12px; 
            background-color: #f9f9f9; 
            border-radius: 5px;
        }
        .summary-box h4 { 
            margin: 0 0 8px 0; 
            color: #333; 
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .summary-box .value { 
            font-size: 16px; 
            font-weight: bold; 
            color: #667eea; 
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 10px; 
            font-size: 12px;
        }
        table thead { 
            background-color: #f8f9fa; 
        }
        table th { 
            border: 1px solid #ddd; 
            padding: 8px; 
            text-align: left; 
            font-weight: bold;
            color: #333;
        }
        table td { 
            border: 1px solid #e0e0e0; 
            padding: 8px; 
        }
        table tbody tr:nth-child(even) { 
            background-color: #f9f9f9; 
        }
        .footer { 
            margin-top: 40px; 
            padding-top: 20px; 
            border-top: 1px solid #ddd; 
            color: #666; 
            font-size: 11px; 
            text-align: center;
        }
        .page-break { 
            page-break-after: always; 
        }
        .no-data {
            text-align: center;
            padding: 20px;
            color: #999;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>IKIMINTA Financial System</h1>
            <p>Member Financial Report</p>
        </div>

        <!-- Title and Date Range -->
        <div class="section">
            <div class="title"><?php echo $type; ?> Report</div>
            <div class="date-range">
                Report Period: <?php echo date('M d, Y', strtotime($report['start_date'])); ?> - <?php echo date('M d, Y', strtotime($report['end_date'])); ?>
            </div>
        </div>

        <!-- User Information -->
        <?php if (isset($report['user'])): ?>
        <div class="user-info">
            <p><strong>Member ID:</strong> <?php echo htmlspecialchars($report['user']['id']); ?></p>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($report['user']['first_name'] . ' ' . $report['user']['last_name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($report['user']['email']); ?></p>
            <p><strong>Username:</strong> <?php echo htmlspecialchars($report['user']['username']); ?></p>
            <p><strong>Current Balance:</strong> RWF <?php echo number_format($report['user']['balance'], 2); ?></p>
        </div>
        <?php endif; ?>

        <!-- Summary Section -->
        <div class="section">
            <h3 class="section-title">Financial Summary</h3>
            <div class="summary-boxes">
                <div class="summary-box">
                    <h4>Total Deposits</h4>
                    <div class="value">RWF <?php echo number_format($report['summary']['total_deposits'], 2); ?></div>
                </div>
                <div class="summary-box">
                    <h4>Total Withdrawals</h4>
                    <div class="value">RWF <?php echo number_format($report['summary']['total_withdrawals'], 2); ?></div>
                </div>
                <div class="summary-box">
                    <h4>Transfers (Sent)</h4>
                    <div class="value">RWF <?php echo number_format($report['summary']['total_transfers_sent'], 2); ?></div>
                </div>
                <div class="summary-box">
                    <h4>Transfers (Received)</h4>
                    <div class="value">RWF <?php echo number_format($report['summary']['total_transfers_received'], 2); ?></div>
                </div>
                <div class="summary-box">
                    <h4>Total Loans</h4>
                    <div class="value">RWF <?php echo number_format($report['summary']['total_loans'], 2); ?></div>
                </div>
                <div class="summary-box">
                    <h4>Net Change</h4>
                    <div class="value">RWF <?php echo number_format($report['summary']['net_change'], 2); ?></div>
                </div>
            </div>
            <p style="margin-top: 15px; font-size: 12px; color: #666;">
                <strong>Total Transactions:</strong> <?php echo $report['summary']['transaction_count']; ?> | 
                <strong>Average Transaction:</strong> RWF <?php echo number_format($report['summary']['average_transaction'], 2); ?>
            </p>
        </div>

        <!-- Transactions Section -->
        <?php if (!empty($report['transactions'])): ?>
        <div class="section page-break">
            <h3 class="section-title">Recent Transactions</h3>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Amount (RWF)</th>
                        <th>Balance After (RWF)</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $txCount = 0;
                    foreach ($report['transactions'] as $transaction): 
                        if ($txCount >= 50) break;
                        $txCount++;
                    ?>
                    <tr>
                        <td><?php echo date('M d, Y H:i', strtotime($transaction['created_at'])); ?></td>
                        <td><?php echo ucfirst(str_replace('_', ' ', $transaction['transaction_type'])); ?></td>
                        <td><?php echo number_format($transaction['amount'], 2); ?></td>
                        <td><?php echo number_format($transaction['balance_after'], 2); ?></td>
                        <td><?php echo ucfirst($transaction['status']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        <!-- Deposits Section -->
        <?php if (!empty($report['deposits'])): ?>
        <div class="section page-break">
            <h3 class="section-title">Deposits</h3>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Amount (RWF)</th>
                        <th>Method</th>
                        <th>Reference</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($report['deposits'] as $deposit): ?>
                    <tr>
                        <td><?php echo date('M d, Y', strtotime($deposit['created_at'])); ?></td>
                        <td><?php echo number_format($deposit['amount'], 2); ?></td>
                        <td><?php echo ucfirst(str_replace('_', ' ', $deposit['payment_method'])); ?></td>
                        <td><?php echo substr($deposit['transaction_reference'], 0, 15); ?>...</td>
                        <td><?php echo ucfirst($deposit['status']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="section">
            <h3 class="section-title">Deposits</h3>
            <div class="no-data">No deposits found for this period</div>
        </div>
        <?php endif; ?>

        <!-- Loans Section -->
        <?php if (!empty($report['loans'])): ?>
        <div class="section page-break">
            <h3 class="section-title">Loans</h3>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Principal (RWF)</th>
                        <th>Interest (%)</th>
                        <th>Duration</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($report['loans'] as $loan): ?>
                    <tr>
                        <td><?php echo date('M d, Y', strtotime($loan['created_at'])); ?></td>
                        <td><?php echo number_format($loan['principal_amount'], 2); ?></td>
                        <td><?php echo $loan['interest_rate']; ?>%</td>
                        <td><?php echo $loan['duration_months']; ?> months</td>
                        <td><?php echo ucfirst($loan['status']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="section">
            <h3 class="section-title">Loans</h3>
            <div class="no-data">No loans found for this period</div>
        </div>
        <?php endif; ?>

        <!-- Savings Section -->
        <?php if (!empty($report['savings'])): ?>
        <div class="section page-break">
            <h3 class="section-title">Savings</h3>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Amount (RWF)</th>
                        <th>Interest (%)</th>
                        <th>Maturity Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($report['savings'] as $saving): ?>
                    <tr>
                        <td><?php echo date('M d, Y', strtotime($saving['created_at'])); ?></td>
                        <td><?php echo number_format($saving['amount'], 2); ?></td>
                        <td><?php echo $saving['interest_rate']; ?>%</td>
                        <td><?php echo date('M d, Y', strtotime($saving['maturity_date'])); ?></td>
                        <td><?php echo ucfirst($saving['status']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="section">
            <h3 class="section-title">Savings</h3>
            <div class="no-data">No savings found for this period</div>
        </div>
        <?php endif; ?>

        <!-- Footer -->
        <div class="footer">
            <p>Generated on: <?php echo date('M d, Y H:i:s'); ?></p>
            <p>IKIMINTA Financial System - Member Report</p>
            <p>This is a confidential document intended only for the member</p>
        </div>
    </div>
</body>
</html>
