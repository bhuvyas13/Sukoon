<?php
// Start session (if needed)
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

// Fetch therapists from the database
$query = "SELECT * FROM Therapists";
$result = $conn->query($query);

// Create an array to store therapist data
$therapists = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $therapists[] = $row;
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>सुकून - Therapy Booking</title>
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
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            flex-direction: column;
        }

        .container {
            width: 90%;
            max-width: 800px;
            text-align: center;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 15px;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
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

        /* Main content styling */
        h1 {
            font-size: 2rem;
            color: #4a4a4a;
            margin-bottom: 20px;
        }

        .search-section {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 20px;
        }

        .search-input, .specialization-dropdown, .book-session-btn {
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ddd;
            font-size: 1rem;
        }

        .book-session-btn {
            background-color: #588da8;
            color: white;
            cursor: pointer;
        }

        .book-session-btn:hover {
            background-color: #507a90;
        }

        .therapist-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
        }

        .therapist-card {
            background-color: #f9f1e7;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0px 2px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: all 0.3s ease;
        }

        .therapist-card:hover {
            transform: translateY(-10px);
            box-shadow: 0px 6px 20px rgba(0, 0, 0, 0.15);
        }

        .therapist-photo {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 10px;
        }

        .therapist-card h3 {
            font-size: 1.2rem;
            color: #4a4a4a;
        }

        .therapist-card p {
            color: #555;
            font-size: 0.9rem;
            margin: 5px 0;
        }

        .rating {
            font-size: 1.1rem;
            color: #FFD700;
            margin: 8px 0;
        }

        .book-session {
            padding: 8px 12px;
            margin: 5px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            background-color: #588da8;
            color: white;
        }

        .book-session:hover {
            background-color: #507a90;
        }

        .reviews {
            margin-top: 10px;
            text-align: left;
        }

        .review {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

    <!-- Sukoon branding -->
    <div class="app-name">सुकून</div>

    <!-- Navigation -->
    <nav>
        <a href="home.php">Home</a>
        <a href="journal.php">Journal</a>
        <a href="therapy.php" class="active">Therapy</a>
        <a href="progress.php">Progress</a>
        <a href="profile.php">Profile</a>
    </nav>

    <div class="container therapy-container">
        <h1>Therapy Booking</h1>
        <div class="search-section">
            <input type="text" id="searchInput" placeholder="Search Name" class="search-input">
            <select id="specializationDropdown" class="specialization-dropdown">
                <option value="">Select Specialization</option>
                <option value="counseling">Counseling</option>
                <option value="psychotherapy">Psychotherapy</option>
                <option value="life-coaching">Life Coaching</option>
                <option value="psychiatrist">Psychiatrist</option>
            </select>
            <button class="book-session-btn" onclick="searchTherapists()">Search</button>
        </div>
        
        <div class="therapist-cards" id="therapistCards">
            <?php foreach ($therapists as $therapist): ?>
                <div class="therapist-card">
                    <img src="<?php echo htmlspecialchars($therapist['img_url']); ?>" alt="<?php echo htmlspecialchars($therapist['name']); ?>" class="therapist-photo">
                    <h3><?php echo htmlspecialchars($therapist['name']); ?></h3>
                    <p>Specialization: <?php echo htmlspecialchars($therapist['specialization']); ?></p>
                    <div class="rating">
                        <?php echo str_repeat("⭐", floor($therapist['rating'])); ?>
                        <span>(<?php echo htmlspecialchars($therapist['rating']); ?>)</span>
                    </div>

                    <!-- Fetch and display reviews for each therapist -->
                    <div class="reviews">
                        <?php
                        // Fetch reviews for each therapist
                        $conn = new mysqli($servername, $username, $password, $dbname);
                        $reviews_query = "SELECT * FROM Reviews WHERE therapist_id = ?";
                        $reviews_stmt = $conn->prepare($reviews_query);
                        $reviews_stmt->bind_param("i", $therapist['therapist_id']);
                        $reviews_stmt->execute();
                        $reviews_result = $reviews_stmt->get_result();
                        
                        if ($reviews_result->num_rows > 0) {
                            while ($review = $reviews_result->fetch_assoc()) {
                                echo "<div class='review'>";
                                echo "<strong>" . htmlspecialchars($review['first_name']) . " " . htmlspecialchars($review['last_name']) . ":</strong>";
                                echo "<p>Rating: " . str_repeat("⭐", floor($review['rating'])) . " (" . $review['rating'] . ")</p>";
                                echo "<p>" . htmlspecialchars($review['comment']) . "</p>";
                                echo "</div>";
                            }
                        } else {
                            echo "<p>No reviews available for this therapist.</p>";
                        }
                        $conn->close();
                        ?>
                    </div>

                    <button class="book-session" onclick="alert('Booking session with <?php echo htmlspecialchars($therapist['name']); ?>')">Book Session</button>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        // JavaScript function for filtering therapists
        function searchTherapists() {
            const searchInput = document.getElementById("searchInput").value.toLowerCase().trim();
            const specialization = document.getElementById("specializationDropdown").value.toLowerCase().trim();
            const therapistCards = document.querySelectorAll(".therapist-card");

            therapistCards.forEach(card => {
                // Get therapist name and specialization from each card
                const name = card.querySelector("h3").textContent.toLowerCase().trim();
                const specializationText = card.querySelector("p").textContent.toLowerCase().replace("specialization: ", "").trim();

                // Check if the name matches the search input and specialization matches the dropdown
                const matchesName = searchInput === "" || name.includes(searchInput);
                const matchesSpecialization = specialization === "" || specializationText === specialization;

                // Display card if both conditions match, otherwise hide it
                if (matchesName && matchesSpecialization) {
                    card.style.display = "block";
                } else {
                    card.style.display = "none";
                }
            });
        }
    </script>
</body>
</html>
