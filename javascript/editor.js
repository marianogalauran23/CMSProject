const elementsContainer = document.getElementById("elementsContainer");
const propertyPanel = document.getElementById("propertyFields");
let currentSelectedElement = null;

// Add resizer styles
const style = document.createElement('style');
style.textContent = `
.resizer {
    width: 12px;
    height: 12px;
    background: #007bff;
    position: absolute;
    right: 0;
    bottom: 0;
    cursor: se-resize;
    opacity: 0.7;
    border-radius: 2px;
    z-index: 1000;
    transition: all 0.2s;
}
.resizer:hover {
    opacity: 1;
}
.element {
    transition: all 0.2s;
    border: 2px solid transparent;
}
.element.selected {
    border: 2px dashed #007bff !important;
}
.element.dragging, .element.resizing {
    border: 2px solid #007bff !important;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
`;
document.head.appendChild(style);

// Setup draggable elements from toolbar
document.querySelectorAll(".draggable").forEach(elem => {
    elem.addEventListener("dragstart", e => {
        e.dataTransfer.setData("type", e.target.dataset.type);
    });
    elem.setAttribute("draggable", "true");
});

// Allow drop in workArea
const workArea = document.getElementById("workArea");
workArea.addEventListener("dragover", e => e.preventDefault());

workArea.addEventListener("drop", e => {
    e.preventDefault();
    const type = e.dataTransfer.getData("type");
    const newEl = createElement(type, e.offsetX, e.offsetY);

    if(type === 'header') {
        newEl.style.width = '100%';
        newEl.style.left = '0';
        newEl.style.top = '0';
    }

    const containerUnder = [...document.querySelectorAll(".container")].find(c => {
        const rect = c.getBoundingClientRect();
        return e.clientX >= rect.left && e.clientX <= rect.right &&
               e.clientY >= rect.top && e.clientY <= rect.bottom;
    });

    if (containerUnder) {
        containerUnder.appendChild(newEl);
        const containerRect = containerUnder.getBoundingClientRect();
        newEl.style.left = `${e.clientX - containerRect.left}px`;
        newEl.style.top = `${e.clientY - containerRect.top}px`;
    } else {
        elementsContainer.appendChild(newEl);
    }
});

