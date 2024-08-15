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
$blobSasUrl = getenv('AZURE_STORAGE_SAS_URL'); // This should be the SAS URL generated from the portal

try {
    // Create PDO instance using environment variables
    $pdo = new PDO("sqlsrv:server = tcp:$dbServer,1433; Database = $dbName", $dbUsername, $dbPassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create Azure Blob client using the SAS URL
    $blobClient = BlobRestProxy::createBlobService($blobSasUrl);
} catch (PDOException $e) {
    echo "Error connecting to the database: " . $e->getMessage();
    exit;
} catch (ServiceException $e) {
    echo "Error connecting to Azure Blob Storage: " . $e->getMessage();
    exit;
}
?>
