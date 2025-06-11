<?php
require_once '../src/db.php';

$isApiRequest = isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json');

if ($isApiRequest) {
    header('Content-Type: application/json');
    echo json_encode(["status" => "success", "message" => "Konekcija sa bazom je uspešna!"]);
} else {
    echo "✅ Konekcija sa bazom je uspešna!";
}
?>