function createElement(type, x, y) {
    const el = document.createElement("div");
    el.classList.add("element");
    el.dataset.type = type;
    el.style.position = "absolute";
    el.style.left = `${x}px`;
    el.style.top = `${y}px`;
    
    switch (type) {
        case "text":
            el.innerHTML = `
                <div contenteditable 
                    style="padding:8px;min-height:100%;width:100%;height:100%;
                           background:transparent;border:none;outline:none">
                    Edit text...
                </div>`;
            el.style.width = "150px";
            el.style.height = "50px";
            break;

        case "image":
            el.innerHTML = `
                <div style="width:100%;height:100%;background:transparent;
                            display:flex;align-items:center;justify-content:center">
                    <span>Upload Image</span>
                    <input type="file" accept="image/*" hidden>
                </div>`;
            el.style.width = "200px";
            el.style.height = "150px";
            break;

        case "container":
            el.style.background = "#f8f9fa";
            el.classList.add("container");
            el.style.width = "300px";
            el.style.height = "200px";
            break;

        case "header":
            el.innerHTML = `
                <nav style="background:var(--header-bg, transparent);padding:15px;width:100%;height:100%;
                            display:flex;align-items:center;gap:20px">
                    <div contenteditable style="font-weight:bold">Website Name</div>
                    <div style="display:flex;gap:15px">
                        <div contenteditable>Home</div>
                        <div contenteditable>About</div>
                        <div contenteditable>Contact</div>
                    </div>
                </nav>`;
            el.style.height = "60px";
            break;

        case "button":
        el.innerHTML = `
            <button style="width:100%;height:100%;
                        background: transparent;
                        color: var(--btn-color, #333);
                        border: 1px solid #ddd;
                        cursor:pointer;
                        transition: all 0.2s">
                Button
            </button>`;
        el.style.width = "120px";
        el.style.height = "40px";
        break;

        case "textbox":
            el.innerHTML = `
                <input type="text" placeholder="Enter text" 
                       style="width:100%;height:100%;padding:8px;border:1px solid #ddd">`;
            el.style.width = "200px";
            el.style.height = "40px";
            break;

        case "textarea":
            el.innerHTML = `
                <textarea placeholder="Enter your text here..." 
                    style="width:100%;height:100%;padding:8px;border:1px solid #ddd;resize:none"></textarea>`;
            el.style.width = "250px";
            el.style.height = "100px";
            break;

        case "radio":
            el.innerHTML = `
                <div class="options-container" style="padding:8px;display:flex;flex-direction:column;gap:6px">
                    ${Array.from({length: 2}).map((_, i) => `
                        <label style="display:flex;align-items:center;gap:6px">
                            <input type="radio" name="radio-group">
                            <span contenteditable>Option ${i+1}</span>
                        </label>
                    `).join('')}
                </div>`;
            el.style.width = "150px";
            break;

        case "checkbox":
            el.innerHTML = `
                <div class="options-container" style="padding:8px;display:flex;flex-direction:column;gap:6px">
                    ${Array.from({length: 2}).map((_, i) => `
                        <label style="display:flex;align-items:center;gap:6px">
                            <input type="checkbox">
                            <span contenteditable>Option ${i+1}</span>
                        </label>
                    `).join('')}
                </div>`;
            el.style.width = "150px";
            break;

        case "dropdown":
            el.innerHTML = `
                <select class="dropdown-options" style="width:100%;padding:8px;border:1px solid #ddd">
                    ${Array.from({length: 3}).map((_, i) => `
                        <option value="option${i+1}">Option ${i+1}</option>
                    `).join('')}
                </select>`;
            el.style.width = "200px";
            el.style.height = "40px";
            break;

        case "slider":
            el.innerHTML = `
                <input type="range" style="width:100%;margin:15px 0" min="0" max="100">`;
            el.style.width = "200px";
            break;

        default:
            el.innerText = "New Element";
            el.style.width = "150px";
            el.style.height = "50px";
    }

    const resizer = document.createElement("div");
    resizer.className = "resizer";
    el.appendChild(resizer);

    makeDraggable(el);
    makeResizable(el, resizer);
    setupElementSelection(el);

    if(type === 'image') {
        const fileInput = el.querySelector('input[type="file"]');
        const preview = el.querySelector('div');
        
        fileInput.addEventListener('change', async (e) => {
            const file = e.target.files[0];
            if(file) {
                const formData = new FormData();
                formData.append('image', file);

                // Client preview
                const reader = new FileReader();
               reader.onload = (e) => {
                    preview.style.background = `url('${e.target.result}') center/cover`; // Added quotes
                    preview.style.backgroundColor = 'transparent';
                    preview.querySelector('span')?.remove();
                };
                reader.readAsDataURL(file);

                // Server upload
                try {
                    const response = await fetch(`upload.php?user_id=${USER_ID}&page_id=${PAGE_ID}`, {
                        method: 'POST',
                        body: formData
                    });
                    
                    const result = await response.json();
                    if(result.success) {
                        preview.dataset.serverSrc = result.fullUrl;
                        preview.style.background = `url('${result.fullUrl}') center/cover`; // Added quotes
                    }
                } catch(error) {
                    console.error('Upload failed:', error);
                }
            }
        });

        preview.addEventListener('contextmenu', (e) => {
            e.preventDefault();
            e.stopPropagation();
            fileInput.click();
        });
    }

    return el;
}

function setupElementSelection(el) {
    el.addEventListener("dblclick", () => {
        el.style.width = "100%";
        el.style.left = "0";
    });

    el.addEventListener("click", e => {
        e.stopPropagation();
        if(currentSelectedElement) {
            currentSelectedElement.classList.remove("selected");
        }
        el.classList.add("selected");
        currentSelectedElement = el;
        showProperties(el);
    });
}

