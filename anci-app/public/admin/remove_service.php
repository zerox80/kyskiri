<?php
/**
 * Ova skripta obrađuje serversku logiku za brisanje usluge.
 * Poziva se putem JavaScript `fetch` zahteva iz admin panela.
 * Očekuje ID usluge putem GET parametra i vraća JSON odgovor.
 */

// Standardno podešavanje: Pokretanje sesije i uključivanje potrebnih pomoćnih datoteka.
session_start();
require_once '../../src/db.php';
require_once '../../src/session.php';
require_once '../../src/auth.php';

// Postavljamo tip sadržaja odgovora na JSON.
header('Content-Type: application/json');

// --- BEZBEDNOST ---
// Proveravamo da li korisnik mora biti prijavljen i da li mora biti administrator.
requireLogin();
if (empty($_SESSION['is_admin'])) {
    echo json_encode(['success' => false, 'message' => 'Nema dozvolu.']);
    exit;
}

// Preuzimamo ID usluge iz URL-ovog query string-a (npr. /remove_service.php?id=5).
$id = $_GET['id'] ?? null;
// Osnovna validacija da se osigura da je ID prosleđen i da je broj.
if (!$id || !is_numeric($id)) {
    echo json_encode(['success' => false, 'message' => 'Neispravan ID.']);
    exit;
}

// Pripremamo DELETE izraz da sprečimo SQL injection.
$stmt = $db->prepare("DELETE FROM usluge WHERE id = :id");
// Izvršavamo izraz, vezujući validirani ID.
$stmt->execute(['id' => $id]);

// --- DINAMIČKO RE-RENDEROVANJE HTML-a ---
// Kao i u add_service.php, ponovo renderujemo celo telo tabele da bismo ga poslali nazad klijentu.

// Preuzimamo novu, manju listu usluga iz baze podataka.
$usluge = $db->query("SELECT * FROM usluge ORDER BY naziv")->fetchAll();

// Pokrećemo izlazni bafer.
ob_start();
// Prolazimo kroz preostale usluge i gradimo HTML redove tabele.
foreach ($usluge as $usluga) {
  echo "<tr data-id='{$usluga['id']}'>
          <td>" . htmlspecialchars($usluga['naziv']) . "</td>
          <td>" . htmlspecialchars($usluga['cena']) . " RSD</td>
          <td><button onclick=\"obrisiUslugu({$usluga['id']})\">Obriši</button></td>
        </tr>";
}
// Preuzimamo baferovani HTML sadržaj u promenljivu.
$html = ob_get_clean();

// Šaljemo uspešan JSON odgovor koji sadrži ažurirani HTML.
echo json_encode(['success' => true, 'message' => 'Usluga obrisana.', 'html' => $html]);

