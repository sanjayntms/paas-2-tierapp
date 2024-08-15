<?php
$serverName = "ntmssql.database.windows.net";
$database = "ntmsphpdb";
$username = "vmadmin";
$password = "123#ntms123#";

try {
    // Create connection
    $conn = new PDO("sqlsrv:server=$serverName;Database=$database", $username, $password);

    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
