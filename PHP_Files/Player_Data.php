<?php

// Set up database connection parameters
$servername = "localhost";
$username = "antoniorr14";
$password = "46949721m";
$database = "antoniorr14";

// Establish a new MySQL database connection
$conn = new mysqli($servername, $username, $password, $database);

// Verificar conexión
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error); // End script if connection fails
}

// Receive data sent from Unity via POST method
$name = isset($_POST['name']) ? $_POST['name'] : "";           // User's name
$country = isset($_POST['country']) ? $_POST['country'] : "";   // User's country
$age = isset($_POST['age']) ? intval($_POST['age']) : 0;        // User's age (converted to integer)
$gender = isset($_POST['gender']) ? $_POST['gender'] : "";      // User's gender
$install_date = isset($_POST['date']) ? $_POST['date'] : "";    // User's installation date

// Check that all required data fields are provided
if (!empty($name) && !empty($country) && $age > 0 && !empty($gender)) {

    // Prepare a SQL statement to insert data into the UsersInfo table
    $stmt = $conn->prepare("INSERT INTO UsersInfo (`Name`, `Country`, `Age`, `Gender`, `Install_Date`) VALUES (?, ?, ?, ?, ?)");

    // Bind the parameters to the SQL statement (s for string, i for integer)
    $stmt->bind_param("ssiss", $name, $country, $age, $gender, $install_date);

    // Execute the prepared statement and check for success
    if ($stmt->execute()) {
        // If successful, return the ID of the last inserted row
        echo $conn->insert_id;
    } else {
        echo "ERROR: " . $stmt->error;
    }
    // Close the prepared statement
    $stmt->close();

} else {
    // Return an error message if required data is missing
    echo "Missing parameters";
}

// Close the database connection
$conn->close();

?>
