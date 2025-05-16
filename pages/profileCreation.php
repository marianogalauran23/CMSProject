<?php
session_start();
include_once("../components/db.php");

// Check if user is logged in and get their session data
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    // User not logged in — redirect to login or show error
    header("Location: login.php");
    exit();
}

$userId = intval($_SESSION['user_id']);
$username = $_SESSION['username'];

// Placeholders and option arrays
$Placeholder = [
    "Enter your first name",
    "Enter your surname",
    "Create a username",
    "Enter your email address",
    "Choose a strong password",
    "Select your gender",
    "Select your occupation",
    "What's your purpose?",
    "Enter your location",          // new
    "Select your marital status"    // new
];

$Gender = ["Male", "Female", "Other"];
$Occupation = ["Student", "Professional", "Freelancer", "Unemployed"];
$BuildingPurp = ["Personal Project", "Client Work", "Learning", "Other"];
$MaritalStatus = ["Single", "Married", "Divorced", "Widowed"]; // new

// These are the input fields we expect in the form
$fields = ['first_name', 'last_name', 'username', 'email', 'password', 'gender', 'profession', 'educational_background', 'location', 'marital_status'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Collect non-empty input fields from POST
    $data = [];
    foreach ($fields as $field) {
        if (!empty($_POST[$field])) {
            if ($field === 'password') {
                // Hash password before storing
                $data[$field] = password_hash($_POST[$field], PASSWORD_DEFAULT);
            } else {
                $data[$field] = trim($_POST[$field]);
            }
        }
    }

    // Security check: username must be present and match session username
    if (empty($data['username']) || $data['username'] !== $username) {
        die("Invalid username or username mismatch.");
    }

    // Validate email format if provided
    if (isset($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format.");
    }

    // Check username uniqueness excluding current user (should never conflict because username fixed)
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
    $stmt->bind_param("si", $data['username'], $userId);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        die("Username already taken by another user.");
    }
    $stmt->close();

    // Check email uniqueness excluding current user if email is provided
    if (isset($data['email'])) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->bind_param("si", $data['email'], $userId);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            die("Email already registered by another user.");
        }
        $stmt->close();
    }

    if (empty($data)) {
        die("No data to update.");
    }

    // Build dynamic UPDATE query based on submitted data
    $setParts = [];
    $types = '';
    $values = [];

    foreach ($data as $col => $val) {
        $setParts[] = "$col = ?";
        $types .= 's';
        $values[] = $val;
    }

    $sql = "UPDATE users SET " . implode(", ", $setParts) . " WHERE id = ?";
    $types .= 'i';
    $values[] = $userId;

    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param($types, ...$values);

    if (!$stmt->execute()) {
        die("Update failed: " . $stmt->error);
    }
    $stmt->close();

    // Handle profile picture upload if file uploaded successfully
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = "uploads/" . $userId;
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $filename = basename($_FILES['profile_picture']['name']);
        $targetFile = $uploadDir . "/" . $filename;

        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $targetFile)) {
            // Update user's profile_image path in DB
            $stmt2 = $conn->prepare("UPDATE users SET profile_image = ? WHERE id = ?");
            $stmt2->bind_param('si', $targetFile, $userId);
            $stmt2->execute();
            $stmt2->close();
        }
    }

    // Handle cover photo upload if file uploaded successfully
    if (isset($_FILES['cover_photo']) && $_FILES['cover_photo']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = "uploads/" . $userId;
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $filename = basename($_FILES['cover_photo']['name']);
        $targetFile = $uploadDir . "/" . $filename;

        if (move_uploaded_file($_FILES['cover_photo']['tmp_name'], $targetFile)) {
            // Update cover_photo path in DB
            $stmt3 = $conn->prepare("UPDATE users SET cover_image = ? WHERE id = ?");
            $stmt3->bind_param('si', $targetFile, $userId);
            $stmt3->execute();
            $stmt3->close();
        }
    }

    // After update, redirect to profile or success page
    header("Location: profile.php");
    exit();
}

// Fetch current user data for pre-filling the form
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$userData = $result->fetch_assoc() ?: [];
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Update Profile - Webseret Builder</title>
<link rel="stylesheet" href="../css/profileCreation.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
<style>
/* Add margin-top so navbar does not overlap content */
.content-container {
    margin-top: 70px;
}
.upload-preview img {
    border-radius: 8px;
    max-width: 150px;
    max-height: 150px;
    object-fit: cover;
}
.submit-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 120px;
    height: 60px;
    border: none;
    border-radius: 12px;
    background-color: #007bff;
    color: #fff;
    cursor: pointer;
    font-size: 14px;
    text-align: center;
    padding: 0;
}

.upload-preview_cover img {
  width: 150px;
  height: 150px;
  border-radius: 17px; /* no rounding */
  object-fit: cover;
}

</style>
</head>
<body>
<?php include '../components/navbar.php' ?>

