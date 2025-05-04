<?php
// api.php
require_once __DIR__ . '/../controllers/AuthController.php';

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

error_log("URI: $uri");

if ($uri === '/api/register' && $method === 'POST') {
    AuthController::register();
} elseif ($uri === '/api/login' && $method === 'POST') {
    AuthController::login();
} else {
    http_response_code(404);
    echo json_encode(["message" => "Endpoint not found"]);
}
?>