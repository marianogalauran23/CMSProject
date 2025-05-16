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
                <div style="width:100%;height:100%;background:#f0f0f0;
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
        
        // In the image element creation code (line 315-334)
        fileInput.addEventListener('change', async (e) => {
            const file = e.target.files[0];
            if(file) {
                // Immediate client-side preview
                const reader = new FileReader();
                reader.onload = (e) => {
                    preview.style.background = `url(${e.target.result}) center/cover`;
                    preview.querySelector('span').remove();
                };
                reader.readAsDataURL(file);

                // Server upload
                const formData = new FormData();
                formData.append('image', file);
                
                try {
                    const response = await fetch(`upload.php?user_id=<?= $user_id ?>&page_id=<?= $page_id ?>`, {
                        method: 'POST',
                        body: formData
                    });
                    
                    const result = await response.json();
                    if(result.success) {
                        // Update to match server storage path
                        preview.style.background = `url(../pages/uploads/${result.user_id}/assets/${result.filename}) center/cover`;
                    }
                } catch(error) {
                    console.error('Upload failed:', error);
                }
            }
        });

        preview.addEventListener('contextmenu', (e) => {
            e.preventDefault();
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
            </div>

            <div class="property-group">
                <h4>Appearance</h4>
                <label>Background: <input type="color" value="${getComputedStyle(el).backgroundColor}" id="propBgColor"></label><br>
                <label>Text Color: <input type="color" value="${type === 'button' ? getComputedStyle(el.querySelector('button')).color : getComputedStyle(el).color}" id="propTextColor"></label><br>
                <label>Font Size: <input type="number" value="${parseInt(getComputedStyle(el).fontSize) || 16}" id="propFontSize"></label><br>
                <label>Font Family: 
                    <select id="propFontFamily">
                        ${['Arial', 'Helvetica', 'Times New Roman', 'Verdana'].map(font => `
                            <option value="${font}" ${getComputedStyle(el).fontFamily.includes(font) ? 'selected' : ''}>${font}</option>
                        `).join('')}
                    </select>
                </label>
                <br>
                <label>Font Style: 
                    <select id="propFontStyle">
                        <option value="normal" ${getComputedStyle(el).fontStyle === 'normal' ? 'selected' : ''}>Normal</option>
                        <option value="italic" ${getComputedStyle(el).fontStyle === 'italic' ? 'selected' : ''}>Italic</option>
                    </select>
                </label>
                <br>
                <label>Border Radius: <input type="number" value="${parseInt(el.style.borderRadius) || 0}" id="propRadius"></label>
            </div>
    `;

    if(['radio', 'checkbox', 'dropdown'].includes(type)) {
        html += `
            <div class="property-group">
                <h4>Options Management</h4>
                ${type === 'dropdown' ? `
                    <div id="dropdown-options-list">
                        ${Array.from(el.querySelectorAll('option')).map((option, index) => `
                            <div class="option-item">
                                <input type="text" value="${option.textContent}" 
                                    data-index="${index}" style="margin:4px 0">
                            </div>
                        `).join('')}
                    </div>
                ` : ''}
                <button onclick="addOption('${type}')">Add Option</button>
                <button onclick="removeLastOption('${type}')">Remove Last Option</button>
            </div>
        `;
    }

    if(type === 'header') {
        html += `
            <div class="property-group">
                <h4>Header Content</h4>
                <label>Website Name: <input type="text" value="${el.querySelector('nav > div:first-child').innerText}" id="propLogo"></label>
                <label>Menu Items: <input type="text" value="${[...el.querySelectorAll('nav > div:last-child > div')].map(d => d.innerText).join(', ')}" id="propMenu"></label>
                <label>Background Color: <input type="color" value="${getComputedStyle(el.querySelector('nav')).backgroundColor}" id="propNavBg"></label>
            </div>
        `;
    }

    if(type === 'button') {
    html += `
        <div class="property-group">
            <h4>Button Settings</h4>
            <label>Button Text: <input type="text" value="${el.querySelector('button').innerText}" id="propBtnText"></label>
            <label>Background Color: <input type="color" value="#ffffff" id="propBtnBg"></label>
            <label>Text Color: <input type="color" value="${getComputedStyle(el.querySelector('button')).color}" id="propBtnColor"></label>
        </div>
    `;
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