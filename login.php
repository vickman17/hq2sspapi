<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once "config.php"; // Include your database configuration file

$data = json_decode(file_get_contents("php://input"), true);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
    exit;
}

$emailOrPhone = $data['emailOrPhone'] ?? null;
$password = $data['password'] ?? null;

if (!$emailOrPhone || !$password) {
    echo json_encode(["status" => "error", "message" => "Email/Phone and password are required"]);
    exit;
}

try {
    // Connect to the database
    $pdo = new PDO($dsn, $dbUsername, $dbPassword, $options);

    // Prepare SQL query to find the user
    $query = "SELECT * FROM ssp WHERE email = :emailOrPhone OR phone1 = :emailOrPhone LIMIT 1";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':emailOrPhone', $emailOrPhone);
    $stmt->execute();
    
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user || !password_verify($password, $user['password'])) {
        echo json_encode(["status" => "error", "message" => "Invalid email/phone or password"]);
        exit;
    }

    // Remove password before sending user info back
    unset($user['password']);

    echo json_encode(["status" => "success", "message" => "Login successful", "user" => $user]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
    exit;
}
