<?php
/**
 * Ova datoteka pruža pomoćne funkcije za upravljanje korisničkim sesijama.
 */

/**
 * Proverava da li je korisnik trenutno prijavljen tražeći korisničko ime u podacima sesije.
 *
 * @return bool True ako je korisnik prijavljen, inače false.
 */
function isLoggedIn() {
    // `$_SESSION` je specijalni PHP globalni niz koji čuva podatke za korisnika na više stranica.
    // `!empty()` je siguran način da se proveri da li ključ 'username' postoji u nizu sesije
    // i da li ima vrednost koja nije prazna.
    return !empty($_SESSION['username']);
}

/**
 * Nameće obavezu prijave na bilo kojoj stranici gde je pozvana.
 * Ako korisnik nije prijavljen, preusmerava ga na stranicu za prijavu i zaustavlja izvršavanje skripte.
 * Ovo je ključna bezbednosna funkcija za zaštitu ograničenog sadržaja.
 */
function requireLogin() {
    // Koristimo funkciju definisanu iznad da proverimo status prijave.
    if (!isLoggedIn()) {
        // `header()` šalje sirovi HTTP header pregledaču.
        // 'Location' header govori pregledaču da izvrši preusmeravanje.
        header('Location: /login.php');
        // `exit;` je od suštinskog značaja. Odmah prekida trenutnu skriptu.
        // Bez ovoga, ostatak PHP koda na zaštićenoj stranici bi se i dalje izvršavao,
        // što bi potencijalno moglo dovesti do curenja privatnih informacija.
        exit;
    }
}

