<?php
include "../components/db.php";

$data = json_decode(file_get_contents("php://input"), true);

$name = $data['name'];
$email = $data['email'];
$facebook_id = $data['facebook_id'];

$query = "SELECT * FROM users WHERE facebook_id = '$facebook_id' OR email = '$email'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);
    echo json_encode(['success' => true, 'message' => 'User logged in successfully']);
} else {
    $insertQuery = "INSERT INTO users (name, email, facebook_id) VALUES ('$name', '$email', '$facebook_id')";
    if (mysqli_query($conn, $insertQuery)) {
        echo json_encode(['success' => true, 'message' => 'User registered successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error registering user']);
    }
}
?>
