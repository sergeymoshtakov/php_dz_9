<?php
require 'db.php';

$query = "SELECT DISTINCT Country FROM employees";
$stmt = $pdo->query($query);
$countries = $stmt->fetchAll(PDO::FETCH_COLUMN);

$query = "SELECT DISTINCT City FROM employees";
$stmt = $pdo->query($query);
$cities = $stmt->fetchAll(PDO::FETCH_COLUMN);

echo json_encode(['countries' => $countries, 'cities' => $cities]);