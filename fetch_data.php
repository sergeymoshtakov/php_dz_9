<?php
$servername = "127.0.0.1";
$username = "root";
$password = "Moshtakov2005s";
$dbname = "dz_nine_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$table = $_POST['table'];
$orderColumn = $_POST['orderColumn'] ?? 'id';
$orderDirection = $_POST['orderDirection'] ?? 'ASC';
$countryFilter = json_decode($_POST['countryFilter']) ?? [];
$cityFilter = json_decode($_POST['cityFilter']) ?? [];
$recordsPerPage = $_POST['recordsPerPage'] ?? 10;
$currentPage = $_POST['currentPage'] ?? 1;

$offset = ($currentPage - 1) * $recordsPerPage;

$countryFilterQuery = '';
if (!empty($countryFilter)) {
    $countryFilterQuery = "AND country IN ('" . implode("','", $countryFilter) . "')";
}

$cityFilterQuery = '';
if (!empty($cityFilter)) {
    $cityFilterQuery = "AND city IN ('" . implode("','", $cityFilter) . "')";
}

$sql = "SELECT * FROM $table WHERE 1=1 $countryFilterQuery $cityFilterQuery ORDER BY $orderColumn $orderDirection LIMIT $offset, $recordsPerPage";
$result = $conn->query($sql);

$rows = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
}

echo json_encode($rows);

$conn->close();
