<?php
require_once '../src/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username && $password && loginUser($username, $password)) {
        header("Location: /");
        exit;
    } else {
        $error = "Pogrešno korisničko ime ili lozinka.";
    }
}
?>

<?php include '../templates/header.php'; ?>
<h2>Prijava</h2>

<form method="POST">
    <input name="username" placeholder="Korisničko ime" required>
    <input name="password" type="password" placeholder="Lozinka" required>
    <button type="submit">Prijavi se</button>
    <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
</form>

<?php include '../templates/footer.php'; ?>

