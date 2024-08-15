<?php
include 'db.php'; // Include database connection
require_once 'vendor/autoload.php'; // Include Azure Blob Storage SDK

use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Blob\Models\CreateBlockBlobOptions;

// SAS Token for the container
$sasToken = "?sp=racwdl&st=2024-08-15T08:02:29Z&se=2024-08-16T16:02:29Z&spr=https&sv=2022-11-02&sr=c&sig=u0FWwZeTmdNXZ5eQ%2B4bLIA85YOdbbQIvlphup8EY03E%3D";

$containerName = "inquiry-images"; // Replace with your container name

// Azure Storage Account credentials (not needed for SAS but may be used for other operations)
$connectionString = "DefaultEndpointsProtocol=https;AccountName=your_account_name;AccountKey=your_account_key";
$blobClient = BlobRestProxy::createBlobService($connectionString);

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];
    $photoUrl = null;

    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['photo']['tmp_name'];
        $fileName = $_FILES['photo']['name'];

        // Upload the file to Azure Blob Storage with SAS Token
        $content = fopen($fileTmpPath, "r");
        $blobName = $fileName;
        $blobClient->createBlockBlob($containerName, $blobName, $content);

        // Construct the URL with SAS token
        $photoUrl = "https://your_account_name.blob.core.windows.net/$containerName/$blobName$sasToken";
    }

    // Insert the inquiry into the database
    $stmt = $conn->prepare("INSERT INTO inquiries (name, email, message, photo) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $email, $message, $photoUrl]);

    // Redirect to a success page
    header("Location: success.php");
    exit();
}
?>
