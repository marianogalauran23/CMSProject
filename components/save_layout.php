<?php
include "./db.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $data = json_decode(file_get_contents('php://input'), true);

    $page_id = (int) $data['page_id'];
    $layout = json_encode($data['layout']);
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("UPDATE pages SET layout = ? 
                          WHERE id = ? AND author_id = ?");
    $stmt->bind_param("sii", $layout, $page_id, $user_id);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error']);
    }
}