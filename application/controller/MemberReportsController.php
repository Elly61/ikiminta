<?php
/**
 * Member Reports Controller - Professional Financial Reports
 * Handles weekly, monthly, and yearly financial reports with PDF & CSV export
 */

require_once dirname(__FILE__) . '/BaseController.php';
require_once dirname(__FILE__) . '/../model/ReportModel.php';

class MemberReportsController extends BaseController {
    private $reportModel;

    public function __construct() {
        parent::__construct();
        $this->reportModel = new ReportModel();
    }

    /**
     * Weekly report view
     */
    public function weekly($params = []) {
        $this->displayReport('weekly');
    }

    /**
     * Monthly report view
     */
    public function monthly($params = []) {
        $this->displayReport('monthly');
    }

    /**
     * Yearly report view
     */
    public function yearly($params = []) {
        $this->displayReport('yearly');
    }

    /**
     * Download weekly PDF
     */
    public function downloadWeeklyPDF($params = []) {
        $this->downloadReportPDF('weekly');
    }

    /**
     * Download monthly PDF
     */
    public function downloadMonthlyPDF($params = []) {
        $this->downloadReportPDF('monthly');
    }

    /**
     * Download yearly PDF
     */
    public function downloadYearlyPDF($params = []) {
        $this->downloadReportPDF('yearly');
    }

    /**
     * Download weekly CSV
     */
    public function downloadWeeklyCSV($params = []) {
        $this->downloadReportCSV('weekly');
    }

    /**
     * Download monthly CSV
     */
    public function downloadMonthlyCSV($params = []) {
        $this->downloadReportCSV('monthly');
    }

    /**
     * Download yearly CSV
     */
    public function downloadYearlyCSV($params = []) {
        $this->downloadReportCSV('yearly');
    }

    /**
     * Display report
     */
    private function displayReport($period) {
        try {
            if (!$this->isLoggedIn()) {
                redirect(BASE_URL . 'member/auth/login');
            }

            $userId = $this->sanitizeUserId($_SESSION['user_id']);
            $methodName = 'get' . ucfirst($period) . 'Report';

            $report = $this->reportModel->$methodName($userId);
            $typeBreakdown = $this->reportModel->getTransactionTypeBreakdown($userId, $report['start_date'], $report['end_date']);
            $deposits = $this->reportModel->getDepositBreakdown($userId, $report['start_date'], $report['end_date']);
            $loans = $this->reportModel->getLoanBreakdown($userId, $report['start_date'], $report['end_date']);
            $savings = $this->reportModel->getSavingsBreakdown($userId, $report['start_date'], $report['end_date']);

            include VIEW_PATH . 'member/reports/' . $period . '.php';
        } catch (Exception $e) {
            error_log("Report Display Error: " . $e->getMessage());
            http_response_code(400);
            echo 'Error loading report: ' . htmlspecialchars($e->getMessage());
        }
    }

    /**
     * Download report as PDF
     */
    private function downloadReportPDF($period) {
        try {
            if (!$this->isLoggedIn()) {
                http_response_code(403);
                die('Access denied');
            }

            $userId = $this->sanitizeUserId($_SESSION['user_id']);
            $methodName = 'get' . ucfirst($period) . 'Report';

            $report = $this->reportModel->$methodName($userId);
            $report['deposits'] = $this->reportModel->getDepositBreakdown($userId, $report['start_date'], $report['end_date']);
            $report['loans'] = $this->reportModel->getLoanBreakdown($userId, $report['start_date'], $report['end_date']);
            $report['savings'] = $this->reportModel->getSavingsBreakdown($userId, $report['start_date'], $report['end_date']);

            $reportType = ucfirst($period);
            include VIEW_PATH . 'member/reports/print.php';
            exit;
        } catch (Exception $e) {
            error_log("PDF Download Error: " . $e->getMessage());
            http_response_code(500);
            die('Error generating PDF: ' . htmlspecialchars($e->getMessage()));
        }
    }

