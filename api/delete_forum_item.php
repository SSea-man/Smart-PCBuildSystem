<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

if (!is_logged_in()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'You must be logged in.']);
    exit;
}

$submitted_token = $_POST['csrf_token'] ?? '';
$session_token = $_SESSION['csrf_token'] ?? '';
if (!hash_equals($session_token, $submitted_token)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Invalid CSRF token. Please refresh the page and try again.']);
    exit;
}

$user      = get_auth_user();
$user_id   = (int)($user['id'] ?? 0);
$user_role = $user['role'] ?? 'user';

$type = trim($_POST['type'] ?? '');
$id   = (int)($_POST['id'] ?? 0);

if (!in_array($type, ['post', 'comment']) || $id <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid parameters.']);
    exit;
}

$is_privileged = in_array($user_role, ['admin', 'moderator']);

try {
    $db = get_db();

    if ($type === 'post') {
        $post = db_row("SELECT post_id, user_id FROM post WHERE post_id = ?", [$id]);
        if (!$post) {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Post not found.']);
            exit;
        }

        if (!$is_privileged && (int)$post['user_id'] !== $user_id) {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'You do not have permission to delete this post.']);
            exit;
        }

        $stmt = $db->prepare("DELETE FROM post WHERE post_id = ?");
        $stmt->execute([$id]);
        $affected = $stmt->rowCount();

        if ($affected === 0) {
            echo json_encode(['success' => false, 'error' => 'Post could not be deleted (may already be gone).']);
            exit;
        }

        if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
            header('Location: ' . BASE_URL . '/forum.php');
            exit;
        }
        echo json_encode(['success' => true, 'message' => 'Post deleted successfully.']);
        exit;

    } elseif ($type === 'comment') {
        $comment = db_row("SELECT comment_id, user_id FROM comment WHERE comment_id = ?", [$id]);
        if (!$comment) {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Comment not found.']);
            exit;
        }

        if (!$is_privileged && (int)$comment['user_id'] !== $user_id) {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'You do not have permission to delete this comment.']);
            exit;
        }

        $stmt = $db->prepare("DELETE FROM comment WHERE comment_id = ?");
        $stmt->execute([$id]);
        $affected = $stmt->rowCount();

        if ($affected === 0) {
            echo json_encode(['success' => false, 'error' => 'Comment could not be deleted (may already be gone).']);
            exit;
        }

        echo json_encode(['success' => true, 'message' => 'Comment deleted successfully.']);
    }

} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error'   => 'Database error: ' . $e->getMessage()
    ]);
}
