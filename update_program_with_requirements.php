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

$data = json_decode(file_get_contents("php://input"), true);
$programId = $data['programId'] ?? null;
$residentId = $data['residentId'] ?? null;
$requirements = $data['requirements'] ?? [];
$action = $data['action'] ?? null; // "accept" or "reject"

if (!$programId || !$residentId || !$action) {
    echo json_encode(["success" => false, "message" => "Missing required fields."]);
    exit();
}

try {
    $conn->begin_transaction();

    if ($action === "accept") {
        $stmt = $conn->prepare("UPDATE resident_submissions SET status = 'approved' WHERE program_id = ? AND resident_id = ?");
        $stmt->bind_param("ii", $programId, $residentId);
        $stmt->execute();

        // Check if approved count matches beneficiaries
        $stmt = $conn->prepare("SELECT COUNT(*) as approvedCount FROM resident_submissions WHERE program_id = ? AND status = 'approved'");
        $stmt->bind_param("i", $programId);
        $stmt->execute();
        $result = $stmt->get_result();
        $approvedCount = $result->fetch_assoc()['approvedCount'];

        $stmt = $conn->prepare("SELECT beneficiaries FROM programs WHERE id = ?");
        $stmt->bind_param("i", $programId);
        $stmt->execute();
        $result = $stmt->get_result();
        $beneficiaries = $result->fetch_assoc()['beneficiaries'];

        if ($approvedCount >= $beneficiaries) {
            $stmt = $conn->prepare("UPDATE programs SET status = 'closed' WHERE id = ?");
            $stmt->bind_param("i", $programId);
            $stmt->execute();
        }

    } elseif ($action === "reject") {
        $stmt = $conn->prepare("UPDATE resident_submissions SET status = 'rejected' WHERE program_id = ? AND resident_id = ?");
        $stmt->bind_param("ii", $programId, $residentId);
        $stmt->execute();
    } else {
        throw new Exception("Invalid action.");
    }

    $conn->commit();
    echo json_encode(["success" => true, "message" => "Applicant status updated successfully."]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
} finally {
    $conn->close();
}
?>