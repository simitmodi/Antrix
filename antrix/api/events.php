<?php
session_start();
require_once '../includes/db.php';
header('Content-Type: application/json');

$action = $_REQUEST['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if ($action === 'list') {
        try {
            $stmt = $pdo->query("SELECT id, title, description, event_type, DATE_FORMAT(event_date, '%Y-%m-%d %H:%i') as event_date, location, image_path FROM events WHERE is_approved = 1 ORDER BY event_date ASC");
            echo json_encode($stmt->fetchAll());
        } catch (\PDOException $e) {
            echo json_encode(['error' => 'Database error']);
        }
        exit;
    } elseif ($action === 'search' || $action === 'news_search') {
        $q = '%' . ($_GET['q'] ?? '') . '%';
        try {
            if ($action === 'search') {
                $stmt = $pdo->prepare("SELECT id, title, description, event_type, DATE_FORMAT(event_date, '%Y-%m-%d %H:%i') as event_date, location, image_path FROM events WHERE is_approved = 1 AND title LIKE ? ORDER BY event_date ASC");
            } else {
                $stmt = $pdo->prepare("SELECT id, title, content as excerpt, image_path, DATE_FORMAT(published_at, '%b %d, %Y') as date, source_url FROM news WHERE title LIKE ? ORDER BY published_at DESC");
            }
            $stmt->execute([$q]);
            echo json_encode($stmt->fetchAll());
        } catch (\PDOException $e) {
            echo json_encode(['error' => 'Database error']);
        }
        exit;
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'interest') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            try {
                $stmt = $pdo->prepare("UPDATE events SET interest_count = interest_count + 1 WHERE id = ?");
                if ($stmt->execute([$id])) {
                    $stmt2 = $pdo->prepare("SELECT interest_count FROM events WHERE id = ?");
                    $stmt2->execute([$id]);
                    $count = $stmt2->fetchColumn();
                    echo json_encode(['success' => true, 'new_count' => $count]);
                    exit;
                }
            } catch (\PDOException $e) {
                // fall through
            }
        }
        echo json_encode(['success' => false]);
        exit;
    }
}

echo json_encode(['error' => 'Invalid action']);
?>
