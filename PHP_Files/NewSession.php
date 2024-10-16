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

// Recibir los datos enviados desde Unity
$userid = isset($_POST['UserId']) ? intval($_POST['UserId']) : 0;
$starttime = isset($_POST['StartTime']) ? $_POST['StartTime'] : "";

if (!empty($userid) && !empty($starttime)) {
    // Usar una sentencia preparada para mayor seguridad
    $stmt = $conn->prepare("INSERT INTO NewSessions (UserId, StartTime, EndTime) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $userid, $starttime);

    if ($stmt->execute()) {
        $last_id = $stmt->insert_id;  // Obtener el último ID insertado
        echo "Record inserted successfully. Last inserted ID is: " . $last_id;
    } else {
        echo "ERROR no va: " . $stmt->error;
    }

    $stmt->close();  // Cerrar la sentencia preparada
} else {
    echo "Missing parameters";
}

// Cerrar la conexión
$conn->close();

?>
