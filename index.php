<?php

session_start(); // Start the session

// Check if the user is already logged in
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    // User is already logged in, continue with the page
} else {
    // User is not logged in, redirect to login page
    header('Location: login.php');
    exit;
}


$host = 'localhost';
$username = 'etisparl_bank';
$password = 'etisparl_bank'; // Replace with your actual password
$database = 'etisparl_bank';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve current values
$names = ['AB', 'EBL', 'DAgent', 'DS', 'BB'];
$currentValues = array();

foreach ($names as $name) {
    $sql = "SELECT value FROM mybank WHERE name = '$name'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $currentValues[$name] = $row['value'];
    } else {
        $currentValues[$name] = "No data found";
    }
}

// Retrieve the last 5 historical values
$sqlHistory = "SELECT * FROM mybank_history ORDER BY timestamp DESC LIMIT 5";
$resultHistory = $conn->query($sqlHistory);

$history = array();
while ($row = $resultHistory->fetch_assoc()) {
    $history[] = $row;
}

// Calculate the total value
$totalValue = 0;



foreach ($currentValues as $name => $value) {
    $totalValue += is_numeric($value) ? floatval($value) : 0;
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bank Information</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<div class="container">
    <h1>Current Bank Values</h1>

    <?php foreach ($currentValues as $name => $value): ?>
        <p><strong><?php echo $name; ?>:</strong> <?php echo $value; ?></p>
    <?php endforeach; ?>

    <h2>Last 5 Bank History Records</h2>
    <table class="bank-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Value</th>
                <th>Timestamp</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($history as $record): ?>
                <tr>
                    <td><?php echo $record['id']; ?></td>
                    <td><?php echo $record['name']; ?></td>
                    <td><?php echo $record['value']; ?></td>
                    <td><?php echo $record['timestamp']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Total Value</h2>
    <p class="total-value"><strong>Total Value:</strong> <?php echo $totalValue; ?></p>

    <h2>Edit Values</h2>
    <form method="post" action="update.php" class="edit-form">
        <?php foreach ($currentValues as $name => $value): ?>
            <div class="form-group">
                <label for="<?php echo $name; ?>"><?php echo $name; ?>:</label>
                <input type="text" name="<?php echo $name; ?>" id="<?php echo $name; ?>" value="<?php echo $value; ?>" required>
            </div>
        <?php endforeach; ?>
        <input type="submit" value="Update" class="submit-btn">
    </form>
</div>

</body>
</html>

