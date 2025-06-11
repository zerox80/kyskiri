<?php
/**
 * Ova skripta obrađuje slanje forme za rezervaciju sa stranice index.php.
 * Validira ulazne podatke, unosi rezervaciju u bazu podataka,
 * a zatim preusmerava korisnika nazad na početnu stranicu sa porukom o uspehu ili grešci.
 */

// Sesija mora biti pokrenuta da bi se pristupilo informacijama prijavljenog korisnika.
session_start();
// Uključujemo pomoćnu datoteku za konekciju sa bazom.
require_once '../src/db.php';

// --- PREUZIMANJE ULAZNIH PODATAKA ---
// Preuzimamo ime trenutno prijavljenog korisnika iz sesije.
$username = $_SESSION['username'] ?? null;
// Preuzimamo podatke iz forme iz `$_POST` superglobalnog niza.
// `trim()` uklanja sve slučajne vodeće/prateće razmake.
// Null coalescing operator `?? ''` osigurava da imamo podrazumevanu vrednost ako POST promenljiva nije postavljena.
$telefon = trim($_POST['telefon'] ?? '');
$uslugaId = $_POST['usluga'] ?? '';
$datum = $_POST['datum'] ?? '';

// --- VALIDACIJA ---
// Jednostavna, ali ključna provera da se osigura da su sva obavezna polja popunjena.
if (!$username || !$telefon || !$uslugaId || !$datum) {
    // Ako neko polje nedostaje, preusmeravamo korisnika nazad na početnu stranicu.
    // Dodajemo `greska=1` query parametar u URL, koji početna stranica
    // može koristiti za prikaz generičke poruke o grešci.
    header("Location: /index.php?greska=1");
    exit; // Zaustavljamo izvršavanje skripte.
}

// --- INTERAKCIJA SA BAZOM PODATAKA ---
// Koristimo `try...catch` blok za elegantno rukovanje potencijalnim greškama u bazi podataka.
try {
    // Pripremamo SQL INSERT izraz sa imenovanim čuvarima mesta da sprečimo SQL injection.
    $stmt = $db->prepare("INSERT INTO rezervacije (ime, telefon, usluga_id, datum) VALUES (:ime, :telefon, :usluga, :datum)");
    // Izvršavamo izraz, vezujući validirane podatke za čuvare mesta.
    $stmt->execute([
        'ime' => $username,
        'telefon' => $telefon,
        'usluga' => $uslugaId,
        'datum' => $datum
    ]);
    // Ako je unos uspešan, preusmeravamo na početnu stranicu sa parametrom za uspeh.
    header("Location: /index.php?uspesno=1");
    exit;
} catch (PDOException $e) {
    // Ako `try` blok ne uspe (npr. problem sa konekcijom baze), ovaj `catch` blok se izvršava.
    // Preusmeravamo na početnu stranicu sa generičkim parametrom za grešku.
    // U produkcionoj aplikaciji, takođe bismo zabeležili specifičnu grešku iz `$e->getMessage()` za debagovanje.
    header("Location: /index.php?greska=1");
    exit;
}

