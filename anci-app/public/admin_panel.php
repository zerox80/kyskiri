<?php
// Moramo pokrenuti sesiju na svakoj stranici koja zahteva pristup podacima sesije.
session_start();
// Uključujemo sve potrebne pomoćne datoteke za konekciju sa bazom, upravljanje sesijom i autentifikaciju.
require_once '../src/db.php';
require_once '../src/session.php';
require_once '../src/auth.php';

// --- BEZBEDNOST ---
// Prvo, osiguravamo da je korisnik uopšte prijavljen.
requireLogin();

// Drugo, proveravamo da li prijavljeni korisnik ima administratorsku oznaku (admin flag).
// Ovo je ključni korak autorizacije za zaštitu admin panela.
if (empty($_SESSION['is_admin'])) {
    http_response_code(403); // Šaljemo "403 Forbidden" statusni kod.
    echo "Pristup odbijen.";
    exit; // Odmah prekidamo izvršavanje skripte.
}

// Preuzimamo sve rezervacije za prikaz. Vršimo JOIN sa `usluge` tabelom
// da bismo dobili čitljiv naziv usluge (`usluga_naziv`) za svaku rezervaciju.
// Rezultati su poređani po datumu u opadajućem redosledu (najnovije prvo).
$rezervacije = $db->query("SELECT r.*, u.naziv AS usluga_naziv FROM rezervacije r JOIN usluge u ON r.usluga_id = u.id ORDER BY datum DESC")->fetchAll();

// Preuzimamo sve usluge za prikaz u tabeli za upravljanje.
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
// Dodajemo event listener na 'submit' događaj forme za dodavanje usluge.
document.getElementById('form-dodaj-uslugu').addEventListener('submit', async (e) => {
  // `async (e) => { ... }` definiše "asinhronu funkciju". Ovo nam omogućava da koristimo `await`
  // ključnu reč, što čini asinhroni kod (poput mrežnih zahteva) mnogo lakšim za čitanje.

  // `e.preventDefault()` zaustavlja podrazumevano ponašanje pregledača za slanje forme,
  // a to je ponovno učitavanje stranice. Želimo da to obradimo pomoću našeg JavaScripta.
  e.preventDefault();
  const form = e.target;

  // `new FormData(form)` je moderan način da se lako prikupe svi podaci iz forme
  // u objekat koji se može poslati sa `fetch` zahtevom.
  const data = new FormData(form);

  // `await` pauzira izvršavanje ove funkcije dok se `fetch` Promise ne razreši.
  // Ovo je čistije od lančanog pozivanja `.then()`. Zahtev se šalje skripti add_service.
  const res = await fetch('/admin/add_service.php', {
    method: 'POST',
    body: data
  });

  // `await` ponovo pauzira dok se JSON telo odgovora ne parsira.
  const json = await res.json();
  // Prikazujemo poruku sa servera (npr. "Usluga dodata.").
  document.getElementById('add-message').textContent = json.message;

  // Ako JSON odgovor servera ukazuje na uspeh...
  if (json.success) {
    const tbody = document.getElementById('usluge-body');
    // ...ažuriramo ceo sadržaj tela tabele sa uslugama svežim HTML-om
    // poslatim sa servera. Ovo je efikasan način za ažuriranje prikaza bez ponovnog učitavanja stranice.
    tbody.innerHTML = json.html;
    form.reset(); // Brišemo polja forme za sledeći unos.
  }
});

/**
 * Briše uslugu kada administrator klikne na dugme "Obriši".
 * Ovo je asinhrona funkcija za čistu obradu mrežnog zahteva.
 * @param {number} id ID usluge koja se briše.
 */
async function obrisiUslugu(id) {
  // `confirm()` prikazuje jednostavan "OK/Cancel" dijalog pregledača. To je osnovni
  // ali efikasan način za sprečavanje slučajnog brisanja.
  if (!confirm('Da li ste sigurni da želite da obrišete uslugu?')) return;

  // `await fetch(...)` šalje zahtev i čeka na odgovor.
  // ID usluge se prosleđuje kao URL query parametar.
  const res = await fetch(`/admin/remove_service.php?id=${id}`, { method: 'GET' });
  const json = await res.json(); // Čekamo na JSON telo odgovora.

  if (json.success) {
    // Ako je brisanje uspešno, ažuriramo telo tabele novim HTML-om sa servera.
    document.getElementById('usluge-body').innerHTML = json.html;
  } else {
    // Ako je došlo do greške, prikazujemo je u jednostavnom alert dijalogu.
    alert(json.message);
  }
}
</script>

<?php include '../templates/footer.php'; ?>

