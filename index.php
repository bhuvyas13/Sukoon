<?php
session_start(); // Start the session

// Enable CORS headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "webApp";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Process the request if it's a POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'] ?? '';

    if ($action === 'signup') {
        $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
        $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);

        $sql = "SELECT * FROM Users WHERE email='$email'";
        $result = mysqli_query($conn, $sql);

        if (!$result) {
            echo json_encode(['status' => 'error', 'message' => 'Database query error']);
            exit();
        }

        if (mysqli_num_rows($result) > 0) {
            echo json_encode(['status' => 'error', 'message' => 'Email already exists']);
        } else {
            $sql = "INSERT INTO Users (first_name, last_name, email) VALUES ('$first_name', '$last_name', '$email')";
            if (mysqli_query($conn, $sql)) {
                echo json_encode(['status' => 'success', 'message' => 'Registration successful']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to register user']);
            }
        }
    } elseif ($action === 'login') {
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $sql = "SELECT * FROM Users WHERE email='$email'";
        $result = mysqli_query($conn, $sql);

        if (!$result) {
            echo json_encode(['status' => 'error', 'message' => 'Database query error']);
            exit();
        }

        if (mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);
            $userId = $user['user_id']; // Assuming 'user_id' is the primary key in Users table
            $_SESSION['user_id'] = $userId; // Set the user_id in the session

            echo json_encode(['status' => 'success', 'message' => 'Login successful']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Email not found']);
        }
    } elseif ($action === 'logout') {
        session_unset();
        session_destroy();
        echo json_encode(['status' => 'success', 'message' => 'Logged out successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mimblu - Sign Up / Log In</title>
    <style>
        /* Basic reset and body styling */
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f2ede4;
        }

        /* Centered container for both forms */
        .container {
            text-align: center;
            max-width: 400px;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
        }

        .logo h1 {
            color: #588da8;
            font-size: 2.5em;
            margin-bottom: 20px;
        }

        form input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            font-size: 1em;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f7f7f7;
            box-sizing: border-box;
        }

        .terms {
            font-size: 0.8em;
            color: #666;
            margin: 10px 0;
        }

        .terms a {
            color: #588da8;
            text-decoration: none;
        }

        .btn.primary {
            background-color: #588da8;
            color: white;
            border: none;
            padding: 15px;
            font-size: 1em;
            border-radius: 5px;
            width: 100%;
            cursor: pointer;
            margin-top: 10px;
            transition: background-color 0.3s;
        }

        .btn.primary:hover {
            background-color: #4a7393;
        }

        .prompt {
            font-size: 0.9em;
            margin-top: 20px;
            color: #4a4a4a;
        }

        .prompt a {
            color: #588da8;
            text-decoration: none;
            cursor: pointer;
        }

        #loginPage {
            display: none;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="logo">
            <h1>सुकून</h1>
        </div>

        <div id="signupPage">
            <form id="signupForm">
                <input type="text" id="first_name" placeholder="First Name" required>
                <input type="text" id="last_name" placeholder="Last Name" required>
                <input type="email" id="email" placeholder="Email Address" required>
                <p class="terms">
                    By signing up you confirm you are over 18 and accept our <a href="#">Terms</a> & <a href="#">Privacy policy</a>
                </p>
                <button type="submit" class="btn primary">Sign Up</button>
            </form>
            <p class="prompt">Have an account already? <a onclick="showLoginPage()">Log In</a></p>
        </div>

        <div id="loginPage">
            <form id="loginForm">
                <input type="email" id="login_email" placeholder="Email Address" required>
                <button type="submit" class="btn primary">Log In</button>
            </form>
            <p class="prompt">Don't have an account? <a onclick="showSignupPage()">Sign Up</a></p>
        </div>

        <div class="logout-section" style="display: none;">
            <button class="btn primary" onclick="logoutUser()">Log Out</button>
        </div>
    </div>

    <script>
        function showLoginPage() {
            document.getElementById("signupPage").style.display = "none";
            document.getElementById("loginPage").style.display = "block";
        }

        function showSignupPage() {
            document.getElementById("loginPage").style.display = "none";
            document.getElementById("signupPage").style.display = "block";
        }

        document.getElementById("signupForm").addEventListener("submit", async (event) => {
            event.preventDefault();

            const firstName = document.getElementById("first_name").value.trim();
            const lastName = document.getElementById("last_name").value.trim();
            const email = document.getElementById("email").value.trim();

            try {
                const response = await fetch("index.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: `action=signup&first_name=${encodeURIComponent(firstName)}&last_name=${encodeURIComponent(lastName)}&email=${encodeURIComponent(email)}`
                });

                const result = await response.json();

                if (result.status === "success") {
                    alert("Sign-up successful!");
                    window.location.href = "home.php";
                } else {
                    alert(result.message || "An error occurred during signup. Please try again.");
                }
            } catch (error) {
                console.error("Signup error:", error);
                alert("An error occurred during signup. Please try again.");
            }
        });

        document.getElementById("loginForm").addEventListener("submit", async (event) => {
            event.preventDefault();

            const email = document.getElementById("login_email").value.trim();

            try {
                const response = await fetch("index.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: `action=login&email=${encodeURIComponent(email)}`
                });

                const result = await response.json();
                if (result.status === "success") {
                    alert("Login successful!");
                    document.querySelector(".logout-section").style.display = "block";
                    window.location.href = "home.php";
                } else {
                    alert(result.message || "An error occurred during login. Please try again.");
                }
            } catch (error) {
                console.error("Login error:", error);
                alert("An error occurred during login. Please try again.");
            }
        });

        async function logoutUser() {
            try {
                const response = await fetch("index.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: `action=logout`
                });

                const result = await response.json();
                if (result.status === "success") {
                    alert("Logged out successfully!");
                    window.location.href = "index.php";
                } else {
                    alert(result.message || "An error occurred during logout. Please try again.");
                }
            } catch (error) {
                console.error("Logout error:", error);
                alert("An error occurred during logout. Please try again.");
            }
        }
    </script>
</body>
</html>
