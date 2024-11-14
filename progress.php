<?php
// Start session
session_start();

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

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Assuming user_id is stored in session
$user_id = $_SESSION['user_id'];

// Emotions mapped to each question number
$emotions = ["Sadness", "Anxiety", "Restfulness", "Motivation", "Mood"];

// Query to fetch average score for each of the five questions (1 to 5) across all sessions for the logged-in user
$query = "SELECT question_number, AVG(score) AS avg_score 
          FROM wellness_checkin_responses 
          WHERE user_id = ? AND question_number BETWEEN 1 AND 5
          GROUP BY question_number
          ORDER BY question_number ASC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$questions = [];
$scores = [];
while ($row = $result->fetch_assoc()) {
    // Map question number to its corresponding emotion
    $questions[] = $emotions[$row['question_number'] - 1];
    $scores[] = round($row['avg_score'], 2);
}

// Query to fetch total water intake for each unique date for the logged-in user
$water_query = "SELECT entry_date, SUM(water_intake) AS total_intake 
                FROM WaterBalance 
                WHERE user_id = ? 
                GROUP BY entry_date";
$water_stmt = $conn->prepare($water_query);
$water_stmt->bind_param("i", $user_id);
$water_stmt->execute();
$water_result = $water_stmt->get_result();

$water_dates = [];
$water_intakes = [];
while ($row = $water_result->fetch_assoc()) {
    $water_dates[] = $row['entry_date'];
    $water_intakes[] = (int)$row['total_intake'];
}

// Query to fetch total sleep hours for each unique date for the logged-in user
$sleep_query = "SELECT entry_date, SUM(sleep_hours) AS total_sleep 
                FROM SleepHours 
                WHERE user_id = ? 
                GROUP BY entry_date";
$sleep_stmt = $conn->prepare($sleep_query);
$sleep_stmt->bind_param("i", $user_id);
$sleep_stmt->execute();
$sleep_result = $sleep_stmt->get_result();

$sleep_dates = [];
$sleep_hours = [];
while ($row = $sleep_result->fetch_assoc()) {
    $sleep_dates[] = $row['entry_date'];
    $sleep_hours[] = (int)$row['total_sleep'];
}

// Close the database connections
$stmt->close();
$water_stmt->close();
$sleep_stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>सुकून - Wellness Check-In Scores</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Styling for layout and charts */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #e8e4de;
            color: #4a4a4a;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }

        .app-name {
            font-size: 2.5em;
            color: #4a4a4a;
            font-weight: bold;
            margin-bottom: 20px;
        }

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

        .container {
            width: 90%;
            max-width: 700px;
            text-align: center;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .chart-container {
            margin-top: 20px;
            max-height: 300px;
        }

        .chart-row {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            margin-top: 20px;
        }

        .chart-row .chart {
            flex: 1;
            max-width: 48%;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
        }

        h2 {
            font-size: 1.8em;
            margin-bottom: 20px;
            color: #4a4a4a;
        }

        canvas {
            width: 100%;
            height: 250px;
        }
    </style>
</head>
<body>

    <div class="app-name">सुकून</div>

    <nav>
        <a href="home.php">Home</a>
        <a href="journal.php">Journal</a>
        <a href="therapy.php">Therapy</a>
        <a href="progress.php" class="active">Progress</a>
        <a href="profile.php">Profile</a>
    </nav>

    <div class="container">
        <h2>Wellness Check-In Scores</h2>
        <div class="chart-container">
            <canvas id="wellnessChart"></canvas>
        </div>
    </div>

    <div class="container">
        <div class="chart-row">
            <div class="chart">
                <h2>Water Balance</h2>
                <canvas id="waterChart"></canvas>
            </div>
            <div class="chart">
                <h2>Sleep Hours</h2>
                <canvas id="sleepChart"></canvas>
            </div>
        </div>
    </div>

    <script>
    // Wellness Check-In Scores Chart
    const wellnessCtx = document.getElementById('wellnessChart').getContext('2d');
    const questions = <?php echo json_encode($questions); ?>;
    const scores = <?php echo json_encode($scores); ?>;
    
    if (questions.length > 0 && scores.length > 0) {
        new Chart(wellnessCtx, {
            type: 'bar',
            data: {
                labels: questions,
                datasets: [{
                    label: 'Average Score by Emotion',
                    data: scores,
                    backgroundColor: '#588da8',
                    borderColor: '#4a4a4a',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true, max: 5 }
                }
            }
        });
    } else {
        wellnessCtx.font = "16px Arial";
        wellnessCtx.fillText("No data available for Wellness Check-In Scores", 10, 50);
    }

    // Water Balance Line Chart
    const waterCtx = document.getElementById('waterChart').getContext('2d');
    const waterDates = <?php echo json_encode($water_dates); ?>;
    const waterIntakes = <?php echo json_encode($water_intakes); ?>;
    
    if (waterDates.length > 0 && waterIntakes.length > 0) {
        new Chart(waterCtx, {
            type: 'line',
            data: {
                labels: waterDates,
                datasets: [{
                    label: 'Water Intake (cups)',
                    data: waterIntakes,
                    backgroundColor: 'rgba(88, 141, 168, 0.2)',
                    borderColor: '#588da8',
                    borderWidth: 2,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    } else {
        waterCtx.font = "16px Arial";
        waterCtx.fillText("No data available for Water Balance", 10, 50);
    }

    // Sleep Hours Line Chart
    const sleepCtx = document.getElementById('sleepChart').getContext('2d');
    const sleepDates = <?php echo json_encode($sleep_dates); ?>;
    const sleepHours = <?php echo json_encode($sleep_hours); ?>;
    
    if (sleepDates.length > 0 && sleepHours.length > 0) {
        new Chart(sleepCtx, {
            type: 'line',
            data: {
                labels: sleepDates,
                datasets: [{
                    label: 'Sleep Hours',
                    data: sleepHours,
                    backgroundColor: 'rgba(88, 141, 168, 0.2)',
                    borderColor: '#4a4a4a',
                    borderWidth: 2,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    } else {
        sleepCtx.font = "16px Arial";
        sleepCtx.fillText("No data available for Sleep Hours", 10, 50);
    }
    </script>

</body>
</html>
