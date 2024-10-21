<?php

$servername = "localhost";
$username = "antoniorr14";
$password = "46949721m";
$database = "antoniorr14";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $database);

// Verificar conexión
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Recibir los datos enviados desde Unity
$name = isset($_POST['name']) ? $_POST['name'] : "";
$country = isset($_POST['country']) ? $_POST['country'] : "";
$age = isset($_POST['age']) ? intval($_POST['age']) : 0;
$gender = isset($_POST['gender']) ? $_POST['gender'] : "";
$install_date = isset($_POST['date']) ? $_POST['date'] : "";

// Verificamos que todos los datos estén presentes
if (!empty($name) && !empty($country) && $age > 0 && !empty($gender)) {

    // Usar consultas preparadas
    $stmt = $conn->prepare("INSERT INTO UsersInfo (`Name`, `Country`, `Age`, `Gender`, `Install_Date`) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiss", $name, $country, $age, $gender, $install_date);

    if ($stmt->execute()) {
        // Devuelve solo el último ID insertado
        echo $conn->insert_id;
    } else {
        echo "ERROR: " . $stmt->error;
    }

    $stmt->close();

} else {
    echo "Missing parameters";
}

$conn->close();

?>