document.addEventListener("click", (e) => {
    if(!propertyPanel.contains(e.target) && !e.target.closest(".element")) {
        if(currentSelectedElement) {
            currentSelectedElement.classList.remove("selected");
            currentSelectedElement = null;
        }
        propertyPanel.innerHTML = "";
    }
});

function makeDraggable(el) {
    let isDragging = false, startX, startY, startLeft, startTop;

    el.addEventListener("mousedown", e => {
        if(e.target.closest(".resizer")) return;

        el.classList.add("dragging");
        e.stopPropagation();
        isDragging = true;
        startX = e.pageX;
        startY = e.pageY;
        startLeft = parseInt(el.style.left, 10) || 0;
        startTop = parseInt(el.style.top, 10) || 0;
        el.style.zIndex = 1000;
        document.draggedElement = el;
    });

    document.addEventListener("mousemove", e => {
        if(!isDragging || document.draggedElement !== el) return;

        let dx = e.pageX - startX;
        let dy = e.pageY - startY;
        let newLeft = startLeft + dx;
        let newTop = startTop + dy;

        // Snapping logic
        const snapThreshold = 10;
        const parent = el.offsetParent;
        const allElements = [...parent.querySelectorAll('.element')].filter(element => element !== el);

        allElements.forEach(otherEl => {
            const otherPos = {
                left: otherEl.offsetLeft,
                top: otherEl.offsetTop,
                right: otherEl.offsetLeft + otherEl.offsetWidth,
                bottom: otherEl.offsetTop + otherEl.offsetHeight
            };

            const currentPos = {
                left: newLeft,
                top: newTop,
                right: newLeft + el.offsetWidth,
                bottom: newTop + el.offsetHeight
            };

            // Vertical snapping
            if (Math.abs(currentPos.bottom - otherPos.top) < snapThreshold) {
                newTop = otherPos.top - el.offsetHeight;
            }
            else if (Math.abs(currentPos.top - otherPos.bottom) < snapThreshold) {
                newTop = otherPos.bottom;
            }

            // Horizontal snapping
            if (Math.abs(currentPos.right - otherPos.left) < snapThreshold) {
                newLeft = otherPos.left - el.offsetWidth;
            }
            else if (Math.abs(currentPos.left - otherPos.right) < snapThreshold) {
                newLeft = otherPos.right;
            }
        });

        el.style.left = `${newLeft}px`;
        el.style.top = `${newTop}px`;
    });

    document.addEventListener("mouseup", () => {
        isDragging = false;
        el.classList.remove("dragging");
        document.draggedElement = null;
    });
}

function makeResizable(el, resizer) {
    let isResizing = false, startX, startY, startW, startH;

    resizer.addEventListener("mousedown", e => {
        el.classList.add("resizing");
        e.stopPropagation();
        isResizing = true;
        startX = e.pageX;
        startY = e.pageY;
        startW = el.offsetWidth;
        startH = el.offsetHeight;
        el.style.zIndex = 1000;
    });

    document.addEventListener("mousemove", e => {
        if(!isResizing) return;
        el.style.width = `${startW + (e.pageX - startX)}px`;
        el.style.height = `${startH + (e.pageY - startY)}px`;
    });

    document.addEventListener("mouseup", () => {
        isResizing = false;
        el.classList.remove("resizing");
        el.style.zIndex = "";
    });
}

// Add this style section for the properties panel
const propertyStyle = document.createElement('style');
propertyStyle.textContent = `
.property-section {
    padding: 15px;
    background: #f8f9fa;
    border-left: 1px solid #dee2e6;
}

.property-group {
    margin-bottom: 20px;
    padding: 15px;
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 4px;
}

.property-group h3 {
    margin-top: 0;
    color: #333;
    font-size: 1.2rem;
}

.property-group h4 {
    margin: 0 0 10px 0;
    color: #666;
    font-size: 0.9rem;
    text-transform: uppercase;
}

.property-group label {
    display: flex;
    align-items: center;
    margin: 8px 0;
    font-size: 0.9rem;
    color: #444;
}

.property-group input[type="color"] {
    width: 30px;
    height: 30px;
    padding: 2px;
    margin-left: auto;
}

.property-group input[type="number"],
.property-group input[type="text"] {
    width: 80px;
    padding: 6px;
    margin-left: auto;
    border: 1px solid #ddd;
    border-radius: 3px;
}

.property-group select {
    margin-left: auto;
    padding: 4px;
}

.property-group button {
    background: #007bff;
    color: white;
    border: none;
    padding: 6px 12px;
    border-radius: 3px;
    cursor: pointer;
    margin: 4px 2px;
}

.property-group button:hover {
    background: #0056b3;
}

.option-item {
    display: flex;
    gap: 8px;
    margin: 4px 0;
}
`;
document.head.appendChild(propertyStyle);

