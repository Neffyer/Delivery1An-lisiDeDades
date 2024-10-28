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
$starttime = isset($_POST['StartSession']) ? $_POST['StartSession'] : "";

if (!empty($userid) && !empty($starttime)) {
    // Usar una sentencia preparada para mayor seguridad
    $stmt = $conn->prepare("INSERT INTO SessionsData (UserId, StartSession) VALUES (?, ?)");
    $stmt->bind_param("is", $userid, $starttime);

    if ($stmt->execute()) {
        $last_id = $stmt->insert_id;  // Obtener el último ID insertado
        echo $last_id;
    } else {
        echo "ERROR: " . $stmt->error;
    }

    $stmt->close();  // Cerrar la sentencia preparada
} else {
    echo "Missing parameters";
}

// Cerrar la conexión
$conn->close();

?>
