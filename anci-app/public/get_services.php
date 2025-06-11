<?php
header('Content-Type: application/json');
require_once '../src/db.php';

try {
    $stmt = $db->query("SELECT * FROM usluge");
    $usluge = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($usluge);
} catch (PDOException $e) {
    http_response_code(500); 
    echo json_encode([
        "error" => "Greška pri povezivanju sa bazom",
        "details" => $e->getMessage()
    ]);
}
?>
