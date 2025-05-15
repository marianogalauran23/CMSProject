<?php
include "../components/db.php";
session_start();

if (!isset($_SESSION['username']) || !isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

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

if (isset($_GET['edit'])) {
    $_SESSION['edit_page_id'] = (int) $_GET['edit'];
    header("Location: editPage.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>BrightDesk Dashboard</title>
    <link rel="stylesheet" href="../css/dashboard.css">
</head>

<body>
    <?php include "../components/navbar.php" ?>

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

            <div class="add_card" onclick="openAddProjectModal()">
                <img src="../assets/add_light.png" alt="add" class="add_icon"
                    style="width: 65%; height: auto; max-width: 100vw;">
                <h1>New Webpage</h1>
            </div>
        </div>
    </div>
    <div class="profile">
        <div class="profile-image">
            <?php
            $stmt = $conn->prepare("SELECT profile_image FROM users WHERE id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            $profileImage = "../assets/default_profile.png";

            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                if (!empty($user['profile_image'])) {
                    $profileImage = htmlspecialchars($user['profile_image']);
                }
            }
            ?>
            <img src="<?php echo $profileImage; ?>" alt="Profile" class="profile_icon">
        </div>

        <div class="overlay"></div>
        <div class="profile-text">
            <h2><?php echo htmlspecialchars($username); ?></h2>
            <h3><?php
            $stmt = $conn->prepare(query: "SELECT first_name, last_name FROM users WHERE id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();

            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                echo htmlspecialchars($user['first_name'] . " " . $user['last_name']);
            } else {
                echo "Guest";
            }
            ?></h3>
            <h3><?php
            $stmt = $conn->prepare("SELECT bio FROM users WHERE id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                echo htmlspecialchars($user['bio']);
            } else {
                echo " ";
            }
            ?></h3>
            <p><a class="Logout" style="color: red">Logout</a></p>
        </div>
    </div>
    <div id="cardMenu" class="context_menu">
        <button onclick="openPage()">Open</button>
        <button onclick="editPage()">Edit</button>
        <button onclick="deletePage()">Delete</button>
    </div>

    <div id="addProjectModal" class="modal-overlay">
        <div class="modal-card">
            <h2>Add a New Project</h2>
            <form id="addProjectForm">
                <label for="title">Project Title:</label><br>
                <input type="text" id="title" name="title" required><br><br>
                <div class="modal-buttons">
                    <button type="submit">Add</button>
                    <button type="button" onclick="closeAddProjectModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../javascript/dashboard.js"></script>
</body>

</html>