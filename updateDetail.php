<?php
header('Content-Type: application/json');

// Allow requests from any origin (adjust for security as necessary)
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle pre-flight OPTIONS request (for CORS)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Database connection
$host = 'localhost';  
$username = 'root';   
$password = '';       
$database = 'hq2app'; 

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed.'])); 
}

// Get the request data
$data = json_decode(file_get_contents('php://input'), true);

// Check if user_id is provided
if (!isset($data['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User ID is missing.']);
    exit;
}

$user_id = $conn->real_escape_string($data['user_id']);
$updateFields = [];

if (isset($data['first_name'])) {
    $first_name = $conn->real_escape_string($data['first_name']);
    $updateFields[] = "first_name = '$first_name'";
}

if (isset($data['last_name'])) {
    $last_name = $conn->real_escape_string($data['last_name']);
    $updateFields[] = "last_name = '$last_name'";
}

if (isset($data['email'])) {
    $email = $conn->real_escape_string($data['email']);
    $updateFields[] = "email = '$email'";
}

if (isset($data['phone'])) {
    $phone = $conn->real_escape_string($data['phone']);
    $updateFields[] = "phone = '$phone'";
}

if (empty($updateFields)) {
    echo json_encode(['success' => false, 'message' => 'No valid fields to update.']);
    exit;
}

$updateQuery = "UPDATE ssp SET " . implode(", ", $updateFields) . " WHERE ssp_id = '$user_id'";

if ($conn->query($updateQuery) === TRUE) {
    echo json_encode(['success' => true, 'message' => 'Profile updated successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error updating profile: ' . $conn->error]);
}

$conn->close();
?>
