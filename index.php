<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Inquiry Form</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 50%;
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0px 0px 10px 0px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        input[type="text"], input[type="submit"], input[type="file"], textarea {
            padding: 10px;
            margin: 10px;
            width: 80%;
            font-size: 16px;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        textarea {
            resize: vertical;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Search Form -->
        <h2>Search Inquiries</h2>
        <form action="search_form.php" method="post">
            <input type="text" name="search_query" placeholder="Enter search term">
            <input type="submit" value="Search">
        </form>

        <!-- Inquiry Form -->
        <h2>Submit an Inquiry</h2>
        <form action="upload.php" method="post" enctype="multipart/form-data">
            <input type="text" name="name" placeholder="Your Name" required>
            <input type="text" name="mobile" placeholder="Mobile Number" required>
            <input type="email" name="email" placeholder="Your Email" required>
            <textarea name="message" placeholder="Your Message" rows="4" required></textarea>
            <input type="file" name="photo" required>
            <input type="submit" value="Submit Inquiry">
        </form>
    </div>
</body>
</html>
<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>
