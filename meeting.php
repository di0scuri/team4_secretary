<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

$hostname = "lesterintheclouds.com";
$username = "IT112-24-M";
$password = "W2Bq@EV[SFEV";
$database = "db_brgy_app";

$conn = new mysqli($hostname, $username, $password, $database);

if ($conn->connect_error) {
    echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['meetingName'], $data['startTime'], $data['endTime'], $data['location'], $data['note'])) {
    echo json_encode(["error" => "Missing required fields"]);
    exit();
}

$meetingName = $data['meetingName'];
$startTime = $data['startTime'];
$endTime = $data['endTime'];
$location = $data['location'];
$note = $data['note'];

$insertMeeting = "INSERT INTO meetings (meetingName, startTime, endTime, location, note, status) 
                  VALUES (?, ?, ?, ?, ?, 'Pending')";
$stmt = $conn->prepare($insertMeeting);
$stmt->bind_param("sssss", $meetingName, $startTime, $endTime, $location, $note);

if ($stmt->execute()) {
    $meetingId = $conn->insert_id;

    if (isset($data['participants']) && is_array($data['participants'])) {
        $insertParticipant = "INSERT INTO participants (meetingId, participantsName, role) VALUES (?, ?, ?)";
        $stmtParticipant = $conn->prepare($insertParticipant);

        foreach ($data['participants'] as $participant) {
            $participantsName = $participant['participantName'];
            $role = $participant['role'] ?? 'No Role';
            $stmtParticipant->bind_param("iss", $meetingId, $participantsName, $role);
            if (!$stmtParticipant->execute()) {
                echo json_encode(["error" => "Failed to insert participant: " . $stmtParticipant->error]);
                exit();
            }
        }
    }

    if (isset($data['expensesList']) && is_array($data['expensesList'])) {
        $insertExpenses = "INSERT INTO meetingexpense (meetingId, quantity, pricePerUnit, total, name) 
                           VALUES (?, ?, ?, ?, ?)";
        $stmtExpenses = $conn->prepare($insertExpenses);
    
        foreach ($data['expensesList'] as $expense) {
            $quantity = (int)$expense['quantity'];
            $pricePerUnit = (float)$expense['pricePerUnit'];
            $total = (float)$expense['total'];
            $name = $expense['name'];
            $stmtExpenses->bind_param("iiffs", $meetingId, $quantity, $pricePerUnit, $total, $name);
            if (!$stmtExpenses->execute()) {
                echo json_encode(["error" => "Failed to insert expense: " . $stmtExpenses->error]);
                exit();
            }
        }
    }
    

    echo json_encode([
        "message" => "Meeting, participants, and expenses saved successfully",
        "meetingId" => $meetingId
    ]);
} else {
    echo json_encode(["error" => "Error saving meeting: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
