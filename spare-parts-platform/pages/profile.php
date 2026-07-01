<?php
session_start();
include '../includes/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = (int)$_SESSION['user_id'];

// Fetch current user info
$stmt = $conn->prepare("SELECT user_id, name, email, phone, user_type, location, state, city, created_at FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) {
    header("Location: logout.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - SparePartsNG</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .profile-page { padding: 110px 0 60px; min-height: 100vh; background: var(--black, #0d1117); }
        .profile-card {
            background: var(--dark-gray, #161b22);
            border: 1px solid var(--border-gray, #484f58);
            border-radius: 12px;
            max-width: 680px;
            margin: 0 auto;
            overflow: hidden;
        }
        .profile-header {
            background: linear-gradient(135deg, #e63946 0%, #c1121f 100%);
            padding: 2rem;
            text-align: center;
        }
        .profile-avatar {
            width: 80px; height: 80px;
            border-radius: 50%;
            background: rgba(255,255,255,0.2);
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1rem;
            font-size: 2.2rem; color: #fff;
        }
        .profile-header h2 { color: #fff; margin: 0 0 4px; font-size: 1.5rem; }
        .profile-header .user-type-badge {
            display: inline-block;
            background: rgba(255,255,255,0.2);
            color: #fff;
            padding: 2px 12px;
            border-radius: 20px;
            font-size: 0.82rem;
            text-transform: capitalize;
        }
        .profile-body { padding: 2rem; }
        .profile-row {
            display: flex;
            align-items: flex-start;
            padding: 14px 0;
            border-bottom: 1px solid var(--border-gray, #30363d);
        }
        .profile-row:last-child { border-bottom: none; }
        .profile-row .label {
            width: 160px;
            flex-shrink: 0;
            color: var(--text-gray, #8b949e);
            font-size: 0.88rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .profile-row .value {
            color: var(--light-text, #c9d1d9);
            font-size: 0.95rem;
            flex: 1;
        }
        .profile-row .value.empty { color: var(--text-gray, #8b949e); font-style: italic; }
        .profile-actions {
            padding: 1.5rem 2rem;
            border-top: 1px solid var(--border-gray, #30363d);
            display: flex;
            gap: 1rem;
        }
        .btn-edit-profile {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 22px;
            background: linear-gradient(135deg, #e63946 0%, #c1121f 100%);
            color: #fff;
            text-decoration: none;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            transition: opacity 0.2s, transform 0.2s;
        }
        .btn-edit-profile:hover { opacity: 0.9; transform: translateY(-1px); color: #fff; }
        .btn-logout-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 22px;
            background: transparent;
            border: 1px solid var(--border-gray, #484f58);
            color: var(--text-gray, #8b949e);
            text-decoration: none;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            transition: border-color 0.2s, color 0.2s;
        }
        .btn-logout-link:hover { border-color: #f85149; color: #f85149; }
    </style>
</head>
<body>
    <?php include '../includes/navbar.php'; ?>

    <section class="profile-page">
        <div class="container">
            <div class="profile-card">
                <div class="profile-header">
                    <div class="profile-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <h2><?php echo htmlspecialchars($user['name']); ?></h2>
                    <span class="user-type-badge"><?php echo htmlspecialchars($user['user_type']); ?></span>
                </div>

                <div class="profile-body">
                    <div class="profile-row">
                        <span class="label"><i class="fas fa-user"></i> Full Name</span>
                        <span class="value"><?php echo htmlspecialchars($user['name']); ?></span>
                    </div>
                    <div class="profile-row">
                        <span class="label"><i class="fas fa-envelope"></i> Email</span>
                        <span class="value"><?php echo htmlspecialchars($user['email']); ?></span>
                    </div>
                    <div class="profile-row">
                        <span class="label"><i class="fas fa-phone"></i> Phone Number</span>
                        <span class="value <?php echo empty($user['phone']) ? 'empty' : ''; ?>">
                            <?php echo !empty($user['phone']) ? htmlspecialchars($user['phone']) : 'Not set'; ?>
                        </span>
                    </div>
                    <div class="profile-row">
                        <span class="label"><i class="fas fa-map-marker-alt"></i> Address</span>
                        <span class="value <?php echo empty($user['location']) ? 'empty' : ''; ?>">
                            <?php echo !empty($user['location']) ? htmlspecialchars($user['location']) : 'Not set'; ?>
                        </span>
                    </div>
                    <div class="profile-row">
                        <span class="label"><i class="fas fa-city"></i> City</span>
                        <span class="value <?php echo empty($user['city']) ? 'empty' : ''; ?>">
                            <?php echo !empty($user['city']) ? htmlspecialchars($user['city']) : 'Not set'; ?>
                        </span>
                    </div>
                    <div class="profile-row">
                        <span class="label"><i class="fas fa-map"></i> State</span>
                        <span class="value <?php echo empty($user['state']) ? 'empty' : ''; ?>">
                            <?php echo !empty($user['state']) ? htmlspecialchars($user['state']) : 'Not set'; ?>
                        </span>
                    </div>
                    <div class="profile-row">
                        <span class="label"><i class="fas fa-id-badge"></i> User Type</span>
                        <span class="value" style="text-transform: capitalize;"><?php echo htmlspecialchars($user['user_type']); ?></span>
                    </div>
                    <div class="profile-row">
                        <span class="label"><i class="fas fa-calendar-alt"></i> Member Since</span>
                        <span class="value"><?php echo date('F j, Y', strtotime($user['created_at'])); ?></span>
                    </div>
                </div>

                <div class="profile-actions">
                    <a href="edit_profile.php" class="btn-edit-profile" id="editProfileBtn">
                        <i class="fas fa-pencil-alt"></i> Edit Profile
                    </a>
                    <a href="logout.php" class="btn-logout-link" id="logoutProfileBtn">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </section>

    <script src="../assets/js/script.js"></script>
</body>
</html>
