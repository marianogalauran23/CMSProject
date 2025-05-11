const container = document.querySelector('.overlay_container');
const background = document.querySelector('.background');

function applyBlur() {
    background.style.filter = "brightness(0.4) contrast(0.9) blur(7px)";
    background.style.transform = "scale(2.05)";
    container.style.transform = "scale(1.08)";
    container.style.transition = "transform 0.2s ease-in-out";
}

function removeBlur() {
    background.style.filter = "none";
    background.style.transform = "scale(1)";
    container.style.transform = "scale(1)";
    container.style.transition = "transform 0.3s ease-in-out";
}

// Track if mouse is inside the container
let isInside = false;

document.addEventListener('mousemove', (e) => {
    const rect = container.getBoundingClientRect();
    const withinX = e.clientX >= rect.left && e.clientX <= rect.right;
    const withinY = e.clientY >= rect.top && e.clientY <= rect.bottom;

    if (withinX && withinY) {
        if (!isInside) {
            isInside = true;
            applyBlur();
        }
    } else {
        if (isInside) {
            isInside = false;
            removeBlur();
        }
    }
});
