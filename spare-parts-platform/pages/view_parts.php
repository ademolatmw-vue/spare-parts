<?php
// View Parts for Specific Shop
$shop_id = isset($_GET['shop_id']) ? intval($_GET['shop_id']) : 1;

include_once '../includes/db_connect.php';
$conn = $conn ?? null;
if (!$conn) {
    die("Database connection failed. Please check includes/db_connect.php");
}


// Fetch shop details
$stmt = $conn->prepare("SELECT s.*, u.name as vendor_name FROM shops s JOIN users u ON s.vendor_id = u.user_id WHERE s.shop_id = ?");
$stmt->bind_param("i", $shop_id);
$stmt->execute();
$result = $stmt->get_result();
$shop = $result->fetch_assoc();

if (!$shop) {
    header('Location: ../index.html');
    exit;
}
$stmt->close();

// Fetch products for this shop's vendor (unique per vendor)
$vendor_id = $shop['vendor_id'];
$stmt = $conn->prepare("SELECT 
        p.product_id,
        p.product_name,
        p.brand,
        c.category_name as category,
        p.description,
        p.price,
        p.stock_quantity,
        p.model_compatibility,
        p.image_url,
        p.image_path
    FROM products p
    JOIN categories c ON p.category_id = c.category_id
    WHERE p.vendor_id = ?
    ORDER BY p.created_at DESC
    LIMIT 20");
$stmt->bind_param("i", $vendor_id);
$stmt->execute();
$result = $stmt->get_result();
$shop_products = [];
while ($row = $result->fetch_assoc()) {
    $imgPath = $row['image_path'] ?? '';
    $imgUrl = $row['image_url'] ?? '';

    if (!empty($imgPath)) {
        $row['image'] = '../assets/images/products/' . $imgPath;
    } elseif (!empty($imgUrl)) {
        $row['image'] = $imgUrl;
    } else {
        $row['image'] = '';
    }

    // Normalize fields for UI requirements based on current DB schema
    $row['compatibility'] = $row['model_compatibility'] ?? '';
    $row['availability'] = ((int)($row['stock_quantity'] ?? 0) > 0) ? 'In Stock' : 'Out of Stock';
    $row['condition'] = 'Original / New';
    $shop_products[] = $row;
}

$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($shop['shop_name']); ?> Parts - SparePartsNG</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Same styles as shop.php */
        body { font-family: 'Poppins', sans-serif; }
        .parts-header { background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); color: white; padding: 40px 0; }
        .parts-header-content { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
        .parts-info h1 { font-size: 2rem; margin-bottom: 10px; }
        .products-section { max-width: 1200px; margin: 40px auto; padding: 0 20px; }
        .section-title { font-size: 1.8rem; margin-bottom: 30px; color: #1a1a2e; }
        .products-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 30px; }
        .product-card { background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.1); transition: all 0.3s; }
        .product-card:hover { transform: translateY(-5px); box-shadow: 0 8px 30px rgba(0,0,0,0.15); }
        .product-image { width: 100%; height: 200px; object-fit: cover; background: #f0f0f0; }
        .product-info { padding: 20px; }
        .product-category { color: #e94560; font-size: 0.9rem; margin-bottom: 5px; }
        .product-name { font-size: 1.1rem; font-weight: 600; color: #1a1a2e; margin-bottom: 5px; }
        .product-brand { color: #666; font-size: 0.9rem; margin-bottom: 15px; }
        .product-price { font-size: 1.3rem; font-weight: 700; color: #1a1a2e; margin-bottom: 15px; }
        .btn-contact { background: #e94560; color: white; padding: 12px 24px; border: none; border-radius: 6px; text-decoration: none; font-weight: 600; transition: all 0.3s; display: block; text-align: center; }
        .btn-contact:hover { background: #c73e54; transform: translateY(-2px); }
        .back-link { display: inline-flex; align-items: center; gap: 10px; color: #666; text-decoration: none; margin-bottom: 20px; }
    </style>
</head>
<body>
    <!-- Navigation -->
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
                <li><a href="login.php" class="btn-nav">Login</a></li>
                <li><a href="register.php" class="btn-nav btn-primary-small">Register</a></li>
            </ul>
        </div>
    </nav>

    <!-- Parts Header -->
    <section class="parts-header">
        <div class="parts-header-content">
            <div class="parts-info">
                <h1><?php echo htmlspecialchars($shop['shop_name']); ?> - <?php echo count($shop_products); ?> Parts Available</h1>
                <p class="address"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($shop['city'] . ', ' . $shop['state']); ?></p>
                <p class="contact">Phone: <a href="tel:<?php echo htmlspecialchars($shop['phone']); ?>"><?php echo htmlspecialchars($shop['phone']); ?></a></p>
            </div>
    <a href="contact_vendor.php?vendor_id=<?php echo htmlspecialchars($shop['vendor_id'] ?? 0); ?>" class="btn-contact">
                <i class="fas fa-comment-dots"></i> Contact Vendor
            </a>

        </div>
    </section>

    <!-- Products Section -->
    <section class="products-section">
        <a href="../index.html#shops" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to Shops
        </a>
        <h2 class="section-title"><?php echo htmlspecialchars($shop['shop_name']); ?> Inventory</h2>
        
        <?php if (empty($shop_products)): ?>
            <p>No parts available at this shop currently. <a href="../index.html#shops">Browse other shops</a></p>
        <?php else: ?>
            <div class="products-grid">
                <?php foreach($shop_products as $product): ?>
                <div class="product-card">
                    <?php
                        // Local image system (no external placeholders)
                        $productImage = '';
                        $imagePath = $product['image_path'] ?? '';
                        $imageUrl = $product['image_url'] ?? '';
                        $productName = $product['product_name'] ?? '';

                        if (!empty($imagePath)) {
                            $productImage = '../assets/images/products/' . $imagePath;
                        } elseif (!empty($imageUrl) && str_starts_with((string)$imageUrl, '../assets/images/products/')) {
                            $productImage = $imageUrl;
                        } else {
                            // Optional mapping by common keywords -> known filenames
                            $slugMap = [
                                'oil filter' => 'oil_filter.jpg',
                                'brake pad' => 'brake_pad.jpg',
                                'brake pads' => 'brake_pad.jpg',
                                'spark plug' => 'spark_plug.jpg',
                                'spark plugs' => 'spark_plug.jpg',
                                'radiator' => 'radiator.jpg',
                                'air filter' => 'air_filter.jpg',
                                'water pump' => 'water_pump.jpg',
                                'battery' => 'battery.jpg',
                                'alternator' => 'alternator.jpg',
                                'headlight' => 'headlight.jpg',
                                'shock absorber' => 'shock_absorber.jpg',
                                'clutch kit' => 'clutch_kit.jpg',
                                'wheel bearing' => 'wheel_bearing.jpg'
                            ];
                            $lowerName = strtolower((string)$productName);
                            $selected = '';
                            foreach ($slugMap as $needle => $file) {
                                if (strpos($lowerName, $needle) !== false) {
                                    $selected = $file;
                                    break;
                                }
                            }
                            $productImage = !empty($selected)
                                ? '../assets/images/products/' . $selected
                                : '../assets/images/products/default_part.jpg';
                        }
                    ?>
                    <img src="<?php echo htmlspecialchars($productImage); ?>" alt="<?php echo htmlspecialchars($product['product_name'] ?? 'Product'); ?>" class="product-image" loading="lazy">
                    <div class="product-info">
                        <p class="product-category"><?php echo htmlspecialchars($product['category'] ?? 'Parts'); ?></p>
                        <h3 class="product-name"><?php echo htmlspecialchars($product['product_name'] ?? 'Product'); ?></h3>

                        <p class="product-brand">Brand: <?php echo htmlspecialchars($product['brand'] ?? 'N/A'); ?></p>

<?php
                            // DB-driven fields normalized in PHP while reading products
                            $availability = $product['availability'] ?? 'In Stock';
                            $compatibility = $product['compatibility'] ?? 'N/A';
                            $condition = $product['condition'] ?? 'Original / New';
                            $description = !empty($product['description']) ? $product['description'] : '';
                        ?>

                        <?php if ($description): ?>
                            <p><?php echo htmlspecialchars($description); ?></p>
                        <?php endif; ?>

                        <p class="small"><strong>Availability:</strong> <?php echo htmlspecialchars($availability); ?></p>
                        <p class="small"><strong>Compatibility:</strong> <?php echo htmlspecialchars($compatibility); ?></p>
                        <p class="small"><strong>Condition:</strong> <?php echo htmlspecialchars($condition); ?></p>

                        <?php
                            require_once '../includes/whatsapp_helper.php';
                            $wa_msg = whatsapp_message_default();
                            $wa_link_prod = whatsapp_link($shop['phone'] ?? '', $wa_msg);
                            $vendor_id = intval($shop['vendor_id'] ?? 0);
                        ?>

                        <div class="product-actions" style="display:flex; gap:10px; flex-wrap:wrap; margin-top:10px;">
                            <a class="btn-contact" href="contact_vendor.php?vendor_id=<?php echo $vendor_id; ?>">
                                <i class="fas fa-comment-dots"></i> Contact Vendor
                            </a>
                            <?php if (!empty($wa_link_prod)) : ?>
                                <a class="btn-contact" href="<?php echo htmlspecialchars($wa_link_prod); ?>" target="_blank" rel="noopener">
                                    <i class="fab fa-whatsapp"></i> WhatsApp
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <script src="../assets/js/script.js"></script>
</body>
</html>
