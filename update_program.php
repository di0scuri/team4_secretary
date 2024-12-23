<?php
header("Content-Type: application/json");

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log file path
$logFile = '/path/to/logfile.log';

// Function to write logs
function writeLog($message) {
    global $logFile;
    file_put_contents($logFile, date('Y-m-d H:i:s') . " - " . $message . PHP_EOL, FILE_APPEND);
}

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

// Get POST data
$data = json_decode(file_get_contents("php://input"), true);
$programId = $data['programId'] ?? null;
$recipients = $data['recipients'] ?? null;
$status = $data['status'] ?? null;

// Log received data
writeLog("Received data: " . json_encode($data));

// Validate required fields
if (!$programId || $recipients === null || !$status) {
    $errorMessage = "Missing required fields.";
    writeLog($errorMessage);
    echo json_encode(["success" => false, "message" => $errorMessage]);
    exit();
}

// Prepare and execute the SQL query
$sql = "UPDATE programs SET recipients = ?, status = ? WHERE programId = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    $errorMessage = "Statement preparation failed: " . $conn->error;
    writeLog($errorMessage);
    echo json_encode(["success" => false, "message" => $errorMessage]);
    exit();
}

$stmt->bind_param("isi", $recipients, $status, $programId);
if ($stmt->execute()) {
    writeLog("Program updated successfully: programId = $programId, recipients = $recipients, status = $status");
    echo json_encode(["success" => true, "message" => "Program updated successfully."]);
} else {
    $errorMessage = "Failed to update program: " . $stmt->error;
    writeLog($errorMessage);
    echo json_encode(["success" => false, "message" => $errorMessage]);
}

// Close connection
$stmt->close();
$conn->close();
?>
