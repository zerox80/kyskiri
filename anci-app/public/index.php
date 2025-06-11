<?php include '../templates/header.php'; ?>

<?php if (isset($_GET['uspesno'])): ?>
  <p style="color: green;">✅ Uspešno ste rezervisali termin!</p>
<?php elseif (isset($_GET['greska'])): ?>
  <p style="color: red;">❌ Došlo je do greške. Pokušajte ponovo.</p>
<?php endif; ?>

<section id="usluge">
  <h2>Naše usluge</h2>
  <div class="usluge-container" id="usluge-container">
  </div>
</section>

<section id="rezervacija">
  <h2>Rezerviši termin</h2>
  <form id="forma-rezervacija" action="obradi_rezervaciju.php" method="POST">
    <div class="form-group">
      <label>Korisničko ime:</label>
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
        <!-- Opcije iz baze putem JavaScript-a -->
      </select>
    </div>
    <div class="form-group">
      <label for="datum">Datum i vreme:</label>
      <input type="datetime-local" id="datum" name="datum" required />
    </div>
    <button type="submit">Rezerviši</button>
  </form>
</section>

<?php include '../templates/footer.php'; ?>

