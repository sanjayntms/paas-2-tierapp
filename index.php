<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inquiry Form</title>
    <style>
        body {
            background-color: #f4f4f9;
            font-family: 'Arial', sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .form-container {
            background-color: #fff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
        }

        .form-container h1 {
            text-align: center;
            font-size: 2rem;
            margin-bottom: 20px;
            color: #333;
        }

        .form-container form {
            display: flex;
            flex-direction: column;
        }

        .form-container label {
            margin-bottom: 8px;
            font-size: 1rem;
            color: #666;
        }

        .form-container input, .form-container textarea {
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 5px;
            border: 1px solid #ddd;
            font-size: 1rem;
            outline: none;
            transition: border-color 0.3s ease-in-out;
        }

        .form-container input:focus, .form-container textarea:focus {
            border-color: #007bff;
        }

        .form-container textarea {
            resize: vertical;
            height: 150px;
        }

        .form-container input[type="submit"] {
            background-color: #007bff;
            color: #fff;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            padding: 12px;
            transition: background-color 0.3s ease-in-out;
        }

        .form-container input[type="submit"]:hover {
            background-color: #0056b3;
        }

        /* Responsive Design */
        @media (max-width: 600px) {
            .form-container {
                padding: 30px;
            }

            .form-container h1 {
                font-size: 1.5rem;
            }

            .form-container input, .form-container textarea {
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h1>NTMS Azure Batch 2tierapp Setup - Inquiry Form</h1>
            <form action="submit_inquiry.php" method="post">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required placeholder="Enter your name">

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required placeholder="Enter your email">

                <label for="message">Message:</label>
                <textarea id="message" name="message" required placeholder="Enter your message"></textarea>

                <input type="submit" value="Submit">
            </form>
        </div>
    </div>
</body>
</html>
