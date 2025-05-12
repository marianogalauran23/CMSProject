<?php
include "../components/db.php";
session_start();

$username = $_SESSION['username'];
$user_id = $_SESSION['user_id'];

$sql = "SELECT pages.id, pages.title, users.username AS author, 
               COALESCE(pages.updated_at, pages.created_at) AS last_modified, 
               pages.thumbnail
        FROM pages 
        INNER JOIN users ON pages.author_id = users.id 
        WHERE users.id = ?
        ORDER BY last_modified DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BrightDesk Dashboard</title>
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
        <h1 id="welcome">Welcome Back! <?php echo htmlspecialchars($username); ?></h1>
        <p>You currently have <?php echo $result->num_rows; ?> project(s)</p>

        <div class="card_container">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="webpage_card" data-id="<?php echo $row['id']; ?>" oncontextmenu="showCardMenu(event, this)">
                    <img src="<?php echo htmlspecialchars($row['thumbnail']) ?: '../assets/placeholder.jpg'; ?>"
                        alt="Project Thumbnail" class="project_thumbnail">
                    <h2><?php echo htmlspecialchars($row['title']); ?></h2>
                    <p>By: <?php echo htmlspecialchars($row['author']); ?></p>
                    <p>Last Updated: <?php echo date("F j, Y, g:i a", strtotime($row['last_modified'])); ?></p>
                </div>
            <?php endwhile; ?>

            <a href="add_project.php" class="add_card">
                <img src="../assets/add_light.png" alt="add" class="add_icon"
                    style="width: 45%; height: auto; max-width: 100vw;">
                <h1>New Webpage</h1>
            </a>
        </div>
    </div>
    <div id="cardMenu" class="context_menu">
        <button onclick="openPage()">Open</button>
        <button onclick="editPage()">Edit</button>
        <button onclick="deletePage()">Delete</button>
    </div>
    <script src="../javascript/dashboard.js"></script>
</body>

</html>