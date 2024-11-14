<?php
// Include the database connection file
$servername = "localhost";
$username = "root"; // Use your database username
$password = ""; // Use your database password
$dbname = "webApp"; // Use your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Start session to check user login status
session_start();

// Get the user ID from session (assuming user is logged in)
$user_id = $_SESSION['user_id'];

// Fetch user details from the database
$sql = "SELECT * FROM Users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Close the connection
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>सुकून - Profile</title>
    <style>
        /* Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background-color: #e8e4de;
            color: #4a4a4a;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
        }

        /* Sukoon branding */
        .app-name {
            font-size: 2.5em;
            color: #4a4a4a;
            font-weight: bold;
            margin-bottom: 20px;
        }

        /* Navigation styling */
        nav {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
            font-size: 1.1em;
            color: #4a4a4a;
        }

        nav a {
            text-decoration: none;
            color: #4a4a4a;
            padding: 10px 15px;
            margin: 0 10px;
            position: relative;
            font-weight: 500;
        }

        nav a.active {
            color: #588da8;
            font-weight: bold;
        }

        nav a.active::after {
            content: '';
            position: absolute;
            width: 100%;
            height: 2px;
            background: #588da8;
            bottom: -5px;
            left: 0;
            border-radius: 2px;
        }

        /* Main profile container */
        .profile-container {
            width: 90%;
            max-width: 800px;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        header {
            text-align: center;
            margin-bottom: 20px;
        }

        .mimblu-logo {
            font-size: 2em;
            color: #588da8;
        }

        h2 {
            margin-top: 10px;
            font-size: 1.5em;
            color: #4a4a4a;
        }

        .profile-info {
            margin-top: 20px;
        }

        .user-info p {
            font-size: 1.1em;
            margin: 10px 0;
        }

        .profile-actions a {
            display: block;
            font-size: 1.1em;
            color: #588da8;
            margin: 10px 0;
            text-decoration: none;
        }

        .profile-actions a:hover {
            text-decoration: underline;
        }

        .logout-button {
    background-color: #000000de;  /* Button background color */              /* Text color */
    padding: 10px 20px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    border-radius: 10px;
    margin-top: 20px;
    font-weight: bold;
    font-size: 1.1em;           /* Larger font size for better visibility */
    transition: background-color 0.3s ease, color 0.3s ease;
}

.logout-button:hover {
    background-color: #507a90;  /* Darker background on hover */
    color: #ffffff;             /* Ensure the text remains white on hover */
}

        footer {
            text-align: center;
            margin-top: 20px;
            font-size: 0.9em;
            color: #777;
        }
    </style>
</head>
<body>

    <!-- Sukoon branding -->
    <div class="app-name">सुकून</div>

    <!-- Navigation Bar -->
    <nav>
        <a href="home.php">Home</a>
        <a href="journal.php">Journal</a>
        <a href="therapy.php">Therapy</a>
        <a href="progress.php">Progress</a>
        <a href="profile.php" class="active">Profile</a>
    </nav>

    <div class="profile-container">
    

        <div class="profile-info">
            <div class="user-info">
                <p><strong>Name:</strong> <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            </div>

            <div class="profile-actions">
                <a href="notifications.php">Notifications</a>
                <a href="access_code.php">Access Code</a>
                <a href="manage_subscription.php">Manage Subscription</a>
                <a href="faqs.php">FAQs</a>
                <p>For customer support queries, you can reach out to us at <a href="mailto:care@sukoon.com">care@sukoon.com</a></p>
                <a href="logout.php" class="logout-button">Log Out</a>
            </div>
        </div>

        <footer>
            <p>&copy; 2024 sukoon</p>
        </footer>
    </div>

    <script>
        // Confirm logout before proceeding
        document.querySelector('.logout-button').addEventListener('click', function(event) {
            const confirmLogout = confirm('Are you sure you want to log out?');
            if (!confirmLogout) {
                event.preventDefault(); // Prevent the logout action if not confirmed
            }
        });
    </script>
</body>
</html>
