<?php
$servername = "amic3db.chksgzqjwzak.us-east-2.rds.amazonaws.com:3306";
$username = "sonic4051";
$password = "keng4051";
$dbname = "amic3";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);

}else echo"Success";
$webURL="https://amic3server.ngrok.io/";

?>
