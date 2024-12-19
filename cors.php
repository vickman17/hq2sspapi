<?php
// Add CORS headers
header("Access-Control-Allow-Origin: *"); // Replace '*' with the specific domain if needed for security
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

header("Content-Type: application/json");
// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Your existing PHP logic goes below
