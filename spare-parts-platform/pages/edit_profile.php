<?php
session_start();
include '../includes/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = (int)$_SESSION['user_id'];
$success = '';
$error   = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_profile'])) {
    $name    = trim($_POST['name']    ?? '');
    $phone   = trim($_POST['phone']   ?? '');
    $address = trim($_POST['address'] ?? '');
    $state   = trim($_POST['state']   ?? '');
    $city    = trim($_POST['city']    ?? '');

    if (empty($name)) {
        $error = 'Full Name is required.';
    } else {
        $stmt = $conn->prepare(
            "UPDATE users SET name = ?, phone = ?, location = ?, state = ?, city = ? WHERE user_id = ?"
        );
        $stmt->bind_param("sssssi", $name, $phone, $address, $state, $city, $user_id);
        if ($stmt->execute()) {
            $stmt->close();
            // Update session name in case it changed
            $_SESSION['user_name'] = $name;
            header("Location: profile.php?updated=1");
            exit();
        } else {
            $stmt->close();
            $error = 'Update failed. Please try again.';
        }
    }
}

// Fetch current user info
$stmt = $conn->prepare("SELECT name, email, phone, user_type, location, state, city, created_at FROM users WHERE user_id = ?");
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
    <title>Edit Profile - SparePartsNG</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .edit-profile-page { padding: 110px 0 60px; min-height: 100vh; background: var(--black, #0d1117); }
        .edit-card {
            background: var(--dark-gray, #161b22);
            border: 1px solid var(--border-gray, #484f58);
            border-radius: 12px;
            max-width: 600px;
            margin: 0 auto;
            overflow: hidden;
        }
        .edit-card-header {
            background: linear-gradient(135deg, #e63946 0%, #c1121f 100%);
            padding: 1.5rem 2rem;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .edit-card-header h2 { color: #fff; margin: 0; font-size: 1.3rem; }
        .edit-card-body { padding: 2rem; }
        .form-group { margin-bottom: 1.4rem; }
        .form-group label {
            display: block;
            font-size: 0.88rem;
            color: var(--text-gray, #8b949e);
            margin-bottom: 6px;
        }
        .form-group input {
            width: 100%;
            padding: 10px 14px;
            background: var(--medium-gray, #21262d);
            border: 1px solid var(--border-gray, #484f58);
            border-radius: 6px;
            color: var(--light-text, #c9d1d9);
            font-family: inherit;
            font-size: 0.95rem;
            transition: border-color 0.2s;
        }
        .form-group input:focus {
            outline: none;
            border-color: #e63946;
        }
        .form-group input[readonly] {
            opacity: 0.55;
            cursor: not-allowed;
            background: var(--dark-gray, #161b22);
        }
        .readonly-note {
            font-size: 0.78rem;
            color: var(--text-gray, #8b949e);
            margin-top: 4px;
        }
        .section-label {
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--text-gray, #8b949e);
            margin: 1.6rem 0 1rem;
            padding-bottom: 6px;
            border-bottom: 1px solid var(--border-gray, #30363d);
        }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
        .msg-success {
            background: rgba(46,160,67,0.15);
            border: 1px solid #2ea043;
            color: #2ea043;
            padding: 10px 14px;
            border-radius: 6px;
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }
        .msg-error {
            background: rgba(248,81,73,0.15);
            border: 1px solid #f85149;
            color: #f85149;
            padding: 10px 14px;
            border-radius: 6px;
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }
        .edit-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }
        .btn-save {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 26px;
            background: linear-gradient(135deg, #e63946 0%, #c1121f 100%);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-family: inherit;
            font-size: 0.92rem;
            font-weight: 500;
            cursor: pointer;
            transition: opacity 0.2s, transform 0.2s;
        }
        .btn-save:hover { opacity: 0.9; transform: translateY(-1px); }
        .btn-cancel {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 22px;
            background: transparent;
            border: 1px solid var(--border-gray, #484f58);
            color: var(--text-gray, #8b949e);
            text-decoration: none;
            border-radius: 8px;
            font-size: 0.92rem;
            font-weight: 500;
            transition: border-color 0.2s, color 0.2s;
        }
        .btn-cancel:hover { border-color: var(--light-text, #c9d1d9); color: var(--light-text, #c9d1d9); }
    </style>
</head>
<body>
    <?php include '../includes/navbar.php'; ?>

    <section class="edit-profile-page">
        <div class="container">
            <div class="edit-card">
                <div class="edit-card-header">
                    <i class="fas fa-user-edit" style="color:#fff; font-size:1.3rem;"></i>
                    <h2>Edit Profile</h2>
                </div>
                <div class="edit-card-body">

                    <?php if ($success): ?>
                        <div class="msg-success"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?></div>
                    <?php endif; ?>
                    <?php if ($error): ?>
                        <div class="msg-error"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>

                    <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" id="editProfileForm">
                        <input type="hidden" name="save_profile" value="1">

                        <p class="section-label">Editable Information</p>

                        <div class="form-group">
                            <label for="ep_name"><i class="fas fa-user"></i> Full Name <span style="color:#e63946;">*</span></label>
                            <input type="text" id="ep_name" name="name" required
                                value="<?php echo htmlspecialchars($user['name']); ?>">
                        </div>

                        <div class="form-group">
                            <label for="ep_phone"><i class="fas fa-phone"></i> Phone Number</label>
                            <input type="text" id="ep_phone" name="phone"
                                value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>"
                                placeholder="e.g. 08012345678">
                        </div>

                        <div class="form-group">
                            <label for="ep_address"><i class="fas fa-map-marker-alt"></i> Address</label>
                            <input type="text" id="ep_address" name="address"
                                value="<?php echo htmlspecialchars($user['location'] ?? ''); ?>"
                                placeholder="e.g. 15 Allen Avenue, Ikeja">
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="ep_city"><i class="fas fa-city"></i> City</label>
                                <input type="text" id="ep_city" name="city"
                                    value="<?php echo htmlspecialchars($user['city'] ?? ''); ?>"
                                    placeholder="e.g. Lagos">
                            </div>
                            <div class="form-group">
                                <label for="ep_state"><i class="fas fa-map"></i> State</label>
                                <input type="text" id="ep_state" name="state"
                                    value="<?php echo htmlspecialchars($user['state'] ?? ''); ?>"
                                    placeholder="e.g. Lagos State">
                            </div>
                        </div>

                        <p class="section-label">Read-Only Information</p>

                        <div class="form-group">
                            <label for="ep_email"><i class="fas fa-envelope"></i> Email</label>
                            <input type="email" id="ep_email" name="_email_ro" readonly
                                value="<?php echo htmlspecialchars($user['email']); ?>">
                            <p class="readonly-note">Email cannot be changed.</p>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="ep_type"><i class="fas fa-id-badge"></i> User Type</label>
                                <input type="text" id="ep_type" readonly
                                    value="<?php echo htmlspecialchars(ucfirst($user['user_type'])); ?>">
                            </div>
                            <div class="form-group">
                                <label for="ep_since"><i class="fas fa-calendar-alt"></i> Member Since</label>
                                <input type="text" id="ep_since" readonly
                                    value="<?php echo date('F j, Y', strtotime($user['created_at'])); ?>">
                            </div>
                        </div>

                        <div class="edit-actions">
                            <button type="submit" class="btn-save" id="saveProfileBtn">
                                <i class="fas fa-save"></i> Save Changes
                            </button>
                            <a href="profile.php" class="btn-cancel" id="cancelEditBtn">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </section>

    <script src="../assets/js/script.js"></script>
</body>
</html>
