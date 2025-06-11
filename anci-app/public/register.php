<?php
/**
 * Ova stranica obrađuje registraciju novih korisnika.
 * Prikazuje formu za registraciju i obrađuje njeno slanje.
 */

// Uključujemo datoteku sa logikom za autentifikaciju, koja sadrži funkciju registerUser().
require_once '../src/auth.php';

// Obrađujemo formu samo ako je poslata putem POST metode.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Preuzimamo i čistimo poslate podatke iz forme.
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm-pw'] ?? '';

    // --- LOGIKA VALIDACIJE ---
    // 1. Proveravamo da li su sva polja popunjena.
    if (!$username || !$password || !$confirmPassword) {
        $error = "Popunite sva polja.";
    // 2. Proveravamo da li se lozinka i potvrda lozinke poklapaju.
    } elseif ($password !== $confirmPassword) {
        $error = "Lozinke se ne poklapaju.";
    } else {
        // 3. Ako validacija prođe, pokušavamo da registrujemo korisnika.
        // Funkcija `registerUser` vraća true u slučaju uspeha i false u slučaju neuspeha
        // (glavni uzrok neuspeha bi bio duplikat korisničkog imena).
        if (registerUser($username, $password)) {
            // U slučaju uspeha, preusmeravamo korisnika na stranicu za prijavu.
            // Dodajemo `registered=1` parametar, koji bi se mogao koristiti za prikaz
            // poruke "Registracija uspešna!" na stranici za prijavu (iako to trenutno nije implementirano).
            header('Location: login.php?registered=1');
            exit;
        } else {
            // Ako `registerUser` ne uspe, skoro sigurno je zato što je korisničko ime zauzeto.
            $error = "Greška: korisničko ime je zauzeto.";
        }
    }
}
?>

<?php include '../templates/header.php'; ?>
<h2>Registracija</h2>
<!-- Forma se šalje na ovu istu stranicu (register.php) putem POST-a. -->
<form method="POST">
    <input name="username" placeholder="Korisničko ime" required>
    <input name="password" type="password" placeholder="Lozinka" required>
    <input name="confirm-pw" type="password" placeholder="Potvrdi lozinku" required>
    <button type="submit">Registruj se</button>
    <?php
    // Ako neka od provera validacije iznad nije uspela, promenljiva `$error` će biti postavljena,
    // i ovaj kod će prikazati odgovarajuću poruku o grešci na stranici.
    if (!empty($error)) echo "<p style='color:red;'>$error</p>";
    ?>
</form>
<?php include '../templates/footer.php'; ?>

