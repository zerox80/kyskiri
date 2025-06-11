<?php
session_start();
require_once '../src/session.php';
require_once '../src/db.php';

requireLogin(); 

$username = $_SESSION['username'];

$stmt = $db->prepare("
  SELECT r.id, r.telefon, r.datum, u.naziv, u.trajanje
  FROM rezervacije r
  JOIN usluge u ON r.usluga_id = u.id
  WHERE r.ime = :username
  ORDER BY r.datum DESC
");
$stmt->execute(['username' => $username]);
$rezervacije = $stmt->fetchAll(PDO::FETCH_ASSOC);

include '../templates/header.php';
?>

<h2>Moje rezervacije</h2>

<?php if (count($rezervacije) > 0): ?>
  <ul>
    <?php foreach ($rezervacije as $r): ?>
      <li>
        <strong><?= htmlspecialchars($r['naziv']) ?></strong> —
        <?= htmlspecialchars($r['datum']) ?> (<?= $r['trajanje'] ?> min)
        <br>📞 <?= htmlspecialchars($r['telefon']) ?>
      </li>
    <?php endforeach; ?>
  </ul>
<?php else: ?>
  <p>Nemate nijednu rezervaciju.</p>
<?php endif; ?>

<?php include '../templates/footer.php'; ?>