// Modified showProperties function
function showProperties(el) {
    const type = el.dataset.type;
    let html = `
        <div class="property-section">
            <h3>${type.charAt(0).toUpperCase() + type.slice(1)} Properties</h3>
            
            <div class="property-group">
                <h4>Position</h4>
                <label>X: <input type="number" value="${parseInt(el.style.left) || 0}" id="propX"></label>
                <label>Y: <input type="number" value="${parseInt(el.style.top) || 0}" id="propY"></label>
            </div>

            <div class="property-group">
                <h4>Size</h4>
                <label>Width: <input type="number" value="${el.offsetWidth}" id="propWidth"></label>
                <label>Height: <input type="number" value="${el.offsetHeight}" id="propHeight"></label>
            </div>`;

    // Only show appearance properties for non-image elements
    if(type !== 'image') {
        html += `
            <div class="property-group">
                <h4>Appearance</h4>
                <label>Background: <input type="color" value="${getComputedStyle(el).backgroundColor}" id="propBgColor"></label>
                <label>Text Color: <input type="color" value="${type === 'button' ? getComputedStyle(el.querySelector('button')).color : getComputedStyle(el).color}" id="propTextColor"></label>
                <label>Font Size: <input type="number" value="${parseInt(getComputedStyle(el).fontSize) || 16}" id="propFontSize"></label>
                <label>Font Family: 
                    <select id="propFontFamily">
                        ${['Arial', 'Times New Roman', 'Verdana'].map(font => `
                            <option value="${font}" ${getComputedStyle(el).fontFamily.includes(font) ? 'selected' : ''}>${font}</option>
                        `).join('')}
                    </select>
                </label>
                <label>Font Style: 
                    <select id="propFontStyle">
                        <option value="normal" ${getComputedStyle(el).fontStyle === 'normal' ? 'selected' : ''}>Normal</option>
                        <option value="italic" ${getComputedStyle(el).fontStyle === 'italic' ? 'selected' : ''}>Italic</option>
                    </select>
                </label>
                <label>Border Radius: <input type="number" value="${parseInt(el.style.borderRadius) || 0}" id="propRadius"></label>
            </div>`;
    }

    html += `</div>`;

    propertyPanel.innerHTML = html;

    // Common property handlers
    const updateStyle = (id, prop, unit = 'px', target = el) => {
        const input = propertyPanel.querySelector(id);
        input?.addEventListener('input', () => {
            target.style[prop] = input.value + unit;
        });
    };

    updateStyle('#propX', 'left');
    updateStyle('#propY', 'top');
    updateStyle('#propWidth', 'width');
    updateStyle('#propHeight', 'height');
    updateStyle('#propRadius', 'borderRadius');
    updateStyle('#propFontSize', 'fontSize');

    propertyPanel.querySelector('#propBgColor')?.addEventListener('input', (e) => {
        el.style.backgroundColor = e.target.value;
    });

    propertyPanel.querySelector('#propTextColor')?.addEventListener('input', (e) => {
        if(type === 'button') {
            el.querySelector('button').style.color = e.target.value;
        } else {
            el.style.color = e.target.value;
        }
    });

    propertyPanel.querySelector('#propFontFamily')?.addEventListener('change', (e) => {
        el.style.fontFamily = e.target.value;
    });

    propertyPanel.querySelector('#propFontStyle')?.addEventListener('change', (e) => {
        el.style.fontStyle = e.target.value;
    });

    if(type === 'header') {
        propertyPanel.querySelector('#propLogo')?.addEventListener('input', (e) => {
            el.querySelector('nav > div:first-child').innerText = e.target.value;
        });

        propertyPanel.querySelector('#propMenu')?.addEventListener('input', (e) => {
            const items = e.target.value.split(',').map(i => i.trim());
            const menuContainer = el.querySelector('nav > div:last-child');
            menuContainer.innerHTML = items.map(item => 
                `<div contenteditable>${item}</div>`
            ).join('');
        });

        propertyPanel.querySelector('#propNavBg')?.addEventListener('input', (e) => {
            el.querySelector('nav').style.backgroundColor = e.target.value;
        });
    }

    if(type === 'button') {
        propertyPanel.querySelector('#propBtnText')?.addEventListener('input', (e) => {
            el.querySelector('button').innerText = e.target.value;
        });

        propertyPanel.querySelector('#propBtnBg')?.addEventListener('input', (e) => {
            el.querySelector('button').style.backgroundColor = e.target.value;
        });

        propertyPanel.querySelector('#propBtnColor')?.addEventListener('input', (e) => {
            el.querySelector('button').style.color = e.target.value;
        });
    }

    if(type === 'dropdown') {
        propertyPanel.querySelectorAll('#dropdown-options-list input').forEach(input => {
            input.addEventListener('input', (e) => {
                const index = e.target.dataset.index;
                el.querySelectorAll('option')[index].textContent = e.target.value;
            });
        });
    }
}

