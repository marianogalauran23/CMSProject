<?php
session_start();
include "../components/db.php";

// 1. Validate session first
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    die(json_encode(['success' => false, 'error' => 'Unauthorized']));
}

$user_id = $_SESSION['user_id'];
$page_id = (int) $_GET['page_id'];

// 2. Sanitize and validate inputs
if (!isset($_FILES['image'])) {
    http_response_code(400);
    die(json_encode(['success' => false, 'error' => 'No file uploaded']));
}

// 3. Create safe directory structure
$base_dir = realpath($_SERVER['DOCUMENT_ROOT']) . "/uploads";
$target_dir = "$base_dir/$user_id/$page_id/";

if (!file_exists($target_dir)) {
    mkdir($target_dir, 0755, true); // Safer permissions
}

// 4. Sanitize filename
$original_name = preg_replace('/[^\w\.-]/', '_', $_FILES['image']['name']);
$filename = uniqid() . '_' . $original_name;
$target_file = $target_dir . $filename;

// 5. Validate file type
$allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
$detected_type = mime_content_type($_FILES['image']['tmp_name']);

if (!in_array($detected_type, $allowed_types)) {
    die(json_encode(['success' => false, 'error' => 'Invalid file type']));
}

// 6. Move file and return proper URL
if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
    // Create URL path relative to web root
    $web_path = "/uploads/$user_id/$page_id/$filename";

    echo json_encode([
        'success' => true,
        'fullUrl' => $web_path, // This is what the client needs
        'filename' => $filename
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'File upload failed'
    ]);
}
?>