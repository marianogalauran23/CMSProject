<?php
session_start();

include "../components/db.php";

if (!isset($_SESSION['username']) || !isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$username = $_SESSION['username'];
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT first_name, last_name, email, profile_image, cover_image, gender, educational_background, profession, location, marital_status FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    die("User not found");
}
$user = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Webseret Builder - User Profile</title>
    <link rel="stylesheet" href="../css/profile.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <?php include "../components/navbar.php" ?>

    <div class="content-container">
        <aside class="sidebar">
            <div class="sidebar-section">
                <h3 class="sidebar-section-title">Main</h3>
                <ul class="sidebar-menu">
                    <li class="sidebar-menu-item">
                        <a href="#" class="sidebar-menu-link active">
                            <i class="fas fa-user"></i>
                            <span>Profile</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item">
                        <a href="#" class="sidebar-menu-link">
                            <i class="fas fa-cog"></i>
                            <span>Settings</span>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="sidebar-section">
                <h3 class="sidebar-section-title">Content</h3>
                <ul class="sidebar-menu">
                    <li class="sidebar-menu-item">
                        <a href="#" class="sidebar-menu-link">
                            <i class="fas fa-newspaper"></i>
                            <span>Posts</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item">
                        <a href="#" class="sidebar-menu-link">
                            <i class="fas fa-rss"></i>
                            <span>Feed</span>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="sidebar-section">
                <h3 class="sidebar-section-title">Social</h3>
                <ul class="sidebar-menu">
                    <li class="sidebar-menu-item">
                        <a href="#" class="sidebar-menu-link">
                            <i class="fas fa-users"></i>
                            <span>Friends</span>
                        </a>
                    </li>
                </ul>
            </div>
        </aside>

        <main id="main-content">
            <section id="profile-container">
                <div class="profile-banner">
                    <img src="<?= $user['cover_image'] ?? '../css/images/Background.jpg' ?>" alt="Profile Banner">
                </div>

                <div id="user-profile">
                    <div class="profile-header">
                        <div class="profile-picture-container">
                            <img id="profile-picture" src="<?= $user['profile_image'] ?? '../css/images/profile.jpg' ?>"
                                alt="Profile Picture">
                        </div>

                        <div class="profile-info">
                            <h2 id="user-name"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>
                            </h2>
                            <p id="user-email"><?= htmlspecialchars($user['email']) ?></p>
                            <button class="edit-profile-btn" onClick="goToEditProfile()">Edit Profile</button>
                        </div>
                    </div>

                    <div class="profile-details">
                        <div class="detail-item">
                            <i class="fas fa-venus-mars"></i>
                            <span><?= htmlspecialchars($user['gender'] ?? 'Not specified') ?></span>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-home"></i>
                            <span><?= htmlspecialchars($user['educational_background'] ?? 'N/A') ?></span>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-briefcase"></i>
                            <span><?= htmlspecialchars($user['profession'] ?? 'N/A') ?></span>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span><?= htmlspecialchars($user['location'] ?? 'Unknown') ?></span>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-user-tie"></i>
                            <span><?= htmlspecialchars($user['marital_status'] ?? 'N/A') ?></span>
                        </div>
                    </div>
                </div>

                <!-- Static User Posts for Now -->
                <section id="user-posts">
                    <h3 class="section-title">Recent Projects</h3>
                    <div class="post-grid">
                        <!-- Post cards here (left as-is) -->
                    </div>
                    <button class="load-more-btn">Load More Posts</button>
                </section>
            </section>
        </main>
    </div>

    <footer id="footer">
        <div class="footer-content">
            <p>&copy; 2023 Webseret Builder. All rights reserved.</p>
            <div class="footer-links">
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Service</a>
                <a href="#">Contact Us</a>
            </div>
        </div>
    </footer>

    <script>
        function goToEditProfile() {
            window.location.href = 'profileCreation.php';
        }

        document.querySelector('.mobile-menu-toggle')?.addEventListener('click', function () {
            document.getElementById('menu').classList.toggle('active');
        });
    </script>
</body>

</html>