<?php
/**
 * Ova skripta funkcioniše kao jednostavan javni API endpoint.
 * Njena jedina svrha je da preuzme sve dostupne usluge iz baze podataka
 * i vrati ih kao JSON objekat.
 * Poziva se putem `fetch` zahteva iz `public/script.js`.
 */

// Eksplicitno postavljamo `Content-Type` header na `application/json`.
// Ovo govori pregledaču (ili bilo kom drugom klijentu) da je telo odgovora JSON podatak.
header('Content-Type: application/json');
// Uključujemo pomoćnu datoteku za konekciju sa bazom.
require_once '../src/db.php';

// Koristimo `try...catch` blok za elegantno rukovanje potencijalnim greškama u bazi podataka.
try {
    // Izvršavamo jednostavan upit za odabir svih kolona iz `usluge` tabele.
    $stmt = $db->query("SELECT * FROM usluge");
    // Preuzimamo sve rezultate u asocijativni niz.
    $usluge = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // `json_encode()` uzima PHP niz ili objekat i pretvara ga u JSON formatiran string.
    // Ovaj string se zatim šalje kao telo odgovora.
    echo json_encode($usluge);
} catch (PDOException $e) {
    // Ako upit baze podataka ne uspe, šaljemo odgovarajući HTTP 500 Server Error statusni kod.
    http_response_code(500);
    // Takođe šaljemo JSON objekat greške, što može biti korisno za debagovanje na frontendu.
    echo json_encode([
        "error" => "Greška pri povezivanju sa bazom",
        "details" => $e->getMessage()
    ]);
}
?>
