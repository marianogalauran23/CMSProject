<?php
header('Content-Type: application/json');
require_once '../components/db.php';

try {
    if (!isset($_GET['page_id'])) {
        throw new Exception('Missing page ID');
    }

    $page_id = (int) $_GET['page_id'];
    $stmt = $conn->prepare("
        SELECT components_data 
        FROM pages 
        WHERE id = ?
    ");
    $stmt->bind_param("i", $page_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception('Page not found');
    }

    $data = $result->fetch_assoc();
    echo json_encode(json_decode($data['components_data']));

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}