<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../helpers/RoleHelper.php';

use App\Middleware\AuthMiddleware;
use App\Helpers\RoleHelper;

class BootcampController {
    public static function createBootcamp() {
        AuthMiddleware::checkAuth();
        RoleHelper::allowRoles(['teacher']);
    
        $input = json_decode(file_get_contents("php://input"), true);
        $title = $input['title'] ?? null;
        $description = $input['description'] ?? null;
        $user_id = $GLOBALS['auth_user']->id ?? null; // Dapatkan user_id dari pengguna yang terautentikasi
    
        // Validasi input
        if (!$title) {
            http_response_code(400);
            echo json_encode(["message" => "Judul bootcamp wajib diisi"]);
            return;
        }
    
        if (!$description) {
            http_response_code(400);
            echo json_encode(["message" => "Deskripsi bootcamp wajib diisi"]);
            return;
        }
    
        if (!$user_id) {
            http_response_code(500);
            echo json_encode(["message" => "Gagal mendapatkan ID pengguna"]);
            return;
        }
    
        $db = (new Database())->connect();
    
        // Dapatkan mentor_id dari tabel teachers berdasarkan user_id.
        $stmt = $db->prepare("SELECT id FROM teachers WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $teacher = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if (!$teacher) {
            http_response_code(400);
            echo json_encode(["message" => "Pengguna yang terautentikasi bukan seorang guru yang valid"]);
            return;
        }
        $mentor_id = $teacher['id'];
    
        try {
            $stmt = $db->prepare("INSERT INTO bootcamps (title, description, mentor_id) VALUES (?, ?, ?)");
            $stmt->execute([$title, $description, $mentor_id]);
            http_response_code(201);
            echo json_encode(["message" => "Bootcamp berhasil dibuat"]);
        } catch (\PDOException $e) {
            http_response_code(500);
            error_log("Error creating bootcamp: " . $e->getMessage());
            echo json_encode(["message" => "Gagal membuat bootcamp: " . $e->getMessage()]);
        }
    }

    public static function joinBootcamp($bootcampId) {
        AuthMiddleware::checkAuth();
        RoleHelper::allowRoles(['student']);
    
        if (!is_numeric($bootcampId) || $bootcampId <= 0) {
            http_response_code(400);
            echo json_encode(["message" => "ID bootcamp tidak valid"]);
            return;
        }
    
        $user_id = $GLOBALS['auth_user']->id ?? null;
        if (!$user_id) {
            http_response_code(401);
            echo json_encode(["message" => "Pengguna tidak terautentikasi"]);
            return;
        }
    
        $db = (new Database())->connect();
    
        try {
            // Dapatkan student_id dari tabel students berdasarkan user_id
            $stmt = $db->prepare("SELECT id FROM students WHERE user_id = ?");
            $stmt->execute([$user_id]);
            $student = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if (!$student) {
                http_response_code(400);
                echo json_encode(["message" => "Pengguna yang terautentikasi bukan seorang siswa yang valid"]);
                return;
            }
            $student_id = $student['id'];
    
            // Cek apakah bootcamp ada (seperti saran sebelumnya)
            $stmt = $db->prepare("SELECT id FROM bootcamps WHERE id = ?");
            $stmt->execute([$bootcampId]);
            if (!$stmt->fetch()) {
                http_response_code(404);
                echo json_encode(["message" => "Bootcamp dengan ID tersebut tidak ditemukan"]);
                return;
            }
    
            // Cek apakah sudah join
            $stmt = $db->prepare("SELECT id FROM bootcamp_participants WHERE bootcamp_id = ? AND student_id = ?");
            $stmt->execute([$bootcampId, $student_id]);
    
            if ($stmt->fetch()) {
                http_response_code(409);
                echo json_encode(["message" => "Kamu sudah bergabung dengan bootcamp ini"]);
                return;
            }
    
            $stmt = $db->prepare("INSERT INTO bootcamp_participants (bootcamp_id, student_id) VALUES (?, ?)");
            $stmt->execute([$bootcampId, $student_id]);
    
            http_response_code(200); // OK
            echo json_encode(["message" => "Berhasil bergabung ke bootcamp"]);
    
        } catch (\PDOException $e) {
            http_response_code(500);
            error_log("Error joining bootcamp: " . $e->getMessage());
            echo json_encode(["message" => "Gagal bergabung ke bootcamp: " . $e->getMessage()]);
        }
    }

    public static function listBootcamps() {
        $db = (new Database())->connect();
        try {
            $stmt = $db->query("SELECT * FROM bootcamps");
            $bootcamps = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(["bootcamps" => $bootcamps]);
        } catch (\PDOException $e) {
            http_response_code(500);
            error_log("Error listing bootcamps: " . $e->getMessage()); // Log error
            echo json_encode(["message" => "Gagal mengambil daftar bootcamp: " . $e->getMessage()]);
        }
    }
}