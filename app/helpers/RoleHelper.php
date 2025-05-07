<?php
namespace App\Helpers;

class RoleHelper {
    public static function allowRoles(array $allowedRoles) {
        if (!isset($GLOBALS['auth_user'])) {
            http_response_code(403);
            echo json_encode(['message' => 'Unauthorized']);
            exit;
        }

        $role = $GLOBALS['auth_user']->role;

        if (!in_array($role, $allowedRoles)) {
            http_response_code(403);
            echo json_encode(['message' => 'Access denied for role: ' . $role]);
            exit;
        }
    }
}
