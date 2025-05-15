// editor.js
let elements = [];
let selectedElement = null;
let currentPageId = null;

// Initialize after DOM loads
document.addEventListener('DOMContentLoaded', () => {
    currentPageId = window.currentPageId;
    initSavedLayout();
    setupInteractions();
});

function setupInteractions() {
    // Setup sidebar elements as draggable
    interact('.element-item').draggable({
        inertia: true,
        autoScroll: true,
        listeners: {
            start(event) {
                event.target.style.opacity = '0.5';
            },
            end(event) {
                event.target.style.opacity = '';
            }
        }
    });

    // Setup work area as dropzone
    interact('#workArea').dropzone({
        accept: '.element-item',
        ondrop: function(event) {
            const type = event.relatedTarget.dataset.type;
            const { x, y } = getRelativeCoordinates(event.clientX, event.clientY);
            createNewElement(type, x, y);
        }
    });
}

function getRelativeCoordinates(clientX, clientY) {
    const workArea = document.getElementById('workArea');
    const rect = workArea.getBoundingClientRect();
    return {
        x: clientX - rect.left - workArea.scrollLeft,
        y: clientY - rect.top - workArea.scrollTop
    };
}

function createNewElement(type, x, y) {
    const element = document.createElement('div');
    const id = Date.now().toString();
    
    element.className = 'draggable-element';
    element.dataset.elementId = id;
    
    Object.assign(element.style, {
        position: 'absolute',
        left: `${x}px`,
        top: `${y}px`,
        width: '200px',
        height: '200px'
    });

    switch(type) {
        case 'text':
            element.contentEditable = true;
            element.textContent = 'New Text';
            break;
        case 'image':
            element.innerHTML = `<div class="image-placeholder"></div>`;
            break;
        case 'button':
            element.textContent = 'Click Me';
            element.style.cssText += `
                background-color: #2196F3;
                color: white;
                text-align: center;
                line-height: 40px;
                cursor: pointer;
            `;
            break;
    }

    document.getElementById('elementsContainer').appendChild(element);
    setupElementInteractions(element);
    
    elements.push({
        id,
        type,
        x,
        y,
        width: 200,
        height: 200,
        styles: element.style.cssText,
        content: ''
    });
    
    saveLayout();
}

function setupElementInteractions(element) {
    interact(element).draggable({
        modifiers: [
            interact.modifiers.snap({
                targets: [
                    interact.createSnapGrid({ x: 20, y: 20 })
                ],
                range: Infinity,
                relativePoints: [ { x: 0, y: 0 } ]
            })
        ],
        listeners: {
            move: event => handleDragMove(event),
            end: () => saveLayout()
        }
    }).resizable({
        edges: { left: true, right: true, bottom: true, top: true },
        modifiers: [
            interact.modifiers.snapSize({
                targets: [
                    interact.snappers.grid({ x: 20, y: 20 })
                ]
            })
        ],
        listeners: {
            move: event => handleResize(event),
            end: () => saveLayout()
        }
    });

    element.addEventListener('click', () => selectElement(element));
}

function handleDragMove(event) {
    const target = event.target;
    const x = (parseFloat(target.dataset.x) || 0) + event.dx;
    const y = (parseFloat(target.dataset.y) || 0) + event.dy;
    
    target.style.left = `${x}px`;
    target.style.top = `${y}px`;
    target.dataset.x = x;
    target.dataset.y = y;
    
    updateElementState(target);
}

function handleResize(event) {
    const target = event.target;
    let x = parseFloat(target.dataset.x) || 0;
    let y = parseFloat(target.dataset.y) || 0;
    
    target.style.width = `${event.rect.width}px`;
    target.style.height = `${event.rect.height}px`;
    
    x += event.deltaRect.left;
    y += event.deltaRect.top;
    
    target.style.left = `${x}px`;
    target.style.top = `${y}px`;
    target.dataset.x = x;
    target.dataset.y = y;
    
    updateElementState(target);
}

function updateElementState(element) {
    const elementData = elements.find(el => el.id === element.dataset.elementId);
    if (elementData) {
        elementData.x = parseFloat(element.dataset.x);
        elementData.y = parseFloat(element.dataset.y);
        elementData.width = element.offsetWidth;
        elementData.height = element.offsetHeight;
        elementData.styles = element.style.cssText;
    }
}

function selectElement(element) {
    document.querySelectorAll('.draggable-element').forEach(el => 
        el.classList.remove('selected'));
    element.classList.add('selected');
    selectedElement = element;
    showPropertiesPanel(element);
}

function showPropertiesPanel(element) {
    const form = document.getElementById('propertiesForm');
    form.innerHTML = `
        <div class="property-group">
            <label>Width:</label>
            <input type="text" value="${element.style.width}" 
                   onchange="updateStyle('width', this.value)">
        </div>
        <div class="property-group">
            <label>Height:</label>
            <input type="text" value="${element.style.height}" 
                   onchange="updateStyle('height', this.value)">
        </div>
        <div class="property-group">
            <label>Background:</label>
            <input type="color" value="${element.style.backgroundColor || '#ffffff'}" 
                   onchange="updateStyle('backgroundColor', this.value)">
        </div>
        <div class="property-group">
            <label>Text Color:</label>
            <input type="color" value="${element.style.color || '#000000'}" 
                   onchange="updateStyle('color', this.value)">
        </div>
    `;
}

window.updateStyle = function(property, value) {
    if (!selectedElement) return;
    selectedElement.style[property] = value;
    updateElementState(selectedElement);
    saveLayout();
};

function saveLayout() {
    if (!currentPageId) return;

    fetch('../components/save_layout.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            page_id: currentPageId,
            layout: elements
        })
    })
    .then(response => {
        if (!response.ok) throw new Error('Network error');
        return response.json();
    })
    .then(data => {
        if (data.status !== 'success') console.error('Save failed');
    })
    .catch(error => console.error('Error:', error));
}

function initSavedLayout() {
    if (!window.initialLayout) return;

    window.initialLayout.forEach(item => {
        const element = document.createElement('div');
        element.className = 'draggable-element';
        element.dataset.elementId = item.id;
        
        element.style.cssText = item.styles;
        element.style.left = `${item.x}px`;
        element.style.top = `${item.y}px`;
        element.dataset.x = item.x;
        element.dataset.y = item.y;

        if (item.type === 'image') {
            element.innerHTML = `<div class="image-placeholder"></div>`;
        } else if (item.type === 'button') {
            element.textContent = item.content || 'Click Me';
        } else if (item.type === 'text') {
            element.textContent = item.content || 'New Text';
            element.contentEditable = true;
        }

        document.getElementById('elementsContainer').appendChild(element);
        setupElementInteractions(element);
    });

    elements = window.initialLayout;
}