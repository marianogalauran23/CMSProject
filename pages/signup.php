<?php
include "../components/db.php";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $_POST['name'];
    $last_name = $_POST['surname'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirmPassword'];

    $result = registerUser($first_name, $last_name, $email, $username, $password, $confirm_password);

    echo "<script>alert('$result');</script>";
    if ($result == "User registered successfully!") {
        header('Location: ../index.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tutor</title>
    <link rel="stylesheet" href="../css/signup.css">
</head>

<body>
    <div class="background"></div>
    <?php include "../components/navbar.php" ?>
    <div class="login-parent">
        <div class="overlay_container">
            <div class="login-container" id="login-container">
                <form id="login-form" method="POST">
                    <h1 id="form-title" style="color: #d9bde3">Sign-Up</h1>
                    <input type="text" name="name" placeholder="Name" required>
                    <input type="text" name="surname" placeholder="Surname" required>
                    <input type="text" name="email" placeholder="Email" required>
                    <input type="text" name="username" placeholder="Username" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <input type="password" name="confirmPassword" placeholder="Confirm Password" required>
                    <button type="submit" id="submit-button">Sign-Up</button>
                    <div>
                        <p id="toggle-signup-text">Already have an account? <a href="../index.php"
                                id="toggle-signup-link">Log-In</a></p>
                    </div>
                </form>
                <div class="social-buttons">
                    <div class="g-signin2" data-onsuccess="onSignIn">
                        <form action="auth_google.php" method="POST">
                            <input type="image" id="google-button" src="../assets/google.png" alt="Google Sign-In">
                        </form>
                    </div>

                    <form id="facebook-form" method="POST">
                        <input type="image" id="facebook-button" src="../assets/facebook.png" alt="Facebook Sign-In">
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="../javascript/register.js"></script>
    <script src="https://apis.google.com/js/platform.js" async defer></script>
    <script src="https://connect.facebook.net/en_US/sdk.js"></script>
</body>

</html>