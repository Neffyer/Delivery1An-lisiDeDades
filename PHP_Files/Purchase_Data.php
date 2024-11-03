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

// Receive POST data for purchase
$itemId = isset($_POST["Item"]) ? $_POST['Item'] : 0;
$sessionId = isset($_POST["Session_ID"]) ? $_POST['Session_ID'] : 0;
$buyDate = isset($_POST['Buy_Date']) ? $_POST['Buy_Date'] : "";

// Log received data for debugging
error_log("Received purchase data: Session_ID={$sessionId}, Item={$itemId}, Buy_Date={$buyDate}");

// Check if item ID, session ID, and buy date are provided
if(!empty($itemId) && !empty($sessionId) && !empty($buyDate))
{
    // Use a prepared statement to insert purchase data securely
    $stmt = $conn->prepare("INSERT INTO `Purchases`(`itemId`, `buyDate`, `sessionId`) VALUES (?, ?, ?)");
    $stmt->bind_param("isi", $itemId, $buyDate, $sessionId);
    
    // Execute the statement and check for success
    if ($stmt->execute()) {
        echo $conn->insert_id;  // Output the last inserted ID
    } else {
        // Log error if insertion fails
        error_log("Error in Purchase_Data.php: " . $stmt->error);
        echo "Error: " . $stmt->error;
    }
} else {
    // Notify if data is missing
    echo "Missing parameters";
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
