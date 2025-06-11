<?php
/**
 * Ova datoteka sadrži ključne funkcije za autentifikaciju u aplikaciji:
 * - registerUser(): Za kreiranje novog korisničkog naloga.
 * - loginUser(): Za proveru korisničkih kredencijala i pokretanje sesije.
 */

// `require_once` osigurava da je datoteka za konekciju sa bazom uključena samo jednom.
// `__DIR__` je "magična konstanta" koja daje putanju do direktorijuma trenutne datoteke (src),
// čineći referencu na putanju pouzdanom.
require_once __DIR__ . '/db.php';

/**
 * Registruje novog korisnika u bazi podataka.
 *
 * @param string $username Željeno korisničko ime.
 * @param string $password Korisnička lozinka u čistom tekstu.
 * @return bool True u slučaju uspešne registracije, false u slučaju neuspeha (npr. korisničko ime je zauzeto).
 */
function registerUser($username, $password) {
    // `global $db;` uvozi objekat konekcije sa bazom `$db` (iz db.php)
    // u lokalni opseg ove funkcije kako bismo mogli da ga koristimo.
    global $db;

    // --- KLJUČNA BEZBEDNOST ---
    // Hešira (hash) korisničku lozinku koristeći snažni BCRYPT algoritam.
    // `password_hash` kreira siguran, jednosmerni heš. Ne možete ga obrnuti da biste dobili originalnu lozinku.
    // Ovo je ispravan način za čuvanje lozinki. Nikada ih nemojte čuvati u čistom tekstu.
    $hashed = password_hash($password, PASSWORD_BCRYPT);

    // Priprema SQL izraz kako bi se sprečili SQL injection napadi.
    // `:username` i `:password` su čuvari mesta (placeholders) za podatke koje ćemo uneti.
    $stmt = $db->prepare("INSERT INTO korisnici (username, password) VALUES (:username, :password)");

    // Izvršava pripremljeni izraz, prosleđujući stvarne podatke u nizu.
    // PDO bezbedno vezuje vrednosti za čuvare mesta, tretirajući ih kao podatke, a ne kao kod.
    // Čuvamo sigurnu `$hashed` lozinku, a ne originalnu.
    return $stmt->execute(['username' => $username, 'password' => $hashed]);
}

/**
 * Pokušava da prijavi korisnika proverom njegovih kredencijala.
 * Ako je uspešno, pokreće sesiju i čuva informacije o korisniku.
 *
 * @param string $username Korisničko ime koje je korisnik uneo.
 * @param string $password Lozinka u čistom tekstu koju je korisnik uneo.
 * @return bool True u slučaju uspešne prijave, inače false.
 */
function loginUser($username, $password) {
    global $db;

    // Priprema izraz za odabir korisničkog zapisa koji odgovara datom korisničkom imenu.
    $stmt = $db->prepare("SELECT * FROM korisnici WHERE username = :username");
    $stmt->execute(['username' => $username]);

    // Preuzima korisničke podatke kao asocijativni niz (npr. $user['password']).
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // --- KLJUČNA BEZBEDNOST ---
    // Ovo je provera u dva koraka za validnu prijavu:
    // 1. `$user`: Da li je `fetch()` poziv iznad uopšte pronašao korisnika u bazi?
    // 2. `password_verify($password, $user['password'])`: Ako smo pronašli korisnika, ova funkcija
    //    bezbedno upoređuje lozinku u čistom tekstu iz forme sa heširanom lozinkom
    //    iz baze podataka. Vraća true ako se poklapaju.
    if ($user && password_verify($password, $user['password'])) {
        // Ako su kredencijali ispravni, započinje novu sesiju (ili nastavlja postojeću).
        session_start();
        // Čuva informacije o korisniku u `$_SESSION` superglobalnom nizu.
        // Ovi podaci će opstati na različitim stranicama dok se korisnik kreće po sajtu.
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['is_admin'] = $user['is_admin'];
        return true; // Označava da je prijava bila uspešna.
    }

    return false; // Označava da prijava nije uspela (pogrešno korisničko ime ili lozinka).
}
?>

