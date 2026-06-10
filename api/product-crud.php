<?php
require '../config/db.php';
require '../includes/auth.php';

header('Content-Type: application/json');

if (!isAdmin()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$action = $_POST['action'] ?? '';
$id = $_POST['id'] ?? '';
$name = $_POST['name'] ?? '';
$price = $_POST['price'] ?? '';
$stock = $_POST['stock'] ?? '';
$category_id = $_POST['category_id'] ?? '';
$image = $_POST['image'] ?? '';
$description = $_POST['description'] ?? '';

if ($action === 'delete') {
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Product deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete product']);
    }
} else {
    // Add or Update
    if (empty($id)) {
        // Add
        $stmt = $conn->prepare("INSERT INTO products (name, price, stock, category_id, image, description) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sdidss", $name, $price, $stock, $category_id, $image, $description);
        $message = "Product added successfully";
    } else {
        // Update
        $stmt = $conn->prepare("UPDATE products SET name=?, price=?, stock=?, category_id=?, image=?, description=? WHERE id=?");
        $stmt->bind_param("sdidssi", $name, $price, $stock, $category_id, $image, $description, $id);
        $message = "Product updated successfully";
    }

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => $message]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to save product']);
    }
}
