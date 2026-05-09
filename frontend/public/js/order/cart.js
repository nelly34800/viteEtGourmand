// Implémenter js de ma page
const cartBody = document.getElementById("cart-body");
const cartTotal = document.getElementById("cart-total");
const dateEvent = document.getElementById("dateEvent");
const btnValidation = document.getElementById("btnValidation");

let cart = [];
// fonction pour charger le panier
async function loadCart() {
  try {
    // Appel sécurisé vers l'API (GET: fonction dans api.js)
    cart = await secureFetch("http://localhost:8082/cart", {
      method: "GET"
    }, ["client"]);

    renderCart();

  } catch (error) {
    console.error("Erreur chargement panier :", error);

    cart = [];

    cartBody.innerHTML = `
      <tr>
        <td colspan="5" class="text-center text-danger">
          Vous devez être connecté pour accéder au panier.
        </td>
      </tr>
    `;

    cartTotal.textContent = "0 €";
    btnValidation.disabled = true;
  }
}

function renderCart() {
  cartBody.innerHTML = "";

  let total = 0;

  cart.forEach(item => {
    total += Number(item.line_total);

    const tr = document.createElement("tr");

    tr.innerHTML = `
      <td>
        <strong>${item.name}</strong>
        <br>
        <small>${getTypeLabel(item.type)}</small>
      </td>

      <td>
        <input 
          type="number"
          class="input-qty"
          min="${item.minimum_people ?? 1}"
          value="${item.quantity}"
          data-id="${item.id}"
          data-type="${item.type}"
        >
        <div class="invalid-feedback">
          Quantité minimum : ${item.minimum_people ?? 1}
        </div>
        <label>personnes</label>
      </td>

      <td>${item.price_per_person} €</td>
      <td>${item.discount > 0 
        ? `<small class="text-success">Remise: -${item.discount.toFixed(2)} €</small>` 
        : ""}<br>
        ${item.line_total.toFixed(2)} €</td>

      <td>
        <button 
          type="button"
          class="btn btn-danger delete-btn"
          data-id="${item.id}"
          data-type="${item.type}"
        >
          X
        </button>
      </td>
    `;

    cartBody.appendChild(tr);
  });

  cartTotal.textContent = `${total.toFixed(2)} €`;

  bindCartEvents();
  validateForm();
}

function getTypeLabel(type) {
  switch (type) {
    case "menu":
      return "Menu";

    case "drink_package":
      return "Forfait boisson";

    case "personal_package":
      return "Forfait personnel";

    case "material":
      return "Matériel";

    default:
      return "Article";
  }
}

function bindCartEvents() {
  document.querySelectorAll(".input-qty").forEach(input => {
    input.addEventListener("change", async (event) => {
      const itemId = event.target.dataset.id;
      const quantity = parseInt(event.target.value, 10);

      await updateCartItem(itemId, quantity);
    });
  });

  document.querySelectorAll(".delete-btn").forEach(button => {
    button.addEventListener("click", async (event) => {
      event.preventDefault();

      const itemId = event.target.dataset.id;

      await deleteCartItem(itemId);
    });
  });
}

async function updateCartItem(itemId, quantity) {
  try {
    await secureFetch(`http://localhost:8082/cart/${itemId}`, {
      method: "PUT",
      body: JSON.stringify({ quantity })
    }, ["client"]);

    await loadCart();

  } catch (error) {
    console.error("Erreur modification panier :", error);
  }
}

async function deleteCartItem(itemId) {
  try {
    await secureFetch(`http://localhost:8082/cart/${itemId}`, {
      method: "DELETE"
    }, ["client"]);

    await loadCart();

  } catch (error) {
    console.error("Erreur suppression panier :", error);
  }
}

dateEvent.addEventListener("change", validateForm);

function validateForm() {
  const dateOk = dateEvent.value !== "";
  const cartOk = cart.length > 0;

  btnValidation.disabled = !(dateOk && cartOk);
}

btnValidation.addEventListener("click", (event) => {
  event.preventDefault();
  if (cart.length === 0) {
    showMessage("Votre panier est vide", "warning");
  }

  if (!dateEvent.value) {
    showMessage("Veuillez choisir une date", "warning");
  }

  localStorage.setItem("service_date", dateEvent.value);
  showMessage("Votre panier est validé avec succès!", "success");

  setTimeout(() => {
    window.location.href = "/order";
  }, 1000);
});

loadCart();