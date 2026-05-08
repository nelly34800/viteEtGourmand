// Implémenter js de ma page
const address = document.getElementById("address");
const postalCode = document.getElementById("postalCode");
const city = document.getElementById("city");
const btnDeliveryCharges = document.getElementById("btnDeliveryCharges");

const orderBody = document.getElementById("order-body");
const cartTotalEl = document.getElementById("cart-total");
const deliveryChargesEl = document.getElementById("delivery-charges");
const orderTotalEl = document.getElementById("order-total");

let cart = [];
let cartTotal = 0;
let deliveryCharges = 0;

// fonction pour charger le panier
async function loadOrder() {
  try {
    // Appel sécurisé vers l'API (GET: fonction dans api.js)
    cart = await secureFetch("http://localhost:8082/cart", {
      method: "GET"
    }, ["client"]);

    renderTable();

  } catch (error) {
    console.error("Erreur chargement panier :", error);
  }
}

function renderTable() {
  orderBody.innerHTML = "";
    cartTotal = 0;

  cart.forEach(item => {
    cartTotal += Number(item.line_total);

    const tr = document.createElement("tr");

     tr.innerHTML = `
      <td>
        <strong>${item.name}</strong><br>
        <small>${getTypeLabel(item.type)}</small>
      </td>
      <td>${item.quantity}</td>
      <td>${item.price_per_person} €</td>
      <td>${item.discount > 0 
        ? `<small class="text-success">Remise: -${item.discount.toFixed(2)} €</small>` 
        : ""}<br>
        ${item.line_total} €</td>
    `;
    orderBody.appendChild(tr);
  });
  updateTotals();
}
// calcul des prix arrondis à 2 decimales
function updateTotals() {
  cartTotalEl.textContent = `${cartTotal.toFixed(2)} €`;
  deliveryChargesEl.textContent = `${deliveryCharges.toFixed(2)} €`;
  orderTotalEl.textContent = `${(cartTotal + deliveryCharges).toFixed(2)} €`;
}

async function calculateDeliveryCharges(event) {
  event.preventDefault();

    try {
      const result = await secureFetch("http://localhost:8082/delivery_charges", {
        method: "POST",
        body: JSON.stringify({
          address: address.value.trim(),
          postalCode: postalCode.value.trim(),
          city: city.value.trim()
        })
      }, ["client"]);

      deliveryCharges = Number(result.delivery_charges);
      deliveryCalculated = true;
      updateTotals();

      btnGoConfirmation.disabled = false;

    } catch (error) {
      console.error("Erreur frais de livraison :", error);
      alert(error.message);
    }
}
//écoute des événements
address.addEventListener("input", validateForm);
postalCode.addEventListener("input", validateForm);
city.addEventListener("input", validateForm);

//fonction permettant de valider le formulaire
function validateForm(){
const addressOk = validateRequired(address);
const postalCodeOk = validateRequired(postalCode);
const cityOk = validateRequired(city);

    if(addressOk && postalCodeOk && cityOk){
    btnDeliveryCharges.disabled = false;
    }
    else{
    btnDeliveryCharges.disabled = true;
    }
}

function validateRequired(input){
    if(input.value != ''){
        // c'est ok
        input.classList.add("is-valid");
        input.classList.remove("is-invalid");
        return true;
    }
    else{
        //c'est pas ok
        input.classList.remove("is-valid");
        input.classList.add("is-invalid");
        return false;
    }
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

btnDeliveryCharges.addEventListener("click", calculateDeliveryCharges);

const btnGoConfirmation = document.getElementById("btnGoConfirmation");
let deliveryCalculated = false;

btnGoConfirmation.addEventListener("click", () => {
  if (!deliveryCalculated) {
    showMassage("Veuillez calculer les frais de livraison avant de continuer.", "success");
  }

  setTimeout(() => {
      window.location.href = "/orderConfirmation";
    }, 2000);
  });

loadOrder();