<?php
session_start();
include 'db.php';
//<script>
//    window.open('https://malicious-website.com', '_blank');
//</script>



/* <script>
    var a = document.createElement('a');
    a.href = 'https://example.com/malicious-file.pdf';
    a.download = 'malicious-file.pdf';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
</script>
*/

//
// Fetch inquiries from the database (vulnerable to stored XSS)
$sql = "SELECT * FROM Inquiries";
$stmt = $pdo->query($sql);
$inquiries = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Inquiries</title>
    <style>
        /* Your CSS styles here */
    </style>
</head>
<body>
    <h2>Inquiries</h2>
    <table border="1">
        <tr>
            <th>Name</th>
            <th>Message</th>
        </tr>
        <?php foreach ($inquiries as $inquiry): ?>
            <tr>
                <td><?php echo $inquiry['name']; ?></td>
                <td><?php echo $inquiry['message']; ?></td> <!-- Vulnerable to XSS -->
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
