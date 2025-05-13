<?php
session_start();
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
        <!-- Sidebar Menu -->
        <aside class="sidebar">
            <div class="sidebar-section">
                <h3 class="sidebar-section-title">Main</h3>
                <ul class="sidebar-menu">
                    <li class="sidebar-menu-item">
                        <a href="#" class="sidebar-menu-link active">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item">
                        <a href="#" class="sidebar-menu-link">
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
                    <li class="sidebar-menu-item">
                        <a href="#" class="sidebar-menu-link">
                            <i class="fas fa-bookmark"></i>
                            <span>Saved</span>
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
                    <li class="sidebar-menu-item">
                        <a href="#" class="sidebar-menu-link">
                            <i class="fas fa-user-plus"></i>
                            <span>Connections</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item">
                        <a href="#" class="sidebar-menu-link">
                            <i class="fas fa-handshake"></i>
                            <span>Collaborations</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item">
                        <a href="#" class="sidebar-menu-link">
                            <i class="fas fa-envelope"></i>
                            <span>Messages</span>
                        </a>
                    </li>
                </ul>
            </div>
        </aside>

        <!-- Main Content -->
        <main id="main-content">
            <!-- Profile Section -->
            <section id="profile-container">
                <!-- Background Banner -->
                <div class="profile-banner">
                    <img src="../css/images/Background.jpg?height=200&width=1000" alt="Profile Banner">
                </div>

                <div id="user-profile">
                    <div class="profile-header">
                        <!-- Profile Picture -->
                        <div class="profile-picture-container">
                            <img id="profile-picture" src="../css/images/profile.jpg?height=150&width=150"
                                alt="Profile Picture">
                        </div>

                        <div class="profile-info">
                            <h2 id="user-name">John Doe</h2>
                            <p id="user-email">johndoe@example.com</p>
                            <button class="edit-profile-btn" onClick="goToEditProfile()">Edit Profile</button>
                        </div>
                    </div>

                    <div class="profile-details">
                        <div class="detail-item">
                            <i class="fas fa-venus-mars"></i>
                            <span>Male</span>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-home"></i>
                            <span>Urban</span>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-briefcase"></i>
                            <span>Professional</span>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>New York, USA</span>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-user-tie"></i>
                            <span>Individual</span>
                        </div>
                    </div>
                </div>

                <!-- User Posts Section -->
                <section id="user-posts">
                    <h3 class="section-title">Recent Posts</h3>

                    <div class="post-grid">
                        <!-- Post 1 -->
                        <div class="post-card">
                            <div class="post-image">
                                <img src="../css/images/Management.jpg?height=200&width=300" alt="Post Image">
                            </div>
                            <div class="post-content">
                                <h4>Getting Started with Web Development</h4>
                                <p class="post-date">Posted on May 12, 2023</p>
                                <p class="post-excerpt">Learn the basics of HTML, CSS, and JavaScript to kickstart your
                                    web development journey.</p>
                                <div class="post-actions">
                                    <button class="action-btn"><i class="far fa-heart"></i> 24</button>
                                    <button class="action-btn"><i class="far fa-comment"></i> 8</button>
                                    <button class="action-btn"><i class="far fa-share-square"></i></button>
                                </div>
                            </div>
                        </div>

                        <!-- Post 2 -->
                        <div class="post-card">
                            <div class="post-image">
                                <img src="../css/images/compiler.webp?height=200&width=300" alt="Post Image">
                            </div>
                            <div class="post-content">
                                <h4>Responsive Design Techniques</h4>
                                <p class="post-date">Posted on April 28, 2023</p>
                                <p class="post-excerpt">Explore modern approaches to creating responsive websites that
                                    work on all devices.</p>
                                <div class="post-actions">
                                    <button class="action-btn"><i class="far fa-heart"></i> 42</button>
                                    <button class="action-btn"><i class="far fa-comment"></i> 15</button>
                                    <button class="action-btn"><i class="far fa-share-square"></i></button>
                                </div>
                            </div>
                        </div>
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
        const editButton = document.getElementById('editButton');

        document.querySelector('.mobile-menu-toggle').addEventListener('click', function () {
            document.getElementById('menu').classList.toggle('active');
        });

        function goToEditProfile() {
            window.location.href = 'profileCreation.php';
        }

    </script>
</body>

</html>