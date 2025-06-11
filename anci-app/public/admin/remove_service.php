<?php
session_start();
require_once '../../src/db.php';
require_once '../../src/session.php';
require_once '../../src/auth.php';

header('Content-Type: application/json');
requireLogin();

if (empty($_SESSION['is_admin'])) {
    echo json_encode(['success' => false, 'message' => 'Nema dozvolu.']);
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    echo json_encode(['success' => false, 'message' => 'Neispravan ID.']);
    exit;
}

$stmt = $db->prepare("DELETE FROM usluge WHERE id = :id");
$stmt->execute(['id' => $id]);

$usluge = $db->query("SELECT * FROM usluge ORDER BY naziv")->fetchAll();

ob_start();
foreach ($usluge as $usluga) {
  echo "<tr data-id='{$usluga['id']}'>
          <td>" . htmlspecialchars($usluga['naziv']) . "</td>
          <td>" . htmlspecialchars($usluga['cena']) . " RSD</td>
          <td><button onclick=\"obrisiUslugu({$usluga['id']})\">Obriši</button></td>
        </tr>";
}
$html = ob_get_clean();

echo json_encode(['success' => true, 'message' => 'Usluga obrisana.', 'html' => $html]);

