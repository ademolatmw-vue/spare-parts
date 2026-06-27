<?php
include '../includes/db_connect.php';

$search_term = isset($_GET['q']) ? trim($_GET['q']) : '';
$location = isset($_GET['location']) ? trim($_GET['location']) : '';

$title = 'Search Results';
$shops_found = [];
$map_center_lat = 6.5244; // Lagos default
$map_center_lng = 3.3792;

if ($conn && $search_term !== '') {
    // Search products by name/brand/category and return matching vendors (shops)
    $stmt = $conn->prepare("
        SELECT DISTINCT s.*, u.name as vendor_name 
        FROM shops s 
        JOIN users u ON s.vendor_id = u.user_id 
        JOIN products p ON s.vendor_id = p.vendor_id 
        JOIN categories c ON p.category_id = c.category_id
        WHERE p.product_name LIKE ?
           OR p.description LIKE ?
           OR p.brand LIKE ?
           OR c.category_name LIKE ?
           OR u.name LIKE ?
        ORDER BY s.rating DESC LIMIT 30
    ");

    $like = "%$search_term%";
    $stmt->bind_param("sssss", $like, $like, $like, $like, $like);

    $stmt->execute();
    $result = $stmt->get_result();
    $shops_found = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    
    if (!empty($shops_found)) {
        $first_shop = $shops_found[0];
        $map_center_lat = $first_shop['latitude'] ?? 6.5244;
        $map_center_lng = $first_shop['longitude'] ?? 3.3792;
    }
} else {
    $shops_found = []; // No results
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search "<?php echo htmlspecialchars($search_term); ?>" - SparePartsNG</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
            </ul>
            <div class="hamburger">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </div>
        </div>
    </nav>

    <!-- Hero -->
    <section class="hero">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <h2>Search Results for "<?php echo htmlspecialchars($search_term); ?>"</h2>
            <?php if (!empty($shops_found)): ?>
            <p><strong><?php echo count($shops_found); ?> shops found</strong> stocking this part near you</p>
            <?php else: ?>
            <p>No shops found for "<?php echo htmlspecialchars($search_term); ?>". Try different terms like "brake pads", "oil filter".</p>
            <?php endif; ?>
            <a href="../index.html" class="btn-new-search">New Search</a>
        </div>
    </section>

    <?php if (!empty($shops_found)): ?>
    <!-- Map -->
    <section class="map-section">
        <div class="container">
            <h2>Shop Locations</h2>
            <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15864!2d<?php echo $map_center_lng; ?>!3d<?php echo $map_center_lat; ?>!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0:0x0!2z<?php echo $map_center_lat; ?>N%20<?php echo $map_center_lng; ?>E!5e0!3m2!1sen!2sng!4v1720000000000" 
                width="100%" height="400" style="border:0; border-radius:12px;" allowfullscreen="" loading="lazy">
            </iframe>
        </div>
    </section>

    <!-- Shops List -->
    <section class="shops-section">
        <div class="container">
            <div class="shops-grid">
                <?php foreach($shops_found as $shop): ?>
                <div class="shop-card">
                    <div class="shop-badge verified">Verified</div>
                    <h3><?php echo htmlspecialchars($shop['shop_name']); ?></h3>
                    <div class="shop-location">
                        <i class="fas fa-map-marker-alt"></i>
                        <?php echo htmlspecialchars($shop['address']); ?>, <?php echo htmlspecialchars($shop['city']); ?>
                    </div>
                    <div class="shop-contact">
                        <p><i class="fas fa-phone"></i> <?php echo htmlspecialchars($shop['phone']); ?></p>
                        <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($shop['email']); ?></p>
                    </div>
                    <div class="shop-actions">
                        <a href="shop.php?id=<?php echo $shop['shop_id']; ?>" class="btn-view-shop">
                            View All Parts <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>

                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Footer -->
    <footer>
        <div class="container">
            <p>&copy; 2024 SparePartsNG - Find Automobile Spare Parts Locations</p>
        </div>
    </footer>

    <script src="../assets/js/script.js"></script>
    <script>
        // Keep page safe even if homepage search form is not present
        try {
            document.getElementById('searchForm')?.addEventListener('submit', function(e) {
                const termEl = document.getElementById('searchInput');
                const term = termEl ? termEl.value : '';
                const locEl = document.getElementById('locationInput');
                const loc = locEl ? locEl.value : '';
                window.location.href = `search.php?q=${encodeURIComponent(term)}&location=${encodeURIComponent(loc)}`;
            });
        } catch (err) {}
    </script>
</body>
</html>

