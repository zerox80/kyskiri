<?php
/**
 * Ova stranica prikazuje listu svih rezervacija koje je napravio trenutno prijavljeni korisnik.
 * To je zaštićena stranica koja zahteva da korisnik bude prijavljen.
 */

// Pokrećemo sesiju da bismo pristupili podacima prijavljenog korisnika.
session_start();
// Uključujemo pomoćne datoteke za upravljanje sesijom i konekciju sa bazom.
require_once '../src/session.php';
require_once '../src/db.php';

// --- BEZBEDNOST ---
// Koristimo funkciju requireLogin() da osiguramo da samo prijavljeni korisnici mogu videti ovu stranicu.
// Ako nije prijavljen, korisnik će biti preusmeren na login.php.
requireLogin();

// Preuzimamo korisničko ime prijavljenog korisnika iz sesije.
$username = $_SESSION['username'];

// --- UPIT BAZI PODATAKA ---
// Pripremamo SQL izraz za odabir svih rezervacija koje pripadaju trenutnom korisniku.
// Koristimo JOIN da bismo dobili i detalje o usluzi (naziv, trajanje) iz `usluge` tabele.
// `WHERE r.ime = :username` klauzula je ključni deo koji filtrira samo za ovog specifičnog korisnika.
$stmt = $db->prepare("
  SELECT r.id, r.telefon, r.datum, u.naziv, u.trajanje
  FROM rezervacije r
  JOIN usluge u ON r.usluga_id = u.id
  WHERE r.ime = :username
  ORDER BY r.datum DESC
");
// Izvršavamo pripremljeni izraz, bezbedno vezujući korisničko ime za čuvar mesta.
$stmt->execute(['username' => $username]);
// Preuzimamo sve odgovarajuće rezervacije u niz.
$rezervacije = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Uključujemo standardno zaglavlje stranice.
include '../templates/header.php';
?>

<h2>Moje rezervacije</h2>

<?php
// Proveravamo da li niz `$rezervacije` ima elemenata.
if (count($rezervacije) > 0): ?>
  <ul>
    <?php
    // Ako ima rezervacija, prolazimo kroz svaku...
    foreach ($rezervacije as $r): ?>
      <li>
        <!-- ...i prikazujemo njene detalje. -->
        <!-- `htmlspecialchars` se koristi na svim podacima koje je uneo korisnik radi bezbednosti. -->
        <strong><?= htmlspecialchars($r['naziv']) ?></strong> —
        <?= htmlspecialchars($r['datum']) ?> (<?= $r['trajanje'] ?> min)
        <br>📞 <?= htmlspecialchars($r['telefon']) ?>
      </li>
    <?php endforeach; ?>
  </ul>
<?php else: ?>
  <!-- Ako je niz `$rezervacije` prazan, prikazujemo prijateljsku poruku. -->
  <p>Nemate nijednu rezervaciju.</p>
<?php endif; ?>

<?php
// Uključujemo standardno podnožje stranice.
include '../templates/footer.php';
?>

