<?php
// api.php
// Pastikan error reporting diaktifkan untuk membantu debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Sertakan file yang diperlukan
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/BootcampController.php';
require_once __DIR__ . '/../controllers/ModuleController.php';
require_once __DIR__ . '/../controllers/ForumController.php';
require_once __DIR__ . '/../controllers/ClassController.php'; // Tambahkan ini
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
$uri = trim($uri); // Tambahkan ini untuk menghapus spasi di awal dan akhir
$method = $_SERVER['REQUEST_METHOD'];

error_log("URI: $uri, Method: $method"); // Logging URI dan Method

// Set header untuk JSON response
header('Content-Type: application/json');

// Rute Autentikasi
if ($uri === '/api/register' && $method === 'POST') {
    AuthController::register();
    exit(); // Tambahkan exit() di sini
} elseif ($uri === '/api/login' && $method === 'POST') {
    AuthController::login();
    exit(); // Tambahkan exit() di sini
}
// Rute Profil
elseif ($uri === '/api/profile' && $method === 'GET') {
    AuthMiddleware::checkAuth();
    $user = $GLOBALS['auth_user'];
    echo json_encode([
        'status' => 'success',
        'profile' => [
            'uid' => $user->id, // Asumsi 'id' adalah properti UID
            'email' => $user->email,
            'role' => $user->role
        ]
    ]);
    exit(); // Tambahkan exit() di sini
}
// Rute Admin (Contoh)
elseif ($uri === '/admin/users' && $method === 'GET') {
    AuthMiddleware::checkAuth();
    RoleHelper::allowRoles(['admin']);
    echo json_encode([
        'users' => [
            ['id' => 1, 'email' => 'admin@example.com'],
            ['id' => 2, 'email' => 'siswa@example.com']
        ]
    ]);
    exit(); // Tambahkan exit() di sini
}
// Rute Bootcamp
elseif ($uri === '/api/bootcamps' && $method === 'POST') {
    BootcampController::createBootcamp();
    exit(); // Tambahkan exit() di sini
} elseif ($uri === '/api/bootcamps' && $method === 'GET') {
    BootcampController::listBootcamps();
    exit(); // Tambahkan exit() di sini
} elseif (preg_match('#^/api/bootcamps/(\d+)/join$#', $uri, $matches) && $method === 'POST') {
    BootcampController::joinBootcamp($matches[1]);
    exit(); // Tambahkan exit() di sini
}

// Modul dalam Bootcamp
elseif (preg_match('#^/api/bootcamps/(\d+)/modules$#', $uri, $matches) && $method === 'POST') {
    ModuleController::addModule($matches[1]);
    exit(); // Tambahkan exit() di sini
} elseif (preg_match('#^/api/bootcamps/(\d+)/modules$#', $uri, $matches) && $method === 'GET') {
    ModuleController::getModules($matches[1]);
    exit(); // Tambahkan exit() di sini
} elseif (preg_match('#^/api/bootcamps/(\d+)/complete-module/(\d+)$#', $uri, $matches) && $method === 'POST') {
    ModuleController::completeModule($matches[1], $matches[2]);
    exit(); // Tambahkan exit() di sini
}
// Rute Progress Module (Siswa Sendiri)
elseif (preg_match('#^/api/bootcamps/(\d+)/progress$#', $uri, $matches) && $method === 'GET') {
    ModuleController::getProgress($matches[1]);
    exit(); // Tambahkan exit() di sini
}
// Rute Progress Semua Siswa dalam Bootcamp (Admin/Teacher)
elseif (preg_match('#^/api/bootcamps/(\d+)/progress/all$#', $uri, $matches) && $method === 'GET') {
    ModuleController::getAllProgress($matches[1]);
    exit(); // Tambahkan exit() di sini
}
// Rute untuk mendapatkan detail modul yang belum selesai (Incomplete Module)
elseif (preg_match('#^/api/bootcamps/(\d+)/module/(\d+)/incomplete$#', $uri, $matches) && $method === 'GET') {
    ModuleController::getIncompleteModule($matches[1], $matches[2]);
    exit(); // Tambahkan exit() di sini
}
// Rute untuk export progress ke CSV
elseif (preg_match('#^/api/bootcamps/(\d+)/progress/export$#', $uri, $matches) && $method === 'GET') {
    ModuleController::exportProgressCSV($matches[1]);
    exit(); // Tambahkan exit() di sini
}
// Rute untuk mendapatkan pertanyaan dalam modul
elseif (preg_match('#^/api/modules/(\d+)/questions$#', $uri, $matches) && $method === 'GET') {
    ModuleController::getModuleQuestions($matches[1]);
    exit(); // Tambahkan exit() di sini
}
// Rute untuk menjawab pertanyaan
elseif (preg_match('#^/api/questions/(\d+)/answer$#', $uri, $matches) && $method === 'POST') {
    ModuleController::answerQuestion($matches[1]);
    exit(); // Tambahkan exit() di sini
}
// Rute untuk mendapatkan hasil kuis modul
elseif (preg_match('#^/api/modules/(\d+)/results$#', $uri, $matches) && $method === 'GET') {
    ModuleController::getModuleQuizResult($matches[1]);
    exit(); // Tambahkan exit() di sini
}

