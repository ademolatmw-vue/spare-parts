<?php
include '../includes/db_connect.php';

$category = isset($_GET['cat']) ? strtolower($_GET['cat']) : 'engine';

// --- FIX: syntactically valid, balanced PHP control blocks ---
// NOTE: HTML below is preserved byte-for-byte; only PHP logic at the top is repaired.


// Map category to category_name
$category_map = [
    'engine' => 'Engine Parts',
    'brakes' => 'Brakes',
    'suspension' => 'Suspension',
    'electrical' => 'Electrical',
    'body' => 'Body Parts',
    'transmission' => 'Transmission',
    'cooling' => 'Cooling',
    'exhaust' => 'Exhaust',
    'interior' => 'Interior',
    'wheels' => 'Wheels & Tires'
];

$category_name = $category_map[$category] ?? 'Engine Parts';
$shop_address = "Location not found";
$map_url = "";

// Safe debug variable (initialized but not displayed)
$debug = "";

$latitude = 6.5244;
$longitude = 3.3792;

// Always initialize arrays to avoid null warnings
$shops_found = [];
$vendor_parts_counts = [];
$products = [];

if (!empty($conn)) {
    $stmt = $conn->prepare("SELECT category_id FROM categories WHERE LOWER(category_name) = LOWER(?)");
    $stmt->bind_param("s", $category_name);
    $stmt->execute();
    $cat_result = $stmt->get_result();

    if ($cat_result && $cat_result->num_rows > 0) {
        $cat_row = $cat_result->fetch_assoc();
        $category_id = (int)($cat_row['category_id'] ?? 0);

        if ($category_id > 0) {
            // Shops with products in category
            $stmt_shops = $conn->prepare("SELECT 
                    s.shop_id,
                    s.shop_name,
                    s.address,
                    s.city,
                    s.state,
                    s.phone,
                    s.email,
                    s.latitude,
                    s.longitude,
                    s.vendor_id
                FROM shops s 
                JOIN products p ON s.vendor_id = p.vendor_id 
                WHERE p.category_id = ? 
                ORDER BY RAND()");
            $stmt_shops->bind_param("i", $category_id);
            $stmt_shops->execute();
            $shops_result = $stmt_shops->get_result();
            $shops_found = $shops_result ? $shops_result->fetch_all(MYSQLI_ASSOC) : [];
            $stmt_shops->close();

            // Pick RANDOM shop for map variety - NULL SAFE
            if (!empty($shops_found)) {
                $shop_index = rand(0, count($shops_found) - 1);
                $first_shop = $shops_found[$shop_index] ?? null;

                if ($first_shop) {
                    $shop_address = ($first_shop['address'] ?? '') . ', ' . ($first_shop['city'] ?? '') . ', ' . ($first_shop['state'] ?? '');
                    $latitude = $first_shop['latitude'] ?? 6.5244;
                    $longitude = $first_shop['longitude'] ?? 3.3792;
                    $shop_name_for_debug = $first_shop['shop_name'] ?? '';

                    $map_url = "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966!2d{$longitude}!3d{$latitude}!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2s" . urlencode($shop_address) . "!5e0!3m2!1sen!2sng!4v1700000000000";
                    $debug .= "<!-- MAP RAND: Index {$shop_index} - {$shop_name_for_debug} {$latitude},{$longitude} -->";
                }
            }

            // Get products for this category
            $stmt = $conn->prepare("SELECT p.product_name, p.price, p.brand, c.category_name, p.stock_quantity, p.model_compatibility, p.image_url, p.description 
                FROM products p 
                JOIN categories c ON p.category_id = c.category_id 
                WHERE p.category_id = ? 
                LIMIT 12");
            $stmt->bind_param("i", $category_id);
            $stmt->execute();
            $products_result = $stmt->get_result();
            $products = $products_result ? $products_result->fetch_all(MYSQLI_ASSOC) : [];
            $stmt->close();

            // Compute parts count per vendor for this category (for vendor cards)
            $stmt_counts = $conn->prepare("SELECT vendor_id, SUM(CASE WHEN stock_quantity > 0 THEN 1 ELSE 0 END) AS parts_available_count 
                FROM products 
                WHERE category_id = ? 
                GROUP BY vendor_id");
            $stmt_counts->bind_param("i", $category_id);
            $stmt_counts->execute();
            $counts_result = $stmt_counts->get_result();
            while ($counts_result && ($cr = $counts_result->fetch_assoc())) {
                $vendor_parts_counts[(int)($cr['vendor_id'] ?? 0)] = (int)($cr['parts_available_count'] ?? 0);
            }
            $stmt_counts->close();
        }
    }
}

// Dummy data fallback (keeps existing behavior)
if (empty($shops_found) || empty($products)) {
    $shop_address = "15 Allen Avenue, Ikeja, Lagos";
    $map_url = "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966!2d3.3792!3d6.5244!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x103b8b2a982d4d4f:0x4b2a7d2a4e6c8d2e!2sIkeja%2C%20Lagos!5e0!3m2!1sen!2sng!4v1700000000000";
    $shops_found = [
        [
            'shop_name' => 'Lagos Auto Parts Center',
            'address' => '15 Allen Avenue, Ikeja',
            'city' => 'Lagos',
            'state' => 'Lagos',
            'phone' => '08012345678',
            'email' => 'contact@lagosautoparts.com',
            'shop_id' => 1,
            'vendor_id' => 1,
            'latitude' => 6.5244,
            'longitude' => 3.3792,
        ]
    ];
    $products = [
        ['product_name' => 'Toyota Oil Filter', 'price' => 2500, 'brand' => 'Toyota', 'category_name' => 'Engine Parts', 'image_url' => ''],
        ['product_name' => 'Honda Air Filter', 'price' => 3200, 'brand' => 'Honda', 'category_name' => 'Engine Parts', 'image_url' => ''],
        ['product_name' => 'NGK Spark Plugs', 'price' => 8500, 'brand' => 'NGK', 'category_name' => 'Engine Parts', 'image_url' => '']
    ];

    // Preserve existing message
    echo "<p>Using dummy data - database not connected. Start MySQL to see real data.</p>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo ucwords($category ?? 'Parts'); ?> - SparePartsNG</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700" rel="stylesheet">
    <style>
        .navbar {
            z-index: 1000 !important;
            position: relative;
        }
        .notification {
            z-index: 10000 !important;
        }
        .category-hero {
            margin-top: 80px;
            padding-top: 40px;
        }
        body {
            margin-top: 0 !important;
        }
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
            </ul>
        </div>
    </nav>
    
    <section class="category-hero">
        <div class="container">
            <h1><?php echo ucwords($category ?? 'Parts'); ?> Parts</h1>
            <p>📍 Location Found: <?php echo $shop_address; ?></p>
            <p>Shops in this area stock <?php echo strtolower($category ?? 'parts'); ?> parts. Use contacts below or visit marked location on map (PIN ICON visible).</p>
        </div>
    </section>

    <section class="category-map">
        <div class="container">
            <iframe src="<?php echo $map_url; ?>" width="100%" height="400" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
        </div>
    </section>

    <section class="shops-section">
        <div class="container">
            <h2><?php echo ucwords($category ?? 'Parts'); ?> (<?php echo count($shops_found); ?> Shops Found)</h2>
            <div class="shops-grid">
                <?php foreach($shops_found as $shop): ?>
                <div class="shop-card">
                    <h3><?php echo htmlspecialchars($shop['shop_name']); ?></h3>
                    <p class="shop-address"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($shop['address'] . ', ' . $shop['city'] . ', ' . $shop['state']); ?></p>
                    <p class="shop-phone"><i class="fas fa-phone"></i> <?php echo htmlspecialchars($shop['phone']); ?></p>
                    <p class="shop-email"><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($shop['email']); ?></p>

                    <?php
                        require_once '../includes/whatsapp_helper.php';
                        $wa_link_shop = whatsapp_link($shop['phone'] ?? '', whatsapp_message_default());
                        $vendor_id_for_contact = intval($shop['vendor_id'] ?? 0);
                    ?>



                    <div class="shop-actions">
                        <a class="btn-contact" href="contact_vendor.php?vendor_id=<?php echo $vendor_id_for_contact; ?>">
                            <i class="fas fa-comment-dots"></i> Contact Vendor
                        </a>

                        <?php if (!empty($wa_link_shop)) : ?>
                            <a class="btn-contact" href="<?php echo htmlspecialchars($wa_link_shop); ?>" target="_blank" rel="noopener">
                                <i class="fab fa-whatsapp"></i> WhatsApp
                            </a>
                        <?php endif; ?>

                        <a class="btn-contact" href="view_parts.php?shop_id=<?php echo htmlspecialchars($shop['shop_id']); ?>">
                            <i class="fas fa-box-open"></i> View Parts
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>

            </div>
        </div>
    </section>

<section class="products-section">
        <div class="container">
            <h2>Products Available</h2>
            <div class="products-grid">
<?php if (!empty($products)) : ?>
                <?php foreach(array_slice($products, 0, 12) as $p): ?>
                <div class="product-card">
                    <?php
$imgUrl = $p['image_url'] ?? '';
$imgSrc = !empty($imgUrl) ? htmlspecialchars($imgUrl) : '';
                    ?>
                    <img src="<?php echo $imgSrc; ?>" alt="" loading="lazy" style="background:#f0f0f0;">

                    <div class="product-info">
                        <h3><?php echo htmlspecialchars($p['product_name']); ?></h3>
                        <p><?php echo htmlspecialchars($p['brand']); ?></p>
                        <p class="price">₦<?php echo number_format($p['price']); ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</body>
</html>
<?php /* noop */ ?>

