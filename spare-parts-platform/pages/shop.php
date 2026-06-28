<?php
// Get the shop ID from the URL parameter
$shop_id = isset($_GET['id']) ? intval($_GET['id']) : 1;

// Include database connection
include '../includes/db_connect.php';

// Fetch shop details
$stmt = $conn->prepare("SELECT s.*, u.name as vendor_name FROM shops s JOIN users u ON s.vendor_id = u.user_id WHERE s.shop_id = ?");
$stmt->bind_param("i", $shop_id);
$stmt->execute();
$result = $stmt->get_result();
$shop = $result->fetch_assoc();

if (!$shop) {
    // Fallback to first shop
    $stmt = $conn->prepare("SELECT s.*, u.name as vendor_name FROM shops s JOIN users u ON s.vendor_id = u.user_id LIMIT 1");
    $stmt->execute();
    $shop = $stmt->get_result()->fetch_assoc();
}
$stmt->close();


// Fetch products for this shop's vendor
$vendor_id = $shop['vendor_id'];
$stmt = $conn->prepare("SELECT p.product_name as name, p.brand, p.price, c.category_name as category FROM products p JOIN categories c ON p.category_id = c.category_id WHERE p.vendor_id = ? ORDER BY p.created_at DESC LIMIT 12");
$stmt->bind_param("i", $vendor_id);
$stmt->execute();
$result = $stmt->get_result();
$shop_products = [];
while ($row = $result->fetch_assoc()) {
    $imgPath = $row['image_url'] ?? ($row['image_url'] ?? '');
    $row['image'] = !empty($imgPath) ? '../assets/images/products/' . $imgPath : '';
    $row['price'] = floatval($row['price']);
    $shop_products[] = $row;
}

