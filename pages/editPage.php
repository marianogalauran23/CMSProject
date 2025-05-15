<?php
include "../components/db.php";
session_start();

$page_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT title, layout FROM pages WHERE id = ? AND author_id = ?");
$stmt->bind_param("ii", $page_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Page not found or unauthorized access");
}

$page = $result->fetch_assoc();
$layout_data = $page['layout'] ? json_decode($page['layout'], true) : [];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?php $page['title'] ?></title>
    <link rel="stylesheet" href="../css/editor.css">
</head>

<body>
    <div class="sidebar">
        <h3>Elements</h3>
        <div class="draggable" data-type="text">Text</div>
        <div class="draggable" data-type="image">Image</div>
    </div>

    <div class="main">
        <div class="work-area" id="workArea">
            <div class="grid-lines"></div>
            <div class="elements-container" id="elementsContainer"></div>
        </div>
        <div class="properties-panel" id="propertiesPanel">
            <h3>Properties</h3>
            <div id="propertyFields"></div>
        </div>
    </div>

    <script src="../javascript/editor.js"></script>
</body>

</html>