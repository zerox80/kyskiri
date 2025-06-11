<?php
session_start();
require_once '../src/db.php';
require_once '../src/session.php';
require_once '../src/auth.php';
requireLogin();

if (empty($_SESSION['is_admin'])) {
    http_response_code(403);
    echo "Pristup odbijen.";
    exit;
}

$rezervacije = $db->query("SELECT r.*, u.naziv AS usluga_naziv FROM rezervacije r JOIN usluge u ON r.usluga_id = u.id ORDER BY datum DESC")->fetchAll();
$usluge = $db->query("SELECT * FROM usluge ORDER BY naziv")->fetchAll();
?>

<?php include '../templates/header.php'; ?>

<h2>Admin Panel</h2>

<h3>Lista rezervacija</h3>
<table>
  <thead>
    <tr>
      <th>Korisnik</th>
      <th>Telefon</th>
      <th>Usluga</th>
      <th>Datum</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($rezervacije as $rez): ?>
      <tr>
        <td><?= htmlspecialchars($rez['ime']) ?></td>
        <td><?= htmlspecialchars($rez['telefon']) ?></td>
        <td><?= htmlspecialchars($rez['usluga_naziv']) ?></td>
        <td><?= htmlspecialchars($rez['datum']) ?></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<h3>Usluge</h3>
<table>
  <thead>
    <tr>
      <th>Naziv</th>
      <th>Cena</th>
      <th>Akcija</th>
    </tr>
  </thead>
  <tbody id="usluge-body">
    <?php foreach ($usluge as $usluga): ?>
      <tr data-id="<?= $usluga['id'] ?>">
        <td><?= htmlspecialchars($usluga['naziv']) ?></td>
        <td><?= htmlspecialchars($usluga['cena']) ?> RSD</td>
        <td>
          <button onclick="obrisiUslugu(<?= $usluga['id'] ?>)">Obriši</button>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<h3>Dodaj novu uslugu</h3>
<form id="form-dodaj-uslugu">
  <input name="naziv" placeholder="Naziv usluge" required>
  <input name="opis" placeholder="Opis usluge" required>
  <input name="cena" type="number" placeholder="Cena (RSD)" required>
  <input name="trajanje" type="number" placeholder="Trajanje (min)" required>
  <button type="submit">Dodaj</button>
</form>
<p id="add-message" style="color: green;"></p>

<script>
document.getElementById('form-dodaj-uslugu').addEventListener('submit', async (e) => {
  e.preventDefault();
  const form = e.target;
  const data = new FormData(form);

  const res = await fetch('/admin/add_service.php', {
    method: 'POST',
    body: data
  });

  const json = await res.json();
  document.getElementById('add-message').textContent = json.message;

  if (json.success) {
    const tbody = document.getElementById('usluge-body');
    tbody.innerHTML = json.html;
    form.reset();
  }
});

async function obrisiUslugu(id) {
  if (!confirm('Da li ste sigurni da želite da obrišete uslugu?')) return;

  const res = await fetch(`/admin/remove_service.php?id=${id}`, { method: 'GET' });
  const json = await res.json();

  if (json.success) {
    document.getElementById('usluge-body').innerHTML = json.html;
  } else {
    alert(json.message);
  }
}
</script>

<?php include '../templates/footer.php'; ?>

