<?php
header('Content-Type: application/json');
require '../config/db.php';

$products = [];

if ($conn) {
    $stmt = $conn->prepare("SELECT p.id, p.name, p.description, p.price, p.image, p.stock, c.name as category_name 
                            FROM products p 
                            LEFT JOIN categories c ON p.category_id = c.id 
                            ORDER BY p.id ASC");
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    $stmt->close();
}

echo json_encode($products);
?>