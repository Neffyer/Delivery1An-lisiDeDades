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

$itemId = isset($_POST["Item"]) ? $_POST['Item'] : 0;
$userId = isset($_POST["User_ID"]) ? $_POST['User_ID'] : 0;
$sessionId = isset($_POST["Session_ID"]) ? $_POST['Session_ID'] : 0;
$buyDate = isset($_POST['Buy_Date']) ? $_POST['Buy_Date'] : "";

error_log("Received purchase data: User_ID={$userId}, Session_ID={$sessionId}, Item={$itemId}, Buy_Date={$buyDate}");

if(!empty($itemId) && !empty($userId) && !empty($sessionId) && !empty($buyDate))
{
    $stmt = $conn->prepare("INSERT INTO `Purchases`(`itemId`, `userId`, `sessionId`, `buyDate`) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $itemId, $userId, $sessionId, $buyDate);
    
    if ($stmt->execute()) {
        echo $conn->insert_id;
    } else {
        error_log("Error in Purchase_Data.php: " . $stmt->error);
        echo "Error: " . $stmt->error;
    }
}
else {
    echo "Missing parameters";
}
$stmt->close();
$conn->close();
?>