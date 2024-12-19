<?php

header("Access-Control-Allow-Origin: http://localhost:8100"); // Allow your frontend's origin
header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE"); // Allow necessary HTTP methods
header("Access-Control-Allow-Headers: Content-Type, Authorization"); // Allow required headers
header("Access-Control-Allow-Credentials: true"); // Optional if credentials are involved

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}



// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection settings
$host = 'localhost';
$dbname = 'hq2app';
$dbusername = 'root';
$password = '';

// Connect to the database
$conn = new mysqli($host, $dbusername, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $conn->connect_error]));
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    // Collect and sanitize inputs
    $ssp_id = uniqid('SSP_', true);
    $firstName = trim($input['first_name'] ?? '');
    $lastName = trim($input['last_name'] ?? '');
    $email = trim($input['email'] ?? '');
    $phone1 = trim($input['phone1'] ?? '');
    $password = trim($input['password'] ?? '');
    $confirmpassword = trim($input['confirmpassword'] ?? '');    

    // Validate required fields
    if (empty($firstName) || empty($lastName) || empty($email) || empty($phone1) || empty($password) || empty($confirmpassword)) {
        echo json_encode(['status' => 'error', 'message' => 'Required fields are missing.']);
        exit;
    }


    // Check if email already exists
    $stmt = $conn->prepare("SELECT ssp_id FROM ssp WHERE email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Email is already registered.']);
        exit;
    }

    if($password !== $confirmpassword){
        echo json_encode(['status' => 'error', 'message' => 'Password field does not match!']);
    }


    $hashpassword = password_hash($password, PASSWORD_BCRYPT);
    // Insert new user into database
    $stmt = $conn->prepare("
        INSERT INTO ssp (ssp_id, first_name, last_name, email, phone1, password) 
        VALUES ( ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param(
        'ssssss',
        $ssp_id,
        $firstName,
        $lastName,
        $email,
        $phone1,
        $hashpassword
    );

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Signup successful.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error during signup: ' . $conn->error]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}

// Close the connection
$conn->close();
?>
