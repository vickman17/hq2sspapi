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

    // Fetch job categories from the database
    $query = "SELECT id, category_name FROM job_categories";
    $stmt = $pdo->prepare($query);
    $stmt->execute();

    // Fetch all job categories
    $jobCategories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return as JSON
    echo json_encode(['status' => 'success', 'data' => $jobCategories]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    exit;
}
?>
