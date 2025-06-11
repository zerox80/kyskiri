<?php
/**
 * Ova skripta obrađuje proces odjave korisnika.
 * Uništava korisničku sesiju i preusmerava ga na stranicu za prijavu.
 */

// `session_start()` je neophodan za pristup i manipulaciju trenutnom sesijom.
session_start();
// `session_destroy()` uklanja sve podatke povezane sa trenutnom sesijom.
// Ovo efektivno odjavljuje korisnika.
session_destroy();

// Preusmeravamo korisnika na stranicu za prijavu nakon što je njegova sesija uništena.
header("Location: login.php");
// `exit;` je važan da bi se osiguralo da se nijedan drugi kod ne izvrši nakon preusmeravanja.
exit;