function addOption(type) {
    if(!currentSelectedElement) return;
    
    const container = currentSelectedElement.querySelector('.options-container') || 
                     currentSelectedElement.querySelector('.dropdown-options');
    
    if(type === 'dropdown') {
        const newOption = document.createElement('option');
        newOption.textContent = `Option ${container.children.length + 1}`;
        container.appendChild(newOption);
    } else {
        const newLabel = document.createElement('label');
        newLabel.style.display = 'flex';
        newLabel.style.alignItems = 'center';
        newLabel.style.gap = '6px';
        newLabel.innerHTML = `
            <input type="${type === 'radio' ? 'radio' : 'checkbox'}">
            <span contenteditable>Option ${container.children.length + 1}</span>
        `;
        container.appendChild(newLabel);
    }
    showProperties(currentSelectedElement);
}

function removeLastOption(type) {
    if(!currentSelectedElement) return;
    
    const container = currentSelectedElement.querySelector('.options-container') || 
                     currentSelectedElement.querySelector('.dropdown-options');
    
    if(container.children.length > 1) {
        container.lastElementChild.remove();
        showProperties(currentSelectedElement);
    }
}

// Delete key handler
document.addEventListener("keydown", e => {
    if(e.key === 'Delete' && currentSelectedElement) {
        currentSelectedElement.remove();
        currentSelectedElement = null;
        propertyPanel.innerHTML = "";
    }
});

// Clear all elements
document.getElementById("clearAllBtn").addEventListener("click", () => {
    elementsContainer.querySelectorAll(".element").forEach(el => el.remove());
    propertyPanel.innerHTML = "";
});

