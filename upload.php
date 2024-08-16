<?php
// Include the database connection and Azure Blob setup
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $mobile = $_POST['mobile'];
    $email = $_POST['email'];
    $message = $_POST['message'];
    $photo = $_FILES['photo']['name'];
    
    // Upload file to Azure Blob Storage
    $content = fopen($_FILES['photo']['tmp_name'], "r");
    $blobName = basename($_FILES["photo"]["name"]);
    $containerName = "inquiry-photos";

    try {
        // Upload to Azure Blob
         $blobClient->createBlockBlob($containerName, $blobName, $content);
        
        // Get the Blob URL
        $blobUrl = "https://$storagename.blob.core.windows.net/$containerName/$blobName";

        // Insert the form data along with Blob URL into the database
        $sql = "INSERT INTO Inquiries (name, mobile_number, email, message, photo_url) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name, $mobile, $email, $message, $blobUrl]);

        echo "Inquiry submitted successfully.";
    } catch (ServiceException $e) {
        echo "Error uploading file to Azure: " . $e->getMessage();
    } catch (PDOException $e) {
        echo "Error inserting data: " . $e->getMessage();
    }
}
?>
