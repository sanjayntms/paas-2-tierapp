<?php
require 'vendor/autoload.php';

use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;

// Fetch database config from environment variables
$dbServer = getenv('DB_SERVER');
$dbName = getenv('DB_NAME');
$dbUsername = getenv('DB_USERNAME');
$dbPassword = getenv('DB_PASSWORD');

// SAS URL for Azure Blob Storage
$fullSasUrl = getenv('AZURE_STORAGE_SAS_URL'); // Full SAS URL

// Parse the Blob endpoint and SAS token
$blobEndpoint = strtok($fullSasUrl, '?'); // Blob endpoint (URL before the ?)
$sasToken = '?' . parse_url($fullSasUrl, PHP_URL_QUERY); // SAS token (everything after the ?)

// Create the Azure Blob client using the endpoint and SAS token
try {
    $blobClient = BlobRestProxy::createBlobService($blobEndpoint . $sasToken);

    // Create PDO instance using environment variables
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
