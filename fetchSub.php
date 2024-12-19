<?php
header('Content-Type: application/json');
// Allow requests from any origin
header('Access-Control-Allow-Origin: *');
// Allow specific methods
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
// Allow specific headers
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require 'db.php';

if (!isset($_GET['category_id'])) {
    echo json_encode(["error" => "Category ID is required."]);
    exit;
}

$category_id = intval($_GET['category_id']);
$query = "SELECT id, subcategory_name FROM job_subcategories WHERE category_id = ?";
$stmt = mysqli_prepare($conn, $query);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $category_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $subcategories = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $subcategories[] = $row;
    }

    echo json_encode($subcategories);
    mysqli_stmt_close($stmt);
} else {
    echo json_encode(["error" => "Failed to prepare query."]);
}

mysqli_close($conn);
?>
