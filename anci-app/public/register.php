<?php
require_once '../src/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm-pw'] ?? '';

    if (!$username || !$password || !$confirmPassword) {
        $error = "Popunite sva polja.";
    } elseif ($password !== $confirmPassword) {
        $error = "Lozinke se ne poklapaju.";
    } else {
        if (registerUser($username, $password)) {
            header('Location: login.php?registered=1');
            exit;
        } else {
            $error = "Greška: korisničko ime je zauzeto.";
        }
    }
}
?>

<?php include '../templates/header.php'; ?>
<h2>Registracija</h2>
<form method="POST">
    <input name="username" placeholder="Korisničko ime" required>
    <input name="password" type="password" placeholder="Lozinka" required>
    <input name="confirm-pw" type="password" placeholder="Potvrdi lozinku" required>
    <button type="submit">Registruj se</button>
    <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
</form>
<?php include '../templates/footer.php'; ?>

