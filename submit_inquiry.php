<?php
include 'db.php'; // Include database connection
require_once 'vendor/autoload.php'; // Include Azure Blob Storage SDK

use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Blob\Models\CreateBlockBlobOptions;

// Azure Storage Account credentials
$connectionString = "DefaultEndpointsProtocol=https;AccountName=your_account_name;AccountKey=your_account_key";
$blobClient = BlobRestProxy::createBlobService($connectionString);

// SAS Token for the container
$sasToken = getenv('AZURE_STORAGE_SAS_TOKEN');
$containerName = "inquiry-images"; // Replace with your container name

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];
    $photoUrl = null;

    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['photo']['tmp_name'];
        $fileName = $_FILES['photo']['name'];

        // Upload the file to Azure Blob Storage
        $content = fopen($fileTmpPath, "r");
        $blobName = $fileName;
        $blobClient->createBlockBlob($containerName, $blobName, $content);

        // Construct the URL with SAS token
        $photoUrl = "https://ntmsphpsa.blob.core.windows.net/$containerName/$blobName$sasToken";
    }

    // Insert the inquiry into the database
    $stmt = $conn->prepare("INSERT INTO inquiries (name, email, message, photo) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $email, $message, $photoUrl]);

    // Redirect to a success page
    header("Location: success.php");
    exit();
}
?>
