<?php
/**
 * Ovo je jednostavna dijagnostička skripta za testiranje konekcije sa bazom podataka.
 * Njena jedina svrha je da uključi `db.php` datoteku i prijavi da li se uspešno izvršila.
 * Ako `db.php` ne uspe (baci izuzetak), ova skripta neće biti dostignuta, a greška
 * iz `db.php` će biti prikazana umesto toga.
 *
 * NAPOMENA: U produkcionom okruženju, često je pametno ukloniti ili ograničiti pristup
 * ovakvim dijagnostičkim datotekama.
 */

// Pokušavamo da uključimo datoteku za konekciju sa bazom.
require_once '../src/db.php';

// Ova skripta pruža različit odgovor u zavisnosti od toga kako je zatražena.
// Ovo proverava da li zaglavlja zahteva ukazuju da je JSON odgovor poželjan
// (tipično za alate za testiranje API-ja ili JavaScript fetch zahteve).
$isApiRequest = isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json');

// Ako je zahtev sličan API zahtevu, odgovaramo sa JSON objektom.
if ($isApiRequest) {
    header('Content-Type: application/json');
    echo json_encode(["status" => "success", "message" => "Konekcija sa bazom je uspešna!"]);
} else {
    // Ako je direktan zahtev iz pregledača, odgovaramo sa jednostavnom HTML porukom.
    echo "✅ Konekcija sa bazom je uspešna!";
}
?>

