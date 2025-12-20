<?php
// C:\xampp\htdocs\PFA\transpori\dashuser\ajax\like.php
require_once '../database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $id = $input['id'] ?? 0;
    $type = $input['type'] ?? 'experience';
    $user_id = $_SESSION['user_id'] ?? 0;
    
    if ($user_id && $id) {
        try {
            // Check if already liked
            $stmt = $conn->prepare("SELECT id FROM likes WHERE member_id = ? AND target_type = ? AND target_id = ?");
            $stmt->execute([$user_id, $type, $id]);
            
            if ($stmt->rowCount() > 0) {
                // Unlike
                $stmt = $conn->prepare("DELETE FROM likes WHERE member_id = ? AND target_type = ? AND target_id = ?");
                $stmt->execute([$user_id, $type, $id]);
                
                // Decrease count
                $conn->prepare("UPDATE {$type}s SET likes_count = likes_count - 1 WHERE id = ?")->execute([$id]);
            } else {
                // Like
                $stmt = $conn->prepare("INSERT INTO likes (member_id, target_type, target_id) VALUES (?, ?, ?)");
                $stmt->execute([$user_id, $type, $id]);
                
                // Increase count
                $conn->prepare("UPDATE {$type}s SET likes_count = likes_count + 1 WHERE id = ?")->execute([$id]);
            }
            
            // Get new count
            $stmt = $conn->prepare("SELECT likes_count FROM {$type}s WHERE id = ?");
            $stmt->execute([$id]);
            $newCount = $stmt->fetchColumn();
            
            echo json_encode(['success' => true, 'newCount' => $newCount]);
            
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid request']);
    }
}
?>