// Save functionality
document.getElementById('savePage').addEventListener('click', async () => {
    const elements = Array.from(elementsContainer.querySelectorAll('.element'));
    const pageId = PAGE_ID;
    
    const components = elements.map((element) => {
        const type = element.dataset.type;
        const styles = {};
        const content = {};
        const computedStyle = getComputedStyle(element);

        // Base properties
        const baseComponent = {
            type,
            position_x: parseInt(element.style.left) || 0,
            position_y: parseInt(element.style.top) || 0,
            width: element.offsetWidth,
            height: element.offsetHeight,
            z_index: parseInt(element.style.zIndex) || 1,
            styles: {},
            content: {}
        };

        // Style and content handling
        switch(type) {
            case 'button':
                const button = element.querySelector('button');
                styles.backgroundColor = getComputedStyle(button).backgroundColor;
                styles.color = getComputedStyle(button).color;
                content.text = button?.innerText || '';
                break;

            case 'header':
                const nav = element.querySelector('nav');
                styles.backgroundColor = getComputedStyle(nav).backgroundColor;
                content.logo = nav.querySelector('div:first-child')?.innerText || '';
                content.menuItems = Array.from(nav.querySelectorAll('div:last-child > div'))
                    .map(item => item.innerText);
                break;

            case 'image':
                const imgDiv = element.querySelector('div');
                content.src = imgDiv.dataset.serverSrc || 
                            imgDiv.dataset.tempSrc || 
                            imgDiv.style.backgroundImage
                                .replace(/^url\(['"]?/, '')
                                .replace(/['"]?\)$/, '');
                
                // Only save necessary background properties
                styles.backgroundSize = imgDiv.style.backgroundSize || 'cover';
                styles.backgroundPosition = imgDiv.style.backgroundPosition || 'center';
                break;

            case 'text':
                const textDiv = element.querySelector('[contenteditable]');
                content.html = textDiv?.innerHTML || '';
                styles.color = getComputedStyle(textDiv).color;
                styles.fontSize = getComputedStyle(textDiv).fontSize;
                break;

            case 'container':
                styles.backgroundColor = computedStyle.backgroundColor;
                styles.border = computedStyle.border;
                styles.padding = computedStyle.padding;
                break;

            default:
                styles.backgroundColor = computedStyle.backgroundColor;
                break;
        }

        return { ...baseComponent, styles, content };
    });

    try {
        const response = await fetch('../components/saveComponents.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                user_id: USER_ID,
                page_id: pageId,
                components
            })
        });
        
        const result = await response.json();
        alert(result.success ? 'Saved!' : `Error: ${result.error}`);
    } catch(error) {
        console.error('Save failed:', error);
        alert('Save failed - check console');
    }
});

// Load functionality
async function loadPage(pageId) {
    try {
        const response = await fetch(`../components/load_page.php?page_id=${pageId}`);
        if (!response.ok) throw new Error(`HTTP error ${response.status}`);
        
        const components = await response.json();
        elementsContainer.querySelectorAll('.element').forEach(el => el.remove());

        components.forEach(component => {
            const el = createElement(component.type, component.position_x, component.position_y);
            
            // Apply dimensions and z-index
            el.style.width = `${component.width}px`;
            el.style.height = `${component.height}px`;
            el.style.zIndex = component.z_index;

            // Apply styles
            Object.entries(component.styles).forEach(([prop, value]) => {
                switch(component.type) {
                    case 'button':
                        el.querySelector('button').style[prop] = value;
                        break;
                    case 'header':
                        el.querySelector('nav').style[prop] = value;
                        break;
                    case 'image':
                        el.querySelector('div').style[prop] = value;
                        break;
                    default:
                        el.style[prop] = value;
                }
            });

            // Apply content
            switch(component.type) {
                case 'text':
                    el.querySelector('[contenteditable]').innerHTML = component.content.html;
                    break;
                case 'header':
                    const nav = el.querySelector('nav');
                    nav.querySelector('div:first-child').innerText = component.content.logo;
                    nav.querySelector('div:last-child').innerHTML = component.content.menuItems
                        .map(item => `<div contenteditable>${item}</div>`)
                        .join('');
                    break;
                case 'image':
                    const imgDiv = el.querySelector('div');
                    if (imgDiv && component.content.src) {
                        imgDiv.style.backgroundImage = `url('${component.content.src}')`;
                        imgDiv.querySelector('span')?.remove();
                    }
                    break;
            }

            elementsContainer.appendChild(el);
        });
    } catch(error) {
        console.error('Load error:', error);
        alert('Failed to load components');
    }
}

// Initial load
if (typeof PAGE_ID !== 'undefined' && PAGE_ID > 0) {
    loadPage(PAGE_ID).catch(error => {
        console.error('Initial load error:', error);
    });
} else {
    console.error('Invalid page ID:', PAGE_ID);
}