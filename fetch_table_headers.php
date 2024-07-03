<?php
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "dz_nine_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$table = $_GET['table'];
$sql = "DESCRIBE $table";
$result = $conn->query($sql);

$headers = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $headers[] = $row['Field'];
    }
}

echo json_encode($headers);

$conn->close();