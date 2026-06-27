<?php
session_start();
include '../includes/db_connect.php';
require_once '../includes/whatsapp_helper.php';

$vendor_id = isset($_GET['vendor_id']) ? intval($_GET['vendor_id']) : 0;

$vendor = null;
if ($vendor_id > 0 && $conn) {
    $stmt = $conn->prepare("SELECT u.user_id, u.name, s.shop_name, s.phone, s.email, s.address, s.city, s.state, s.latitude, s.longitude
                            FROM shops s
                            JOIN users u ON s.vendor_id = u.user_id
                            WHERE s.vendor_id = ?
                            LIMIT 1");
    $stmt->bind_param("i", $vendor_id);
    $stmt->execute();
    $vendor = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

if (!$vendor && $vendor_id > 0 && $conn) {
    // Fallback: try vendor fields only from shops
    $stmt = $conn->prepare("SELECT vendor_id, shop_name, phone, email, address, city, state, latitude, longitude FROM shops WHERE vendor_id = ? LIMIT 1");
    $stmt->bind_param("i", $vendor_id);
    $stmt->execute();
    $vendor = $stmt->get_result()->fetch_assoc();
    if ($vendor) {
        $vendor['name'] = $vendor['shop_name'] ?? $vendor_id;
    }
    $stmt->close();
}

if (!$vendor) {
    header('Location: ../index.html');
    exit;
}

$phone_raw = $vendor['phone'] ?? '';
$email = $vendor['email'] ?? '';
$address = trim(($vendor['address'] ?? '') . (isset($vendor['city']) && $vendor['city'] ? ', ' . $vendor['city'] : '') . (isset($vendor['state']) && $vendor['state'] ? ', ' . $vendor['state'] : ''));

$wa_message = whatsapp_message_default();
$wa_link = whatsapp_link($phone_raw, $wa_message);
$tel_link = $phone_raw ? 'tel:' . htmlspecialchars($phone_raw) : '';

$vendor_display_name = $vendor['shop_name'] ?? $vendor['name'] ?? 'Vendor';
$vendor_display_phone = $phone_raw ?: 'Phone not available';
$vendor_display_email = $email ?: 'Email not available';
$vendor_display_address = $address ?: 'Address not available';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Vendor - SparePartsNG</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .page-wrap{padding:100px 0 40px;}
        .card{
            background:white;
            border-radius:12px;
            box-shadow:0 4px 20px rgba(0,0,0,0.08);
            padding:24px;
            max-width:900px;
            margin:0 auto;
        }
        .grid{
            display:grid;
            grid-template-columns: 1.2fr 0.8fr;
            gap:20px;
            align-items:start;
        }
        @media(max-width:900px){.grid{grid-template-columns:1fr;}}
        .meta{color:#444; line-height:1.8;}
        .meta i{color:#e94560; width:22px;}
        .actions{display:flex; flex-direction:column; gap:12px;}
        .btn{display:inline-flex; align-items:center; justify-content:center; gap:10px; padding:12px 18px; border-radius:10px; text-decoration:none; font-weight:600;}
        .btn-primary{background:#e94560; color:#fff;}
        .btn-secondary{background:#f0f0f0; color:#222;}
        .btn-secondary:hover{filter:brightness(0.96)}
        .btn-primary:hover{background:#c73e54;}
        .back-link{margin-bottom:18px; display:inline-flex; align-items:center; gap:10px; color:#666; text-decoration:none; font-weight:600;}
        .small{color:#777; font-size:0.95rem;}
        .divider{height:1px; background:#eee; margin:16px 0;}
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="logo">
                <h1><i class="fas fa-car"></i> SpareParts<span class="highlight">NG</span></h1>
            </div>
            <ul class="nav-links">
                <li><a href="../index.html">Home</a></li>
                <li><a href="../index.html#parts">Parts</a></li>
                <li><a href="../index.html#shops">Shops</a></li>
                <li><a href="../index.html#about">About</a></li>
            </ul>
            <div class="hamburger">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </div>
        </div>
    </nav>

    <div class="page-wrap">
        <div class="card">
            <a class="back-link" href="javascript:history.back()">
                <i class="fas fa-arrow-left"></i> Back
            </a>

            <div class="grid">
                <div>
                    <h2 style="color:#1a1a2e; margin-bottom:8px;"><?php echo htmlspecialchars($vendor_display_name); ?></h2>

                    <p class="small">Contact details for vendor location and spare parts availability.</p>
                    <div class="divider"></div>

                    <div class="meta">
                        <p><i class="fas fa-phone"></i> <strong>Phone:</strong> <span><?php echo htmlspecialchars($vendor_display_phone); ?></span></p>
                        <p><i class="fas fa-envelope"></i> <strong>Email:</strong> <span><?php echo htmlspecialchars($vendor_display_email); ?></span></p>
                        <p><i class="fas fa-map-marker-alt"></i> <strong>Address:</strong> <span><?php echo htmlspecialchars($vendor_display_address); ?></span></p>
                    </div>
                </div>

                <div class="actions">
                    <?php if ($tel_link): ?>
                        <a class="btn btn-secondary" href="<?php echo $tel_link; ?>">
                            <i class="fas fa-phone"></i> Call Vendor
                        </a>
                    <?php endif; ?>

                    <?php if (!empty($wa_link)): ?>
                        <a class="btn btn-primary" href="<?php echo htmlspecialchars($wa_link); ?>" target="_blank" rel="noopener">
                            <i class="fab fa-whatsapp"></i> WhatsApp Vendor
                        </a>
                    <?php else: ?>
                        <div class="small">WhatsApp not available (missing phone).</div>
                    <?php endif; ?>

                    <a class="btn btn-secondary" href="../index.html#parts">
                        <i class="fas fa-search"></i> Search Another Part
                    </a>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>SpareParts<span class="highlight">NG</span></h3>
                    <p>Your trusted platform for finding original automobile spare parts in Nigeria.</p>
                </div>
                <div class="footer-section">
                    <h4>Contact Us</h4>
                    <ul>
                        <li><i class="fas fa-envelope"></i> info@sparepartsng.com</li>
                        <li><i class="fas fa-phone"></i> +234 800 123 4567</li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2026 SparePartsNG. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>

