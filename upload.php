<?php
// Include the database connection
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

    // Read file content
    $content = fopen($_FILES['photo']['tmp_name'], "r");
    $blobName = basename($_FILES["photo"]["name"]);
    $containerName = "inquiry-photos"; // Ensure this container exists

    // SAS URL from environment variables
    $fullSasUrl = getenv('AZURE_STORAGE_SAS_URL');

    // Create the full URL for the blob including the SAS token
    $blobUrl = "{$fullSasUrl}/{$blobName}";

    // Use cURL to upload the file
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $blobUrl);
    curl_setopt($ch, CURLOPT_PUT, true);
    curl_setopt($ch, CURLOPT_INFILE, $content);
    curl_setopt($ch, CURLOPT_INFILESIZE, filesize($_FILES['photo']['tmp_name']));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "x-ms-blob-type: BlockBlob"
    ]);

    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    if ($httpCode == 201) {
        // File uploaded successfully, save details in the database
        try {
            $sql = "INSERT INTO Inquiries (name, mobile_number, email, message, photo_url) VALUES (?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$name, $mobile, $email, $message, $blobUrl]);

            echo "Inquiry submitted successfully.";
        } catch (PDOException $e) {
            echo "Error inserting data: " . $e->getMessage();
        }
    } else {
        echo "Error uploading file to Azure Blob Storage.";
    }
}
?>