// Forum routes
if ($uri === '/api/forum/posts' && $method === 'POST') {
    ForumController::createPost();
    exit(); // Tambahkan exit() di sini
} elseif ($uri === '/api/forum/posts' && $method === 'GET') {
    ForumController::listPosts();
    exit(); // Tambahkan exit() di sini
} elseif (preg_match('#^/api/forum/posts/(\d+)/comments$#', $uri, $matches) && $method === 'POST') {
    ForumController::addComment($matches[1]);
    exit(); // Tambahkan exit() di sini
} elseif (preg_match('#^/api/forum/posts/(\d+)/comments$#', $uri, $matches) && $method === 'GET') {
    ForumController::listComments($matches[1]);
    exit(); // Tambahkan exit() di sini
}

// Rute untuk Class
if ($uri === '/api/classes' && $method === 'POST') {
    ClassController::createClass();
    exit(); // Tambahkan exit() di sini
}

if ($uri === '/api/classes' && $method === 'GET') {
    ClassController::listClasses();
    exit();
}

if (preg_match('#^/api/classes/(\d+)/join$#', $uri, $matches) && $method === 'POST') {
    ClassController::joinClass($matches[1]);
    exit();
}

if (preg_match('#^/api/classes/(\d+)/members$#', $uri, $matches) && $method === 'GET') {
    ClassController::listClassMembers($matches[1]);
    exit();
}

if (preg_match('#^/api/classes/(\d+)/assignments$#', $uri, $matches) && $method === 'POST') {
    ClassController::createAssignment($matches[1]);
    exit();
}

if (preg_match('#^/api/classes/(\d+)/assignments$#', $uri, $matches) && $method === 'GET') {
    ClassController::listAssignments($matches[1]);
    exit();
}

if (preg_match('#^/api/assignments/(\d+)/submit$#', $uri, $matches) && $method === 'POST') {
    ClassController::submitAssignment($matches[1]);
    exit();
}

if (preg_match('#^/api/assignments/(\d+)/submissions$#', $uri, $matches) && $method === 'GET') {
    ClassController::listSubmissions($matches[1]);
    exit();
}

if (preg_match('#^/api/submissions/(\d+)/grade$#', $uri, $matches) && $method === 'POST') {
    ClassController::gradeSubmission($matches[1]);
    exit();
}


// Rute Tidak Ditemukan
else {
    http_response_code(404);
    echo json_encode(["message" => "Endpoint not found"]);
    exit(); // Tambahkan exit() di sini (opsional, tapi baik untuk kejelasan)
}
?>