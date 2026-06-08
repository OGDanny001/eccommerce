<?php
require 'config/db.php';

echo "<h1>Database Product Verification</h1>";
echo "<table border='1' cellpadding='8' style='border-collapse: collapse;'>";
echo "<tr><th>ID</th><th>Name</th><th>Price</th><th>Image</th><th>Category_ID</th><th>Stock</th></tr>";

$products = [];
if ($conn) {
    $stmt = $conn->prepare("SELECT id, name, price, image, category_id, stock FROM products ORDER BY id ASC");
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['name']}</td>";
        echo "<td>\${$row['price']}</td>";
        echo "<td>{$row['image']}</td>";
        echo "<td>{$row['category_id']}</td>";
        echo "<td>{$row['stock']}</td>";
        echo "</tr>";
    }
    $stmt->close();
}

echo "</table>";
echo "<p>Total products: " . count($products) . "</p>";
