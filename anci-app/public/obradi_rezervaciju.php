<?php
session_start();
require_once '../src/db.php';

$username = $_SESSION['username'] ?? null;
$telefon = trim($_POST['telefon'] ?? '');
$uslugaId = $_POST['usluga'] ?? '';
$datum = $_POST['datum'] ?? '';

if (!$username || !$telefon || !$uslugaId || !$datum) {
    // Missing fields, redirect back with error
    header("Location: /index.php?greska=1");
    exit;
}

try {
    $stmt = $db->prepare("INSERT INTO rezervacije (ime, telefon, usluga_id, datum) VALUES (:ime, :telefon, :usluga, :datum)");
    $stmt->execute([
        'ime' => $username,
        'telefon' => $telefon,
        'usluga' => $uslugaId,
        'datum' => $datum
    ]);
    header("Location: /index.php?uspesno=1");
    exit;
} catch (PDOException $e) {
    // Error in DB
    header("Location: /index.php?greska=1");
    exit;
}

