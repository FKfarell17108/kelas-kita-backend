<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../helpers/RoleHelper.php';
require_once __DIR__ . '/../helpers/Utility.php'; // Add this line to include the Utility class

use App\Middleware\AuthMiddleware;
use App\Helpers\RoleHelper;
use App\Helpers\Utility; // Add this line to use the Utility class

class ModuleController
{
    public static function addModule($bootcampId)
    {
        AuthMiddleware::checkAuth();
        RoleHelper::allowRoles(['teacher']);

        $input = json_decode(file_get_contents("php://input"), true);
        $title = $input['title'] ?? null;
        $content = $input['content'] ?? '';

        if (!$title) {
            http_response_code(400);
            echo json_encode(["message" => "Judul modul wajib diisi"]);
            return;
        }

        try {
            $db = (new Database())->connect();
            $stmt = $db->prepare("INSERT INTO bootcamp_modules (bootcamp_id, title, content) VALUES (?, ?, ?)");
            $stmt->execute([$bootcampId, $title, $content]);

            echo json_encode(["message" => "Modul berhasil ditambahkan"]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["message" => "Gagal menambahkan modul: " . $e->getMessage()]);
        }
    }

    public static function getModules($bootcampId)
    {
        try {
            $db = (new Database())->connect();
            $stmt = $db->prepare("SELECT * FROM bootcamp_modules WHERE bootcamp_id = ?");
            $stmt->execute([$bootcampId]);
            $modules = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(["modules" => $modules]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["message" => "Gagal mengambil modul: " . $e->getMessage()]);
        }
    }

    public static function completeModule($bootcampId, $moduleId)
    {
        AuthMiddleware::checkAuth();
        RoleHelper::allowRoles(['student']);

        // Pastikan $GLOBALS['auth_user'] sudah di-set oleh middleware
        if (!isset($GLOBALS['auth_user']) || !isset($GLOBALS['auth_user']->uid)) {
            http_response_code(401); // Unauthorized
            echo json_encode(["message" => "User tidak terautentikasi."]);
            return;
        }

        $student_id = $GLOBALS['auth_user']->uid;
        $db = (new Database())->connect();

        try {
            // Cek apakah sudah pernah diselesaikan
            $check = $db->prepare("SELECT * FROM completed_module_logs WHERE student_id = ? AND module_id = ?");
            $check->execute([$student_id, $moduleId]);

            if ($check->fetch()) {
                http_response_code(409);
                echo json_encode(["message" => "Modul ini sudah ditandai selesai sebelumnya."]);
                return;
            }

            // Insert log baru
            $insert = $db->prepare("INSERT INTO completed_module_logs (student_id, module_id, bootcamp_id) VALUES (?, ?, ?)");
            $insert->execute([$student_id, $moduleId, $bootcampId]);

            // Tambahkan +1 ke kolom cache completed_modules
            $update = $db->prepare("UPDATE bootcamp_participants SET completed_modules = completed_modules + 1 WHERE student_id = ? AND bootcamp_id = ?");
            $update->execute([$student_id, $bootcampId]);

            echo json_encode(["message" => "Modul berhasil ditandai selesai."]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["message" => "Gagal menyelesaikan modul: " . $e->getMessage()]);
        }
    }

    public static function getStudentProgress($bootcampId, $studentId)
    {
        try {
            $db = (new Database())->connect();

            // Dapatkan total modul dalam bootcamp
            $totalModulesStmt = $db->prepare("SELECT COUNT(*) AS total FROM bootcamp_modules WHERE bootcamp_id = ?");
            $totalModulesStmt->execute([$bootcampId]);
            $totalModulesResult = $totalModulesStmt->fetch(PDO::FETCH_ASSOC);
            $totalModules = $totalModulesResult['total'];

            // Dapatkan jumlah modul yang diselesaikan oleh siswa
            $completedModulesStmt = $db->prepare("SELECT completed_modules FROM bootcamp_participants WHERE bootcamp_id = ? AND student_id = ?");
            $completedModulesStmt->execute([$bootcampId, $studentId]);
            $completedModulesResult = $completedModulesStmt->fetch(PDO::FETCH_ASSOC);
            $completedModules = $completedModulesResult['completed_modules'];

            // Hitung persentase
            if ($totalModules > 0) {
                $percentage = ($completedModules / $totalModules) * 100;
            } else {
                $percentage = 0; // Atau nilai lain yang sesuai jika tidak ada modul
            }

            echo json_encode(["percentage" => $percentage]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["message" => "Gagal mengambil progress: " . $e->getMessage()]);
        }
    }

