<?php
require 'vendor/autoload.php';

use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;

// Fetch database config from environment variables
$dbServer = getenv('DB_SERVER');
$dbName = getenv('DB_NAME');
$dbUsername = getenv('DB_USERNAME');
$dbPassword = getenv('DB_PASSWORD');
$storagename = getenv('Storage_NAME');
// Connection string for Azure Blob Storage
$connectionString = getenv('AZURE_STORAGE_CONNECTION_STRING');

try {
    // Create the Blob client using the connection string
    $blobClient = BlobRestProxy::createBlobService($connectionString);

    // Create PDO instance for database connection
    $pdo = new PDO("sqlsrv:server = tcp:$dbServer,1433; Database = $dbName", $dbUsername, $dbPassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Error connecting to the database: " . $e->getMessage();
    exit;
} catch (ServiceException $e) {
    echo "Error connecting to Azure Blob Storage: " . $e->getMessage();
    exit;
}
?>
