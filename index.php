<?php
include 'db.php'; // Include your database connection
$sasToken = "?sp=racwdl&st=2024-08-15T08:02:29Z&se=2024-08-16T16:02:29Z&spr=https&sv=2022-11-02&sr=c&sig=u0FWwZeTmdNXZ5eQ%2B4bLIA85YOdbbQIvlphup8EY03E%3D";
 use MicrosoftAzure\Storage\Blob\BlobRestProxy;
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_inquiry'])) {
    // Inquiry form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];
    $photoUrl = null;

    // Upload photo if present
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        require_once 'vendor/autoload.php'; // Include Azure Blob Storage SDK

       

        // SAS Token and Azure Storage Account credentials
        $sasToken = "?sp=racwdl&st=2024-08-15T08:02:29Z&se=2024-08-16T16:02:29Z&spr=https&sv=2022-11-02&sr=c&sig=u0FWwZeTmdNXZ5eQ%2B4bLIA85YOdbbQIvlphup8EY03E%3D"; // Replace with your actual SAS token
        $containerName = "inquiry-images"; // Replace with your container name
       // $connectionString = "DefaultEndpointsProtocol=https;AccountName=your_account_name;AccountKey=your_account_key";
        $blobClient = BlobRestProxy::createBlobService($sasToken);

        $fileTmpPath = $_FILES['photo']['tmp_name'];
        $fileName = $_FILES['photo']['name'];

        // Upload the file to Azure Blob Storage with SAS Token
        $content = fopen($fileTmpPath, "r");
        $blobName = $fileName;
        try {
            $blobClient->createBlockBlob($containerName, $blobName, $content);
            $photoUrl = "https://ntmsphpsa.blob.core.windows.net/$containerName/$blobName$sasToken";
        } catch (Exception $e) {
            echo "Error uploading file: " . $e->getMessage();
        }
    }

    // Insert the inquiry into the database
    $stmt = $conn->prepare("INSERT INTO inquiries (name, email, message, photo) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $email, $message, $photoUrl]);

    // Redirect to the same page to avoid resubmission on refresh
    header("Location: index.php");
    exit();
}

// Handle search
$searchTerm = '';
if (isset($_POST['search'])) {
    $searchTerm = $_POST['search'];
    $stmt = $conn->prepare("SELECT * FROM inquiries WHERE name LIKE ? OR email LIKE ? OR message LIKE ?");
    $stmt->execute(["%$searchTerm%", "%$searchTerm%", "%$searchTerm%"]);
} else {
    $stmt = $conn->prepare("SELECT * FROM inquiries");
    $stmt->execute();
}

$inquiries = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inquiry Form and List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .container {
            width: 80%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        h1 {
            text-align: center;
            color: #444;
        }
        .form-container, .search-container {
            margin-bottom: 20px;
        }
        .form-container form, .search-container form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .form-container input, .form-container textarea {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 100%;
            max-width: 500px;
            margin-bottom: 10px;
        }
        .form-container button, .search-container button {
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            border-radius: 4px;
            background: #007bff;
            color: #fff;
            cursor: pointer;
        }
        .form-container button:hover, .search-container button:hover {
            background: #0056b3;
        }
        .inquiry-container {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 8px;
            background: #fafafa;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .inquiry-photo {
            max-width: 200px;
            max-height: 200px;
            margin-top: 10px;
            display: block;
        }
        .inquiry-details {
            margin-top: 10px;
        }
    </style>
</head>
<body>

<div class="container">

    <!-- Inquiry Form -->
    <div class="form-container">
        <h1>Submit an Inquiry</h1>
        <form method="post" enctype="multipart/form-data">
            <input type="text" name="name" placeholder="Your Name" required>
            <input type="email" name="email" placeholder="Your Email" required>
            <textarea name="message" placeholder="Your Message" rows="4" required></textarea>
            <input type="file" name="photo">
            <button type="submit" name="submit_inquiry">Submit Inquiry</button>
        </form>
    </div>

    <!-- Search Form -->
    <div class="search-container">
        <form method="post">
            <input type="text" name="search" value="<?php echo htmlspecialchars($searchTerm); ?>" placeholder="Search by name, email, or message">
            <button type="submit">Search</button>
        </form>
    </div>

<h1>Submitted Inquiries</h1>

<?php if (empty($inquiries)): ?>
    <p>No inquiries found.</p>
<?php else: ?>
    <?php foreach ($inquiries as $inquiry): ?>
        <div class="inquiry-container">
            <p><strong>Name:</strong> <?php echo htmlspecialchars($inquiry['name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($inquiry['email']); ?></p>
            <p><strong>Message:</strong> <?php echo htmlspecialchars($inquiry['message']); ?></p>
            
            <?php if (!empty($inquiry['photo'])): ?>
                <p><strong>Photo:</strong></p>
                <img src="<?php echo htmlspecialchars($inquiry['photo']) . $sasToken; ?>" alt="Uploaded Photo" class="inquiry-photo">
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
</body>
</html>
