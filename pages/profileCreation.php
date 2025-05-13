<?php
$Placeholder = [
    "Enter First Name",
    "Enter Surname",
    "Enter Username",
    "Enter Email",
    "Enter Password",
    "Select Gender",
    "Select Life Environment Type",
    "Select Building Purpose"
];

$Gender = ["Male", "Female", "Non-Binary", "Prefered Private"];

$Occupation = [
    "Student",
    "Company Worker",
    "Personal Training and Preferences",
    "Others: "
];

$BuildingPurp = [
    "School Works",
    "Office Works",
    "Personal Training",
    "Project Documentation and References",
    "Others: "
];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Profile - Webseret Builder</title>
    <link rel="stylesheet" href="../css/profileCreation.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <header id="header">
        <div id="logo">
            <h1>Webseret Builder</h1>
        </div>
        <button class="mobile-menu-toggle">
            <i class="fas fa-bars"></i>
        </button>
        <nav id="menu">
            <ul>
                <li><a href="#" class="nav-link">Home</a></li>
                <li><a href="#" class="nav-link">Settings</a></li>
                <li>
                    <div class="user-avatar">
                        <img src="../css/images/profile.jpg?height=40&width=40" alt="User Avatar">
                    </div>
                </li>
            </ul>
        </nav>
    </header>

    <div class="content-container">
        <!-- Sidebar Menu -->
        <aside class="sidebar">
            <div class="sidebar-section">
                <h3 class="sidebar-section-title">Main</h3>
                <ul class="sidebar-menu">
                    <li class="sidebar-menu-item">
                        <a href="#" class="sidebar-menu-link">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item">
                        <a href="#" class="sidebar-menu-link active">
                            <i class="fas fa-user-plus"></i>
                            <span>Create Profile</span>
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
                    <li class="sidebar-menu-item">
                        <a href="#" class="sidebar-menu-link">
                            <i class="fas fa-handshake"></i>
                            <span>Collaborations</span>
                        </a>
                    </li>
                </ul>
            </div>
        </aside>

        <!-- Main Content -->
        <main id="main-content">
            <div class="form-container">
                <div class="form-header">
                    <h2>Create Your Profile</h2>
                    <p>Fill in the details below to set up your profile</p>
                </div>

                <form id="profile-form">
                    <!-- Personal Information Section -->
                    <div class="form-section">
                        <h3 class="section-title">Personal Information</h3>

                        <div class="form-grid">
                            <div class="form-group">
                                <label for="firstName">First Name</label>
                                <div class="input-with-icon">
                                    <i class="fas fa-user"></i>
                                    <input type="text" id="firstName" placeholder="<?php echo $Placeholder[0]; ?>"
                                        required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="surname">Surname</label>
                                <div class="input-with-icon">
                                    <i class="fas fa-user"></i>
                                    <input type="text" id="surname" placeholder="<?php echo $Placeholder[1]; ?>"
                                        required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="username">Username</label>
                                <div class="input-with-icon">
                                    <i class="fas fa-at"></i>
                                    <input type="text" id="username" placeholder="<?php echo $Placeholder[2]; ?>"
                                        required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="email">Email</label>
                                <div class="input-with-icon">
                                    <i class="fas fa-envelope"></i>
                                    <input type="email" id="email" placeholder="<?php echo $Placeholder[3]; ?>"
                                        required>
                                </div>
                            </div>

                            <div class="form-group full-width">
                                <label for="password">Password</label>
                                <div class="input-with-icon">
                                    <i class="fas fa-lock"></i>
                                    <input type="password" id="password" placeholder="<?php echo $Placeholder[4]; ?>"
                                        required>
                                    <button type="button" class="toggle-password">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Preferences Section -->
                    <div class="form-section">
                        <h3 class="section-title">Preferences</h3>

                        <div class="form-grid">
                            <!-- Gender Selection -->
                            <div class="form-group">
                                <label><?php echo $Placeholder[5]; ?></label>
                                <div class="radio-group">
                                    <?php foreach ($Gender as $i => $val) { ?>
                                        <div class="radio-option">
                                            <input type="radio" name="gender" id="gender_<?php echo $i; ?>"
                                                value="<?php echo $val; ?>">
                                            <label for="gender_<?php echo $i; ?>"><?php echo $val; ?></label>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>

                            <!-- Occupation Selection -->
                            <div class="form-group">
                                <label><?php echo $Placeholder[6]; ?></label>
                                <div class="radio-group">
                                    <?php foreach ($Occupation as $i => $val) { ?>
                                        <div class="radio-option">
                                            <input type="radio" name="occupation" id="occupation_<?php echo $i; ?>"
                                                value="<?php echo $val; ?>">
                                            <label for="occupation_<?php echo $i; ?>"><?php echo $val; ?></label>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Purpose Section -->
                    <div class="form-section">
                        <h3 class="section-title">Purpose</h3>

                        <div class="form-group full-width">
                            <label><?php echo $Placeholder[7]; ?></label>
                            <div class="radio-group purpose-group">
                                <?php foreach ($BuildingPurp as $i => $val) { ?>
                                    <div class="radio-option purpose-option">
                                        <input type="radio" name="purpose" id="purpose_<?php echo $i; ?>"
                                            value="<?php echo $val; ?>">
                                        <label for="purpose_<?php echo $i; ?>"><?php echo $val; ?></label>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>

                    <!-- Profile Picture Upload -->
                    <div class="form-section">
                        <h3 class="section-title">Profile Picture</h3>

                        <div class="profile-upload">
                            <div class="upload-preview">
                                <img src="../placeholder.svg?height=150&width=150" alt="Profile Preview"
                                    id="profile-preview">
                            </div>
                            <div class="upload-controls">
                                <p>Upload a profile picture (optional)</p>
                                <label for="profile-upload" class="upload-btn">
                                    <i class="fas fa-upload"></i> Choose File
                                </label>
                                <input type="file" id="profile-upload" accept="image/*" hidden>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create Profile</button>
                    </div>
                </form>
            </div>
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
        // Simple script to toggle mobile menu
        document.querySelector('.mobile-menu-toggle').addEventListener('click', function () {
            document.getElementById('menu').classList.toggle('active');
        });

        // Toggle password visibility
        document.querySelector('.toggle-password').addEventListener('click', function () {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('i');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });

        // Profile picture preview
        document.getElementById('profile-upload').addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    document.getElementById('profile-preview').src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>

</html>