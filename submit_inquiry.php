<?php
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];

    // Insert inquiry into database
    try {
        $sql = "INSERT INTO Inquiries (name, email, message) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$name, $email, $message]);

        echo "Thank you for your inquiry!";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
