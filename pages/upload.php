<?php
session_start();
include "../components/db.php";

$user_id = $_SESSION['user_id'];
$page_id = $_GET['page_id'];
$target_dir = "../pages/uploads/$user_id/assets/";

if (!file_exists($target_dir)) {
    mkdir($target_dir, 0777, true);
}

$filename = uniqid() . '_' . basename($_FILES["image"]["name"]);
$target_file = $target_dir . $filename;

if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
    echo json_encode([
        'success' => true,
        'filename' => $filename,
        'user_id' => $user_id
    ]);
} else {
    echo json_encode(['success' => false]);
}
?>