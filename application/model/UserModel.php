<?php
/**
 * User Model
 */

require_once dirname(__FILE__) . '/../config/Database.php';

class UserModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function register($data) {
        // Check if user exists
        if ($this->emailExists($data['email']) || $this->legalIdExists($data['legal_id'])) {
            return false;
        }

        $insertData = [
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_BCRYPT),
            'legal_id' => $data['legal_id'],
            'phone_number' => $data['phone_number'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'user_type' => 'member',
            'status' => 'active'
        ];

        try {
            $this->db->beginTransaction();
            
            $userId = $this->db->insert('users', $insertData);
            
            // Add signup bonus if applicable
            $settings = $this->getSettings();
            if ($settings['signup_bonus'] > 0) {
                $this->db->insert('transactions', [
                    'user_id' => $userId,
                    'transaction_type' => 'deposit',
                    'amount' => $settings['signup_bonus'],
                    'description' => 'Signup Bonus',
                    'status' => 'completed'
                ]);
                
                $this->db->update('users', ['balance' => $settings['signup_bonus']], 'id = ?', [$userId]);
            }
            
            $this->db->commit();
            return $userId;
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }
    }

    public function login($email, $password) {
        $user = $this->db->selectOne('SELECT * FROM users WHERE email = ?', [$email]);
        
        if ($user && password_verify($password, $user['password']) && $user['status'] === 'active') {
            $this->updateLastLogin($user['id']);
            return $user;
        }
        
        return false;
    }

    public function getUserById($id) {
        return $this->db->selectOne('SELECT * FROM users WHERE id = ?', [$id]);
    }

    public function getUserByEmail($email) {
        return $this->db->selectOne('SELECT * FROM users WHERE email = ?', [$email]);
    }

    public function getUserByUsername($username) {
        return $this->db->selectOne('SELECT * FROM users WHERE username = ?', [$username]);
    }

    public function getUserByPhoneNumber($phoneNumber) {
        return $this->db->selectOne('SELECT * FROM users WHERE phone_number = ?', [$phoneNumber]);
    }

    public function searchUserByPhonePartial($phoneNumber) {
        // Normalize phone number - remove spaces, dashes, and leading zeros
        $normalized = preg_replace('/[^\d+]/', '', $phoneNumber);
        
        // Try exact match first
        $user = $this->db->selectOne('SELECT * FROM users WHERE phone_number = ?', [$phoneNumber]);
        if ($user) return $user;
        
        // Try with normalized number
        $user = $this->db->selectOne('SELECT * FROM users WHERE REPLACE(REPLACE(phone_number, " ", ""), "-", "") = ?', [$normalized]);
        if ($user) return $user;
        
        // Try with LIKE for partial matches
        $user = $this->db->selectOne('SELECT * FROM users WHERE phone_number LIKE ?', ['%' . $phoneNumber . '%']);
        if ($user) return $user;
        
        return null;
    }

    public function getAllActiveMembers() {
        return $this->db->select(
            'SELECT id, username, phone_number, email, first_name, last_name FROM users WHERE user_type = "member" AND status = "active" ORDER BY username ASC'
        );
    }

    public function emailExists($email) {
        return $this->db->count('users', 'email = ?', [$email]) > 0;
    }

    public function legalIdExists($legal_id) {
        return $this->db->count('users', 'legal_id = ?', [$legal_id]) > 0;
    }

    public function updateLastLogin($userId) {
        $this->db->update('users', ['last_login' => date('Y-m-d H:i:s')], 'id = ?', [$userId]);
    }

    public function updateBalance($userId, $amount) {
        return $this->db->update('users', ['balance' => $amount], 'id = ?', [$userId]);
    }

    public function getBalance($userId) {
        $user = $this->getUserById($userId);
        return $user ? $user['balance'] : 0;
    }

    public function getAllUsers($limit = 50, $offset = 0) {
        return $this->db->select(
            'SELECT * FROM users WHERE user_type = "member" ORDER BY date_created DESC LIMIT ? OFFSET ?',
            [$limit, $offset]
        );
    }

    public function getSettings() {
        return $this->db->selectOne('SELECT * FROM settings LIMIT 1');
    }

    public function updateSettings($data) {
        return $this->db->update('settings', $data, 'id = ?', [1]);
    }

    public function suspendUser($userId) {
        return $this->db->update('users', ['status' => 'suspended'], 'id = ?', [$userId]);
    }

    public function activateUser($userId) {
        return $this->db->update('users', ['status' => 'active'], 'id = ?', [$userId]);
    }

    public function updateUser($userId, $data) {
        // Only allow updating certain fields
        $allowedFields = ['first_name', 'last_name', 'phone_number'];
        $updateData = [];

        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $updateData[$field] = $data[$field];
            }
        }

        if (empty($updateData)) {
            return false;
        }

        return $this->db->update('users', $updateData, 'id = ?', [$userId]);
    }

    public function getTotalUsers() {
        return $this->db->count('users');
    }
}
