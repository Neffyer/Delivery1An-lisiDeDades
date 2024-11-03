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

// Receive data sent from Unity via POST
$userid = isset($_POST['UserId']) ? intval($_POST['UserId']) : 0;
$starttime = isset($_POST['StartSession']) ? $_POST['StartSession'] : "";

// Check if both userid and starttime are provided
if (!empty($userid) && !empty($starttime)) {
    // Use a prepared statement to insert data securely
    $stmt = $conn->prepare("INSERT INTO SessionsData (UserId, StartSession) VALUES (?, ?)");
    $stmt->bind_param("is", $userid, $starttime);

    // Execute the statement and check for success
    if ($stmt->execute()) {
        $last_id = $stmt->insert_id;  // Retrieve the last inserted ID
        echo $last_id;
    } else {
        echo "ERROR: " . $stmt->error;
    }

    // Close the prepared statement
    $stmt->close();
} else {
    echo "Missing parameters";  // Notify if data is missing
}

// Close the database connection
$conn->close();

?>
