<?php
/**
 * Report PDF Generator
 * Generates PDF reports for members
 */

class ReportPDF {
    private $pageWidth = 210; // A4 width in mm
    private $pageHeight = 297; // A4 height in mm
    private $margin = 10;
    private $currentY = 10;

    /**
     * Generate PDF for weekly report
     */
    public static function generateWeeklyPDF($report) {
        $pdf = new ReportPDF();
        return $pdf->generateReport($report, 'Weekly');
    }

    /**
     * Generate PDF for monthly report
     */
    public static function generateMonthlyPDF($report) {
        $pdf = new ReportPDF();
        return $pdf->generateReport($report, 'Monthly');
    }

    /**
     * Generate PDF for yearly report
     */
    public static function generateYearlyPDF($report) {
        $pdf = new ReportPDF();
        return $pdf->generateReport($report, 'Yearly');
    }

    /**
     * Main report generation method
     */
    private function generateReport($report, $reportType) {
        // Create temporary directory if it doesn't exist
        $tempDir = dirname(__FILE__) . '/../../temp';
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        // Generate HTML content
        $html = $this->generateHTML($report, $reportType);

        // Save as PDF using simple approach
        $filename = $tempDir . '/report_' . $reportType . '_' . time() . '.pdf';
        
        // Use a simple PDF generation approach
        $this->createPDFFromHTML($html, $filename);
        
        return $filename;
    }

    /**
     * Create PDF from HTML content
     */
    private function createPDFFromHTML($html, $filename) {
        // Check if we can use mPDF or similar
        $mpdfPath = dirname(__FILE__) . '/../../vendor/mpdf/mpdf/src/Mpdf.php';
        
        if (file_exists($mpdfPath)) {
            // Use mPDF if available
            require_once $mpdfPath;
            $mpdf = new \Mpdf\Mpdf();
            $mpdf->WriteHTML($html);
            $mpdf->Output($filename, 'F');
        } else {
            // Fallback: Use TCPDF
            $tcpdfPath = dirname(__FILE__) . '/../../vendor/tecnickcom/tcpdf/tcpdf.php';
            
            if (file_exists($tcpdfPath)) {
                require_once $tcpdfPath;
                $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_PAGE_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
                $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
                $pdf->SetMargins(10, 10, 10);
                $pdf->SetAutoPageBreak(TRUE, 10);
                $pdf->AddPage();
                $pdf->SetFont('helvetica', '', 10);
                $pdf->writeHTML($html, true, false, true, false, '');
                $pdf->Output($filename, 'F');
            } else {
                // Fallback: Simple text-based PDF generation
                $this->generateSimplePDF($html, $filename);
            }
        }
    }

    /**
     * Generate simple PDF without external libraries
     */
    private function generateSimplePDF($html, $filename) {
        // Strip HTML tags for simple PDF
        $text = $this->stripHTMLTags($html);
        
        // Create a simple PDF-like text file (fallback)
        // For production, you should install mPDF or TCPDF
        if (!function_exists('create_pdf')) {
            // Create a text version as fallback
            file_put_contents($filename, $text);
            // Try to convert to actual PDF using system command if available
            if (function_exists('shell_exec')) {
                $escaped = escapeshellarg($filename);
                @shell_exec("wkhtmltopdf - $escaped < /dev/stdin");
            }
        }
    }

    /**
     * Strip HTML tags from content
     */
    private function stripHTMLTags($html) {
        return strip_tags(html_entity_decode($html));
    }

