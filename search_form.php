<?php
// Include the database connection
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $searchQuery = $_POST['search_query'];

    try {
        // SQL query to search for inquiries based on the search query
        $sql = "SELECT * FROM Inquiries WHERE name LIKE ? OR mobile_number LIKE ? OR email LIKE ? OR message LIKE ?";
        $stmt = $pdo->prepare($sql);
        $searchTerm = "%" . $searchQuery . "%";
        $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $searchTerm]);

        // Fetch all matching records
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error fetching search results: " . $e->getMessage();
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
            max-width: 150px; /* Adjust as needed */
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
                                <a href="<?php echo htmlspecialchars($result['photo_url']); ?>" target="_blank">
                                    <img src="<?php echo htmlspecialchars($result['photo_url']); ?>" alt="Photo">
                                </a>
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
