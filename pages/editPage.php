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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page['title']); ?> Editor</title>
    <link rel="stylesheet" href="../css/editor.css">
</head>

<body>
    <div class="editor-container">
        <div class="sidebar" id="elementsSidebar">
            <div class="sidebar-header">
                <h3>Elements</h3>
                <button class="collapse-btn" onclick="toggleSidebar()">☰</button>
            </div>
            <div class="elements-list">
                <div class="element-item" draggable="true" data-type="container">Container</div>
                <div class="element-item" draggable="true" data-type="text">Text Block</div>
                <div class="element-item" draggable="true" data-type="image">Image</div>
                <div class="element-item" draggable="true" data-type="button">Button</div>
            </div>
        </div>

        <div class="work-area" id="workArea">
            <div class="grid-lines"></div>
            <div class="elements-container" id="elementsContainer"></div>
        </div>

        <div class="properties-panel" id="propertiesPanel">
            <div class="panel-header">
                <h3>Properties</h3>
            </div>
            <div class="properties-form" id="propertiesForm">
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@interactjs/interactjs/dist/interact.min.js"></script>
    <script src="../js/editor.js"></script>
    <script>
        const initialLayout = <?php echo json_encode($layout_data); ?>;
        const currentPageId = <?php echo $page_id; ?>;
    </script>
</body>

</html>