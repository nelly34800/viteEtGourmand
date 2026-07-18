const acceptCgv = document.getElementById("acceptCgv");
const acceptAllergens = document.getElementById("acceptAllergens");
const acceptConditions = document.getElementById("acceptConditions");
const confirmOrderBtn = document.getElementById("confirmOrderBtn");

const RAW_CART_KEY = "vgc_cart_raw";
const DELIVERY_KEY = "vgc_delivery";

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
    // Extraction des données persistées localement
    const localCart = JSON.parse(localStorage.getItem(RAW_CART_KEY)) || [];
    const localDelivery =JSON.parse(localStorage.getItem(DELIVERY_KEY)) || null;
    const serviceDate = localStorage.getItem("service_date");

    console.log("Cart local:", localStorage.getItem(RAW_CART_KEY));
    console.log("Delivery local:", localStorage.getItem(DELIVERY_KEY));
    console.log("Service date local:", localStorage.getItem("service_date"));

    const response = await secureFetch(`${API_URL}/order`, {
      method: "POST",
      body: JSON.stringify({
        cart: localCart,
        delivery: localDelivery,
        service_date: serviceDate
      })
    }, ["client"]);
    showMessage("Commande enregistrée avec succès", "success");

    // Nettoyage du LocalStorage pour éviter les commandes doublons
    localStorage.removeItem(RAW_CART_KEY);
    localStorage.removeItem(DELIVERY_KEY);
    localStorage.removeItem("service_date");

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
    const localCart = JSON.parse(localStorage.getItem(RAW_CART_KEY)) || [];

    const allergensList = document.getElementById("allergensList");
    allergensList.innerHTML = "";

    const allergensSet = new Set();

    for (const item of localCart) {
      if (item.type !== "menu") continue;

      // Récupération des détails du menu
      const menu = await secureFetch(
        `${API_URL}/menu/${item.id}`,
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
    const localCart = JSON.parse(localStorage.getItem(RAW_CART_KEY)) || [];

    const tbody = document.getElementById("conditionsTableBody");
    tbody.innerHTML = '';

    for (const item of localCart) {
      if (item.type !== "menu") continue;

      const menu = await secureFetch(
        `${API_URL}/menu/${item.id}`,
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
