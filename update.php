<?php
$host = 'localhost';
$username = 'etisparl_bank';
$password = 'etisparl_bank'; // Replace with your actual password
$database = 'etisparl_bank';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    foreach ($_POST as $name => $value) {
        $name = $conn->real_escape_string($name);
        $value = $conn->real_escape_string($value);

        // Insert old value into history table
        $oldValueSql = "INSERT INTO mybank_history (name, value) SELECT name, value FROM mybank WHERE name = '$name'";
        $conn->query($oldValueSql);

        // Update current value in the main table
        $updateSql = "UPDATE mybank SET value = '$value' WHERE name = '$name'";
        if ($conn->query($updateSql) === FALSE) {
            echo "Error updating record: " . $conn->error;
        }
    }
}

header("Location: index.php");
exit();
?>
