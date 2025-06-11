<?php
/**
 * Ova stranica obrađuje proces prijave korisnika.
 * Prikazuje formu za prijavu i obrađuje poslate kredencijale.
 */

// Uključujemo datoteku sa logikom za autentifikaciju, koja sadrži funkciju loginUser().
require_once '../src/auth.php';

// `$_SERVER['REQUEST_METHOD']` sadrži metodu koja je korišćena za pristup stranici (npr. 'GET', 'POST').
// Želimo da obrađujemo logiku prijave samo ako je forma poslata koristeći POST metodu.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Preuzimamo i čistimo poslato korisničko ime i lozinku.
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Proveravamo da li polja nisu prazna, a zatim pozivamo funkciju `loginUser`.
    // Funkcija `loginUser` vraća `true` u slučaju uspeha i `false` u slučaju neuspeha.
    if ($username && $password && loginUser($username, $password)) {
        // Prilikom uspešne prijave, `loginUser` takođe pokreće sesiju.
        // Preusmeravamo korisnika na početnu stranicu.
        header("Location: /");
        exit;
    } else {
        // Ako prijava ne uspe, definišemo poruku o grešci koja će biti prikazana korisniku.
        $error = "Pogrešno korisničko ime ili lozinka.";
    }
}
?>

<?php
// Uključujemo zajedničko zaglavlje stranice. Zaglavlje pokreće sesiju i gradi navigaciju.
include '../templates/header.php';
?>
<h2>Prijava</h2>

<!-- Forma se šalje na istu stranicu (login.php) koristeći POST metodu. -->
<form method="POST">
    <input name="username" placeholder="Korisničko ime" required>
    <input name="password" type="password" placeholder="Lozinka" required>
    <button type="submit">Prijavi se</button>
    <?php
    // Ako je promenljiva `$error` definisana iznad (prilikom neuspešne prijave),
    // ovaj PHP blok će ispisati poruku o grešci direktno na stranicu.
    if (!empty($error)) echo "<p style='color:red;'>$error</p>";
    ?>
</form>

<?php
// Uključujemo zajedničko podnožje stranice.
include '../templates/footer.php';
?>

