<?php
$conn = new mysqli("localhost", "root", "", "spare_parts_db");
if ($conn->connect_error) {
    echo "Connection failed: " . $conn->connect_error;
    $conn = null;
}
echo "DB Connected: " . ($conn ? 'YES' : 'NO') . "<br>";

echo "=== SPARE PARTS PLATFORM DB TEST ===<br>";
echo "DB Connected: " . ($conn ? '<span style=\"color:green\">YES</span>' : '<span style=\"color:red\">NO</span>') . "<br><br>";
 
if ($conn) {
    $result = $conn->query("SELECT COUNT(*) as total_shops FROM shops");
    $row = $result->fetch_assoc();
    echo "Total Shops: {$row['total_shops']}<br>";
    
    $result = $conn->query("SELECT COUNT(*) as total_products FROM products");
    $row = $result->fetch_assoc();
    echo "Total Products: {$row['total_products']}<br>";
    
    $result = $conn->query("SELECT COUNT(*) as engine_products FROM products WHERE category_id = 1");
    $row = $result->fetch_assoc();
    echo "Engine Products (cat 1): {$row['engine_products']}<br>";
    
    $result = $conn->query("SELECT COUNT(*) as engine_shops FROM (SELECT DISTINCT s.shop_id FROM shops s JOIN products p ON s.vendor_id = p.vendor_id WHERE p.category_id = 1) as sub");
    $row = $result->fetch_assoc();
    echo "Engine Shops: {$row['engine_shops']}<br><br>";
    
    echo "FIRST SHOP:<br><pre>";
    $result = $conn->query("SELECT * FROM shops LIMIT 1");
    echo print_r($result->fetch_assoc(), true);
    echo "</pre>";
    
    echo "<br>FIX category.php?cat=engine NOW!";
} else {
    echo "CRITICAL: MySQL NOT connected - check phpMyAdmin spare_parts_db exists?";
}
?>

