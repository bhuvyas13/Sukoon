<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>सुकून - Home</title>
    <style>
        /* Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #e8e4de;
            color: #4a4a4a;
        }

        .container {
            width: 90%;
            max-width: 1200px;
            text-align: center;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* Sukoon branding */
        .app-name {
            font-size: 2.5em;
            color: #4a4a4a;
            font-weight: bold;
            margin-bottom: 20px;
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

        /* Main layout */
        .main-content {
            display: flex;
            gap: 20px;
            width: 100%;
            justify-content: center;
            align-items: flex-start;
            margin-top: 20px;
        }

        /* Side cards layout with flex to push bottom cards to the end */
        .side-cards {
            display: flex;
            flex-direction: column;
            justify-content: space-between; /* Space out the cards */
            gap: 20px;
            width: 23%; /* Ensure it fits within the layout */
        }

        /* Video styling */
        .background-video {
            width: 100%;
            border-radius: 15px;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
            max-width: 450px;
        }

        /* Card styling */
        .card {
            background-color: #ffffff;
            border-radius: 15px;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            width: 100%; /* Ensures the card fits within its container */
            aspect-ratio: 1; /* Ensures square shape */
            font-size: 1.2em;
            font-weight: bold;
            padding: 20px;
            text-decoration: none;
            color: #4a4a4a;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.15);
        }

        /* Colors for each card to match the pastel palette */
        .wellness-card { background: #dbe2e7; }
        .affirmation-card { background: #d4e8e1; }
        .journal-card { background: #f7dcd4; }
        .grounding-card { background: #e7e0d8; }

        /* Rectangle styling for GIFs */
        .card-icon {
            width: 80%;
            height: 80%;
            border-radius: 10px;
            background-color: #ffffff;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            margin-bottom: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }

        .card-icon img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
    </style>
</head>
<body>

    <div class="container">
        <!-- Sukoon name in Hindi -->
        <div class="app-name">सुकून</div>

        <!-- Navigation -->
        <nav>
            <a href="home.php" class="active">Home</a>
            <a href="journal.php">Journal</a>
            <a href="therapy.php">Therapy</a>
            <a href="progress.php">Progress</a>
            <a href="profile.php">Profile</a>
        </nav>

        <!-- Main content layout -->
        <div class="main-content">
            <!-- Left side cards with Affirmation at the bottom -->
            <div class="side-cards">
                <a href="wellness_checkin.php" class="card wellness-card">
                    <div class="card-icon"><img src="gifs/gif1.gif" alt="Wellness Check-In GIF"></div>
                    <span>Daily Wellness Check-In</span>
                </a>
                <a href="affirmations.php" class="card affirmation-card">
                    <div class="card-icon"><img src="gifs/gif4.gif" alt="Affirmation GIF"></div>
                    <span>Manifestation Board</span>
                </a>
            </div>

            <!-- Center video -->
            <video class="background-video" autoplay loop muted playsinline>
                <source src="videos/vid1.mp4" type="video/mp4">
                Your browser does not support the video tag.
            </video>

            <!-- Right side cards with Grounding Techniques at the bottom -->
            <div class="side-cards">
                <a href="journal.php" class="card journal-card">
                    <div class="card-icon"><img src="gifs/gif3.gif" alt="Journal GIF"></div>
                    <span>Enter My Journal</span>
                </a>
                <a href="grounding_tech.html" class="card grounding-card">
                    <div class="card-icon" ><img src="gifs/gif5.gif" alt="Grounding Techniques GIF"></div>
                    <span>Grounding Techniques</span>
                </a>
            </div>
        </div>
    </div>

</body>
</html>
