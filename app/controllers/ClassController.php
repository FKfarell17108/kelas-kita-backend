<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../helpers/Utility.php';
require_once __DIR__ . '/../helpers/RoleHelper.php';

use App\Middleware\AuthMiddleware;
use App\Helpers\Utility;
use App\Helpers\RoleHelper;

class ClassController {
    public static function createClass() {
        AuthMiddleware::checkAuth();
        RoleHelper::allowRoles(['teacher']);

        $input = json_decode(file_get_contents("php://input"), true);
        $name = $input['name'] ?? null;
        $description = $input['description'] ?? '';

        if (!$name) {
            Utility::jsonResponse(["message" => "Nama kelas wajib diisi"], 400);
        }

        $user = Utility::getAuthUser();
        $db = Utility::getDatabase();

        // Ambil teacher_id dari tabel teachers
        $stmt = $db->prepare("SELECT id FROM teachers WHERE user_id = ?");
        $stmt->execute([$user->id]);
        $teacher = $stmt->fetch();

        if (!$teacher) {
            Utility::jsonResponse(["message" => "Akun ini tidak terdaftar sebagai guru"], 403);
        }

        $stmt2 = $db->prepare("INSERT INTO classes (name, description, teacher_id) VALUES (?, ?, ?)");
        $stmt2->execute([$name, $description, $teacher['id']]);

        Utility::jsonResponse(["message" => "Kelas berhasil dibuat"]);
    }

