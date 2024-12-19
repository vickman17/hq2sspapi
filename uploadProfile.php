<?php
// File: upload_profile_image.php

// Enable CORS if needed
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Check if a file is uploaded
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_image'])) {
    $uploadDir = 'uploads/profile_pictures/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true); // Create directory if it doesn't exist
    }

    $file = $_FILES['profile_image'];
    $fileName = uniqid() . '_' . basename($file['name']); // Generate a unique file name
    $targetFilePath = $uploadDir . $fileName;

    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $targetFilePath)) {
        // Save path to the database
        $userId = $_POST['user_id']; // Assuming `user_id` is sent in the request
        $db = new mysqli('localhost', 'root', '', 'hq2app');

        if ($db->connect_error) {
            die("Database connection failed: " . $db->connect_error);
        }

        $stmt = $db->prepare("UPDATE ssp SET profile_picture = ? WHERE ssp_id = ?");
        $stmt->bind_param('ss', $targetFilePath, $userId);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Profile image uploaded successfully.', 'path' => $targetFilePath]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update database.']);
        }

        $stmt->close();
        $db->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to upload file.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
