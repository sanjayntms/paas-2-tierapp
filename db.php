<?php
require 'vendor/autoload.php';

use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;

// Fetch database config from environment variables
$dbServer = getenv('DB_SERVER');
$dbName = getenv('DB_NAME');
$dbUsername = getenv('DB_USERNAME');
$dbPassword = getenv('DB_PASSWORD');

// Full SAS URL (includes both endpoint and token)
$fullSasUrl = getenv('AZURE_STORAGE_SAS_URL');

// Split the SAS URL into Blob endpoint and SAS token
$blobEndpoint = strtok($fullSasUrl, '?'); // Everything before the '?' is the endpoint
$sasToken = '?' . parse_url($fullSasUrl, PHP_URL_QUERY); // Everything after the '?' is the SAS token

// Create the Blob client using only the Blob endpoint
try {
    $blobClient = BlobRestProxy::createBlobService($blobEndpoint);

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
