<?php
/**
 * Ovo je glavna početna stranica aplikacije.
 * Uključuje zaglavlje i podnožje templejte, prikazuje dinamičke poruke o uspehu/grešci,
 * i sadrži strukturu za listu usluga i formu za rezervaciju.
 */
include '../templates/header.php'; // Zaglavlje pokreće sesiju i gradi navigacionu traku.
?>

<?php
// --- DINAMIČKE PORUKE ---
// Ovaj blok proverava URL za query parametre kako bi prikazao privremene poruke.
// Ovo je jednostavan način za davanje povratnih informacija korisniku nakon slanja forme.

// Primer URL-a: /index.php?uspesno=1
if (isset($_GET['uspesno'])): ?>
  <p style="color: green;">✅ Uspešno ste rezervisali termin!</p>
<?php // Primer URL-a: /index.php?greska=1
elseif (isset($_GET['greska'])): ?>
  <p style="color: red;">❌ Došlo je do greške. Pokušajte ponovo.</p>
<?php endif; ?>

<section id="usluge">
  <h2>Naše usluge</h2>
  <!-- Ovaj kontejner je inicijalno prazan.
       Datoteka `public/script.js` će preuzeti usluge sa servera
       i dinamički kreirati i umetnuti HTML za njihov prikaz ovde. -->
  <div class="usluge-container" id="usluge-container">
  </div>
</section>

<section id="rezervacija">
  <h2>Rezerviši termin</h2>
  <!-- Ova forma se koristi za kreiranje nove rezervacije.
       Svoje podatke šalje na `obradi_rezervaciju.php` koristeći POST metodu. -->
  <form id="forma-rezervacija" action="obradi_rezervaciju.php" method="POST">
    <div class="form-group">
      <label>Korisničko ime:</label>
      <!-- Korisničko ime je unapred popunjeno imenom prijavljenog korisnika iz sesije.
           `htmlspecialchars` se koristi kao bezbednosna mera za sprečavanje XSS napada. -->
      <strong><?= htmlspecialchars($_SESSION['username']) ?></strong>
    </div>
    <div class="form-group">
      <label for="telefon">Telefon:</label>
      <input type="tel" id="telefon" name="telefon" required />
    </div>
    <div class="form-group">
      <label for="usluga">Usluga:</label>
      <select id="usluga" name="usluga" required>
        <option value="">Izaberi uslugu</option>
        <!-- Ovaj padajući meni je takođe inicijalno prazan (osim ove prve opcije).
             Datoteka `public/script.js` popunjava ovaj meni sa `<option>` elementima
             preuzetim iz baze podataka. -->
      </select>
    </div>
    <div class="form-group">
      <label for="datum">Datum i vreme:</label>
      <input type="datetime-local" id="datum" name="datum" required />
    </div>
    <button type="submit">Rezerviši</button>
  </form>
</section>

<?php include '../templates/footer.php'; // Uključuje zatvarajuće HTML tagove i glavnu script.js datoteku. ?>

