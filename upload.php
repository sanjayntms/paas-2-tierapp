<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Include the database connection
include 'db.php';

// Include Azure Blob SDK
require_once 'vendor/autoload.php'; // Make sure you installed microsoft/azure-storage-blob via Composer

use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;

// Get the Azure Storage connection string from environment
$connectionString = getenv('AZURE_STORAGE_CONNECTION_STRING');

// Create the Blob client
$blobClient = BlobRestProxy::createBlobService($connectionString);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'] ?? '';
    $mobile = $_POST['mobile'] ?? '';
    $email = $_POST['email'] ?? '';
    $message = $_POST['message'] ?? '';

    if (!empty($_FILES['photo']['tmp_name'])) {
        $photoTmpPath = $_FILES['photo']['tmp_name'];
        $photoName = basename($_FILES['photo']['name']);
        $containerName = "inquiry-photos";

        try {
            // Upload the photo to Azure Blob Storage
            $content = fopen($photoTmpPath, "r");
            $blobClient->createBlockBlob($containerName, $photoName, $content);

            // Build the Blob URL
            $accountName = parse_url(explode(';', $connectionString)[2], PHP_URL_HOST);
            $accountName = explode('.', $accountName)[0]; // Safely extract accountName from connection string

            $blobUrl = "https://{$accountName}.blob.core.windows.net/{$containerName}/{$photoName}";

            // Insert the form data into the database
            $sql = "INSERT INTO Inquiries (name, mobile_number, email, message, photo_url) VALUES (?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$name, $mobile, $email, $message, $blobUrl]);

            echo "✅ Inquiry submitted successfully.";
        } catch (ServiceException $e) {
            echo "❌ Azure upload error: " . $e->getMessage();
        } catch (PDOException $e) {
            echo "❌ Database error: " . $e->getMessage();
        }
    } else {
        echo "❌ No file uploaded.";
    }
}
?>
