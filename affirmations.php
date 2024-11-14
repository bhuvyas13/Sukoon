<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>सुकून - Affirmation for the Day</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body, html {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            background-color: #f2ede4; /* Light beige background to match home page */
            color: #4a4a4a;
        }

        /* Sukoon branding */
        .app-name {
            font-size: 2.5em;
            color: #4a4a4a;
            font-weight: bold;
            margin: 20px 0 10px;
        }

        /* Navigation styling */
        nav {
            display: flex;
            justify-content: center;
            font-size: 1.1em;
            color: #4a4a4a;
            width: 100%;
            padding: 10px 0;
            margin-bottom: 20px;
        }

        nav a {
            text-decoration: none;
            color: inherit;
            padding: 10px 15px;
            margin: 0 5px; /* Reduce space between links */
            font-weight: 500;
            position: relative;
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

        .controls, .canvas-container, .buttons {
            margin-top: 20px;
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .canvas-container {
            position: relative;
            background-size: cover;
            background-position: center;
            border: 2px solid #d4c7b7; /* Border color matching the soft aesthetic */
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
            transition: width 0.3s, height 0.3s;
            border-radius: 10px;
        }

        .canvas-landscape {
            width: 90%;
            height: 70vh;
        }

        .canvas-portrait {
            width: 50%;
            height: 90vh;
        }

        .sticker {
            position: absolute;
            cursor: move;
            resize: both;
            overflow: hidden;
            min-width: 50px;
            min-height: 50px;
        }

        .resize-handle {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 15px;
            height: 15px;
            background-color: #4CAF50;
            cursor: se-resize;
        }

        .background-option, .sticker-option {
            cursor: pointer;
            border: 2px solid transparent;
            transition: border 0.3s;
            border-radius: 5px;
        }

        .background-option:hover, .sticker-option:hover {
            border-color: #588da8;
        }

        .btn {
            padding: 10px;
            background-color: #588da8;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 1em;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: #4a4a4a;
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

    <!-- Control area for backgrounds and stickers -->
    <div class="controls">
        <label>Choose Background:</label>
        <div id="backgroundChoices">
            <img src="images/background1.jpg" class="background-option" width="50" height="50" onclick="changeBackground('images/background1.jpg')">
            <img src="images/background2.jpg" class="background-option" width="50" height="50" onclick="changeBackground('images/background2.jpg')">
            <img src="images/background3.jpg" class="background-option" width="50" height="50" onclick="changeBackground('images/background3.jpg')">
        </div>
        
        <label>Choose Sticker:</label>
        <div id="stickerChoices">
            <img src="images/sticker1.png" class="sticker-option" width="50" height="50" onclick="addSticker('images/sticker1.png')">
            <img src="images/sticker2.png" class="sticker-option" width="50" height="50" onclick="addSticker('images/sticker2.png')">
            <img src="images/sticker3.png" class="sticker-option" width="50" height="50" onclick="addSticker('images/sticker3.png')">
        </div>
    </div>

    <!-- Orientation Toggle -->
    <div class="buttons">
        <button class="btn" onclick="setCanvasOrientation('landscape')">Landscape</button>
        <button class="btn" onclick="setCanvasOrientation('portrait')">Portrait</button>
    </div>

    <div class="canvas-container canvas-landscape" id="canvasContainer"></div>

    <div class="buttons">
        <button class="btn" onclick="saveDesign()">Save Design</button>
        <button class="btn" onclick="clearCanvas()">Clear Canvas</button>
    </div>

    <script>
        function changeBackground(imagePath) {
            const canvas = document.getElementById("canvasContainer");
            canvas.style.backgroundImage = `url(${imagePath})`;
        }

        function addSticker(imagePath) {
            const canvas = document.getElementById("canvasContainer");

            const sticker = document.createElement("div");
            sticker.classList.add("sticker");

            const img = document.createElement("img");
            img.src = imagePath;
            img.style.width = "100%";
            img.style.height = "100%";
            sticker.appendChild(img);

            const resizeHandle = document.createElement("div");
            resizeHandle.classList.add("resize-handle");
            sticker.appendChild(resizeHandle);

            enableDrag(sticker);
            enableResize(sticker, resizeHandle);

            sticker.addEventListener("dblclick", () => canvas.removeChild(sticker));

            canvas.appendChild(sticker);
        }

        function enableDrag(element) {
            let offsetX, offsetY;

            element.addEventListener("mousedown", (e) => {
                offsetX = e.clientX - element.getBoundingClientRect().left;
                offsetY = e.clientY - element.getBoundingClientRect().top;
                document.addEventListener("mousemove", moveSticker);
                document.addEventListener("mouseup", stopDragging);
            });

            function moveSticker(e) {
                element.style.left = `${e.clientX - offsetX}px`;
                element.style.top = `${e.clientY - offsetY}px`;
            }

            function stopDragging() {
                document.removeEventListener("mousemove", moveSticker);
                document.removeEventListener("mouseup", stopDragging);
            }
        }

        function enableResize(element, handle) {
            let originalWidth, originalHeight, originalMouseX, originalMouseY;

            handle.addEventListener("mousedown", (e) => {
                e.stopPropagation();

                originalWidth = parseFloat(getComputedStyle(element, null).getPropertyValue('width').replace('px', ''));
                originalHeight = parseFloat(getComputedStyle(element, null).getPropertyValue('height').replace('px', ''));
                originalMouseX = e.clientX;
                originalMouseY = e.clientY;

                document.addEventListener("mousemove", resizeSticker);
                document.addEventListener("mouseup", stopResizing);
            });

            function resizeSticker(e) {
                const width = originalWidth + (e.clientX - originalMouseX);
                const height = originalHeight + (e.clientY - originalMouseY);
                element.style.width = `${width}px`;
                element.style.height = `${height}px`;
            }

            function stopResizing() {
                document.removeEventListener("mousemove", resizeSticker);
                document.removeEventListener("mouseup", stopResizing);
            }
        }

        function setCanvasOrientation(orientation) {
            const canvas = document.getElementById("canvasContainer");
            canvas.classList.remove("canvas-landscape", "canvas-portrait");

            if (orientation === "landscape") {
                canvas.classList.add("canvas-landscape");
            } else {
                canvas.classList.add("canvas-portrait");
            }
        }

        function clearCanvas() {
            const canvas = document.getElementById("canvasContainer");
            canvas.innerHTML = "";
            canvas.style.backgroundImage = "";
        }

        function saveDesign() {
            alert("Saving the design as an image is under development. Use HTML2Canvas or similar libraries to implement it.");
        }
    </script>
</body>
</html>
