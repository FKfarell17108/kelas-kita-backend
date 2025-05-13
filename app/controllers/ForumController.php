<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../helpers/Utility.php';

use App\Middleware\AuthMiddleware;
use App\Helpers\Utility;

class ForumController {
    public static function createPost() {
        AuthMiddleware::checkAuth();

        $input = json_decode(file_get_contents("php://input"), true);
        $title = $input['title'] ?? null;
        $content = $input['content'] ?? null;

        if (!$title || !$content) {
            Utility::jsonResponse(["message" => "Judul dan isi harus diisi"], 400);
        }

        $user = Utility::getAuthUser();
        $db = Utility::getDatabase();

        $stmt = $db->prepare("INSERT INTO forum_posts (user_id, title, content) VALUES (?, ?, ?)");
        $stmt->execute([$user->id, $title, $content]);

        Utility::jsonResponse(["message" => "Postingan berhasil dibuat"]);
    }

    public static function listPosts() {
        $db = Utility::getDatabase();
        $stmt = $db->query("
            SELECT p.id, p.title, p.content, p.created_at, u.name AS author
            FROM forum_posts p
            JOIN users u ON p.user_id = u.id
            ORDER BY p.created_at DESC
        ");
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        Utility::jsonResponse(["posts" => $posts]);
    }

    public static function addComment($postId) {
        AuthMiddleware::checkAuth();

        $input = json_decode(file_get_contents("php://input"), true);
        $content = $input['content'] ?? null;

        if (!$content) {
            Utility::jsonResponse(["message" => "Komentar tidak boleh kosong"], 400);
        }

        $user = Utility::getAuthUser();
        $db = Utility::getDatabase();

        $stmt = $db->prepare("INSERT INTO forum_comments (post_id, user_id, content) VALUES (?, ?, ?)");
        $stmt->execute([$postId, $user->id, $content]);

        Utility::jsonResponse(["message" => "Komentar berhasil ditambahkan"]);
    }

    public static function listComments($postId) {
        $db = Utility::getDatabase();

        $stmt = $db->prepare("
            SELECT c.id, c.content, c.created_at, u.name AS commenter
            FROM forum_comments c
            JOIN users u ON c.user_id = u.id
            WHERE c.post_id = ?
            ORDER BY c.created_at ASC
        ");
        $stmt->execute([$postId]);
        $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        Utility::jsonResponse(["comments" => $comments]);
    }
}
