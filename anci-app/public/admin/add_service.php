<?php
session_start();
require_once '../../src/db.php';
require_once '../../src/auth.php';
require_once '../../src/session.php';

header('Content-Type: application/json');
requireLogin();

if (empty($_SESSION['is_admin'])) {
    echo json_encode(['success' => false, 'message' => 'Nema dozvolu.']);
    exit;
}

$naziv = trim($_POST['naziv'] ?? '');
$opis = trim($_POST['opis'] ?? '');
$cena = trim($_POST['cena'] ?? '');
$trajanje = trim($_POST['trajanje'] ?? '');

if ($naziv && $opis && is_numeric($cena) && is_numeric($trajanje)) {
    $stmt = $db->prepare("INSERT INTO usluge (naziv, opis, cena, trajanje) VALUES (:naziv, :opis, :cena, :trajanje)");
    $stmt->execute([
        'naziv' => $naziv,
        'opis' => $opis,
        'cena' => $cena,
        'trajanje' => $trajanje
    ]);

    // Re-render tbody
    ob_start();
    $usluge = $db->query("SELECT * FROM usluge ORDER BY naziv")->fetchAll();
    foreach ($usluge as $usluga) {
        echo "<tr data-id='{$usluga['id']}'>
            <td>" . htmlspecialchars($usluga['naziv']) . "</td>
            <td>" . htmlspecialchars($usluga['cena']) . " RSD</td>
            <td><button onclick='obrisiUslugu({$usluga['id']})'>Obriši</button></td>
        </tr>";
    }
    $html = ob_get_clean();

    echo json_encode(['success' => true, 'message' => 'Usluga dodata.', 'html' => $html]);
} else {
    echo json_encode(['success' => false, 'message' => 'Popunite sva polja ispravno.']);
}

