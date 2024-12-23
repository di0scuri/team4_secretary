<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Database connection parameters
$hostname = "lesterintheclouds.com";
$username = "IT112-24-M";
$password = "W2Bq@EV[SFEV";
$database = "db_brgy_app";

// Create connection
$conn = new mysqli($hostname, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die(json_encode(array("error" => "Connection Failed: " . $conn->connect_error)));
}

// Get the request payload
$request = json_decode(file_get_contents('php://input'), true);

// Ensure all necessary fields are set
if (
    isset($request['meetingName']) && isset($request['startTime']) && isset($request['endTime']) &&
    isset($request['location']) && isset($request['note']) && isset($request['participants']) && isset($request['expensesList'])
) {
    $meetingName = $conn->real_escape_string($request['meetingName']);
    $startTime = $conn->real_escape_string($request['startTime']);
    $endTime = $conn->real_escape_string($request['endTime']);
    $location = $conn->real_escape_string($request['location']);
    $note = $conn->real_escape_string($request['note']);
    $participants = $request['participants']; // Array of participants
    $expensesList = $request['expensesList']; // Array of expenses

    // Prepare SQL query to insert the meeting data
    $stmt = $conn->prepare("INSERT INTO meetings (meetingName, startTime, endTime, location, note) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) {
        die(json_encode(array("error" => "Prepare failed: " . $conn->error)));
    }
    $stmt->bind_param("sssss", $meetingName, $startTime, $endTime, $location, $note);

    if ($stmt->execute()) {
        $meetingId = $stmt->insert_id; // Get the ID of the newly inserted meeting

        // Insert participants
        $stmtParticipants = $conn->prepare("INSERT INTO participants (meetingId, participantName, role) VALUES (?, ?, ?)");
        if (!$stmtParticipants) {
            die(json_encode(array("error" => "Prepare failed: " . $conn->error)));
        }

        foreach ($participants as $participant) {
            $participantName = $conn->real_escape_string($participant['participantName']);
            $role = $conn->real_escape_string($participant['role']);
            $stmtParticipants->bind_param("iss", $meetingId, $participantName, $role);
            if (!$stmtParticipants->execute()) {
                die(json_encode(array("error" => "Error inserting participant: " . $stmtParticipants->error)));
            }
        }

        // Insert meeting expenses
        $stmtExpense = $conn->prepare("INSERT INTO meetingexpense (meetingId, quantity, pricePerUnit, total) VALUES (?, ?, ?, ?)");
        if (!$stmtExpense) {
            die(json_encode(array("error" => "Prepare failed: " . $conn->error)));
        }

        foreach ($expensesList as $expense) {
            $quantity = $conn->real_escape_string($expense['quantity']);
            $pricePerUnit = $conn->real_escape_string($expense['pricePerUnit']);
            $total = $conn->real_escape_string($expense['total']); // Could be calculated client-side before sending
            $stmtExpense->bind_param("isss", $meetingId, $quantity, $pricePerUnit, $total);
            if (!$stmtExpense->execute()) {
                die(json_encode(array("error" => "Error inserting expenses: " . $stmtExpense->error)));
            }
        }

        // Return success message
        echo json_encode(array("message" => "Meeting, participants, and expenses saved successfully"));
    } else {
        // Return error message
        echo json_encode(array("error" => "Error: " . $stmt->error));
    }

    // Close statements
    $stmt->close();
    $stmtParticipants->close();
    $stmtExpense->close();
} else {
    // Invalid input
    echo json_encode(array("error" => "Invalid input"));
}

// Close connection
$conn->close();
?>
