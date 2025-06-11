<?php
/**
 * Ova skripta obrađuje serversku logiku za dodavanje nove usluge.
 * Poziva se putem JavaScript `fetch` zahteva iz admin panela.
 * Očekuje POST podatke i vraća JSON odgovor.
 */

// Standardno podešavanje: Pokretanje sesije i uključivanje potrebnih pomoćnih datoteka.
session_start();
require_once '../../src/db.php';
require_once '../../src/auth.php';
require_once '../../src/session.php';

// Postavljamo tip sadržaja odgovora na JSON, jer naš JavaScript to očekuje.
header('Content-Type: application/json');

// --- BEZBEDNOST ---
// Proveravamo da li korisnik mora biti prijavljen i da li mora biti administrator.
requireLogin();
if (empty($_SESSION['is_admin'])) {
    // Ako nije administrator, šaljemo JSON grešku i zaustavljamo izvršavanje.
    echo json_encode(['success' => false, 'message' => 'Nema dozvolu.']);
    exit;
}

// Preuzimamo i čistimo POST podatke iz forme.
// `trim()` uklanja razmake sa početka i kraja stringa.
// Null coalescing operator `?? ''` pruža podrazumevanu praznu vrednost stringa ako POST promenljiva nije postavljena.
$naziv = trim($_POST['naziv'] ?? '');
$opis = trim($_POST['opis'] ?? '');
$cena = trim($_POST['cena'] ?? '');
$trajanje = trim($_POST['trajanje'] ?? '');

// Osnovna validacija da se osigura da su obavezna polja prisutna i da su numerička polja validni brojevi.
if ($naziv && $opis && is_numeric($cena) && is_numeric($trajanje)) {
    // Ako validacija prođe, pripremamo SQL INSERT izraz da sprečimo SQL injection.
    $stmt = $db->prepare("INSERT INTO usluge (naziv, opis, cena, trajanje) VALUES (:naziv, :opis, :cena, :trajanje)");
    // Izvršavamo izraz sa očišćenim podacima.
    $stmt->execute([
        'naziv' => $naziv,
        'opis' => $opis,
        'cena' => $cena,
        'trajanje' => $trajanje
    ]);

    // --- DINAMIČKO RE-RENDEROVANJE HTML-a ---
    // Ovo je pametna tehnika da se izbegne da klijentski JavaScript ponovo gradi HTML.
    // Server to radi umesto njega i šalje kompletan, ažuriran blok nazad.

    // `ob_start()` uključuje PHP-ov izlazni bafer (output buffering). Svaki izlaz iz `echo` biće sačuvan
    // u internom baferu umesto da se odmah pošalje pregledaču.
    ob_start();

    // Ponovo preuzimamo *celu* listu usluga, uključujući i onu koju smo upravo dodali.
    $usluge = $db->query("SELECT * FROM usluge ORDER BY naziv")->fetchAll();
    // Prolazimo kroz sveže podatke i ispisujemo HTML za svaki red tabele.
    // Ovaj izlaz se hvata u bafer.
    foreach ($usluge as $usluga) {
        echo "<tr data-id='{$usluga['id']}'>
            <td>" . htmlspecialchars($usluga['naziv']) . "</td>
            <td>" . htmlspecialchars($usluga['cena']) . " RSD</td>
            <td><button onclick='obrisiUslugu({$usluga['id']})'>Obriši</button></td>
        </tr>";
    }
    // `ob_get_clean()` preuzima kompletan sadržaj bafera kao string, a zatim čisti bafer.
    $html = ob_get_clean();

    // Šaljemo uspešan JSON odgovor, uključujući i novorenderovani HTML blok.
    echo json_encode(['success' => true, 'message' => 'Usluga dodata.', 'html' => $html]);
} else {
    // Ako validacija ne uspe, šaljemo JSON odgovor o grešci.
    echo json_encode(['success' => false, 'message' => 'Popunite sva polja ispravno.']);
}