    /**
     * Download report as CSV
     */
    private function downloadReportCSV($period) {
        try {
            if (!$this->isLoggedIn()) {
                http_response_code(403);
                die('Access denied');
            }

            $userId = $this->sanitizeUserId($_SESSION['user_id']);
            $methodName = 'get' . ucfirst($period) . 'Report';

            $report = $this->reportModel->$methodName($userId);
            $report['deposits'] = $this->reportModel->getDepositBreakdown($userId, $report['start_date'], $report['end_date']);
            $report['loans'] = $this->reportModel->getLoanBreakdown($userId, $report['start_date'], $report['end_date']);
            $report['savings'] = $this->reportModel->getSavingsBreakdown($userId, $report['start_date'], $report['end_date']);

            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $this->getFilename($report, $period, 'csv') . '"');
            header('Cache-Control: public, must-revalidate, max-age=0');

            $output = fopen('php://output', 'w');
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

            // Header
            fputcsv($output, ['IKIMINTA FINANCIAL REPORT - ' . strtoupper($period)]);
            fputcsv($output, ['Member: ' . $report['user']['first_name'] . ' ' . $report['user']['last_name']]);
            fputcsv($output, ['Period: ' . date('M d, Y', strtotime($report['start_date'])) . ' to ' . date('M d, Y', strtotime($report['end_date']))]);
            fputcsv($output, ['Generated: ' . date('M d, Y H:i:s')]);
            fputcsv($output, []);

            // Summary
            fputcsv($output, ['FINANCIAL SUMMARY']);
            fputcsv($output, ['Metric', 'Amount (RWF)']);
            fputcsv($output, ['Total Deposits', number_format($report['summary']['total_deposits'], 2)]);
            fputcsv($output, ['Total Withdrawals', number_format($report['summary']['total_withdrawals'], 2)]);
            fputcsv($output, ['Transfers Sent', number_format($report['summary']['total_transfers_sent'], 2)]);
            fputcsv($output, ['Transfers Received', number_format($report['summary']['total_transfers_received'], 2)]);
            fputcsv($output, ['Total Loans', number_format($report['summary']['total_loans'], 2)]);
            fputcsv($output, ['Net Change', number_format($report['summary']['net_change'], 2)]);
            fputcsv($output, []);

            //  Transactions
            if (!empty($report['transactions'])) {
                fputcsv($output, ['RECENT TRANSACTIONS']);
                fputcsv($output, ['Date', 'Type', 'Amount (RWF)', 'Balance After (RWF)', 'Status']);
                foreach (array_slice($report['transactions'], 0, 100) as $tx) {
                    fputcsv($output, [
                        date('M d, Y H:i', strtotime($tx['created_at'])),
                        ucfirst(str_replace('_', ' ', $tx['transaction_type'])),
                        number_format($tx['amount'], 2),
                        number_format($tx['balance_after'], 2),
                        ucfirst($tx['status'])
                    ]);
                }
                fputcsv($output, []);
            }

            fclose($output);
            exit;
        } catch (Exception $e) {
            error_log("CSV Download Error: " . $e->getMessage());
            http_response_code(500);
            die('Error generating CSV: ' . htmlspecialchars($e->getMessage()));
        }
    }

    /**
     * Generate PDF HTML
     */
    private function generatePDFHTML($report, $period) {
        ob_start();
        include dirname(__FILE__) . '/../views/member/reports/pdf_template.php';
        return ob_get_clean();
    }

    /**
     * Get download filename
     */
    private function getFilename($report, $period, $format) {
        $username = preg_replace('/[^a-zA-Z0-9_-]/', '', $report['user']['username']);
        $date = date('Y-m-d');
        return "{$username}_{$period}_report_{$date}.{$format}";
    }

    /**
     * Sanitize user ID
     */
    private function sanitizeUserId($userId) {
        return intval($userId);
    }

    /**
     * Check if logged in
     */
    private function isLoggedIn() {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

}
?>
