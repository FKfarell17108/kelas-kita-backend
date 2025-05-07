<?php
namespace App\Middleware;

use App\Helpers\TokenHelper;

require_once __DIR__ . '/../helpers/TokenHelper.php';

class AuthMiddleware {
    public static function checkAuth() {
        $headers = apache_request_headers();

        if (!isset($headers['Authorization'])) {
            http_response_code(401);
            echo json_encode(['message' => 'Authorization header missing']);
            exit;
        }

        $authHeader = $headers['Authorization'];
        if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            http_response_code(401);
            echo json_encode(['message' => 'Invalid authorization format']);
            exit;
        }

        $token = $matches[1];
        $decoded = TokenHelper::verifyToken($token);

        if (!$decoded) {
            http_response_code(401);
            echo json_encode(['message' => 'Invalid or expired token']);
            exit;
        }

        // Simpan info user ke $_SESSION atau global
        $GLOBALS['auth_user'] = $decoded;
    }
}