    public static function listClasses() {
        AuthMiddleware::checkAuth(); // Siapa saja yang login bisa akses
    
        $db = Utility::getDatabase();
    
        $stmt = $db->query("
            SELECT c.id, c.name, c.description, c.created_at, u.name AS teacher
            FROM classes c
            JOIN teachers t ON c.teacher_id = t.id
            JOIN users u ON t.user_id = u.id
            ORDER BY c.created_at DESC
        ");
    
        $classes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        Utility::jsonResponse([
            "classes" => $classes
        ]);
    }
    
    public static function joinClass($classId) {
        AuthMiddleware::checkAuth();
        RoleHelper::allowRoles(['student']);
    
        $db = Utility::getDatabase();
        $user = Utility::getAuthUser();
    
        // Ambil student_id dari tabel students
        $stmt = $db->prepare("SELECT id FROM students WHERE user_id = ?");
        $stmt->execute([$user->id]);
        $student = $stmt->fetch();
    
        if (!$student) {
            Utility::jsonResponse(["message" => "Akun ini tidak terdaftar sebagai siswa"], 403);
        }
    
        $studentId = $student['id'];
    
        // Cek apakah sudah bergabung
        $check = $db->prepare("SELECT * FROM class_members WHERE class_id = ? AND student_id = ?");
        $check->execute([$classId, $studentId]);
        if ($check->fetch()) {
            Utility::jsonResponse(["message" => "Kamu sudah tergabung dalam kelas ini"], 409);
        }
    
        // Masukkan ke class_members
        $insert = $db->prepare("INSERT INTO class_members (class_id, student_id) VALUES (?, ?)");
        $insert->execute([$classId, $studentId]);
    
        Utility::jsonResponse(["message" => "Berhasil bergabung ke kelas"]);
    }    

    public static function listClassMembers($classId) {
        AuthMiddleware::checkAuth();
        RoleHelper::allowRoles(['teacher']);
    
        $db = Utility::getDatabase();
        $user = Utility::getAuthUser();
    
        // Cek apakah guru adalah pemilik kelas
        $stmt = $db->prepare("
            SELECT c.id FROM classes c
            JOIN teachers t ON c.teacher_id = t.id
            WHERE c.id = ? AND t.user_id = ?
        ");
        $stmt->execute([$classId, $user->id]);
        $class = $stmt->fetch();
    
        if (!$class) {
            Utility::jsonResponse(["message" => "Kelas tidak ditemukan atau Anda tidak memiliki akses"], 403);
        }
    
        // Ambil anggota kelas
        $stmt = $db->prepare("
            SELECT u.name, u.email, cm.joined_at
            FROM class_members cm
            JOIN students s ON cm.student_id = s.id
            JOIN users u ON s.user_id = u.id
            WHERE cm.class_id = ?
            ORDER BY cm.joined_at ASC
        ");
        $stmt->execute([$classId]);
        $members = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        Utility::jsonResponse([
            "class_id" => $classId,
            "members" => $members
        ]);
    }
    
    public static function createAssignment($classId) {
        AuthMiddleware::checkAuth();
        RoleHelper::allowRoles(['teacher']);
    
        $input = json_decode(file_get_contents("php://input"), true);
        $title = $input['title'] ?? null;
        $description = $input['description'] ?? '';
        $due_date = $input['due_date'] ?? null;
    
        if (!$title || !$due_date) {
            Utility::jsonResponse(["message" => "Judul dan tanggal deadline harus diisi"], 400);
        }
    
        $user = Utility::getAuthUser();
        $db = Utility::getDatabase();
    
        // Cek apakah guru pemilik kelas
        $stmt = $db->prepare("
            SELECT c.id FROM classes c
            JOIN teachers t ON c.teacher_id = t.id
            WHERE c.id = ? AND t.user_id = ?
        ");
        $stmt->execute([$classId, $user->id]);
        $class = $stmt->fetch();
    
        if (!$class) {
            Utility::jsonResponse(["message" => "Anda tidak memiliki akses ke kelas ini"], 403);
        }
    
        // Simpan tugas
        $stmt = $db->prepare("INSERT INTO assignments (class_id, title, description, due_date) VALUES (?, ?, ?, ?)");
        $stmt->execute([$classId, $title, $description, $due_date]);
    
        Utility::jsonResponse(["message" => "Tugas berhasil dibuat"]);
    }
    
    public static function listAssignments($classId) {
        AuthMiddleware::checkAuth(); // Semua pengguna login boleh akses
    
        $db = Utility::getDatabase();
    
        $stmt = $db->prepare("
            SELECT id, title, description, due_date, created_at 
            FROM assignments 
            WHERE class_id = ? 
            ORDER BY due_date ASC
        ");
        $stmt->execute([$classId]);
        $assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        Utility::jsonResponse([
            "class_id" => $classId,
            "assignments" => $assignments
        ]);
    }

    public static function submitAssignment($assignmentId) {
        AuthMiddleware::checkAuth();
        RoleHelper::allowRoles(['student']);
    
        $input = json_decode(file_get_contents("php://input"), true);
        $file_url = $input['file_url'] ?? null;
    
        if (!$file_url) {
            Utility::jsonResponse(["message" => "URL file tugas wajib diisi"], 400);
        }
    
        $user = Utility::getAuthUser();
        $db = Utility::getDatabase();
    
        // Ambil student_id dari user
        $stmt = $db->prepare("SELECT id FROM students WHERE user_id = ?");
        $stmt->execute([$user->id]);
        $student = $stmt->fetch();
    
        if (!$student) {
            Utility::jsonResponse(["message" => "Siswa tidak ditemukan"], 403);
        }
    
        $student_id = $student['id'];
    
        // Cek apakah sudah submit sebelumnya
        $check = $db->prepare("SELECT id FROM submissions WHERE assignment_id = ? AND student_id = ?");
        $check->execute([$assignmentId, $student_id]);
        if ($check->fetch()) {
            Utility::jsonResponse(["message" => "Kamu sudah mengumpulkan tugas ini"], 409);
        }
    
        $stmt = $db->prepare("INSERT INTO submissions (assignment_id, student_id, file_url) VALUES (?, ?, ?)");
        $stmt->execute([$assignmentId, $student_id, $file_url]);
    
        Utility::jsonResponse(["message" => "Tugas berhasil dikumpulkan"]);
    }
    
    public static function listSubmissions($assignmentId) {
        AuthMiddleware::checkAuth();
        RoleHelper::allowRoles(['teacher']);
    
        $user = Utility::getAuthUser();
        $db = Utility::getDatabase();
    
        // Cek apakah assignment ini milik guru
        $stmt = $db->prepare("
            SELECT a.class_id 
            FROM assignments a
            JOIN classes c ON a.class_id = c.id
            JOIN teachers t ON c.teacher_id = t.id
            WHERE a.id = ? AND t.user_id = ?
        ");
        $stmt->execute([$assignmentId, $user->id]);
        $assignment = $stmt->fetch();
    
        if (!$assignment) {
            Utility::jsonResponse(["message" => "Tugas tidak ditemukan atau Anda tidak memiliki akses"], 403);
        }
    
        // Ambil data submissions
        $stmt = $db->prepare("
            SELECT 
                u.name AS student_name,
                u.email AS student_email,
                s.id AS submission_id,
                s.file_url,
                s.submitted_at,
                g.score,
                g.feedback
            FROM submissions s
            JOIN students st ON s.student_id = st.id
            JOIN users u ON st.user_id = u.id
            LEFT JOIN grades g ON s.id = g.submission_id
            WHERE s.assignment_id = ?
            ORDER BY s.submitted_at ASC
        ");
        $stmt->execute([$assignmentId]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        Utility::jsonResponse([
            "assignment_id" => $assignmentId,
            "submissions" => $results
        ]);
    }
    
    public static function gradeSubmission($submissionId) {
        AuthMiddleware::checkAuth();
        RoleHelper::allowRoles(['teacher']);
    
        $input = json_decode(file_get_contents("php://input"), true);
        $score = $input['score'] ?? null;
        $feedback = $input['feedback'] ?? '';
    
        if ($score === null || !is_numeric($score)) {
            Utility::jsonResponse(["message" => "Nilai harus berupa angka"], 400);
        }
    
        $user = Utility::getAuthUser();
        $db = Utility::getDatabase();
    
        // Pastikan guru memang pemilik submission → melalui assignment → melalui kelas
        $stmt = $db->prepare("
            SELECT a.id FROM submissions s
            JOIN assignments a ON s.assignment_id = a.id
            JOIN classes c ON a.class_id = c.id
            JOIN teachers t ON c.teacher_id = t.id
            WHERE s.id = ? AND t.user_id = ?
        ");
        $stmt->execute([$submissionId, $user->id]);
        $valid = $stmt->fetch();
    
        if (!$valid) {
            Utility::jsonResponse(["message" => "Anda tidak berhak menilai submission ini"], 403);
        }
    
        // Cek apakah sudah ada penilaian sebelumnya
        $stmt_check = $db->prepare("SELECT id FROM grades WHERE submission_id = ?");
        $stmt_check->execute([$submissionId]);
        $existing = $stmt_check->fetch();
    
        if ($existing) {
            // Update existing grade
            $stmt = $db->prepare("UPDATE grades SET score = ?, feedback = ?, graded_at = NOW() WHERE submission_id = ?");
            $stmt->execute([$score, $feedback, $submissionId]);
        } else {
            // Insert new grade
            $stmt = $db->prepare("INSERT INTO grades (submission_id, score, feedback) VALUES (?, ?, ?)");
            $stmt->execute([$submissionId, $score, $feedback]);
        }
    
        Utility::jsonResponse(["message" => "Penilaian berhasil disimpan"]);
    }
    
}
