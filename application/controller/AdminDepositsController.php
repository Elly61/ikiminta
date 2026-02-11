<?php
/**
 * Admin Deposits Controller
 */

require_once dirname(__FILE__) . '/BaseController.php';
require_once dirname(__FILE__) . '/../model/DepositModel.php';

class AdminDepositsController extends BaseController {
    private $depositModel;

    public function __construct() {
        parent::__construct();
        if (!$this->isAdmin()) {
            $this->redirect(BASE_URL . 'admin/auth/login');
        }
        $this->depositModel = $this->loadModel('DepositModel');
    }

    public function index() {
        $deposits = $this->depositModel->getPendingDeposits();
        $this->loadView('admin/requests/deposits', ['deposits' => $deposits]);
    }

    public function approve($depositId) {
        $admin = $this->getCurrentUser();
        if ($this->depositModel->approveDeposit($depositId, $admin['id'])) {
            return $this->response('success', 'Deposit approved successfully');
        }
        return $this->response('error', 'Failed to approve deposit');
    }

    public function reject($depositId) {
        $reason = htmlspecialchars($_POST['reason'] ?? '');
        $admin = $this->getCurrentUser();
        if ($this->depositModel->rejectDeposit($depositId, $reason, $admin['id'])) {
            return $this->response('success', 'Deposit rejected');
        }
        return $this->response('error', 'Failed to reject deposit');
    }

    public function downloadProof($depositId) {
        $deposit = $this->depositModel->getDepositById($depositId);
        if (!$deposit || empty($deposit['proof'])) {
            $this->redirect(BASE_URL . 'admin/deposits');
        }

        $filePath = __DIR__ . '/../../' . $deposit['proof'];
        if (!file_exists($filePath)) {
            $this->redirect(BASE_URL . 'admin/deposits');
        }

        // Log audit entry for download
        $admin = $this->getCurrentUser();
        $this->depositModel->logAudit($admin['id'], 'download_proof', $depositId, null, json_encode(['proof_download' => date('Y-m-d H:i:s')]));

        // Serve file
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $filePath);
        finfo_close($finfo);

        header('Content-Description: File Transfer');
        header('Content-Type: ' . $mime);
        header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
        exit();
    }
}
