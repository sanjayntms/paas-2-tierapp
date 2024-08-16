<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Fetch the user from the database
    $sql = "SELECT * FROM Users WHERE username = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if user exists and password matches (plain text comparison)
    if ($user && $password === $user['password_hash']) {
        // Correct login, store username in session
        $_SESSION['username'] = $username;
        header("Location: inquiry_form.php");
        exit();
    } else {
        $error = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap');

        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, #6e8efb, #a777e3);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            position: relative;
        }

        .login-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.1);
            width: 350px;
            animation: fadeIn 1s ease-in-out;
            z-index: 2; /* Ensure form stays above background text */
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
            font-size: 24px;
        }

        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 12px 15px;
            margin: 8px 0;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        input[type="submit"] {
            width: 100%;
            padding: 12px;
            background-color: #6e8efb;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #5a73d9;
        }

        .error {
            color: red;
            text-align: center;
            margin-bottom: 10px;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Background Text Styling */
        .background-text {
            position: absolute;
            top: 10%; /* Move the text near the top */
            left: 50%;
            transform: translateX(-50%);
            font-size: 36px;
            color: rgba(255, 255, 255, 0.2);
            text-align: center;
            z-index: 1; /* Behind the form */
            user-select: none;
            pointer-events: none;
        }

        /* Media query for smaller screens */
        @media (max-width: 400px) {
            .login-container {
                width: 100%;
                margin: 0 20px;
            }

            .background-text {
                font-size: 28px; /* Adjust text size on smaller screens */
            }
        }
    </style>
</head>
<body>
    <!-- Background Text -->
    <div class="background-text">NTMS Azure batch PaaS based 2 tier Sample Application</div>

    <!-- Login Form -->
    <div class="login-container">
        <h2>Login</h2>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="submit" value="Login">
        </form>
    </div>
</body>
</html>
