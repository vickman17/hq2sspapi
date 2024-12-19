<?php
// File: get_user_profile.php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['user_id'])) {
    $userId = $_GET['user_id'];
    $db = new mysqli('localhost', 'root', '', 'hq2app');

    if ($db->connect_error) {
        die(json_encode(['success' => false, 'message' => 'Database connection failed.']));
    }

    $stmt = $db->prepare("SELECT profile_picture FROM ssp WHERE ssp_id = ?");
    $stmt->bind_param('s', $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode(['success' => true, 'profile_picture' => $row['profile_picture']]);
    } else {
        echo json_encode(['success' => false, 'message' => 'User not found.']);
    }

    $stmt->close();
    $db->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
