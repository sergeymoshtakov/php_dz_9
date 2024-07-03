<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $currentNumber = isset($_POST['number']) ? intval($_POST['number']) : 0;

    $newNumber = $currentNumber + 1;

    echo $newNumber;
}