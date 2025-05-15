const elementsContainer = document.getElementById("elementsContainer");
const propertyPanel = document.getElementById("propertyFields");

document.querySelectorAll(".draggable").forEach(elem => {
    elem.addEventListener("dragstart", e => {
        e.dataTransfer.setData("type", e.target.dataset.type);
    });
    elem.setAttribute("draggable", "true");
});

document.getElementById("workArea").addEventListener("dragover", e => {
    e.preventDefault();
});

document.getElementById("workArea").addEventListener("drop", e => {
    e.preventDefault();
    const type = e.dataTransfer.getData("type");
    const newEl = createElement(type, e.offsetX, e.offsetY);
    elementsContainer.appendChild(newEl);
});

function createElement(type, x, y) {
    const el = document.createElement("div");
    el.classList.add("element");
    el.style.left = `${x}px`;
    el.style.top = `${y}px`;
    el.setAttribute("tabindex", 0);
    el.setAttribute("contenteditable", true);

    if (type === "text") el.innerText = "Edit text...";
    else if (type === "image") el.innerHTML = `<img src="https://via.placeholder.com/100" width="100">`;

    makeDraggable(el);
    el.addEventListener("click", () => showProperties(el));
    return el;
}

function makeDraggable(el) {
    let isDragging = false;
    let offsetX, offsetY;

    el.addEventListener("mousedown", e => {
        if (e.target !== el) return;
        isDragging = true;
        offsetX = e.offsetX;
        offsetY = e.offsetY;
        el.style.zIndex = 1000;
    });

    document.addEventListener("mousemove", e => {
        if (!isDragging) return;
        el.style.left = `${e.pageX - offsetX}px`;
        el.style.top = `${e.pageY - offsetY}px`;
    });

    document.addEventListener("mouseup", () => {
        isDragging = false;
    });
}

function showProperties(el) {
    propertyPanel.innerHTML = `
        <label>Text:</label>
        <input type="text" id="propText" value="${el.innerText}">
        <br>
        <label>Width:</label>
        <input type="number" id="propWidth" value="${el.offsetWidth}">
        <br>
        <label>Height:</label>
        <input type="number" id="propHeight" value="${el.offsetHeight}">
    `;

    document.getElementById("propText").addEventListener("input", e => {
        el.innerText = e.target.value;
    });
    document.getElementById("propWidth").addEventListener("input", e => {
        el.style.width = e.target.value + "px";
    });
    document.getElementById("propHeight").addEventListener("input", e => {
        el.style.height = e.target.value + "px";
    });
}
