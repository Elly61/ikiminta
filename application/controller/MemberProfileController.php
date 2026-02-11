<?php
/**
 * Member Profile Controller
 */

require_once dirname(__FILE__) . '/BaseController.php';
require_once dirname(__FILE__) . '/../model/UserModel.php';

class MemberProfileController extends BaseController {
    private $userModel;

    public function __construct() {
        parent::__construct();
        $this->userModel = new UserModel();
    }

    public function index() {
        $userId = $_SESSION['user_id'];
        $user = $this->userModel->getUserById($userId);

        if (!$user) {
            $this->redirect(BASE_URL . 'member/auth/login');
            return;
        }

        $this->loadView('member/profile/index', ['user' => $user]);
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(BASE_URL . 'member/profile');
            return;
        }

        $userId = $_SESSION['user_id'];
        $firstName = htmlspecialchars($_POST['first_name'] ?? '');
        $lastName = htmlspecialchars($_POST['last_name'] ?? '');
        $phoneNumber = htmlspecialchars($_POST['phone_number'] ?? '');

        $data = [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'phone_number' => $phoneNumber
        ];

        $success = $this->userModel->updateUser($userId, $data);

        if ($success) {
            return $this->response('success', 'Profile updated successfully');
        } else {
            return $this->response('error', 'Failed to update profile');
        }
    }
}