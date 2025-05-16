<?php
include "./db.php";

$slug = $_GET['slug'] ?? '';

$stmt = $conn->prepare("
    SELECT title, components_data 
    FROM pages 
    WHERE slug = ? AND published = TRUE
");
$stmt->bind_param("s", $slug);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(404);
    die("Page not found");
}

$page = $result->fetch_assoc();
$components = json_decode($page['components_data'], true);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= htmlspecialchars($page['title']) ?></title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            width: 100vw;
            min-height: 100vh;
            overflow-x: hidden;
            background: #f0f0f0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        #scalingContainer {
            position: relative;
            width: 1378px;
            transform-origin: top center;
            min-height: 100vh;
        }

        .element {
            position: absolute;
        }
    </style>
</head>

<body>
    <div id="scalingContainer">
        <div id="publishedContainer" style="position: relative">
            <!-- Components will be injected here -->
        </div>
    </div>

    <script>
        const COMPONENTS = <?= json_encode($components) ?>;
        const DESIGN_WIDTH = 1378;

        // Convert px to vw based on design width
        function pxToVw(px) {
            return (px / DESIGN_WIDTH * 100).toFixed(4);
        }

        // Update scaling based on window size
        function updateScaling() {
            const container = document.getElementById('scalingContainer');
            const scale = window.innerWidth / DESIGN_WIDTH;

            container.style.transform = `scale(${scale})`;
            container.style.margin = `0 ${(window.innerWidth - (DESIGN_WIDTH * scale)) / 2}px`;
            document.body.style.minHeight = `${container.scrollHeight * scale}px`;
        }

        // Render components with responsive units
        function renderComponent(component) {
            const el = document.createElement('div');
            el.className = 'element';

            // Convert positions and sizes to vw
            el.style.cssText = `
                left: ${pxToVw(component.position_x)}vw;
                top: ${pxToVw(component.position_y)}vw;
                width: ${pxToVw(component.width)}vw;
                height: ${pxToVw(component.height)}vw;
                z-index: ${component.z_index};
                ${Object.entries(component.styles).map(([k, v]) => {
                if (typeof v === 'string' && v.includes('px')) {
                    return `${k}: ${v.replace(/(\d+)px/g, (_, p1) => pxToVw(p1) + 'vw')};`;
                }
                return `${k}: ${v};`;
            }).join('')}
            `;

            // Component-specific rendering
            switch (component.type) {
                case 'text':
                    el.innerHTML = component.content.html
                        .replace(/(\d+)px/g, (_, p1) => pxToVw(p1) + 'vw');
                    break;

                case 'image':
                    el.innerHTML = `
                        <img src="${component.content.src}" 
                            style="width:100%;height:100%;object-fit:cover">`;
                    break;

                case 'button':
                    el.innerHTML = `
                        <button style="
                            width: 100%;
                            height: 100%;
                            font-size: ${pxToVw(component.styles.fontSize || 16)}vw;
                            padding: ${pxToVw(component.styles.padding || 8)}vw;
                            border-radius: ${pxToVw(component.styles.borderRadius || 0)}vw;
                            background: ${component.styles.backgroundColor || 'transparent'};
                            color: ${component.styles.color || '#333'};
                        ">
                            ${component.content.text}
                        </button>`;
                    break;

                case 'header':
                    el.innerHTML = `
                        <nav style="
                            width: 100%;
                            height: 100%;
                            padding: ${pxToVw(component.styles.padding || 15)}vw;
                            gap: ${pxToVw(component.styles.gap || 20)}vw;
                            background: ${component.styles.backgroundColor || 'transparent'};
                            display: flex;
                            align-items: center;
                        ">
                            <div style="
                                font-weight: bold;
                                font-size: ${pxToVw(component.styles.fontSize || 16)}vw;
                            ">
                                ${component.content.logo}
                            </div>
                            <div style="
                                display: flex;
                                gap: ${pxToVw(component.styles.gap || 15)}vw;
                            ">
                                ${component.content.menuItems.map(item => `
                                    <div style="
                                        font-size: ${pxToVw(component.styles.fontSize || 14)}vw;
                                    ">
                                        ${item}
                                    </div>
                                `).join('')}
                            </div>
                        </nav>`;
                    break;

                case 'container':
                    el.innerHTML = `
                        <div style="
                            width: 100%;
                            height: 100%;
                            padding: ${pxToVw(component.styles.padding || 20)}vw;
                            background: ${component.styles.backgroundColor || '#f8f9fa'};
                        "></div>`;
                    break;

                case 'textbox':
                    el.innerHTML = `
                        <input type="text" 
                            placeholder="${component.content.placeholder}" 
                            style="
                                width: 100%;
                                height: 100%;
                                padding: ${pxToVw(component.styles.padding || 8)}vw;
                                font-size: ${pxToVw(component.styles.fontSize || 14)}vw;
                                border-radius: ${pxToVw(component.styles.borderRadius || 4)}vw;
                            ">`;
                    break;

                case 'textarea':
                    el.innerHTML = `
                        <textarea 
                            placeholder="${component.content.placeholder}"
                            style="
                                width: 100%;
                                height: 100%;
                                padding: ${pxToVw(component.styles.padding || 8)}vw;
                                font-size: ${pxToVw(component.styles.fontSize || 14)}vw;
                                border-radius: ${pxToVw(component.styles.borderRadius || 4)}vw;
                            "></textarea>`;
                    break;

                case 'radio':
                case 'checkbox':
                    el.innerHTML = `
                        <div style="
                            padding: ${pxToVw(component.styles.padding || 8)}vw;
                            display: flex;
                            flex-direction: column;
                            gap: ${pxToVw(component.styles.gap || 6)}vw;
                        ">
                            ${component.content.options.map((option, index) => `
                                <label style="
                                    display: flex;
                                    align-items: center;
                                    gap: ${pxToVw(component.styles.gap || 6)}vw;
                                ">
                                    <input type="${component.type}" 
                                           ${option.checked ? 'checked' : ''}
                                           style="
                                               width: ${pxToVw(16)}vw;
                                               height: ${pxToVw(16)}vw;
                                           ">
                                    <span style="
                                        font-size: ${pxToVw(component.styles.fontSize || 14)}vw;
                                    ">
                                        ${option.text}
                                    </span>
                                </label>
                            `).join('')}
                        </div>`;
                    break;

                case 'dropdown':
                    el.innerHTML = `
                        <select style="
                            width: 100%;
                            height: 100%;
                            padding: ${pxToVw(component.styles.padding || 8)}vw;
                            font-size: ${pxToVw(component.styles.fontSize || 14)}vw;
                            border-radius: ${pxToVw(component.styles.borderRadius || 4)}vw;
                        ">
                            ${component.content.options.map((option, index) => `
                                <option value="${option.value}" ${option.selected ? 'selected' : ''}>
                                    ${option.text}
                                </option>
                            `).join('')}
                        </select>`;
                    break;

                case 'slider':
                    el.innerHTML = `
                        <input type="range" 
                            min="${component.content.min}" 
                            max="${component.content.max}" 
                            value="${component.content.value}"
                            style="
                                width: 100%;
                                margin: ${pxToVw(15)}vw 0;
                                height: ${pxToVw(24)}vw;
                            ">`;
                    break;
            }

            return el;
        }

        // Initial setup
        window.addEventListener('DOMContentLoaded', () => {
            const container = document.getElementById('publishedContainer');
            COMPONENTS.forEach(component => {
                container.appendChild(renderComponent(component));
            });
            updateScaling();
            setTimeout(updateScaling, 100); // Ensure proper scaling after render
        });

        // Handle window resize
        window.addEventListener('resize', () => {
            updateScaling();
        });
    </script>
</body>

</html>