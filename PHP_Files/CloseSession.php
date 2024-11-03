<?php

// Database connection details
$servername = "localhost";
$username = "antoniorr14";
$password = "46949721m";
$database = "antoniorr14";

// Create and verify the connection
$conn = new mysqli($servername, $username, $password, $database);

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Receive POST data for session ending
$sessionId = $_POST["Session_ID"];
$endSession = $_POST["End_Session"];

// Log received data for debugging purposes
error_log("Received end session data: Session_ID={$sessionId}, End_Session={$endSession}");

// Prepare SQL statement to update the session's end time
$stmt = $conn->prepare("UPDATE SessionsData SET `EndSession` = ? WHERE `id` = ?");
$stmt->bind_param("si", $endSession, $sessionId);

// Execute the update and check if any row was affected
if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        // Return the end session time if successful
        echo $endSession;
    } else {
        // Log if no session was updated
        error_log("No session updated in Close_Session_Data.php");
        echo "No session updated";
    }
} else {
    // Log error if update fails
    error_log("Error in Close_Session_Data.php: " . $stmt->error);
    echo "Error: " . $stmt->error;
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
