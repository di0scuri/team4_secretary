<?php
header("Content-Type: application/json");

// Database configuration
$hostname = "lesterintheclouds.com";
$username = "IT112-24-M";
$password = "W2Bq@EV[SFEV";
$database = "db_brgy_app";

// Connect to the database
$conn = new mysqli($hostname, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    $errorMessage = "Connection failed: " . $conn->connect_error;
    writeLog($errorMessage);
    echo json_encode(["success" => false, "message" => $errorMessage]);
    exit();
}

// Process request
$data = json_decode(file_get_contents("php://input"), true);
$programId = $data['programId'] ?? null;
$status = $data['status'] ?? null;

if (!$programId || !$status) {
    echo json_encode(["success" => false, "message" => "Invalid input. Program ID and status are required."]);
    exit();
}

try {
    $stmt = $conn->prepare("UPDATE programs SET status = ? WHERE programId = ?");
    $stmt->bind_param("si", $status, $programId);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Program status updated successfully."]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to update program status."]);
    }
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
} finally {
    $conn->close();
}
?>
