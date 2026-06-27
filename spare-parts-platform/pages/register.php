<?php
session_start();
include '../includes/db_connect.php';
require_once '../includes/send_email.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $user_type = trim($_POST['user_type'] ?? '');

    if (!empty($name) && !empty($email) && !empty($password) && !empty($user_type)) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Please enter a valid email address.";
        } else {
            // Prevent duplicate emails with a friendly message
            $stmt_check = $conn->prepare("SELECT user_id FROM users WHERE email = ? LIMIT 1");
            $stmt_check->bind_param("s", $email);
            $stmt_check->execute();
            $exists = $stmt_check->get_result();
            $stmt_check->close();

            if ($exists && $exists->num_rows > 0) {
                $error = "Registration failed. This email is already registered.";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                $stmt = $conn->prepare("INSERT INTO users (name, email, password, user_type) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $name, $email, $hashed_password, $user_type);

                if ($stmt->execute()) {
                    $newUserId = $stmt->insert_id;
                    $stmt->close();

                    $_SESSION['user_id'] = $newUserId;
                    $_SESSION['user_name'] = $name;
                    $_SESSION['user_type'] = $user_type;

                    // Send Welcome email (fail silently)
                    $subject = "Welcome to SparePartsNG";
                    $bodyText = "Hello {$name},\n\nWelcome to SparePartsNG! You can now locate spare parts and contact vendors.\n\nThank you.";
                    $bodyHtml = "<p>Hello <strong>{$name}</strong>,</p><p>Welcome to <strong>SparePartsNG</strong>! You can now locate spare parts and contact vendors.</p><p>Thank you.</p>";
                    spng_send_email($email, $name, $subject, $bodyHtml, $bodyText);

                    if ($user_type == 'vendor') {
                        header("Location: vendor_dashboard.php");
                    } else {
                        header("Location: ../index.php");
                    }
                    exit();
                } else {
                    $stmt->close();
                    $error = "Registration failed. Please try again.";
                }
            }
        }
    } else {
        $error = "All fields are required.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Spare Parts Platform</title>
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
                <li><a href="login.php">Login</a></li>
            </ul>
            <div class="hamburger">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </div>
        </div>
    </div>

    <section class="register-section" style="padding: 100px 0; background-color: #f8f9fa;">
        <div class="container">
            <div style="max-width: 400px; margin: 0 auto; background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
                <h2 style="text-align: center; margin-bottom: 2rem; color: #dc3545;">Register</h2>
                <?php if (isset($error)) echo "<p style='color: red; text-align: center;'>$error</p>"; ?>
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <div style="margin-bottom: 1rem;">
                        <label for="name" style="display: block; margin-bottom: 0.5rem;">Full Name:</label>
                        <input type="text" id="name" name="name" required style="width: 100%; padding: 0.8rem; border: 1px solid #ccc; border-radius: 5px;">
                    </div>
                    <div style="margin-bottom: 1rem;">
                        <label for="email" style="display: block; margin-bottom: 0.5rem;">Email:</label>
                        <input type="email" id="email" name="email" required style="width: 100%; padding: 0.8rem; border: 1px solid #ccc; border-radius: 5px;">
                    </div>
                    <div style="margin-bottom: 1rem;">
                        <label for="password" style="display: block; margin-bottom: 0.5rem;">Password:</label>
                        <input type="password" id="password" name="password" required style="width: 100%; padding: 0.8rem; border: 1px solid #ccc; border-radius: 5px;">
                    </div>
                    <div style="margin-bottom: 1rem;">
                        <label for="user_type" style="display: block; margin-bottom: 0.5rem;">Account Type:</label>
                        <select id="user_type" name="user_type" required style="width: 100%; padding: 0.8rem; border: 1px solid #ccc; border-radius: 5px;">
                            <option value="">Select account type</option>
                            <option value="customer">Customer</option>
                            <option value="vendor">Vendor</option>
                        </select>
                    </div>
                    <button type="submit" style="width: 100%; padding: 0.8rem; background-color: #dc3545; color: white; border: none; border-radius: 5px; cursor: pointer;">Register</button>
                </form>
                <p style="text-align: center; margin-top: 1rem;">Already have an account? <a href="login.php" style="color: #dc3545;">Login here</a></p>
            </div>
        </div>
    </section>

    <script src="../assets/js/script.js"></script>
</body>
</html>
