<?php
session_start();
include 'db.php'; // Must set $pdo and $blobClient

function getBlobAccountNameFromEnv() {
    $acct = getenv('AZURE_STORAGE_ACCOUNT');
    if ($acct) return $acct;

    $conn = getenv('AZURE_STORAGE_CONNECTION_STRING');
    if ($conn && preg_match('/AccountName=([^;]+)/', $conn, $m)) {
        return $m[1];
    }
    return null;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "Method not allowed.";
    exit;
}

$name    = trim($_POST['name'] ?? '');
$mobile  = trim($_POST['mobile'] ?? '');
$email   = trim($_POST['email'] ?? '');
$message = trim($_POST['message'] ?? '');

$errors = [];
if ($name === '') $errors[] = 'Name is required.';
if ($mobile === '') $errors[] = 'Mobile is required.';
if ($email === '') $errors[] = 'Email is required.';
if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
    $errors[] = 'Photo upload failed.';
}

if (!empty($errors)) {
    error_log('Upload validation errors: ' . implode('; ', $errors));
    header('Location: index.php?upload=0');
    exit;
}

$fileTmp  = $_FILES['photo']['tmp_name'];
$fileName = basename($_FILES['photo']['name']);
$containerName = 'inquiry-photos';

try {
    if (!isset($blobClient)) {
        throw new Exception('$blobClient missing from db.php');
    }

    $contentStream = @fopen($fileTmp, 'r');
    if (!$contentStream) {
        throw new Exception("Failed to open uploaded file for reading.");
    }

    $contentType = mime_content_type($fileTmp) ?: 'application/octet-stream';

    $optionsClass = 'MicrosoftAzure\\Storage\\Blob\\Models\\CreateBlockBlobOptions';
    if (class_exists($optionsClass)) {
        $options = new $optionsClass();
        $options->setContentType($contentType);
        $blobClient->createBlockBlob($containerName, $fileName, $contentStream, $options);
    } else {
        $blobClient->createBlockBlob($containerName, $fileName, $contentStream);
    }

    // Close only if it's a valid stream resource
    if (is_resource($contentStream)) {
        fclose($contentStream);
    }

    $accountName = getBlobAccountNameFromEnv();
    if (!$accountName) {
        throw new Exception('Storage account name not found in env vars.');
    }

    $blobUrl = "https://{$accountName}.blob.core.windows.net/{$containerName}/" . rawurlencode($fileName);

    $sql = "INSERT INTO Inquiries (name, mobile_number, email, message, photo_url, created_at)
            VALUES (:name, :mobile, :email, :message, :photo_url, GETDATE())";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':name' => $name,
        ':mobile' => $mobile,
        ':email' => $email,
        ':message' => $message,
        ':photo_url' => $blobUrl
    ]);

    header('Location: index.php?upload=1');
    exit;

} catch (Exception $e) {
    error_log('Upload error: ' . $e->getMessage());

    if (isset($contentStream) && is_resource($contentStream)) {
        fclose($contentStream);
    }

    header('Location: index.php?upload=0');
    exit;
}
