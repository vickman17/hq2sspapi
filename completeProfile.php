<?php

include_once "cors.php";

$data = json_decode(file_get_contents("php://input"), true);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $selectedCategory = $data['selectedCategory'] ?? null;
    $selectedSubcategories = $data['selectedSubcategories'] ?? [];
    $residentialAddress = $data['residentialAddress'] ?? '';
    $workAddress = $data['workAddress'] ?? '';
    $otherNumber = $data['otherNumber'] ?? '';
    $qualification = $data["qualification"] ?? '';
    $sspId = $data['sspId'] ?? "";

    if (empty($sspId)) {
        echo json_encode(["success" => false, "message" => "Please log in to complete the profile."]);
        exit;
    }

    // Database connection
    $conn = new mysqli("localhost", "root", "", "hq2app");
    if ($conn->connect_error) {
        echo json_encode(["success" => false, "message" => "Database connection failed."]);
        exit;
    }

    // Check if sspId exists in the database
    $stmt = $conn->prepare("SELECT ssp_id FROM ssp WHERE ssp_id = ?");
    $stmt->bind_param("s", $sspId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(["success" => false, "message" => "Invalid sspId. Please log in again."]);
        $stmt->close();
        $conn->close();
        exit;
    }

    $stmt->close();

    // Validate other fields
    if (!$selectedCategory || empty($selectedSubcategories) || !$residentialAddress) {
        echo json_encode(["success" => false, "message" => "All fields are required."]);
        $conn->close();
        exit;
    }

    // Update the record in the database
    $stmt = $conn->prepare("
        UPDATE ssp 
        SET category_id = ?, subcategories = ?, residential_address = ?, work_address = ?, phone2 = ?,  qualification = ?
        WHERE ssp_id = ?
    ");
    $subcategoriesJSON = json_encode($selectedSubcategories);
    $stmt->bind_param("sssssss", $selectedCategory, $subcategoriesJSON, $residentialAddress, $workAddress, $otherNumber, $qualification, $sspId);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Profile updated successfully!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to update profile."]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method."]);
}
