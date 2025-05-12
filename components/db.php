<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cms_database";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

function getlogin($username, $password)
{
    global $conn;

    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        if (password_verify($password, $user['password'])) {
            mysqli_stmt_close($stmt);
            return true;
        } else {
            mysqli_stmt_close($stmt);
            return false;
        }
    } else {
        mysqli_stmt_close($stmt);
        return false;
    }
}

function gettingUserId($username, $password): ?int
{
    global $conn;

    $sql = "SELECT id, password FROM users WHERE username = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        if (password_verify($password, $user['password'])) {
            return (int) $user['id'];
        }
    }

    return null;
}

function registerUser($first_name, $last_name, $email, $username, $password, $confirm_password)
{
    if ($password !== $confirm_password) {
        return "Passwords do not match!";
    }

    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    $sql = "INSERT INTO users (first_name, last_name, email, username, password, role, created_at) 
            VALUES (?, ?, ?, ?, ?, 'subscriber', NOW())";

    $stmt = mysqli_prepare($GLOBALS['conn'], $sql);
    mysqli_stmt_bind_param($stmt, "sssss", $first_name, $last_name, $email, $username, $hashed_password);

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        return "User registered successfully!";
    } else {
        mysqli_stmt_close($stmt);
        return "Error registering user: " . mysqli_error($GLOBALS['conn']);
    }
}
?>