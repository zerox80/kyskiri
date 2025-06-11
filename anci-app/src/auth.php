<?php
require_once __DIR__ . '/db.php';

function registerUser($username, $password) {
    global $db;

    $hashed = password_hash($password, PASSWORD_BCRYPT);

    $stmt = $db->prepare("INSERT INTO korisnici (username, password) VALUES (:username, :password)");
    return $stmt->execute(['username' => $username, 'password' => $hashed]);
}

function loginUser($username, $password) {
    global $db;

    $stmt = $db->prepare("SELECT * FROM korisnici WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        session_start();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['is_admin'] = $user['is_admin'];
        return true;
    }

    return false;
}
?>

