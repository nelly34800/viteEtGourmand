const acceptCgv = document.getElementById("acceptCgv");
const acceptAllergens = document.getElementById("acceptAllergens");
const acceptConditions = document.getElementById("acceptConditions");
const confirmOrderBtn = document.getElementById("confirmOrderBtn");

if (!acceptCgv || !acceptAllergens || !acceptConditions || !confirmOrderBtn) {
} else {
  function validateConfirmation() {
    confirmOrderBtn.disabled = !(
      acceptCgv.checked &&
      acceptAllergens.checked &&
      acceptConditions.checked
    );
  }

  [acceptCgv, acceptAllergens, acceptConditions].forEach(input => {
    input.addEventListener("change", validateConfirmation);
  });

  confirmOrderBtn.addEventListener("click", async (event) => {
  event.preventDefault();

  try {
    const response = await secureFetch("http://localhost:8082/order", {
      method: "POST",
      body: JSON.stringify({
        service_date: localStorage.getItem("service_date")
      })
    }, ["client"]);
    showMessage("Commande enregistrée avec succès", "success");

    setTimeout(() => {
      window.location.href = "/account";
    }, 2000);

  } catch (error) {
    showMessage("Une erreur est survenue", "danger");
  }
});
  validateConfirmation();
}

// ouvrir la modale avec des allèrgènes
document.addEventListener("click", async (event) => {
  const button = event.target.closest(".allergensModalBtn");
  if (!button) return;

  try {
    const response = await secureFetch("http://localhost:8082/cart", { 
      method: "GET" 
    }, ["client"]);

    const allergensList = document.getElementById("allergensList");
    allergensList.innerHTML = "";

    const allergensSet = new Set();

    for (const item of response) {

      if (item.type !== "menu") continue;

      const menu = await secureFetch(
        `http://localhost:8082/menu/${item.id}`,
        { method: "GET" },
        ["client"]
      );

      menu.dishes.forEach(dish => {
        dish.allergens.forEach(allergen => {
          allergensSet.add(allergen.name);
        });
      });
    }

    allergensSet.forEach(allergen => {
      const li = document.createElement("li");
      li.textContent = allergen;
      allergensList.appendChild(li);
    });

    const modal = new bootstrap.Modal(document.getElementById("allergensModal"));
    modal.show();

  } catch (error) {
  console.error("Erreur affichage des allèrgènes", error);
  showMessage(error.message, "danger");
  }
});

// ouvrir la modale avec des condition
document.addEventListener("click", async (event) => {
  const button = event.target.closest(".conditionsModalBtn");
  if (!button) return;

  try {
    const response = await secureFetch("http://localhost:8082/cart", { 
      method: "GET" 
    }, ["client"]);

    const tbody = document.getElementById("conditionsTableBody");
    tbody.innerHTML = '';

    for (const item of response) {

      if (item.type !== "menu") continue;

      const menu = await secureFetch(
        `http://localhost:8082/menu/${item.id}`,
        { method: "GET" },
        ["client"]
      );

      if (!menu.conditions.length) {
        const tr = document.createElement("tr");
            tr.innerHTML = `
              <td colspan="2">Aucune condition particulière</td>
            `;
            tbody.appendChild(tr);
      } else {
        menu.conditions.forEach(condition => {
          const tr = document.createElement("tr");
          const tdType = document.createElement("td");
          tdType.textContent = condition.type;
          tr.appendChild(tdType);

          const tdDescription = document.createElement("td");
          tdDescription.textContent = condition.description;
          tr.appendChild (tdDescription);

          tbody.appendChild(tr);
        });
      }
    }

    const modal = new bootstrap.Modal(document.getElementById("conditionsModal"));
      modal.show();

    } catch (error) {
    console.error("Erreur affichage des conditions", error);
    showMessage(error.message, "danger");
    }
  })
