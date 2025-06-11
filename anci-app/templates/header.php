<?php
/**
 * Ovo je zajednički templejt zaglavlja za sve stranice na sajtu.
 * Započinje HTML dokument, uključuje metapodatke i stilove,
 * i gradi glavnu navigacionu traku.
 *
 * Uključen je na vrhu svake korisnički orijentisane .php datoteke.
 */

// `session_start()` se postavlja ovde da bi se osiguralo da se sesija pokrene ili nastavi
// pri svakom učitavanju stranice, čineći podatke sesije globalno dostupnim.
session_start();
?>
<!DOCTYPE html>
<!-- `lang` atribut pomaže pristupačnosti i pretraživačima. -->
<html lang="sr">
<head>
  <meta charset="UTF-8" />
  <!-- Ovaj meta tag je ključan za responzivni dizajn, osiguravajući da se sajt pravilno skalira na mobilnim uređajima. -->
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Beauty By Anci</title>
  <!-- Povezuje jedinstveni stylesheet koji se koristi za celu aplikaciju. -->
  <link rel="stylesheet" href="/style.css" />
</head>
<body>
<header>
  <h1>Beauty by Anci</h1>
  <nav>
    <!-- --- LOGIKA DINAMIČKE NAVIGACIJE --- -->
    <!-- Linkovi prikazani u navigacionoj traci se menjaju u zavisnosti od statusa prijave korisnika i njegove uloge (admin/korisnik). -->
    <div class="nav-left">
      <a href="/index.php">Početna</a>

      <?php // Proveravamo da li je korisnik prijavljen.
      if (!empty($_SESSION['username'])): ?>
        <?php // Ako je prijavljen, proveravamo da li je administrator.
        if (!empty($_SESSION['is_admin'])): ?>
          <a href="/admin_panel.php">Panel</a> <!-- Link za Administratore -->
        <?php else: ?>
          <a href="/rezervacije.php">Rezervacije</a> <!-- Link za obične Korisnike -->
        <?php endif; ?>
      <?php endif; ?>
    </div>

    <div class="nav-right">
      <?php if (!empty($_SESSION['username'])): ?>
        <!-- Ako je korisnik prijavljen, prikazujemo poruku dobrodošlice i link za odjavu. -->
        <!-- `htmlspecialchars` je ključna bezbednosna funkcija za sprečavanje XSS napada, osiguravajući da se korisničko ime tretira kao običan tekst. -->
        <span>Zdravo, <?= htmlspecialchars($_SESSION['username']) ?></span>
        <a href="/logout.php">Odjavi se</a>
      <?php else: ?>
        <!-- Ako je korisnik gost (nije prijavljen), prikazujemo linkove za prijavu ili registraciju. -->
        <a href="/login.php">Prijava</a>
        <a href="/register.php">Registracija</a>
      <?php endif; ?>
    </div>
</nav>

</header>
<!-- `<main>` tag se otvara ovde. Specifični sadržaj stranice biće umetnut između -->
<!-- ovog taga i `</main>` taga u templejtu podnožja. -->
<main>

