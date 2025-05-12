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

 window.fbAsyncInit = function() {
        FB.init({
            appId      : '670621425725278', 
            cookie     : true,
            xfbml      : true,
            version    : 'v10.0'
        });

        document.getElementById('facebook-button').onclick = function(event) {
            event.preventDefault();
            FB.login(function(response) {
                if (response.authResponse) {
                    FB.api('/me?fields=name,email', function(response) {
                        var userData = {
                            name: response.name,
                            email: response.email,
                            facebook_id: response.id
                        };

                        fetch('auth_facebook.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify(userData)
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                window.location.href = "./dashboard.php";
                            } else {
                                alert("Error: " + data.message);
                            }
                        })
                        .catch(error => console.error('Error:', error));
                    });
                } else {
                    alert("User login failed");
                }
            }, {scope: 'email'});
        };
    };
