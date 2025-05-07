<?php
use App\Helpers\TokenHelper; // Tambahkan ini
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/TokenHelper.php';
class AuthController {
    public static function register() {
        $db = (new Database())->connect();
        $input = json_decode(file_get_contents("php://input"), true);

        if (!$input || !isset($input['name'], $input['email'], $input['password'], $input['role'])) {
            http_response_code(400);
            echo json_encode(["message" => "Data pendaftaran tidak lengkap. Harap isi nama, email, kata sandi, dan peran."]);
            return;
        }

        $name = $input['name'];
        $email = $input['email'];
        $password = password_hash($input['password'], PASSWORD_BCRYPT);
        $role = $input['role'];
        $id_number = $input['id_number'] ?? null;

        // Validasi role
        if ($role !== 'teacher' && $role !== 'student') {
            http_response_code(400);
            echo json_encode(["message" => "Peran tidak valid. Peran harus berupa 'teacher' atau 'student'."]);
            return;
        }

        // Pengecekan email sebelum insert
        $stmt_check = $db->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $stmt_check->execute([$email]);
        $emailCount = $stmt_check->fetchColumn();

        if ($emailCount > 0) {
            http_response_code(409); // Conflict
            echo json_encode(["message" => "Alamat email ini sudah terdaftar. Gunakan alamat email lain atau coba login."]);
            return;
        }

        try {
            $stmt = $db->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $password, $role]);
            $user_id = $db->lastInsertId();

            if ($role === 'teacher') {
                $stmt2 = $db->prepare("INSERT INTO teachers (user_id, nip) VALUES (?, ?)");
                $stmt2->execute([$user_id, $id_number]);
            } elseif ($role === 'student') {
                $stmt2 = $db->prepare("INSERT INTO students (user_id, nis) VALUES (?, ?)");
                $stmt2->execute([$user_id, $id_number]);
            }

            http_response_code(201);
            echo json_encode(["message" => "Pendaftaran berhasil! Akun Anda telah dibuat."]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["message" => "Pendaftaran gagal. Terjadi kesalahan pada server.", "error" => $e->getMessage()]);
        }
    }

    public static function login() {
        $db = (new Database())->connect();
        $input = json_decode(file_get_contents("php://input"), true);

        if (!$input || !isset($input['email'], $input['password'])) {
            http_response_code(400);
            echo json_encode(["message" => "Email dan kata sandi diperlukan untuk login."]);
            return;
        }

        $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$input['email']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($input['password'], $user['password'])) {
            unset($user['password']);
            // Setelah cek email & password valid:
            $token = TokenHelper::generateToken([
                'id' => $user['id'],
                'email' => $user['email'],
                'role' => $user['role']
            ]);
            echo json_encode([
                'status' => 'success',
                'token' => $token
            ]);
        } else if (!$user) {
            http_response_code(401);
            echo json_encode(["message" => "Akun dengan email ini tidak ditemukan. Pastikan Anda memasukkan alamat email yang benar atau daftar terlebih dahulu."]);
        } else {
            http_response_code(401);
            echo json_encode(["message" => "Kata sandi yang Anda masukkan salah. Periksa kembali kata sandi Anda dan coba lagi."]);
        }
    }
}