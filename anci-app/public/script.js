window.addEventListener("DOMContentLoaded", () => {
  fetch("get_services.php")
    .then((res) => res.json())
    .then((data) => {
      const container = document.getElementById("usluge-container");
      const select = document.getElementById("usluga");

      data.forEach((usluga) => {
        const div = document.createElement("div");
        div.className = "usluga";
        div.textContent = usluga.naziv;
        container.appendChild(div);

        const opt = document.createElement("option");
        opt.value = usluga.id;
        opt.textContent = usluga.naziv;
        select.appendChild(opt);
      });
    });
});
