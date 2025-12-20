<?php
// C:\xampp\htdocs\PFA\transpori\dashuser\ajax\add_comment.php
session_start();
require_once '../database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $experience_id = $_POST['experience_id'] ?? 0;
    $comment = $_POST['comment'] ?? '';
    $user_id = $_SESSION['user_id'] ?? 0;
    
    if (!$user_id) {
        echo json_encode(['success' => false, 'error' => 'Please log in']);
        exit;
    }
    
    if (empty($comment) || empty($experience_id)) {
        echo json_encode(['success' => false, 'error' => 'Please fill in all fields']);
        exit;
    }
    
    try {
        // Insert comment
        $stmt = $conn->prepare("
            INSERT INTO comments (member_id, content, parent_type, parent_id) 
            VALUES (?, ?, 'experience', ?)
        ");
        $stmt->execute([$user_id, $comment, $experience_id]);
        
        // Update comments count
        $stmt = $conn->prepare("
            UPDATE experiences 
            SET comments_count = comments_count + 1 
            WHERE id = ?
        ");
        $stmt->execute([$experience_id]);
        
        echo json_encode(['success' => true]);
        
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}