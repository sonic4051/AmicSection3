<?php
$servername = "amic3server.ngrok.io";
$username = "root";
$password = "keng4051";
$dbname = "amic3";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$webURL="https://amic3server.ngrok.io/";

?>
