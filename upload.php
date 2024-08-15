<?php
// Include the database connection and Azure Blob setup
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $mobile = $_POST['mobile'];
    $email = $_POST['email'];
    $message = $_POST['message'];
    $photo = $_FILES['photo']['name'];
    
    // Check if file was uploaded
    if (empty($photo)) {
        echo "No file uploaded. Please select a file.";
        exit;
    }

    // Upload file to Azure Blob Storage
    $content = fopen($_FILES['photo']['tmp_name'], "r");
    $blobName = basename($_FILES["photo"]["name"]);
    $containerName = "inquiry-photos"; // Ensure this container exists

    try {
        // Use the blob client to upload to Azure Blob Storage
        $blobClient->createBlockBlob($containerName, $blobName, $content);

        // Construct the Blob URL using the SAS URL and the blob name
        $blobUrl = "{$blobSasUrl}{$containerName}/$blobName";

        // Insert the form data along with the Blob URL into the database
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
