<?php
function isLoggedIn() {
    return !empty($_SESSION['username']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /login.php');
        exit;
    }
}

