<?php
include __DIR__ . '/components/db.php';

session_start();
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
header("Pragma: no-cache");
header("Expires: 0");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['signupbutton'])) {
        header('Location: pages/signup.php');
        exit();
    }

    if (isset($_POST['loginbtn'])) {

        if (isset($_POST['username']) && isset($_POST['password'])) {
            $username = $_POST['username'];
            $password = $_POST['password'];

            if (getlogin($username, $password)) {
                $_SESSION['username'] = $username;
                header('Location: pages/dashboard.php');
                exit();
            } else {
                echo "<script>alert('Invalid username or password');</script>";
            }
        } else {
            echo "<script>alert('Username or password not set.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Cache-Control" content="no-store" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/index.css">
    <title>BrightDesk - Free Website Creation Management</title>
</head>

<body>
    <nav>
        <img src="./assets/logo_dark.png" alt="Logo" draggable="false" style="width: 170px; height: 150px;">
        <ul>
            <li><a href="index.html">Home</a></li>
            <li><a href="about.html">About</a></li>
            <li><a href="services.html">Documentation</a></li>
            <li><a href="contact.html">Contact</a></li>
        </ul>
    </nav>
    <div class="hero">
        <img src="./assets/team.jpg" alt="Hero Image" class="hero-image"
            style="width: 100%; height: auto; max-width: 100vw;">
        <div id="hero-text">
            <h1>Welcome to BrightDesk</h1>
            <p>Unleash your creativity, Go bold, Go BrightDesk. Stop dreaming about your perfect website, start building
                it.</p>
            <p> BrightDesk offers an easy, intuitive way to bring your creative ideas to life online – no complex code
                required. Go bold, go bright, go live effortlessly.</p>
        </div>
        <div id="login">
            <h1>Let's Start this!</h1>
            <form method="POST">
                <div id="login-form">
                    <input type="text" name="username" placeholder="Username" class="username">
                    <input type="password" name="password" placeholder="Password" class="password">
                </div>
                <div id="login-buttons">
                    <button type="submit" name="loginbtn" class="loginbtn">Login</button>
                    <button type="submit" name="signupbutton" class="signupbutton">No Account?</button>
                </div>
                <div class="social-buttons">
                    <input type="image" id="google-button" src="./assets/google.png" alt="Google Sign-In">
                    <input type="image" id="facebook-button" src="./assets/facebook.png" alt="Facebook Sign-In">
                </div>
            </form>
        </div>
    </div>
    <img src="./assets/3d_render_adobe.png" alt="Hero Image" class="floating-image"
        style="width: 40%; height: auto; max-width: 100vw;">
    <div class="reviews">
        <h1>🌟 Trusted by creators, startups, and small businesses</h1>
        <div class="reviews_container">
            <p>“BrightDesk helped us launch our site in just one afternoon. Super clean interface!”
                — Happy User</p>
            <p>“I love how easy it is to use BrightDesk. I was able to create my website in no time!”
                — Satisfied Customer</p>
            <p>“BrightDesk is a game-changer for anyone looking to create a website without the hassle of coding.”
                — Enthusiastic User</p>
        </div>
    </div>
    <img src="./assets/prgramming.jpg" alt="programming" class="programming_img" style="width: 100%; height: auto;" />
    <div class="middle_text">
        <h1>Still having doubts?</h1>
        <p>Visit our Documentation to learn more about how to create your website.</p>
    </div>
    <div class="qna">
        <h1>💬 Still have questions?</h1>
        <p>Don't hesitate to reach out to us. We're here to help.</p>
        <p>Contact us or check out our Help Center</p>
    </div>
    <img src="./assets/3d_rocket.png" alt="floating-image-rocket" class="floating-image-rocket"
        style="width: 40%; height: auto; max-width: 100vw;">
    <div class="footer">
        <h1>Try BrightDesk today.</h1>
        <p>No coding. No stress. Just content.</p>
    </div>
    <script src="./javascript/index.js"></script>
</body>

</html>