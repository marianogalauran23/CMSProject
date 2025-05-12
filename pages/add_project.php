<?php
include "../components/db.php";
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

function generateSlug($title)
{
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
    return $slug;
}

function getUniqueSlug($conn, $slug)
{
    $baseSlug = $slug;
    $i = 1;

    while (true) {
        $stmt = $conn->prepare("SELECT id FROM pages WHERE slug = ?");
        $stmt->bind_param("s", $slug);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            return $slug;
        }

        $slug = $baseSlug . '-' . $i;
        $i++;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $slug = generateSlug($title);
    $slug = getUniqueSlug($conn, $slug);

    $stmt = $conn->prepare("INSERT INTO pages (title, slug, author_id, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("ssi", $title, $slug, $user_id);
    $stmt->execute();

    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Add Project</title>
</head>

<body>
    <h1>Add a New Project</h1>
    <form method="post">
        <label>Project Title:</label><br>
        <input type="text" name="title" required><br><br>
        <input type="submit" value="Add Project">
    </form>
</body>

</html>