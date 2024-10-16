<?php

$servername = "localhost";
$username = "antoniorr14";
$password = "46949721m";
$database = "antoniorr14";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Recibir los datos enviados desde Unity
$name = isset($_POST['name']) ? $_POST['name'] : "";
$country = isset($_POST['country']) ? $_POST['country'] : "";
$age = isset($_POST['age']) ? intval($_POST['age']) : 0;
$gender = isset($_POST['gender']) ? $_POST['gender'] : "";
$install_date = isset($_POST['date']) ? $_POST['date'] : "";

// Muestra los valores recibidos para verificar
echo "name: $name, country: $country, age: $age, gender: $gender, date: $install_date";

// Verificamos que todos los datos estén presentes
if (!empty($name) && !empty($country) && $age > 0 && !empty($gender)) {
    // Preparar la consulta SQL para insertar los datos
    $stmt = "INSERT INTO UsersInfo (`Name`, `Country`, `Age`, `Gender`, `Install_Date`) VALUES ('$name', '$country', '$age', '$gender', '$install_date')";
    //$stmt->bind_param( $name, $country, $age, $gender);

    echo $stmt;
   // echo "\n";

    // Ejecutar la consulta
    if ($conn->query($stmt)) {
        //Recibir el último ID
        $last_id = $conn->insert_id;
        echo "Record inserted successfully. Last inserted ID is: " . $last_id;
    } else {
        echo "ERROR no va: " . $stmt . "<br>" .  $conn->error;
    }

} else {
    echo "Missing parameters";
}

// Cerrar la conexión
$conn->close();

?>