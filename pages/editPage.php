<?php
include "../components/db.php";
session_start();

// Validate session and permissions
if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access");
}

$page_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$user_id = $_SESSION['user_id'];

// Fetch page data with components
$stmt = $conn->prepare("
    SELECT title, layout, components_data 
    FROM pages 
    WHERE id = ? AND author_id = ?
");
$stmt->bind_param("ii", $page_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Page not found or unauthorized access");
}

$page = $result->fetch_assoc();
$components_data = $page['components_data'] ? json_decode($page['components_data'], true) : [];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($page['title']); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <script>
        // Pass PHP data to JavaScript safely
        const PAGE_ID = <?= json_encode($page_id) ?>;
        const USER_ID = <?= json_encode($user_id) ?>;
        const COMPONENTS_DATA = <?= json_encode($components_data) ?>;
    </script>
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

        #savePage,
        #publishPage {
            padding: 10px 20px;
            font-size: 14px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin: 5px;
        }

        #savePage {
            background-color: #007bff;
            color: white;
        }

        #savePage:hover {
            background-color: #0056b3;
        }

        #publishPage {
            background-color: #28a745;
            color: white;
        }

        #publishPage:hover {
            background-color: #218838;
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
        <button id="savePage" class="btn btn-primary">Save</button>
        <button id="publishPage" class="btn btn-success">Publish</button>
    </div>

    <script>
        // Sidebar toggle function
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('collapsed');
        }

        // Initial load
        document.addEventListener('DOMContentLoaded', () => {
            if (COMPONENTS_DATA && COMPONENTS_DATA.length > 0) {
                loadComponents(COMPONENTS_DATA);
            }
        });

        // Component loader
        function loadComponents(components) {
            components.forEach(component => {
                const el = createElement(component.type, component.position_x, component.position_y);

                Object.entries(component.styles).forEach(([prop, value]) => {
                    el.style[prop] = value;
                });
                el.style.width = `${component.width}px`;
                el.style.height = `${component.height}px`;
                el.style.zIndex = component.z_index || 1;

                // Apply content
                switch (component.type) {
                    case 'button':
                        const button = el.querySelector('button');
                        button.innerText = component.content.text;
                        button.style.backgroundColor = component.styles.backgroundColor;
                        button.style.color = component.styles.color;
                        break;

                    case 'image':
                        const imgDiv = el.querySelector('div');
                        imgDiv.style.backgroundImage = `url(${component.content.src})`;
                        imgDiv.style.backgroundSize = component.styles.backgroundSize || 'cover';
                        imgDiv.querySelector('span')?.remove();
                        break;

                    case 'text':
                        const textDiv = el.querySelector('[contenteditable]');
                        textDiv.innerHTML = component.content.html;
                        textDiv.style.fontSize = component.styles.fontSize;
                        textDiv.style.color = component.styles.color;
                        break;

                    case 'header':
                        const nav = el.querySelector('nav');
                        nav.style.backgroundColor = component.styles.backgroundColor;
                        nav.querySelector('div:first-child').innerText = component.content.logo;
                        const menuContainer = nav.querySelector('div:last-child');
                        menuContainer.innerHTML = component.content.menuItems
                            .map(item => `<div contenteditable>${item}</div>`)
                            .join('');
                        break;

                    case 'container':
                        el.style.backgroundColor = component.styles.backgroundColor;
                        el.style.border = component.styles.border;
                        el.style.padding = component.styles.padding;
                        break;

                    case 'textbox':
                        const input = el.querySelector('input[type="text"]');
                        input.value = component.content.value;
                        input.placeholder = component.content.placeholder;
                        input.style.fontSize = component.styles.fontSize;
                        break;

                    case 'textarea':
                        const textarea = el.querySelector('textarea');
                        textarea.value = component.content.value;
                        textarea.placeholder = component.content.placeholder;
                        textarea.style.fontSize = component.styles.fontSize;
                        break;

                    case 'radio':
                    case 'checkbox':
                        const optionsContainer = el.querySelector('.options-container');
                        optionsContainer.innerHTML = component.content.options
                            .map((option, index) => `
                <label style="display:flex;align-items:center;gap:6px">
                    <input type="${component.type}" 
                           name="${component.content.groupName}" 
                           ${option.checked ? 'checked' : ''}>
                    <span contenteditable>${option.text}</span>
                </label>
            `).join('');
                        break;

                    case 'dropdown':
                        const select = el.querySelector('select');
                        select.innerHTML = component.content.options
                            .map((option, index) => `
                <option value="option${index + 1}" 
                        ${option.selected ? 'selected' : ''}>
                    ${option.text}
                </option>
            `).join('');
                        break;

                    case 'slider':
                        const slider = el.querySelector('input[type="range"]');
                        slider.value = component.content.value;
                        slider.min = component.content.min;
                        slider.max = component.content.max;
                        break;

                    case 'footer':
                        el.style.backgroundColor = component.styles.backgroundColor;
                        el.querySelector('div').innerHTML = component.content.text;
                        break;

                    default:
                        console.warn('Unknown component type:', component.type);
                        break;
                }

                elementsContainer.appendChild(el);
            });
        }
    </script>
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('collapsed');
        }
    </script>

    <script src="../javascript/editor.js"></script>
</body>

</html>