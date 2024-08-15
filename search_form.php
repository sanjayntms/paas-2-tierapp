<?php
// Include the database connection
include 'db.php';

if (isset($_GET['search'])) {
    $search = $_GET['search'];

    try {
        $sql = "SELECT * FROM Inquiries WHERE name LIKE ? OR mobile_number LIKE ? OR email LIKE ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['%' . $search . '%', '%' . $search . '%', '%' . $search . '%']);
        $results = $stmt->fetchAll();

        if ($results) {
            echo "<h3>Search Results:</h3>";
            echo "<table><tr><th>Name</th><th>Mobile</th><th>Email</th><th>Message</th><th>Photo</th></tr>";
            foreach ($results as $row) {
                echo "<tr>";
                echo "<td>{$row['name']}</td>";
                echo "<td>{$row['mobile_number']}</td>";
                echo "<td>{$row['email']}</td>";
                echo "<td>{$row['message']}</td>";
                echo "<td><img src='{$row['photo_url']}' width='100' height='100'></td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "No inquiries found.";
        }
    } catch (PDOException $e) {
        echo "Error fetching data: " . $e->getMessage();
    }
}
?>
