<?php
try {
    // --- NAJBOLJA BEZBEDNOSNA PRAKSA ---
    // Umesto da kredencijale upisujemo direktno u kod, sada ih preuzimamo iz promenljivih okruženja (environment variables).
    // Ovo je ključno bezbednosno poboljšanje. Promenljive okruženja se konfigurišu na samom serveru
    // i nisu deo izvornog koda, što sprečava curenje lozinke baze podataka
    // ako kod ikada bude izložen.
    //
    // Za lokalni razvoj, obično biste koristili `.env` datoteku. Za produkcioni server,
    // konfigurisali biste ih na kontrolnoj tabli vašeg hosting provajdera (npr. Heroku, Vercel, AWS).

    // `getenv()` je PHP funkcija koja čita vrednost promenljive okruženja.
    // `?:` (Elvis operator) pruža rezervnu vrednost ako promenljiva okruženja nije postavljena.
    // Pružamo rezervne vrednosti za neosetljive podatke radi praktičnosti.
    $host = getenv('DB_HOST') ?: 'aws-0-eu-central-1.pooler.supabase.com';
    $port = getenv('DB_PORT') ?: '6543';
    $dbname = getenv('DB_NAME') ?: 'postgres';

    // Za stvarno korisničko ime i lozinku, NE pružamo rezervnu vrednost.
    // Ako oni nisu postavljeni na serveru, konekcija bi TREBALA da ne uspe.
    $user = getenv('DB_USER');
    $password = getenv('DB_PASS');

    // Provera da li su ključne promenljive zaista pronađene u okruženju.
    if (!$user || !$password) {
        // Ako kredencijali nisu postavljeni, "bacamo izuzetak" (throw an exception). Ovo zaustavlja izvršavanje
        // `try` bloka i odmah skače na `catch` blok ispod,
        // pružajući čistu poruku o grešci bez otkrivanja detalja konekcije.
        throw new Exception("Database credentials (DB_USER, DB_PASS) are not set in the environment variables.");
    }

    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;sslmode=require";
    $db = new PDO($dsn, $user, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException | Exception $e) { // Catch blok je ažuriran da hvata i naš prilagođeni Exception i PDOExceptions.
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        "error" => "Database connection failed",
        "details" => $e->getMessage()
    ]);
    exit;
}
?>

