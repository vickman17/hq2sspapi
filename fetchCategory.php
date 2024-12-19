<?php
header('Content-Type: application/json');
// Allow requests from any origin
header('Access-Control-Allow-Origin: *');
// Allow specific methods
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
// Allow specific headers
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require 'db.php';

$query = "SELECT id, category_name FROM job_categories";
$result = mysqli_query($conn, $query);

if (!$result) {
    echo json_encode(["error" => "Failed to fetch categories."]);
    exit;
}

$categories = [];
while ($row = mysqli_fetch_assoc($result)) {
    $categories[] = $row;
}

echo json_encode($categories);
?>
