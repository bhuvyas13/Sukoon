<?php
// Start the session
session_start();

// Destroy the session to log the user out
session_unset();  // Unset all session variables
session_destroy();  // Destroy the session

// Redirect to the sign-up page (index.php)
header("Location: index.php"); // Replace with the path to your sign-up page if necessary
exit();
?>
