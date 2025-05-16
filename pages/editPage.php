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
    <title><?php echo htmlspecialchars($page['title']); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            display: flex;
            font-family: Arial, sans-serif;
            height: 100vh;
        }

        .sidebar,
        .properties-panel {
            width: 250px;
            background: #f8f9fa;
            border-right: 1px solid #ddd;
            padding: 20px;
            overflow-y: auto;
            transition: all 0.3s ease;
        }

        .sidebar.collapsed {
            width: 0;
            padding: 0;
            overflow: hidden;
        }

        .toggle-sidebar {
            position: absolute;
            top: 10px;
            left: 260px;
            background: #007bff;
            color: white;
            border: none;
            padding: 5px 8px;
            cursor: pointer;
            z-index: 10;
            border-radius: 3px;
        }

        .sidebar h3,
        .properties-panel h3 {
            margin-top: 0;
        }

        .sidebar-category,
        .properties-section {
            margin-bottom: 20px;
        }

        .sidebar-category h4,
        .properties-section h4 {
            margin-bottom: 10px;
            font-size: 16px;
            color: #333;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
        }

        .draggable {
            background: #fff;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 8px 10px;
            margin: 5px 0;
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: grab;
            transition: background 0.2s;
        }

        .draggable:hover {
            background: #e2e6ea;
        }

        .main {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .work-area {
            flex: 1;
            padding: 20px;
            position: relative;
        }

        .elements-container {
            width: 100%;
            height: 600px;
            border: 1px solid #ccc;
            background: #fff;
            position: relative;
        }

        .properties-panel {
            width: 250px;
            background: #f1f1f1;
            border-left: 1px solid #ccc;
        }

        .property-field {
            margin-bottom: 10px;
        }

        .property-field label {
            display: block;
            font-size: 14px;
            margin-bottom: 3px;
        }

        .property-field input {
            width: 100%;
            padding: 6px;
            border: 1px solid #ccc;
            border-radius: 3px;
        }

        #clearAllBtn {
            padding: 8px 10px;
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }

        #clearAllBtn:hover {
            background: #c82333;
        }
    </style>
</head>

<body>

    <!-- Collapsible Sidebar -->
    <div class="sidebar" id="sidebar">
        <h3><i class="fas fa-layer-group"></i> Elements</h3>

        <div class="sidebar-category">
            <h4><i class="fas fa-object-group"></i> Layout</h4>
            <div class="draggable" data-type="container" draggable="true"><i class="fas fa-box"></i> Container</div>
            <div class="draggable" data-type="header" draggable="true"><i class="fas fa-heading"></i> Header</div>
            <div class="draggable" data-type="footer" draggable="true"><i class="fas fa-shoe-prints"></i> Footer</div>
        </div>

        <div class="sidebar-category">
            <h4><i class="fas fa-font"></i> Content</h4>
            <div class="draggable" data-type="text" draggable="true"><i class="fas fa-font"></i> Text</div>
            <div class="draggable" data-type="image" draggable="true"><i class="fas fa-image"></i> Image</div>
            <div class="draggable" data-type="button" draggable="true"><i class="fas fa-square"></i> Button</div>
        </div>

        <div class="sidebar-category">
            <h4><i class="fas fa-pen-square"></i> Forms</h4>
            <div class="draggable" data-type="textbox" draggable="true"><i class="fas fa-i-cursor"></i> Textbox</div>
            <div class="draggable" data-type="textarea" draggable="true"><i class="fas fa-align-left"></i> Textarea
            </div>
            <div class="draggable" data-type="radio" draggable="true"><i class="fas fa-dot-circle"></i> Radio Group
            </div>
            <div class="draggable" data-type="checkbox" draggable="true"><i class="fas fa-check-square"></i> Checkbox
            </div>
            <div class="draggable" data-type="dropdown" draggable="true"><i class="fas fa-caret-down"></i> Dropdown
            </div>
            <div class="draggable" data-type="slider" draggable="true"><i class="fas fa-sliders-h"></i> Slider</div>
        </div>
    </div>

    <button class="toggle-sidebar" onclick="toggleSidebar()">☰</button>

    <div class="main">
        <div class="work-area" id="workArea">
            <div class="elements-container" id="elementsContainer">
                <!-- Elements will be dropped here -->
            </div>
        </div>
    </div>

    <div class="properties-panel" id="propertiesPanel">
        <h3><i class="fas fa-sliders-h"></i> Properties</h3>
        <div id="propertyFields">
            <!-- Properties will show up here -->
        </div>
        <button id="clearAllBtn">Clear All Elements</button>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('collapsed');
        }
    </script>

    <script src="../javascript/editor.js"></script>
</body>

</html>