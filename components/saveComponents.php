<?php
header('Content-Type: application/json');
require_once '../components/db.php'; // Corrected path

$data = json_decode(file_get_contents('php://input'), true);

try {
    // Validate input
    if (!isset($data['user_id'], $data['page_id'], $data['components'])) {
        throw new Exception('Missing required fields in request');
    }

    $user_id = (int) $data['user_id'];
    $page_id = (int) $data['page_id'];
    $componentsJson = json_encode($data['components']);

    // Validate JSON encoding
    if ($componentsJson === false) {
        throw new Exception('Failed to encode components data');
    }

    // Update database using MySQLi
    $stmt = $conn->prepare("
        UPDATE pages 
        SET components_data = ?, 
            version = version + 1 
        WHERE id = ? AND author_id = ?
    ");

    if (!$stmt) {
        throw new Exception('Database prepare error: ' . $conn->error);
    }

    $stmt->bind_param("sii", $componentsJson, $page_id, $user_id);

    if (!$stmt->execute()) {
        throw new Exception('Database update error: ' . $stmt->error);
    }

    // Ensure backup directory exists
    $backupDir = "../pages/uploads/$user_id";
    if (!file_exists($backupDir)) {
        if (!mkdir($backupDir, 0755, true)) {
            throw new Exception('Failed to create backup directory');
        }
    }

    // Save backup file
    $backupPath = "$backupDir/components_{$page_id}_v" . time() . ".json";
    if (!file_put_contents($backupPath, $componentsJson)) {
        throw new Exception('Failed to write backup file');
    }

    echo json_encode([
        'success' => true,
        'message' => 'Components saved successfully',
        'affected_rows' => $stmt->affected_rows
    ]);

} catch (Exception $e) {
    error_log('SaveComponents Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'request_data' => $data // For debugging purposes only
    ]);
}

// Close connection
if (isset($stmt))
    $stmt->close();
$conn->close();
?>