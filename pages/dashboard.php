<?php
session_start();

if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
} else {
    $username = "Guest";
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BrightDesk</title>
    <link rel="stylesheet" href="../css/dashboard.css">
</head>

<body>
    <nav>
        <img src="../assets/logo_light.png" alt="Logo" draggable="false" style="width: 170px; height: 150px;">
        <ul>
            <li><a href="index.html">Home</a></li>
            <li><a href="about.html">About</a></li>
            <li><a href="services.html">Documentation</a></li>
            <li><a href="contact.html">Contact</a></li>
        </ul>
    </nav>
    <div class="content">
        <h1 id="welcome">Welcome Back! <?php echo $username; ?></h1>
        <p>You currently have "number" events</p>
        <div class="card_container">
            <div class="webpage_card">
            </div>
            <div class="add_card">
                <img src="../assets/add_light.png" alt="add" class="add_icon"
                    style="width: 45%; height: auto; max-width: 100vw;">
                <h1>New Webpage</h1>
            </div>
        </div>
    </div>
    <script src="../javascript/dashboard.js"></script>
</body>

</html>