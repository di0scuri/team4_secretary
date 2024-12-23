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
$residentId = $_GET['residentId'] ?? null;

if (!$programId || !$residentId) {
    echo json_encode(["success" => false, "message" => "Program ID and Resident ID are required."]);
    exit();
}

try {
    $stmt = $conn->prepare("SELECT rs.id, rs.file_path, rq.name 
                            FROM resident_submissions rs 
                            JOIN requirements rq ON rs.requirement_id = rq.id 
                            WHERE rs.program_id = ? AND rs.resident_id = ?");
    $stmt->bind_param("ii", $programId, $residentId);
    $stmt->execute();
    $result = $stmt->get_result();

    $requirements = [];
    while ($row = $result->fetch_assoc()) {
        $requirements[] = [
            "id" => $row['id'],
            "name" => $row['name'],
            "filePath" => $row['file_path'],
        ];
    }

    echo json_encode(["success" => true, "data" => $requirements]);
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
} finally {
    $conn->close();
}
?>
