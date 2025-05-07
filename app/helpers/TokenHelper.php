<?php
namespace App\Helpers;

require_once __DIR__ . '/../../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Dotenv\Dotenv;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/../../'); // Path ke root proyek
$dotenv->safeLoad();

class TokenHelper {
    private static $secretKey = 'kelas-kita-secret';
    private static $algorithm = 'HS256';

    public static function setSecretKey(string $key) {
        self::$secretKey = $key;
    }

    public static function generateToken($user) {
        $secretKey = $_ENV['JWT_SECRET_KEY'] ?? 'kelas-kita-secret';
        $payload = [
            'iss' => $_ENV['JWT_ISSUER'] ?? 'kelas-kita',
            'iat' => time(),
            'exp' => time() + (60 * 60 * 24),
            'id' => isset($user['id']) ? $user['id'] : null,
            'email' => $user['email'],
            'role' => $user['role']
        ];
        return JWT::encode($payload, $secretKey, self::$algorithm);
    }

    public static function verifyToken($token) {
        $secretKey = $_ENV['JWT_SECRET_KEY'] ?? 'kelas-kita-secret';
        try {
            return JWT::decode($token, new Key($secretKey, self::$algorithm));
        } catch (\Exception $e) {
            error_log("JWT Verification Error: " . $e->getMessage());
            return null;
        }
    }
}
