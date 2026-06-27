<?php
session_start();
include '../includes/db_connect.php';

$product = null;

if (isset($_GET['id'])) {
    $product_id = intval($_GET['id']);

    // Get product details
    $stmt = $conn->prepare("SELECT p.*, v.name as vendor_name, v.location as vendor_location, v.verification_status FROM products p JOIN vendors v ON p.vendor_id = v.vendor_id WHERE p.product_id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $stmt->close();
}

if (!$product) {
    header("Location: search.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - Spare Parts Platform</title>
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
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="profile.php">Profile</a></li>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php">Register</a></li>
                <?php endif; ?>
            </ul>
            <div class="hamburger">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </div>
        </div>
    </div>

    <section class="product-section" style="padding: 100px 0 2rem;">
        <div class="container">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; align-items: start;">
                <div>
                    <img src="../assets/images/product-placeholder.jpg" alt="<?php echo htmlspecialchars($product['name']); ?>" style="width: 100%; max-width: 400px; height: auto; border-radius: 10px;">
                </div>

                <div>
                    <h2><?php echo htmlspecialchars($product['name']); ?></h2>
                    <p><strong>Brand:</strong> <?php echo htmlspecialchars($product['brand']); ?></p>
                    <p><strong>Category:</strong> <?php echo htmlspecialchars($product['category']); ?></p>
                    <p><strong>Vendor:</strong> <?php echo htmlspecialchars($product['vendor_name']); ?> <?php if (($product['verification_status'] ?? '') === 'verified'): ?><span style="color: green;">(Verified)</span><?php endif; ?></p>
                    <p><strong>Location:</strong> <?php echo htmlspecialchars($product['vendor_location']); ?></p>

                    <p><strong>Price:</strong> ₦<?php echo number_format((float)($product['price'] ?? 0), 2); ?></p>

                    <?php if (!empty($product['description'])): ?>
                        <p><?php echo htmlspecialchars($product['description']); ?></p>
                    <?php endif; ?>

                    <?php
                        require_once '../includes/whatsapp_helper.php';
                        $wa_msg = whatsapp_message_default();
                        $wa_link_from_product = whatsapp_link($product['phone'] ?? ($product['vendor_phone'] ?? ''), $wa_msg);
                        $vendor_id = intval($product['vendor_id'] ?? 0);
                    ?>

                    <div style="display:flex; gap:12px; flex-wrap:wrap; margin-top:14px;">
                        <a class="btn-primary" style="padding: 0.8rem 2rem; background-color: #e94560; color: white; border: none; border-radius: 5px; text-decoration:none; cursor:pointer;" href="contact_vendor.php?vendor_id=<?php echo htmlspecialchars($vendor_id); ?>">
                            <i class="fas fa-comment-dots"></i> Contact Vendor
                        </a>

                        <?php if (!empty($wa_link_from_product)) : ?>
                            <a class="btn-primary" style="padding: 0.8rem 2rem; background-color: #25D366; color: white; border: none; border-radius: 5px; text-decoration:none;" href="<?php echo htmlspecialchars($wa_link_from_product); ?>" target="_blank" rel="noopener">
                                <i class="fab fa-whatsapp"></i> WhatsApp
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="../assets/js/script.js"></script>
</body>
</html>

