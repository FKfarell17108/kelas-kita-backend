<?php
// api.php
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../helpers/RoleHelper.php';

use App\Middleware\AuthMiddleware;
use App\Helpers\RoleHelper;

// Mendapatkan URI
$basePath = dirname($_SERVER['SCRIPT_NAME']);
$uri = substr($_SERVER['REQUEST_URI'], strlen($basePath));
if (strpos($uri, '?') !== false) {
    $uri = substr($uri, 0, strpos($uri, '?'));
}
$uri = rtrim($uri, '/');
if (empty($uri)) {
    $uri = '/';
}

$method = $_SERVER['REQUEST_METHOD'];

error_log("URI: $uri, Method: $method"); // Tambahkan logging method

// Rute
if ($uri === '/api/register' && $method === 'POST') {
    AuthController::register();
} elseif ($uri === '/api/login' && $method === 'POST') {
    AuthController::login();
}
// Rute untuk mendapatkan profil
elseif ($uri === '/api/profile' && $method === 'GET') {
    AuthMiddleware::checkAuth();

    $user = $GLOBALS['auth_user'];

    echo json_encode([
        'status' => 'success',
        'profile' => [
            'uid' => $user->id,
            'email' => $user->email,
            'role' => $user->role
        ]
    ]);
}
// Rute untuk mendapatkan daftar pengguna admin
elseif ($uri === '/admin/users' && $method === 'GET') {
    AuthMiddleware::checkAuth();
    RoleHelper::allowRoles(['admin']);

    // Contoh dummy response
    echo json_encode([
        'users' => [
            ['id' => 1, 'email' => 'admin@example.com'],
            ['id' => 2, 'email' => 'siswa@example.com']
        ]
    ]);
}
else {
    http_response_code(404);
    echo json_encode(["message" => "Endpoint not found"]);
}
?>