<div class="content-container">
    <aside class="sidebar">
        <!-- Sidebar content unchanged -->
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
        <div class="form-container">
            <div class="form-header">
                <h2>Update Your Profile</h2>
                <p>Fill in the details below to update your profile</p>
            </div>

            <form id="profile-form" method="POST" enctype="multipart/form-data" novalidate>
                <div class="form-section">
                    <h3 class="section-title">Personal Information</h3>
                    <div class="form-grid">

                        <div class="form-group">
                            <label for="first_name">First Name</label>
                            <div class="input-with-icon">
                                <i class="fas fa-user"></i>
                                <input type="text" id="first_name" name="first_name"
                                    placeholder="<?php echo $Placeholder[0]; ?>"
                                    value="<?php echo htmlspecialchars($userData['first_name'] ?? ''); ?>" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="last_name">Surname</label>
                            <div class="input-with-icon">
                                <i class="fas fa-user"></i>
                                <input type="text" id="last_name" name="last_name"
                                    placeholder="<?php echo $Placeholder[1]; ?>"
                                    value="<?php echo htmlspecialchars($userData['last_name'] ?? ''); ?>" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="username">Username <span style="color:red;">*</span></label>
                            <div class="input-with-icon">
                                <i class="fas fa-at"></i>
                                <input type="text" id="username" name="username" required
                                    placeholder="<?php echo $Placeholder[2]; ?>"
                                    value="<?php echo htmlspecialchars($username); ?>" readonly />
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <div class="input-with-icon">
                                <i class="fas fa-envelope"></i>
                                <input type="email" id="email" name="email"
                                    placeholder="<?php echo $Placeholder[3]; ?>"
                                    value="<?php echo htmlspecialchars($userData['email'] ?? ''); ?>" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="password">Password</label>
                            <div class="input-with-icon">
                                <i class="fas fa-lock"></i>
                                <input type="password" id="password" name="password"
                                    placeholder="<?php echo $Placeholder[4]; ?>" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Gender</label>
                            <div class="radio-group">
                                <?php foreach ($Gender as $i => $val): ?>
                                    <div class="radio-option">
                                        <input type="radio" id="gender_<?php echo $i; ?>" name="gender"
                                            value="<?php echo $val; ?>"
                                            <?php if (($userData['gender'] ?? '') === $val) echo 'checked'; ?> />
                                        <label for="gender_<?php echo $i; ?>"><?php echo $val; ?></label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="profession">Occupation</label>
                            <select id="profession" name="profession">
                                <option value="">-- Select Occupation --</option>
                                <?php foreach ($Occupation as $val): ?>
                                    <option value="<?php echo $val; ?>"
                                        <?php if (($userData['profession'] ?? '') === $val) echo 'selected'; ?>>
                                        <?php echo $val; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="educational_background">Purpose</label>
                            <select id="educational_background" name="educational_background">
                                <option value="">-- Select Purpose --</option>
                                <?php foreach ($BuildingPurp as $val): ?>
                                    <option value="<?php echo $val; ?>"
                                        <?php if (($userData['educational_background'] ?? '') === $val) echo 'selected'; ?>>
                                        <?php echo $val; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- New Location input -->
                        <div class="form-group">
                            <label for="location">Location</label>
                            <div class="input-with-icon">
                                <i class="fas fa-map-marker-alt"></i>
                                <input type="text" id="location" name="location"
                                    placeholder="<?php echo $Placeholder[8]; ?>"
                                    value="<?php echo htmlspecialchars($userData['location'] ?? ''); ?>" />
                            </div>
                        </div>

                        <!-- New Marital Status radios -->
                        <div class="form-group">
                            <label><?php echo $Placeholder[9]; ?></label>
                            <div class="radio-group">
                                <?php foreach ($MaritalStatus as $i => $val): ?>
                                    <div class="radio-option">
                                        <input type="radio" name="marital_status" id="marital_status_<?php echo $i; ?>"
                                            value="<?php echo $val; ?>"
                                            <?php if (($userData['marital_status'] ?? '') === $val) echo 'checked'; ?> />
                                        <label for="marital_status_<?php echo $i; ?>"><?php echo $val; ?></label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="form-section">
                    <h3 class="section-title">Profile Picture</h3>
                    <div class="profile-upload">
                        <div class="upload-preview">
                            <img src="<?php echo htmlspecialchars($userData['profile_image'] ?? '../placeholder-profile.jpg'); ?>"
                                alt="Profile Picture Preview" id="profile-preview" />
                        </div>
                        <div class="upload-controls">
                            <p>Upload a profile picture (optional)</p>
                            <label for="profile-upload" class="upload-btn">
                                <i class="fas fa-upload"></i> Choose File
                            </label>
                            <input type="file" id="profile-upload" name="profile_picture" accept="image/*" hidden />
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h3 class="section-title">Cover Photo</h3>
                    <div class="profile-upload">
                        <div class="upload-preview_cover" style="max-width: 300px;">
                            <img src="<?php echo htmlspecialchars($userData['cover_image'] ?? '../placeholder-cover.jpg'); ?>"
     alt="Cover Photo Preview" id="cover-preview" style="width:100%; max-height: 200px; object-fit: cover;" />
                        </div>
                        <div class="upload-controls">
                            <p>Upload a cover photo (optional)</p>
                            <label for="cover-upload" class="upload-btn">
                                <i class="fas fa-upload"></i> Choose File
                            </label>
                            <input type="file" id="cover-upload" name="cover_photo" accept="image/*" hidden />
                        </div>
                    </div>
                </div>

                <div class="form-section submit-section">
                    <button type="submit" class="submit-btn">Update Profile</button>
                </div>
            </form>
        </div>
    </main>
</div>

<script>
document.getElementById("profile-upload").addEventListener("change", function() {
    const preview = document.getElementById("profile-preview");
    const file = this.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = e => preview.src = e.target.result;
        reader.readAsDataURL(file);
    }
});

document.getElementById("cover-upload").addEventListener("change", function() {
    const preview = document.getElementById("cover-preview");
    const file = this.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = e => preview.src = e.target.result;
        reader.readAsDataURL(file);
    }
});
</script>
</body>
</html>
