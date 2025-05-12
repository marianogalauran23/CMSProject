window.addEventListener('popstate', function (event) {
  window.location.href = 'index.php';
});

let selectedCard = null;

function showCardMenu(event, card) {
    event.preventDefault();
    selectedCard = card;

    const menu = document.getElementById('cardMenu');
    menu.style.left = event.pageX + 'px';
    menu.style.top = event.pageY + 'px';
    menu.style.display = 'block';
}

function hideCardMenu() {
    document.getElementById('cardMenu').style.display = 'none';
}

document.addEventListener('click', function () {
    hideCardMenu();
});

function openPage() {
    alert("Open page: " + selectedCard.querySelector('h2').textContent);
    hideCardMenu();
}

function editPage() {
    alert("Edit page: " + selectedCard.querySelector('h2').textContent);
    hideCardMenu();
}

function deletePage() {
    const id = selectedCard.getAttribute('data-id');
    if (!id) return;

    if (confirm("Are you sure you want to delete this page?")) {
        fetch("../components/delete_page.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: "id=" + encodeURIComponent(id)
        })
        .then(res => res.text())
        .then(res => {
            if (res.trim() === "success") {
                selectedCard.remove();
                alert("Page deleted.");
            } else {
                alert("Failed to delete.");
            }
        });
    }

    hideCardMenu();
}