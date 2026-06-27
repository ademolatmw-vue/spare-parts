<?php
session_start();
include '../includes/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle profile update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);

    if (!empty($name) && !empty($email)) {
        $stmt = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE user_id = ?");
        $stmt->bind_param("ssi", $name, $email, $user_id);
        $stmt->execute();
        $stmt->close();
        $_SESSION['user_name'] = $name;
        $success = "Profile updated successfully.";
    } else {
        $error = "Name and email are required.";
    }
}

// Handle password change
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (!empty($current_password) && !empty($new_password) && $new_password === $confirm_password) {
        $stmt = $conn->prepare("SELECT password FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if (password_verify($current_password, $user['password'])) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
            $stmt->bind_param("si", $hashed_password, $user_id);
            $stmt->execute();
            $stmt->close();
            $success = "Password changed successfully.";
        } else {
            $error = "Current password is incorrect.";
        }
    } else {
        $error = "All password fields are required and new passwords must match.";
    }
}

// Get user info
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Get user's orders
$stmt = $conn->prepare("SELECT o.*, p.name as product_name, v.name as vendor_name FROM orders o JOIN products p ON o.product_id = p.product_id JOIN vendors v ON p.vendor_id = v.vendor_id WHERE o.user_id = ? ORDER BY o.created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Spare Parts Platform</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="navbar">
        <div class="container">
            <div class="logo">
                <h1>SpareParts</h1>
            </div>
            <ul class="nav-links">
                <li><a href="../index.php">Home</a></li>
                <li><a href="search.php">Search</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
            <div class="hamburger">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </div>
        </div>
    </div>

    <section class="profile-section" style="padding: 100px 0 2rem;">
        <div class="container">
            <h2>Your Profile</h2>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-top: 2rem;">
                <div>
                    <h3>Update Profile</h3>
                    <?php if (isset($success)) echo "<p style='color: green;'>$success</p>"; ?>
                    <?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" style="background: white; padding: 1rem; border-radius: 10px; box-shadow: 0 0 5px rgba(0,0,0,0.1);">
                        <input type="hidden" name="update_profile" value="1">
                        <div style="margin-bottom: 1rem;">
                            <label for="name">Full Name:</label>
                            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required style="width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 5px;">
                        </div>
                        <div style="margin-bottom: 1rem;">
                            <label for="email">Email:</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required style="width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 5px;">
                        </div>
                        <button type="submit" class="btn-primary" style="padding: 0.5rem 1rem; background-color: #dc3545; color: white; border: none; border-radius: 5px; cursor: pointer;">Update Profile</button>
                    </form>

                    <h3 style="margin-top: 2rem;">Change Password</h3>
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" style="background: white; padding: 1rem; border-radius: 10px; box-shadow: 0 0 5px rgba(0,0,0,0.1);">
                        <input type="hidden" name="change_password" value="1">
                        <div style="margin-bottom: 1rem;">
                            <label for="current_password">Current Password:</label>
                            <input type="password" id="current_password" name="current_password" required style="width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 5px;">
                        </div>
                        <div style="margin-bottom: 1rem;">
                            <label for="new_password">New Password:</label>
                            <input type="password" id="new_password" name="new_password" required style="width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 5px;">
                        </div>
                        <div style="margin-bottom: 1rem;">
                            <label for="confirm_password">Confirm New Password:</label>
                            <input type="password" id="confirm_password" name="confirm_password" required style="width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 5px;">
                        </div>
                        <button type="submit" class="btn-primary" style="padding: 0.5rem 1rem; background-color: #dc3545; color: white; border: none; border-radius: 5px; cursor: pointer;">Change Password</button>
                    </form>
                </div>

                <div>
                    <h3>Your Orders</h3>
                    <div style="background: white; padding: 1rem; border-radius: 10px; box-shadow: 0 0 5px rgba(0,0,0,0.1); max-height: 400px; overflow-y: auto;">
                        <?php if (!empty($orders)): ?>
                            <?php foreach ($orders as $order): ?>
                                <div style="border-bottom: 1px solid #eee; padding: 0.5rem 0;">
                                    <h4><?php echo htmlspecialchars($order['product_name']); ?></h4>
                                    <p>Vendor: <?php echo htmlspecialchars($order['vendor_name']); ?> | Status: <?php echo htmlspecialchars($order['status']); ?></p>
                                    <p>Date: <?php echo date('M d, Y', strtotime($order['created_at'])); ?></p>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>No orders yet.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="../assets/js/script.js"></script>
</body>
</html>
