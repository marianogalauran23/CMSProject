const profilecontainer = document.querySelector('.profile');
const profileImage = document.querySelector('.profile-image');
const profileName = document.querySelector('.profile-text h2'); 
const profiletext = document.querySelector('.profile-text');
const profileDetails = document.querySelectorAll('.profile-text h3');
const Logout = document.querySelector('.Logout');

profilecontainer.addEventListener('click', () => {
    window.location.href = "profile.php";
});

window.addEventListener('popstate', function () {
    fetch("components/destroy_session.php", {
    method: "POST"
    })
    .then(response => response.text())
    .then(data => {
    console.log(data);
    window.location.href = "../index.php";
    });

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

function openAddProjectModal() {
    document.getElementById('addProjectModal').style.display = 'flex';
}

function closeAddProjectModal() {
    document.getElementById('addProjectModal').style.display = 'none';
}

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('addProjectForm');
    if (!form) return;

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const title = document.getElementById('title').value.trim();

        if (title === "") {
            alert("Please enter a project title.");
            return;
        }

        fetch("add_project.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: "title=" + encodeURIComponent(title)
        })
        .then(response => {
            if (response.redirected) {
                window.location.href = response.url;
            } else {
                return response.text();
            }
        })
        .catch(error => {
            console.error("Error:", error);
            alert("Something went wrong. Please try again.");
        });
    });
});

function ProfileHighlight() {
    profilecontainer.style.transform = "scale(1.08)";
    profilecontainer.style.transition = "transform 0.2s ease-in-out";
    profilecontainer.querySelector('.overlay').style.opacity = "1"; 
    profilecontainer.querySelector('.overlay').style.transition = "opacity 1s ease-in-out";

    profileName.style.transform = "translateY(-8px)";
    profileName.style.color = "white";


    profiletext.style.backgroundColor = "rgba(88, 81, 81, 0.25)";
    profiletext.style.backdropFilter = "blur(10px)";
    profiletext.style.transition = "background-color 0.3s ease-in-out, backdrop-filter 0.3s ease-in-out";

    setTimeout(() => {
        profileDetails.forEach(item => {
            item.style.transform = "translateY(-5px)";
            item.style.color = "white";
        });
    }, 100);

    profileName.style.transition = "transform 0.8s ease-in-out, color 0.3s ease-in-out";
    profileDetails.forEach(item => {
        item.style.transition = "transform 0.3s ease-in-out, color 0.3s ease-in-out";
    });
}

function ProfileUnhighlight() {
    profilecontainer.style.transform = "scale(1)";
    profilecontainer.style.transition = "transform 0.3s ease-in-out";
    profilecontainer.querySelector('.overlay').style.opacity = "0";
    profilecontainer.querySelector('.overlay').style.transition = "opacity 1s ease-in-out";

    profileName.style.transform = "translateY(0px)";
    profileName.style.color = "white";

    profiletext.style.backgroundColor = "#0a192f80";
    profiletext.style.backdropFilter = "blur(5px)";
    profiletext.style.transition = "background-color 0.3s ease-in-out, backdrop-filter 0.3s ease-in-out";

    setTimeout(() => {
        profileDetails.forEach(item => {
            item.style.transform = "translateY(0px)";
            item.style.color = "white";
        });
    }, 100);

    profileName.style.transition = "transform 0.3s ease-in-out, color 0.3s ease-in-out";
    profileDetails.forEach(item => {
        item.style.transition = "transform 0.3s ease-in-out, color 0.3s ease-in-out";
    });
}

Logout.addEventListener('click', function (event) {
    event.preventDefault();
    fetch("components/destroy_session.php", {
        method: "POST"
    })
    .then(response => response.text())
    .then(data => {
        console.log(data);
        window.location.href = "../index.php";
    });
});

profilecontainer.addEventListener('mouseenter', ProfileHighlight);
profilecontainer.addEventListener('mouseleave', ProfileUnhighlight);


window.addEventListener('load', ProfileUnhighlight);
