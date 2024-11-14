<?php
session_start();

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection
$servername = "localhost";
$username = "root";
$password = ""; // Replace with your MySQL password
$dbname = "webApp";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = json_decode(file_get_contents("php://input"), true);

    // Ensure user_id is available from session
    $user_id = $_SESSION['user_id'] ?? null;

    if ($user_id && isset($data['responses'])) {
        $responses = $data['responses'];

        // Insert a new session record and get the session_id
        $session_stmt = $conn->prepare("INSERT INTO wellness_checkin_sessions (user_id, total_score, session_date) VALUES (?, 0, NOW())");
        $session_stmt->bind_param("i", $user_id);
        if (!$session_stmt->execute()) {
            echo json_encode(['status' => 'error', 'message' => 'Error inserting session: ' . $session_stmt->error]);
            exit();
        }
        $session_id = $session_stmt->insert_id;
        $session_stmt->close();

        // Prepare SQL statement for individual responses
        $response_stmt = $conn->prepare("INSERT INTO wellness_checkin_responses (user_id, session_id, question_number, answer, score, response_date) VALUES (?, ?, ?, ?, ?, NOW())");
        if (!$response_stmt) {
            echo json_encode(['status' => 'error', 'message' => 'Database prepare error: ' . $conn->error]);
            exit();
        }

        $total_score = 0;

        foreach ($responses as $response) {
            $question_number = $response['question_number'];
            $answer = $response['answer'];
            $score = $response['score'];
            $total_score += $score;

            $response_stmt->bind_param("iiisi", $user_id, $session_id, $question_number, $answer, $score);
            if (!$response_stmt->execute()) {
                echo json_encode(['status' => 'error', 'message' => 'Database execute error: ' . $response_stmt->error]);
                exit();
            }
        }
        $response_stmt->close();

        // Update the total score for this session
        $update_stmt = $conn->prepare("UPDATE wellness_checkin_sessions SET total_score = ? WHERE session_id = ?");
        $update_stmt->bind_param("ii", $total_score, $session_id);
        $update_stmt->execute();
        $update_stmt->close();

        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid data format or user not logged in']);
    }
    $conn->close();
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>सुकून - Daily Wellness Check-In</title>
    <style>
        /* Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background-color: #f2ede4; /* Light beige background for aesthetic */
            color: #4a4a4a;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            min-height: 100vh;
            margin: 0;
            padding-top: 20px; /* Add padding to the top */
        }

        /* Sukoon branding */
        .app-name {
            font-size: 2.5em;
            color: #4a4a4a;
            font-weight: bold;
            margin-bottom: 10px;
            font-family: 'Arial', sans-serif;
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

        /* Content container */
        .container {
            width: 90%;
            max-width: 600px;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .question {
            font-size: 1.2em;
            margin-bottom: 20px;
            color: #4a4a4a;
        }

        .options {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .options label {
            display: flex;
            align-items: center;
            background-color: #dbe2e7; /* Muted blue-gray background for options */
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            color: #4a4a4a;
        }

        .options input {
            margin-right: 10px;
        }

        .progress-bar {
            width: 100%;
            height: 5px;
            background-color: #ddd;
            margin-top: 20px;
            position: relative;
        }

        .progress-bar span {
            display: block;
            height: 100%;
            background-color: #588da8;
            width: 0;
        }

        .navigation {
            margin-top: 20px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            background-color: #588da8;
            color: #fff;
            font-size: 1em;
            text-decoration: none;
            width: 100%;
            margin-top: 10px;
        }

        .btn:disabled {
            background-color: #bbb;
            cursor: not-allowed;
        }
    </style>
  
</head>
<body>
    <!-- Sukoon title -->
    <div class="app-name">सुकून</div>

    <!-- Navigation -->
    <nav>
        <a href="home.php"class="active">Home</a>
        <a href="journal.php">Journal</a>
        <a href="therapy.php">Therapy</a>
        <a href="progress.php">Progress</a>
        <a href="profile.php">Profile</a>
    </nav>

    <!-- Question Container -->
    <div class="container" id="questionContainer">
        <!-- Progress bar -->
        <div class="progress-bar"><span id="progress"></span></div>

        <!-- Question text -->
        <div class="question" id="questionText"></div>

        <!-- Options (answers) -->
        <div class="options" id="optionsContainer"></div>

        <!-- Navigation -->
        <div class="navigation">
            <button class="btn" id="nextButton" disabled>Next</button>
        </div>
    </div>

    <script>
        const questions = [
            {
                question: "How often have you felt down or uninterested in things you usually enjoy?",
                options: ["Not at all", "Rarely", "Sometimes", "Often", "Constantly"],
                weight: [0, 1, 2, 3, 4]
            },
            {
                question: "How often have you felt worried or on edge?",
                options: ["Not at all", "Rarely", "Sometimes", "Often", "Constantly"],
                weight: [0, 1, 2, 3, 4]
            },
            {
                question: "How well did you sleep last night?",
                options: ["Very well", "Fairly well", "So-so", "Not very well", "Very poorly"],
                weight: [4, 3, 2, 1, 0]
            },
            {
                question: "How's your energy and motivation today?",
                options: ["Very high", "High", "Okay", "Low", "Very low"],
                weight: [4, 3, 2, 1, 0]
            },
            {
                question: "How would you describe your overall mood today?",
                options: ["Very high", "High", "Okay", "Low", "Very low"],
                weight: [4, 3, 2, 1, 0]
            }
        ];

        let currentQuestion = 0;
        let responses = [];

        const questionText = document.getElementById("questionText");
        const optionsContainer = document.getElementById("optionsContainer");
        const progress = document.getElementById("progress");
        const nextButton = document.getElementById("nextButton");

        function loadQuestion() {
            const question = questions[currentQuestion];
            questionText.innerText = `${currentQuestion + 1}/${questions.length} ${question.question}`;
            optionsContainer.innerHTML = "";

            question.options.forEach((option, index) => {
                const label = document.createElement("label");
                const input = document.createElement("input");
                input.type = "radio";
                input.name = "option";
                input.value = question.weight[index];
                input.addEventListener("change", () => {
                    nextButton.disabled = false;
                });
                label.appendChild(input);
                label.appendChild(document.createTextNode(` ${option}`));
                optionsContainer.appendChild(label);
            });

            progress.style.width = `${((currentQuestion + 1) / questions.length) * 100}%`;
            nextButton.disabled = true;
        }

        nextButton.addEventListener("click", () => {
            const selectedOption = document.querySelector('input[name="option"]:checked');
            if (selectedOption) {
                const answer = selectedOption.nextSibling.textContent.trim();
                const score = parseInt(selectedOption.value);
                responses.push({ question_number: currentQuestion + 1, answer, score });
            }

            currentQuestion++;
            if (currentQuestion < questions.length) {
                loadQuestion();
            } else {
                submitResponses();
            }
        });

        function submitResponses() {
            fetch("wellness_checkin.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ responses: responses })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === "success") {
                    showScore();
                } else {
                    alert("Error saving responses: " + data.message);
                }
            })
            .catch(error => console.error("Error:", error));
        }

        function showScore() {
            const score = responses.reduce((acc, cur) => acc + cur.score, 0);
            questionContainer.innerHTML = `
                <h2>Your Wellness Score</h2>
                <div style="font-size: 3em; margin: 20px;">${score}/${questions.length * 4} Points</div>
                <a href="therapy.php" class="btn">Improve Your Score</a>
            `;
        }

        loadQuestion();
    </script>
</body>
</html>
