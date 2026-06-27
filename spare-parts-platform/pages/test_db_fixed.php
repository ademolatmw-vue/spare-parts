<?php
$conn = new mysqli("localhost", "root", "", "spare_parts_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "✅ DB Connected!<br><br>";
 
$result = $conn->query("SELECT COUNT(*) as shops FROM shops");
$row = $result->fetch_assoc();
echo "🛒 Total Shops: " . $row['shops'] . "<br>";

$result = $conn->query("SELECT COUNT(*) as products FROM products");
$row = $result->fetch_assoc();
echo "📦 Total Products: " . $row['products'] . "<br>";

$result = $conn->query("SELECT COUNT(*) as engine FROM products WHERE category_id = 1");
$row = $result->fetch_assoc();
echo "🔧 Engine Products: " . $row['engine'] . "<br>";

$result = $conn->query("SELECT COUNT(DISTINCT s.shop_id) as engine_shops FROM shops s JOIN products p ON s.vendor_id = p.vendor_id WHERE p.category_id = 1");
$row = $result->fetch_assoc();
echo "🏪 Engine Shops: " . $row['engine_shops'] . "<br>";

$result = $conn->query("SELECT * FROM shops LIMIT 1");
$shop = $result->fetch_assoc();
echo "<br>🏪 FIRST SHOP: " . $shop['shop_name'] . " (" . $shop['latitude'] . "," . $shop['longitude'] . ")<br>";

echo "<br><a href='category.php?cat=engine'>TEST CATEGORY NOW</a>";
?>

