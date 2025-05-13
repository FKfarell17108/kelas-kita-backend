<?php
namespace App\Helpers;

class Utility {
    public static function getAuthUser() {
        return $GLOBALS['auth_user'] ?? null;
    }

    public static function jsonResponse($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    public static function getDatabase() {
        return (new \Database())->connect();
    }
}