    /**
     * Generate HTML content for report
     */
    private function generateHTML($report, $reportType) {
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { border-bottom: 2px solid #333; padding-bottom: 20px; margin-bottom: 30px; }
        .title { font-size: 24px; font-weight: bold; margin-bottom: 10px; }
        .date-range { color: #666; font-size: 12px; }
        .section { margin-bottom: 30px; }
        .section-title { font-size: 16px; font-weight: bold; border-bottom: 1px solid #ccc; padding-bottom: 10px; margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f8f9fa; font-weight: bold; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .summary-boxes { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; margin: 20px 0; }
        .summary-box { border: 1px solid #ddd; padding: 15px; background-color: #f9f9f9; }
        .summary-box h4 { margin: 0 0 10px 0; color: #333; }
        .summary-box .value { font-size: 18px; font-weight: bold; color: #667eea; }
        .user-info { background-color: #f0f0f0; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>';

        // Header
        $html .= '<div class="header">
            <div class="title">' . $reportType . ' Financial Report</div>
            <div class="date-range">Report Period: ' . date('M d, Y', strtotime($report['start_date'])) . ' - ' . date('M d, Y', strtotime($report['end_date'])) . '</div>
        </div>';

        // User Information
        if (isset($report['user'])) {
            $html .= '<div class="user-info">
                <strong>Member:</strong> ' . htmlspecialchars($report['user']['first_name'] . ' ' . $report['user']['last_name']) . '<br/>
                <strong>Email:</strong> ' . htmlspecialchars($report['user']['email']) . '<br/>
                <strong>Username:</strong> ' . htmlspecialchars($report['user']['username']) . '<br/>
                <strong>Current Balance:</strong> RWF ' . number_format($report['user']['balance'], 2) . '
            </div>';
        }

        // Summary Cards
        $html .= '<div class="section">
            <h3 class="section-title">Summary</h3>
            <div class="summary-boxes">
                <div class="summary-box">
                    <h4>Total Deposits</h4>
                    <div class="value">RWF ' . number_format($report['summary']['total_deposits'], 2) . '</div>
                </div>
                <div class="summary-box">
                    <h4>Total Withdrawals</h4>
                    <div class="value">RWF ' . number_format($report['summary']['total_withdrawals'], 2) . '</div>
                </div>
                <div class="summary-box">
                    <h4>Transfers Sent</h4>
                    <div class="value">RWF ' . number_format($report['summary']['total_transfers_sent'], 2) . '</div>
                </div>
                <div class="summary-box">
                    <h4>Transfers Received</h4>
                    <div class="value">RWF ' . number_format($report['summary']['total_transfers_received'], 2) . '</div>
                </div>
                <div class="summary-box">
                    <h4>Total Loans</h4>
                    <div class="value">RWF ' . number_format($report['summary']['total_loans'], 2) . '</div>
                </div>
                <div class="summary-box">
                    <h4>Net Change</h4>
                    <div class="value">RWF ' . number_format($report['summary']['net_change'], 2) . '</div>
                </div>
            </div>
        </div>';

        // Transaction Details
        if (!empty($report['transactions'])) {
            $html .= '<div class="section page-break">
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
                    <tbody>';

            foreach (array_slice($report['transactions'], 0, 50) as $transaction) {
                $html .= '<tr>
                    <td>' . date('M d, Y H:i', strtotime($transaction['created_at'])) . '</td>
                    <td>' . ucfirst(str_replace('_', ' ', $transaction['transaction_type'])) . '</td>
                    <td>' . number_format($transaction['amount'], 2) . '</td>
                    <td>' . number_format($transaction['balance_after'], 2) . '</td>
                    <td>' . ucfirst($transaction['status']) . '</td>
                </tr>';
            }

            $html .= '</tbody>
                </table>
            </div>';
        }

        // Deposits Section
        if (!empty($report['deposits'])) {
            $html .= '<div class="section page-break">
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
                    <tbody>';

            foreach ($report['deposits'] as $deposit) {
                $html .= '<tr>
                    <td>' . date('M d, Y', strtotime($deposit['created_at'])) . '</td>
                    <td>' . number_format($deposit['amount'], 2) . '</td>
                    <td>' . ucfirst(str_replace('_', ' ', $deposit['payment_method'])) . '</td>
                    <td>' . substr($deposit['transaction_reference'], 0, 15) . '...</td>
                    <td>' . ucfirst($deposit['status']) . '</td>
                </tr>';
            }

            $html .= '</tbody>
                </table>
            </div>';
        }

        // Loans Section
        if (!empty($report['loans'])) {
            $html .= '<div class="section page-break">
                <h3 class="section-title">Loans</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Principal (RWF)</th>
                            <th>Interest Rate (%)</th>
                            <th>Duration</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>';

            foreach ($report['loans'] as $loan) {
                $html .= '<tr>
                    <td>' . date('M d, Y', strtotime($loan['created_at'])) . '</td>
                    <td>' . number_format($loan['principal_amount'], 2) . '</td>
                    <td>' . $loan['interest_rate'] . '%</td>
                    <td>' . $loan['duration_months'] . ' months</td>
                    <td>' . ucfirst($loan['status']) . '</td>
                </tr>';
            }

            $html .= '</tbody>
                </table>
            </div>';
        }

        // Savings Section
        if (!empty($report['savings'])) {
            $html .= '<div class="section page-break">
                <h3 class="section-title">Savings</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Amount (RWF)</th>
                            <th>Interest Rate (%)</th>
                            <th>Maturity Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>';

            foreach ($report['savings'] as $saving) {
                $html .= '<tr>
                    <td>' . date('M d, Y', strtotime($saving['created_at'])) . '</td>
                    <td>' . number_format($saving['amount'], 2) . '</td>
                    <td>' . $saving['interest_rate'] . '%</td>
                    <td>' . date('M d, Y', strtotime($saving['maturity_date'])) . '</td>
                    <td>' . ucfirst($saving['status']) . '</td>
                </tr>';
            }

            $html .= '</tbody>
                </table>
            </div>';
        }

        // Footer
        $html .= '<div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #ccc; color: #666; font-size: 12px;">
            <p>Generated on ' . date('M d, Y H:i:s') . '</p>
            <p>IKIMINTA Financial System</p>
        </div>';

        $html .= '</body>
</html>';

        return $html;
    }
}
?>
