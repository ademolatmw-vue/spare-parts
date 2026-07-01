<?php
session_start();
include '../includes/db_connect.php';
require_once '../includes/send_email.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT user_id, name, email, password, user_type FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            // Regenerate session id to mitigate session fixation
            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_type'] = $user['user_type'];

            // Send Login notification (fail silently)
            $loginTime = date('Y-m-d H:i:s');
            $subject = "Login Successful - SparePartsNG";
            $bodyText = "Hello {$user['name']},\n\nYou successfully logged in to SparePartsNG at {$loginTime}. If this was not you, please change your password immediately.\n\nThank you.";
            $bodyHtml = "<p>Hello <strong>{$user['name']}</strong>,</p><p>You successfully logged in to <strong>SparePartsNG</strong> at <strong>{$loginTime}</strong>.</p><p>If this was not you, please change your password immediately.</p>";
            spng_send_email($user['email'], $user['name'], $subject, $bodyHtml, $bodyText);

            if ($user['user_type'] == 'vendor') {
                header("Location: vendor_dashboard.php");
            } else {
                header("Location: ../index.php");
            }
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "No account found with that email.";
    }
    $stmt->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Spare Parts Platform</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="navbar">
        <div class="container">
            <div class="logo">
                <h1>SpareParts</h1>
            </div>
            <ul class="nav-links">
                <li><a href="../index.html">Home</a></li>
                <li><a href="register.php">Register</a></li>
            </ul>
            <div class="hamburger">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </div>
        </div>
    </div>

    <section class="login-section" style="padding: 100px 0; background-color: #f8f9fa;">
        <div class="container">
            <div style="max-width: 400px; margin: 0 auto; background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
                <h2 style="text-align: center; margin-bottom: 2rem; color: #dc3545;">Login</h2>
                <?php if (isset($error)) echo "<p style='color: red; text-align: center;'>$error</p>"; ?>
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <div style="margin-bottom: 1rem;">
                        <label for="email" style="display: block; margin-bottom: 0.5rem;">Email:</label>
                        <input type="email" id="email" name="email" required style="width: 100%; padding: 0.8rem; border: 1px solid #ccc; border-radius: 5px;">
                    </div>
                    <div style="margin-bottom: 1rem;">
                        <label for="password" style="display: block; margin-bottom: 0.5rem;">Password:</label>
                        <input type="password" id="password" name="password" required style="width: 100%; padding: 0.8rem; border: 1px solid #ccc; border-radius: 5px;">
                    </div>
                    <button type="submit" style="width: 100%; padding: 0.8rem; background-color: #dc3545; color: white; border: none; border-radius: 5px; cursor: pointer;">Login</button>
                </form>
                <p style="text-align: center; margin-top: 1rem;">Don't have an account? <a href="register.php" style="color: #dc3545;">Register here</a></p>
            </div>
        </div>
    </section>

    <script src="../assets/js/script.js"></script>
</body>
</html>