    public static function getProgress($bootcampId)
    {
        AuthMiddleware::checkAuth();
        RoleHelper::allowRoles(['student']);

        $student_id = $GLOBALS['auth_user']->id;
        $db = (new Database())->connect();

        try {
            // Hitung jumlah modul dalam bootcamp
            $stmt_total = $db->prepare("SELECT COUNT(*) FROM bootcamp_modules WHERE bootcamp_id = ?");
            $stmt_total->execute([$bootcampId]);
            $total_modules = $stmt_total->fetchColumn();

            // Hitung jumlah modul yang sudah diselesaikan siswa ini
            $stmt_done = $db->prepare("SELECT COUNT(*) FROM completed_module_logs WHERE bootcamp_id = ? AND student_id = ?");
            $stmt_done->execute([$bootcampId, $student_id]);
            $completed_modules = $stmt_done->fetchColumn();

            $progress = $total_modules > 0 ? round(($completed_modules / $total_modules) * 100, 2) : 0;

            echo json_encode([
                "bootcamp_id" => $bootcampId,
                "student_id" => $student_id,
                "completed_modules" => (int) $completed_modules,
                "total_modules" => (int) $total_modules,
                "progress_percent" => $progress
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["message" => "Gagal mengambil progress: " . $e->getMessage()]);
        }
    }

    public static function getAllProgress($bootcampId)
    {
        AuthMiddleware::checkAuth();
        RoleHelper::allowRoles(['teacher']);

        $db = (new Database())->connect();

        // Hitung total modul di bootcamp
        $stmt_total = $db->prepare("SELECT COUNT(*) FROM bootcamp_modules WHERE bootcamp_id = ?");
        $stmt_total->execute([$bootcampId]);
        $total_modules = $stmt_total->fetchColumn();

        // Ambil semua peserta
        $stmt = $db->prepare("
            SELECT u.id AS user_id, u.name, u.email, s.id AS student_id
            FROM bootcamp_participants bp
            JOIN students s ON bp.student_id = s.id
            JOIN users u ON s.user_id = u.id
            WHERE bp.bootcamp_id = ?
        ");
        $stmt->execute([$bootcampId]);
        $participants = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $progressList = [];

        foreach ($participants as $participant) {
            $student_id = $participant['student_id'];

            $stmt_done = $db->prepare("
                SELECT COUNT(*) FROM completed_module_logs 
                WHERE bootcamp_id = ? AND student_id = ?
            ");
            $stmt_done->execute([$bootcampId, $student_id]);
            $completed = $stmt_done->fetchColumn();

            $progress_percent = $total_modules > 0
                ? round(($completed / $total_modules) * 100, 2)
                : 0;

            $progressList[] = [
                "student_name" => $participant['name'],
                "student_email" => $participant['email'],
                "completed_modules" => (int) $completed,
                "total_modules" => (int) $total_modules,
                "progress_percent" => $progress_percent
            ];
        }

        echo json_encode([
            "bootcamp_id" => $bootcampId,
            "participants" => $progressList
        ]);
    }

    public static function getIncompleteModule($bootcampId, $moduleId)
    {
        AuthMiddleware::checkAuth();
        RoleHelper::allowRoles(['teacher']);

        $db = (new Database())->connect();

        // Ambil semua peserta bootcamp
        $stmt = $db->prepare("
            SELECT u.name, u.email, s.id AS student_id
            FROM bootcamp_participants bp
            JOIN students s ON bp.student_id = s.id
            JOIN users u ON s.user_id = u.id
            WHERE bp.bootcamp_id = ?
        ");
        $stmt->execute([$bootcampId]);
        $participants = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $incomplete = [];

        foreach ($participants as $p) {
            $stmt2 = $db->prepare("
                SELECT COUNT(*) FROM completed_module_logs 
                WHERE module_id = ? AND student_id = ?
            ");
            $stmt2->execute([$moduleId, $p['student_id']]);
            $completed = $stmt2->fetchColumn();

            if ($completed == 0) {
                $incomplete[] = [
                    "name" => $p['name'],
                    "email" => $p['email']
                ];
            }
        }

        echo json_encode([
            "bootcamp_id" => $bootcampId,
            "module_id" => $moduleId,
            "incomplete_students" => $incomplete
        ]);
    }

    public static function exportProgressCSV($bootcampId)
    {
        AuthMiddleware::checkAuth();
        RoleHelper::allowRoles(['teacher']);

        $db = (new Database())->connect();

        // Hitung total modul
        $stmt_total = $db->prepare("SELECT COUNT(*) FROM bootcamp_modules WHERE bootcamp_id = ?");
        $stmt_total->execute([$bootcampId]);
        $total_modules = $stmt_total->fetchColumn();

        // Ambil peserta
        $stmt = $db->prepare("
            SELECT u.name, u.email, s.id AS student_id
            FROM bootcamp_participants bp
            JOIN students s ON bp.student_id = s.id
            JOIN users u ON s.user_id = u.id
            WHERE bp.bootcamp_id = ?
        ");
        $stmt->execute([$bootcampId]);
        $participants = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Header CSV
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename="bootcamp_progress.csv"');

        $output = fopen("php://output", "w");
        fputcsv($output, ['Name', 'Email', 'Completed Modules', 'Total Modules', 'Progress %']);

        foreach ($participants as $p) {
            $stmt_done = $db->prepare("
                SELECT COUNT(*) FROM completed_module_logs 
                WHERE bootcamp_id = ? AND student_id = ?
            ");
            $stmt_done->execute([$bootcampId, $p['student_id']]);
            $completed = $stmt_done->fetchColumn();

            $percent = $total_modules > 0 ? round(($completed / $total_modules) * 100, 2) : 0;

            fputcsv($output, [$p['name'], $p['email'], $completed, $total_modules, $percent]);
        }

        fclose($output);
    }

    public static function getModuleQuestions($moduleId)
    {
        AuthMiddleware::checkAuth(); // Semua user login boleh lihat soal

        $db = Utility::getDatabase();

        // Ambil semua soal dari modul
        $stmt = $db->prepare("SELECT id, question FROM bootcamp_questions WHERE module_id = ?");
        $stmt->execute([$moduleId]);
        $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $result = [];

        foreach ($questions as $q) {
            // Ambil semua pilihan jawaban untuk setiap soal
            $stmt2 = $db->prepare("SELECT id, answer_text FROM bootcamp_answers WHERE question_id = ?");
            $stmt2->execute([$q['id']]);
            $answers = $stmt2->fetchAll(PDO::FETCH_ASSOC);

            $result[] = [
                "question_id" => $q['id'],
                "question" => $q['question'],
                "answers" => $answers
            ];
        }

        Utility::jsonResponse([
            "module_id" => $moduleId,
            "questions" => $result
        ]);
    }

    public static function answerQuestion($questionId)
    {
        AuthMiddleware::checkAuth();
        RoleHelper::allowRoles(['student']);

        $input = json_decode(file_get_contents("php://input"), true);
        $selected_answer_id = $input['answer_id'] ?? null;

        if (!$selected_answer_id) {
            Utility::jsonResponse(["message" => "Jawaban harus dipilih"], 400);
        }

        $db = Utility::getDatabase();
        $user = Utility::getAuthUser();

        // Ambil student_id dari user
        $stmt = $db->prepare("SELECT id FROM students WHERE user_id = ?");
        $stmt->execute([$user->id]);
        $student = $stmt->fetch();

        if (!$student) {
            Utility::jsonResponse(["message" => "Siswa tidak ditemukan"], 403);
        }

        $student_id = $student['id'];

        // Cek apakah sudah menjawab
        $stmt = $db->prepare("SELECT id FROM bootcamp_user_answers WHERE question_id = ? AND student_id = ?");
        $stmt->execute([$questionId, $student_id]);
        if ($stmt->fetch()) {
            Utility::jsonResponse(["message" => "Soal ini sudah kamu jawab sebelumnya"], 409);
        }

        // Simpan jawaban
        $stmt = $db->prepare("INSERT INTO bootcamp_user_answers (student_id, question_id, selected_answer_id) VALUES (?, ?, ?)");
        $stmt->execute([$student_id, $questionId, $selected_answer_id]);

        // Cek apakah jawaban benar
        $stmt = $db->prepare("SELECT is_correct FROM bootcamp_answers WHERE id = ?");
        $stmt->execute([$selected_answer_id]);
        $answer = $stmt->fetch();

        if (!$answer) {
            Utility::jsonResponse(["message" => "Jawaban tidak valid"], 400);
        }

        $correct = (bool) $answer['is_correct'];

        Utility::jsonResponse([
            "message" => $correct ? "Jawaban kamu benar!" : "Jawaban kamu salah.",
            "correct" => $correct
        ]);
    }

    public static function getModuleQuizResult($moduleId)
    {
        AuthMiddleware::checkAuth();
        RoleHelper::allowRoles(['student']);

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

        // Hitung total soal
        $stmt = $db->prepare("SELECT COUNT(*) FROM bootcamp_questions WHERE module_id = ?");
        $stmt->execute([$moduleId]);
        $total_questions = $stmt->fetchColumn();

        // Ambil semua jawaban user di modul ini
        $stmt = $db->prepare("
        SELECT a.is_correct
        FROM bootcamp_user_answers ua
        JOIN bootcamp_questions q ON ua.question_id = q.id
        JOIN bootcamp_answers a ON ua.selected_answer_id = a.id
        WHERE q.module_id = ? AND ua.student_id = ?
    ");
        $stmt->execute([$moduleId, $student_id]);
        $answers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $answered = count($answers);
        $correct = count(array_filter($answers, fn($a) => $a['is_correct']));

        $score_percent = $total_questions > 0 ? round(($correct / $total_questions) * 100, 2) : 0;

        Utility::jsonResponse([
            "module_id" => $moduleId,
            "total_questions" => (int) $total_questions,
            "answered" => (int) $answered,
            "correct" => (int) $correct,
            "incorrect" => (int) ($answered - $correct),
            "score_percent" => $score_percent
        ]);
    }
}
