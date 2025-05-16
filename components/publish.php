<?php
session_start();
require './db.php'; // Ensure correct path

// Add security headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: ' . ($_SERVER['HTTP_ORIGIN'] ?? '*'));
header('Access-Control-Allow-Credentials: true');

// Rest of your publish.php code...

// Enable error reporting
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/publish_errors.log');

header('Content-Type: application/json');

// Validate session
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    die(json_encode(['success' => false, 'error' => 'Unauthorized access']));
}

// Validate inputs
if (!isset($_GET['page_id']) || !ctype_digit($_GET['page_id'])) {
    http_response_code(400);
    die(json_encode(['success' => false, 'error' => 'Invalid page ID']));
}

$page_id = (int) $_GET['page_id'];
$user_id = $_SESSION['user_id'];

// Slug generation with collision check
function generateSlug($conn)
{
    $attempts = 0;
    do {
        if ($attempts++ > 5) {
            throw new Exception('Slug generation failed after 5 attempts');
        }

        $slug = bin2hex(random_bytes(4)); // 8-character slug
        $stmt = $conn->prepare("SELECT id FROM pages WHERE slug = ?");
        $stmt->bind_param("s", $slug);
        $stmt->execute();
        $result = $stmt->get_result();
    } while ($result->num_rows > 0);

    return $slug;
}

try {
    // Start transaction
    $conn->begin_transaction();

    // Verify page ownership
    $stmt = $conn->prepare("
        SELECT components_data 
        FROM pages 
        WHERE id = ? 
        AND author_id = ?
        FOR UPDATE
    ");
    $stmt->bind_param("ii", $page_id, $user_id);
    $stmt->execute();

    if ($stmt->errno) {
        throw new Exception("Database error: " . $stmt->error);
    }

    $page = $stmt->get_result()->fetch_assoc();

    if (!$page) {
        throw new Exception("Page not found or access denied");
    }

    // Generate unique slug
    $slug = generateSlug($conn);

    // Update publication status
    $update = $conn->prepare("
        UPDATE pages 
        SET published = TRUE,
            slug = ?,
            published_at = NOW()
        WHERE id = ?
    ");
    $update->bind_param("si", $slug, $page_id);
    $update->execute();

    if ($update->affected_rows === 0) {
        throw new Exception("No rows updated - publication failed");
    }

    $conn->commit();

    // Return successful response
    echo json_encode([
        'success' => true,
        'slug' => $slug,
        'url' => "/CMSproject/public/{$slug}"  // Add project directory
    ]);

} catch (Exception $e) {
    $conn->rollback();
    error_log("Publish Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Publication failed: ' . $e->getMessage()
    ]);
}
?>