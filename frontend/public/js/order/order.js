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

const LOCAL_STORAGE_KEY = "vgc_cart_raw";

// fonction pour charger le panier
async function loadOrder() {
  try {
    const localCart = JSON.parse(localStorage.getItem(LOCAL_STORAGE_KEY)) || [];

    if (localCart.length === 0) {
      showMessage("Votre panier est vide.", "warning");
      setTimeout(() => {
        window.location.href = "/cart";
      }, 2000);
      return;
    }
    // envoie le panier brut au backend pour récupérer les nom et les prix
    const response = await secureFetch(`${API_URL}/cart/details`, {
      method: "POST",
      body: JSON.stringify({ cart: localCart })
    }, ["client"]);

    // récupère le panier détaillé calculé par PHP
    cart = response.detailed_cart || [];
    renderTable();

  } catch (error) {
    showMessage("Une erreur est survenue lors de la récupération de votre panier.", "danger");
  }
}

function renderTable() {
  orderBody.innerHTML = "";
    cartTotal = 0;

  cart.forEach(item => {

  cartTotal += Number(item.line_total);

  const tr = document.createElement("tr");

  // Colonne nom/type
  const tdInfo = document.createElement("td");

  const strong = document.createElement("strong");
  strong.textContent = item.name;
  tdInfo.appendChild(strong);

  const br = document.createElement("br");
  tdInfo.appendChild(br);

  const small = document.createElement("small");
  small.textContent = getTypeLabel(item.type);
  tdInfo.appendChild(small);

  tr.appendChild(tdInfo);

  // Colonne quantité
  const tdQuantity = document.createElement("td");
  tdQuantity.textContent = item.quantity;
  tr.appendChild(tdQuantity);

  // Colonne prix
  const tdPrice = document.createElement("td");
  tdPrice.textContent = `${item.price_per_person} €`;
  tr.appendChild(tdPrice);

  // Colonne total/remise
  const tdTotal = document.createElement("td");
  tr.appendChild(tdTotal);

  // formatage de la remise (au cas où elle est indéfinie)
    const discount = Number(item.discount) || 0;
    if (discount > 0) {
      const discountEl = document.createElement("small");
      discountEl.className = "text-success";
      discountEl.textContent = `Remise : -${discount.toFixed(2)} €`;

      tdTotal.appendChild(discountEl);
      tdTotal.appendChild(document.createElement("br"));
    }

    const lineTotal = Number(item.line_total) || 0;
    tdTotal.append(`${lineTotal.toFixed(2)} €`);

  // Ajout de la ligne au tableau
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
      const result = await secureFetch(`${API_URL}/delivery_charges`, {
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
      showMessage("Une erreur est survenue lors du calcul des frais", "danger");
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
    showMessage("Veuillez calculer les frais de livraison avant de continuer.", "success");
    return;
  }

  showMessage("Adresse de livraison enregistrée !", "success");

  setTimeout(() => {
      window.location.href = "/orderConfirmation";
    }, 2000);
  });

  // Chargement initial
const savedDate = localStorage.getItem("service_date");
if (savedDate) {
  document.getElementById("summary-date").textContent = savedDate;
}

loadOrder();