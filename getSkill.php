<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Handle preflight (OPTIONS) requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}



// Database configuration
$host = 'localhost';
$dbname = 'hq2app';
$dbusername = 'root';
$password = '';

header('Content-Type: application/json');

try {
    // Connect to the database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $dbusername, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get category ID from query parameters
    $categoryId = $_GET['category_id'] ?? null;

    if (!$categoryId) {
        echo json_encode(['status' => 'error', 'message' => 'Category ID is required']);
        exit;
    }

    // Fetch sub-categories from the database
    $query = "SELECT id, subcategory_name FROM job_subcategories WHERE category_id = :category_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':category_id', $categoryId, PDO::PARAM_INT);
    $stmt->execute();

    // Fetch all sub-categories
    $subCategories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return as JSON
    echo json_encode(['status' => 'success', 'data' => $subCategories]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    exit;
}
?>
