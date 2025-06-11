/**
 * Ova skripta pruža klijentsku interaktivnost za aplikaciju,
 * prvenstveno za preuzimanje i prikazivanje liste usluga na početnoj stranici.
 */

// Dodajemo "event listener" na `window` objekat. Događaj 'DOMContentLoaded' se aktivira
// kada je početni HTML dokument u potpunosti učitan i parsiran od strane pregledača.
// Ovo je standardni i najsigurniji trenutak za pokretanje JavaScript koda koji manipuliše
// elementima stranice, jer osigurava da su svi elementi dostupni u DOM-u.
window.addEventListener("DOMContentLoaded", () => {

  // `fetch()` je moderni JavaScript API za slanje mrežnih zahteva (poput AJAX-a).
  // Asinhron je, što znači da se izvršava u pozadini bez zamrzavanja pregledača.
  // Vraća "Promise", objekat koji predstavlja budući završetak (ili neuspeh) zahteva.
  fetch("get_services.php")
    // `.then()` metoda se poziva kada se Promise razreši (tj. kada server odgovori).
    // `res` je Response objekat. `res.json()` je ugrađena metoda za parsiranje tela odgovora kao JSON.
    // Ovo takođe vraća Promise.
    .then((res) => res.json())
    // Ovaj drugi `.then()` se nadovezuje i izvršava kada se `res.json()` Promise razreši.
    // `data` parametar sada sadrži stvarni JavaScript niz objekata usluga poslatih sa servera.
    .then((data) => {
      // Dobijamo referencu na HTML elemente koje želimo da popunimo.
      const container = document.getElementById("usluge-container");
      const select = document.getElementById("usluga");

      // Prolazimo kroz svaki objekat usluge u `data` nizu.
      data.forEach((usluga) => {
        // --- Deo 1: Popunjavanje glavnog prikaza liste usluga ---

        // `document.createElement()` kreira novi HTML element u memoriji.
        const div = document.createElement("div");
        // Dodeljujemo mu klasu za CSS stilizovanje.
        div.className = "usluga";
        // Postavljamo tekst unutar diva da bude naziv usluge.
        div.textContent = usluga.naziv;
        // `appendChild()` dodaje novokreirani <div> kao dete glavnog kontejnera, čineći ga vidljivim na stranici.
        container.appendChild(div);

        // --- Deo 2: Popunjavanje padajućeg menija u formi za rezervaciju ---

        const opt = document.createElement("option");
        // `value` atribut opcije je ono što se šalje serveru. Koristimo jedinstveni `id` usluge.
        opt.value = usluga.id;
        // `textContent` je ono što korisnik vidi u padajućoj listi.
        opt.textContent = usluga.naziv;
        // Dodajemo novu <option> u <select> element.
        select.appendChild(opt);
      });
    });
});
