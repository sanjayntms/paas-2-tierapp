<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Include the database connection
include 'db.php';

// Load Azure Storage SDK
require 'vendor/autoload.php';

use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Common\SharedAccessSignatureHelper;
use MicrosoftAzure\Storage\Common\Internal\Resources;

$connectionString = getenv('AZURE_STORAGE_CONNECTION_STRING');
$containerName = "inquiry-photos"; // Change to your container name

$blobClient = BlobRestProxy::createBlobService($connectionString);

// Function to generate a SAS URL for a blob
function generateBlobSasUrl($containerName, $blobName) {
    $connectionString = getenv('AZURE_STORAGE_CONNECTION_STRING');

    // Correctly parse the connection string
    $params = [];
    foreach (explode(';', $connectionString) as $part) {
        $kv = explode('=', $part, 2);
        if (count($kv) == 2) {
            $params[$kv[0]] = $kv[1];
        }
    }

    $accountName = $params['AccountName'];
    $accountKey = $params['AccountKey'];

    $helper = new SharedAccessSignatureHelper(
        $accountName,
        $accountKey
    );

    $sasToken = $helper->generateBlobServiceSharedAccessSignatureToken(
        Resources::RESOURCE_TYPE_BLOB,
        "$containerName/$blobName",
        'r', // Read permission
        (new \DateTime())->modify('+30 minutes'), // Expire in 30 min
        (new \DateTime())->modify('-5 minutes')  // Start time
    );

    return "https://$accountName.blob.core.windows.net/$containerName/$blobName?$sasToken";
}

// Handle search form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $searchQuery = $_POST['search_query'];

    try {
        $sql = "SELECT * FROM Inquiries WHERE name LIKE ? OR mobile_number LIKE ? OR email LIKE ? OR message LIKE ?";
        $stmt = $pdo->prepare($sql);
        $searchTerm = "%" . $searchQuery . "%";
        $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error fetching search results: " . $e->getMessage();
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Search Results</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0px 0px 10px 0px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .no-results {
            text-align: center;
            margin-top: 20px;
            color: #888;
        }
        .photo-link img {
            max-width: 150px;
            height: auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Search Results</h2>

        <?php if (isset($results) && count($results) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Mobile Number</th>
                        <th>Email</th>
                        <th>Message</th>
                        <th>Photo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results as $result): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($result['name']); ?></td>
                            <td><?php echo htmlspecialchars($result['mobile_number']); ?></td>
                            <td><?php echo htmlspecialchars($result['email']); ?></td>
                            <td><?php echo htmlspecialchars($result['message']); ?></td>
                            <td class="photo-link">
                                <?php
                                if (!empty($result['photo_url'])) {
                                    // Extract blob name from the URL
                                    $blobPath = parse_url($result['photo_url'], PHP_URL_PATH);
                                    $blobName = ltrim(str_replace("/$containerName/", "", $blobPath), "/");

                                    $sasUrl = generateBlobSasUrl($containerName, $blobName);
                                    ?>
                                    <a href="<?php echo htmlspecialchars($sasUrl); ?>" target="_blank">
                                        <img src="<?php echo htmlspecialchars($sasUrl); ?>" alt="Photo">
                                    </a>
                                <?php } else { ?>
                                    No Photo
                                <?php } ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no-results">No matching inquiries found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
