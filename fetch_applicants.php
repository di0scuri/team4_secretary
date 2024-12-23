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
    echo json_encode(["success" => false, "message" => "Connection failed: " . $conn->connect_error]);
    exit();
}

$programId = $_GET['programId'] ?? null;

if (!$programId) {
    echo json_encode(["success" => false, "message" => "Program ID is required."]);
    exit();
}

try {
    $stmt = $conn->prepare("SELECT rs.resident_id, r.name, rs.status, rs.uploaded_at 
                            FROM resident_submissions rs 
                            JOIN residents r ON rs.resident_id = r.id 
                            WHERE rs.program_id = ?");
    $stmt->bind_param("i", $programId);
    $stmt->execute();
    $result = $stmt->get_result();

    $applicants = [];
    while ($row = $result->fetch_assoc()) {
        $applicants[] = [
            "residentId" => $row['resident_id'],
            "name" => $row['name'],
            "status" => $row['status'],
            "submittedAt" => $row['uploaded_at'],
        ];
    }

    echo json_encode(["success" => true, "data" => $applicants]);
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
} finally {
    $conn->close();
}
?>
