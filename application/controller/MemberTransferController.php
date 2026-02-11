<?php
/**
 * Member Transfer Controller
 */

require_once dirname(__FILE__) . '/BaseController.php';
require_once dirname(__FILE__) . '/../model/TransferModel.php';
require_once dirname(__FILE__) . '/../model/UserModel.php';

class MemberTransferController extends BaseController {
    private $transferModel;
    private $userModel;

    public function __construct() {
        parent::__construct();
        $this->transferModel = $this->loadModel('TransferModel');
        $this->userModel = $this->loadModel('UserModel');
    }

    public function index() {
        $user = $this->getCurrentUser();
        $transfers = $this->transferModel->getUserTransfers($user['id']);
        
        $this->loadView('member/transfer/index', [
            'transfers' => $transfers,
            'user' => $user
        ]);
    }

    public function find() {
        $members = $this->userModel->getAllActiveMembers();
        $this->loadView('member/transfer/find', ['members' => $members]);
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->loadView('member/transfer/create');
        } else {
            return $this->handleCreate();
        }
    }

    public function lookup() {
        $this->requireAjaxAuth();
        
        $input = json_decode(file_get_contents('php://input'), true);
        $phoneNumber = trim($input['phone_number'] ?? '');

        if (empty($phoneNumber)) {
            return $this->response('error', 'Phone number is required');
        }

        // Try multiple lookup strategies
        $user = $this->userModel->searchUserByPhonePartial($phoneNumber);

        if (!$user) {
            return $this->response('error', 'User not found');
        }

        if ($user['user_type'] !== 'member' || $user['status'] !== 'active') {
            return $this->response('error', 'User not available');
        }

        if ($user['id'] == $this->getCurrentUser()['id']) {
            return $this->response('error', 'Cannot transfer to yourself');
        }

        return $this->response('success', 'User found', [
            'user' => [
                'id' => $user['id'],
                'username' => $user['username'],
                'phone_number' => $user['phone_number']
            ]
        ]);
    }

    private function handleCreate() {
        $user = $this->getCurrentUser();
        $receiverRaw = trim($_POST['receiver_id'] ?? '');
        $receiverId = ctype_digit($receiverRaw) ? intval($receiverRaw) : 0;
        $amount = floatval($_POST['amount'] ?? 0);
        $description = htmlspecialchars($_POST['description'] ?? '');

        // Handle uploaded proof file (optional)
        $proofPath = null;
        if (!empty($_FILES['proof']) && $_FILES['proof']['error'] !== UPLOAD_ERR_NO_FILE) {
            $file = $_FILES['proof'];
            $allowedExts = ['png', 'jpg', 'jpeg', 'pdf'];
            $maxSize = 5 * 1024 * 1024; // 5MB

            if ($file['error'] !== UPLOAD_ERR_OK) {
                return $this->response('error', 'Error uploading proof file');
            }

            if ($file['size'] > $maxSize) {
                return $this->response('error', 'Proof file exceeds maximum size of 5MB');
            }

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, $allowedExts)) {
                return $this->response('error', 'Invalid proof file type');
            }

            $allowedMimes = [
                'image/png', 'image/jpeg', 'application/pdf'
            ];
            if (!in_array($mime, $allowedMimes)) {
                return $this->response('error', 'Invalid proof file MIME type');
            }

            $uploadDir = __DIR__ . '/../../public/uploads/transfers/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $safeName = 'proof_' . time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
            $destination = $uploadDir . $safeName;

            if (!move_uploaded_file($file['tmp_name'], $destination)) {
                return $this->response('error', 'Failed to save proof file');
            }

            $proofPath = 'public/uploads/transfers/' . $safeName;
        }

        if ($amount <= 0) {
            return $this->response('error', 'Invalid amount');
        }

        if ($receiverRaw === '') {
            return $this->response('error', 'Receiver MOMO number is required');
        }

        $receiver = $this->userModel->getUserByPhoneNumber($receiverRaw);

        if (!$receiver) {
            return $this->response('error', 'Receiver not found. Please check the MOMO number.');
        }

        $receiverId = (int)$receiver['id'];

        if ($receiverId === (int)$user['id']) {
            return $this->response('error', 'You cannot transfer to yourself. Use a different receiver.');
        }
        if (($receiver['user_type'] ?? '') !== 'member') {
            return $this->response('error', 'Receiver must be a member account.');
        }

        if (($receiver['status'] ?? '') !== 'active') {
            return $this->response('error', 'Receiver account is not active.');
        }

        if (!$this->transferModel->validateReceiver($receiverId, $user['id'])) {
            return $this->response('error', 'Invalid receiver');
        }

        $transferId = $this->transferModel->createTransfer($user['id'], $receiverId, $amount, $description, $proofPath);

        if ($transferId) {
            return $this->response('success', 'Transfer completed successfully', [
                'transfer_id' => $transferId,
                'redirect' => BASE_URL . 'member/transfer'
            ]);
        } else {
            return $this->response('error', 'Transfer failed. Check your balance.');
        }
    }
}
