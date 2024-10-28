<?php

$servername = "localhost";  // Cambiar por la dirección del servidor correcto
$username = "antoniorr14";
$password = "46949721m";
$database = "antoniorr14";

// Crear y verificar la conexión
$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sessionId = $_POST["User_Id"];
$endSession = $_POST["End_Session"];

error_log("Received end session data: UserId={$sessionId}, End_Session={$endSession}");

$stmt = $conn->prepare("UPDATE SessionsData SET `EndSession` = ? WHERE `UserId` = ?");
$stmt->bind_param("si", $endSession, $sessionId);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        //echo "Session closed successfully";
        echo $endSession;
    } else {
        error_log("No session updated in Close_Session_Data.php");
        echo "No session updated";
    }
} else {
    error_log("Error in Close_Session_Data.php: " . $stmt->error);
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>