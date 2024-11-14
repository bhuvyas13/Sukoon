<?php
session_start(); // Start session

// Initialize the success message
$success_message = '';

// Database credentials
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "webApp";

// Create database connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

// Retrieve the logged-in user's ID from the session
$user_id = $_SESSION['user_id'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if all fields are set
    if (isset($_POST['entry_date'], $_POST['water_intake'], $_POST['sleep_hours'], $_POST['grateful_for'], $_POST['challenges'])) {
        // Get form data
        $entry_date = $_POST['entry_date'];
        $water_intake = $_POST['water_intake'];
        $sleep_hours = $_POST['sleep_hours'];
        $grateful_for = $_POST['grateful_for'];
        $challenges = $_POST['challenges'];

        // Insert wellness check-in session
        $session_stmt = $conn->prepare("INSERT INTO wellness_checkin_sessions (user_id, total_score, session_date) VALUES (?, 0, ?)");
        $session_stmt->bind_param("is", $user_id, $entry_date);
        $session_stmt->execute();
        $session_id = $session_stmt->insert_id; // Get session ID
        $session_stmt->close();

        // Insert journal entries
        $journal_stmt = $conn->prepare("INSERT INTO JournalEntries (session_id, grateful_for, challenges, entry_date, user_id) VALUES (?, ?, ?, ?, ?)");
        $journal_stmt->bind_param("isssi", $session_id, $grateful_for, $challenges, $entry_date, $user_id);
        $journal_stmt->execute();
        $journal_stmt->close();

        // Insert water balance
        $water_stmt = $conn->prepare("INSERT INTO WaterBalance (session_id, water_intake, entry_date, user_id) VALUES (?, ?, ?, ?)");
        $water_stmt->bind_param("iisi", $session_id, $water_intake, $entry_date, $user_id);
        $water_stmt->execute();
        $water_stmt->close();

        // Insert sleep hours
        $sleep_stmt = $conn->prepare("INSERT INTO SleepHours (session_id, sleep_hours, entry_date, user_id) VALUES (?, ?, ?, ?)");
        $sleep_stmt->bind_param("iisi", $session_id, $sleep_hours, $entry_date, $user_id);
        $sleep_stmt->execute();
        $sleep_stmt->close();

        // Success message
        $success_message = "Your journal has been saved successfully!";
    } else {
        // Error message if required fields are not set
        $success_message = "Please fill all the required fields.";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>सुकून - Journal</title>
    <style>
        /* CSS styles */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; background-color: #e8e4de; color: #4a4a4a; }
        .container { width: 90%; max-width: 800px; text-align: center; padding: 20px; display: flex; flex-direction: column; align-items: center; background-color: #ffffff; border-radius: 15px; box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1); }
        .app-name { font-size: 2.5em; color: #4a4a4a; font-weight: bold; margin-bottom: 20px; }
        nav { display: flex; justify-content: center; margin-bottom: 20px; font-size: 1.1em; color: #4a4a4a; }
        nav a { text-decoration: none; color: #4a4a4a; padding: 10px 15px; margin: 0 10px; position: relative; font-weight: 500; }
        nav a.active { color: #588da8; font-weight: bold; }
        nav a.active::after { content: ''; position: absolute; width: 100%; height: 2px; background: #588da8; bottom: -5px; left: 0; border-radius: 2px; }
        .section { background-color: #f9f1e7; border-radius: 10px; padding: 15px; margin: 10px 0; color: #4a4a4a; text-align: left; width: 100%; box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1); }
        .section h2 { font-size: 1.2em; margin-bottom: 10px; color: #4a4a4a; }
        .image-display { display: flex; gap: 5px; margin-top: 10px; }
        .btn { width: 100%; padding: 12px; background-color: #588da8; color: white; border: none; border-radius: 10px; font-size: 1.1em; font-weight: bold; cursor: pointer; margin-top: 20px; }
        .message { margin-top: 20px; color: green; font-size: 1em; text-align: center; }
    </style>
</head>
<body>

    <div class="container">
        <div class="app-name">सुकून</div>

        <nav>
            <a href="home.php">Home</a>
            <a href="journal.php" class="active">Journal</a>
            <a href="therapy.php">Therapy</a>
            <a href="progress.php">Progress</a>
            <a href="profile.php">Profile</a>
        </nav>

        <form method="POST">
            <div class="section">
                <h2>Water Balance (cups)</h2>
                <input type="number" name="water_intake" id="water_intake" required placeholder="Enter cups" min="0" oninput="displayImages('water_intake', 'water_images', 'glass.png');">
                <div id="water_images" class="image-display"></div>
            </div>

            <div class="section">
                <h2>Hours of Sleep</h2>
                <input type="number" name="sleep_hours" id="sleep_hours" required placeholder="Enter hours" min="0" oninput="displayImages('sleep_hours', 'sleep_images', 'moon.png');">
                <div id="sleep_images" class="image-display"></div>
            </div>

            <div class="section">
                <h2>Today I Am Grateful For</h2>
                <textarea name="grateful_for" id="grateful_for" required placeholder="What are you grateful for today?"></textarea>
            </div>
            <div class="section">
                <h2>What Was Challenging Today?</h2>
                <textarea name="challenges" id="challenges" required placeholder="Describe any challenges faced today."></textarea>
            </div>

            <div class="section">
                <label for="entry_date">Entry Date:</label>
                <input type="date" name="entry_date" id="entry_date" required />
            </div>

            <button class="btn" type="submit">Submit Journal</button>
        </form>

        <?php if ($success_message) { ?>
            <div class="message"><?php echo $success_message; ?></div>
        <?php } ?>
    </div>

    <script>
        function displayImages(inputId, containerId, imageFile) {
            const count = document.getElementById(inputId).value;
            const container = document.getElementById(containerId);
            container.innerHTML = "";
            for (let i = 0; i < count; i++) {
                const img = document.createElement("img");
                img.src = `images/${imageFile}`;
                img.alt = "Icon";
                img.style.width = "30px";
                img.style.height = "30px";
                container.appendChild(img);
            }
        }
    </script>

</body>
</html>