$stmt->close();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($shop['shop_name'] ?? ''); ?> - SparePartsNG</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .shop-header {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            color: white;
            padding: 60px 0;
            margin-top: 60px;
        }
        
        .shop-header-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }
        
        .shop-info h1 {
            font-size: 2rem;
            margin-bottom: 10px;
        }
        
        .shop-info .address {
            color: #aaa;
            margin-bottom: 10px;
        }
        
        .shop-info .rating {
            color: #ffc107;
        }
        
        .shop-contact-btn {
            background: #e94560;
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        
        .shop-contact-btn:hover {
            background: #c73e54;
            transform: translateY(-2px);
        }
        
        .products-section {
            max-width: 1200px;
            margin: 50px auto;
            padding: 0 20px;
        }
        
        .section-title {
            font-size: 1.8rem;
            margin-bottom: 30px;
            color: #1a1a2e;
        }
        
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 30px;
        }
        
        .product-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            transition: all 0.3s;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.15);
        }
        
        .product-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            background: #f0f0f0;
        }
        
        .product-info {
            padding: 20px;
        }
        
        .product-category {
            color: #e94560;
            font-size: 0.9rem;
            margin-bottom: 5px;
        }
        
        .product-name {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1a1a2e;
            margin-bottom: 5px;
        }
        
        .product-brand {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 15px;
        }
        
        .product-price {
            font-size: 1.3rem;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 15px;
        }
        
        .product-actions {
            display: flex;
            gap: 10px;
        }
        
        .btn-add-cart {
            background: #e94560;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            flex: 1;
        }
        
        .btn-add-cart:hover {
            background: #c73e54;
        }
        
        .btn-view-details {
            background: #f0f0f0;
            color: #333;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-view-details:hover {
            background: #e0e0e0;
        }
        
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: #666;
            text-decoration: none;
            margin-bottom: 20px;
            transition: color 0.3s;
        }
        
        .back-link:hover {
            color: #e94560;
        }
        
        .map-section {
            max-width: 1200px;
            margin: 50px auto;
            padding: 0 20px;
        }
        
        .map-container {
            border-radius: 12px;
            overflow: hidden;
            height: 300px;
        }
        
        .map-container iframe {
            width: 100%;
            height: 100%;
            border: none;
        }
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
            <div class="hamburger">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </div>
        </div>
    </nav>

    <!-- Shop Header -->
    <section class="shop-header">
        <div class="shop-header-content">
            <div class="shop-info">
                <h1><?php echo htmlspecialchars($shop['shop_name'] ?? ''); ?></h1>
                <p class="address"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($shop['address']); ?></p>
                <p class="rating">
                    <?php 
                    $full_stars = floor($shop['rating']);
                    for($i=0; $i<$full_stars; $i++) echo '<i class="fas fa-star"></i>';
                    if($shop['rating'] - $full_stars >= 0.5) echo '<i class="fas fa-star-half-alt"></i>';
                    echo ' ' . $shop['rating'];
                    ?>
                </p>
            </div>
            <div class="shop-actions">
                <a href="tel:<?php echo htmlspecialchars($shop['phone']); ?>" class="shop-contact-btn">
                    <i class="fas fa-phone"></i> Call Now
                </a>
            </div>
        </div>
    </section>

    <!-- Products Section -->
    <section class="products-section">
        <a href="../index.html#shops" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to Shops
        </a>
        
        <h2 class="section-title">Available Parts at This Shop</h2>
        
        <div class="products-grid">
            <?php foreach($shop_products as $product): ?>
            <div class="product-card">
                <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-image">
                <div class="product-info">
                    <p class="product-category"><?php echo htmlspecialchars($product['category']); ?></p>
                    <h3 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h3>
                    <p class="product-brand">Brand: <?php echo htmlspecialchars($product['brand']); ?></p>
                        <p class="product-price">₦<?php echo number_format($product['price'], 0); ?></p>

                        <?php
                            require_once '../includes/whatsapp_helper.php';
                            $wa_link_shop_prod = whatsapp_link($shop['phone'] ?? '', whatsapp_message_default());
                            $vendor_id_shop = intval($shop['vendor_id'] ?? 0);
                        ?>

                        <div class="product-actions">
                            <a class="btn-add-cart" style="background:#e94560;" href="contact_vendor.php?vendor_id=<?php echo $vendor_id_shop; ?>">
                                <i class="fas fa-comment-dots"></i> Contact Vendor
                            </a>
                            <?php if (!empty($wa_link_shop_prod)) : ?>
                                <a class="btn-view-details" style="text-decoration:none;" href="<?php echo htmlspecialchars($wa_link_shop_prod); ?>" target="_blank" rel="noopener">
                                    <i class="fab fa-whatsapp"></i> WhatsApp
                                </a>
                            <?php endif; ?>
                        </div>

                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Map Section -->
    <section class="map-section">
        <h2 class="section-title">Shop Location</h2>
        <div class="map-container">
            <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966!2d<?php echo $shop['longitude'] ?? 3.3792; ?>!3d<?php echo $shop['latitude'] ?? 6.5244; ?>!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0:0x0!2z<?php echo $shop['latitude'] ?? 6.5244; ?>N%20<?php echo $shop['longitude'] ?? 3.3792; ?>E!5e0!3m2!1sen!2sng!4v1720000000000" 
                width="100%" height="100%" style="border:0;" 
                allowfullscreen="" 
                loading="lazy">
            </iframe>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>SpareParts<span class="highlight">NG</span></h3>
<p>Your trusted platform for finding original automobile spare parts in Nigeria.</p>

                </div>
                <div class="footer-section">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="../index.html">Home</a></li>
                        <li><a href="../index.html#parts">Parts</a></li>
                        <li><a href="../index.html#shops">Shops</a></li>
                        <li><a href="../index.html#about">About Us</a></li>
                        <li><a href="contact_vendor.php?vendor_id=<?php echo $shop['vendor_id'] ?? 0; ?>">Vendor Support</a></li>

                    </ul>
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
                <p>&copy; 2024 SparePartsNG. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="../assets/js/script.js"></script>
</body>
</html>

