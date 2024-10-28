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

$itemId = $_POST["Item"];
$userId = $_POST["User_ID"];
$sessionId = $_POST["Session_ID"];
$buyDate = $_POST["Buy_Date"];

error_log("Received purchase data: User_ID={$userId}, Session_ID={$sessionId}, Item={$itemId}, Buy_Date={$buyDate}");

$stmt = $conn->prepare("INSERT INTO `Purchases`(`userId`, `sessionId`, `itemId`, `buyDate`) VALUES (?, ?, ?, ?)");
$stmt->bind_param("iiis", $userId, $sessionId, $itemId, $buyDate);

if ($stmt->execute()) {
    echo $conn->insert_id;
} else {
    error_log("Error in Purchase_Data.php: " . $stmt->error);
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>