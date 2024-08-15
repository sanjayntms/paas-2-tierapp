<!DOCTYPE html>
<html>
<head>
    <title>Inquiry Form</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .form-container {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 40px;
            max-width: 400px;
            width: 100%;
            margin: auto;
        }

        h2 {
            text-align: center;
            color: #333333;
            margin-bottom: 24px;
        }

        label {
            font-weight: bold;
            color: #555555;
        }

        input[type="text"], input[type="email"], textarea, input[type="file"] {
            width: 100%;
            padding: 12px;
            margin: 8px 0 16px 0;
            border-radius: 4px;
            border: 1px solid #cccccc;
            box-sizing: border-box;
        }

        input[type="submit"] {
            width: 100%;
            background-color: #007bff;
            color: white;
            padding: 14px 20px;
            margin: 16px 0;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Submit Your Inquiry</h2>
        <form action="upload.php" method="post" enctype="multipart/form-data">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>
            
            <label for="mobile">Mobile Number:</label>
            <input type="text" id="mobile" name="mobile" required>
            
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            
            <label for="message">Message:</label>
            <textarea id="message" name="message" required></textarea>
            
            <label for="photo">Photo:</label>
            <input type="file" id="photo" name="photo" accept="image/*" required>
            
            <input type="submit" value="Submit Inquiry">
        </form>
    </div>
</body>
</html>
