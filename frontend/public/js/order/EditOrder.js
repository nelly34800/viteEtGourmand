//validation des données
// Implémenter js de ma page
const dateEvent = document.getElementById("dateEvent");
const address = document.getElementById("address");
const postalCode = document.getElementById("postalCode");
const city = document.getElementById("city");
const btnGoConfirmation = document.getElementById("btnGoConfirmation");
const btnDeliveryCharges = document.getElementById("btnDeliveryCharges");

const params = new URLSearchParams(window.location.search);
const orderId = params.get("id");

const orderBody = document.getElementById("order-body");
const cartTotalEl = document.getElementById("cart-total");
const deliveryChargesEl = document.getElementById("delivery-charges");
const orderTotalEl = document.getElementById("order-total");

let order = null;
let items = [];
let productsTotal = 0;
let deliveryCharges = 0;
let updateReady = false;

// fonction pour charger le commande à modifier
async function loadOrder() {
  try {
    if (!orderId) {
      showMessage("Commande introuvable", "danger");
      return;
    }
    // Appel sécurisé vers l'API (GET: fonction dans api.js)
    order = await secureFetch(`http://localhost:8082/order/${orderId}`, {
      method: "GET"
    }, ["client"]);

    fillForm(order);
    prepareItems(order);
    renderTable();
    validateForm();

  } catch (error) {
    showMessage("Impossible de charger la commande", "danger");
  }
}
// rempli le formulaire avec les données rentrées initialement
function fillForm(order) {
  dateEvent.value = order.service_date.replace(" ", "T").slice(0, 16);
  address.value = order.delivery_address;
  postalCode.value = order.postal_code;
  city.value = order.city;

  deliveryCharges = Number(order.delivery_charges);
}
// prépare l'affichage des produits de la commande (transformer les données API en tableau exploitable)
function prepareItems(order) {
  items = [ // construit un tableau unique pour rendu OK
    ...(order.menus || []).map(item => ({
      type: "menu",
      name: item.name,
      quantity: item.number,
      price: item.price,
      discount: item.discount ?? 0,
      subtotal: item.subtotal
    })),
    ...(order.drink_packages || []).map(item => ({
      type: "drink_package",
      name: item.name,
      quantity: item.number,
      price: item.price,
      discount: 0,
      subtotal: item.subtotal
    })),
    ...(order.personal_packages || []).map(item => ({
      type: "personal_package",
      name: item.name,
      quantity: item.number,
      price: item.price,
      discount: 0,
      subtotal: item.subtotal
    })),
    ...(order.materials || []).map(item => ({
      type: "material",
      name: item.name,
      quantity: item.number,
      price: item.price,
      discount: 0,
      subtotal: item.subtotal
    }))
  ];
}
// affiche les données non modifiables
function renderTable() {
  orderBody.innerHTML = "";
  productsTotal = 0;

  items.forEach(item => {
    productsTotal += Number(item.subtotal);

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

  if (item.discount > 0) {
    const discount = document.createElement("small");
    discount.className = "text-success";
    discount.textContent = `Remise : -${item.discount.toFixed(2)} €`;

    tdTotal.appendChild(discount);
    tdTotal.appendChild(document.createElement("br"));
  }

  tdTotal.append(`${item.line_total.toFixed(2)} €`);

  // Ajout des cellules à la ligne

    orderBody.appendChild(tr);
  });
  updateTotals();
}
// calcul des prix arrondis à 2 decimales
function updateTotals() {
  cartTotalEl.textContent = `${productsTotal.toFixed(2)} €`;
  deliveryChargesEl.textContent = `${deliveryCharges.toFixed(2)} €`;
  orderTotalEl.textContent = `${(productsTotal + deliveryCharges).toFixed(2)} €`;
}
// calculer les frais de livraison
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
      updateReady = true;
      updateTotals();

      btnGoConfirmation.disabled = false;

    } catch (error) {
      showMessage("Impossible de calculer les frais de livraison", "danger");
    }
}

async function updateOrder(event) {
  event.preventDefault();

  if (!updateReady) {
    showMessage("Veuillez recalculer les frais de livraison avant de confirmer.", "warning");
    return;
  }
  try {
    await secureFetch(`http://localhost:8082/order/${orderId}`, {
      method: "PUT",
      body: JSON.stringify({
        service_date: dateEvent.value,
        delivery_address: address.value.trim(),
        postal_code: postalCode.value.trim(),
        city: city.value.trim()
      })
    }, ["client"]);

    showMessage("Commande modifiée avec succès", "success");

    setTimeout(() => {
      window.location.href = "/account";
    }, 2000);

  } catch (error) {
    showMessage("Une erreur est survenue", "danger");
  }
}

//écoute des événements
dateEvent.addEventListener("input", validateForm);
address.addEventListener("input", validateForm);
postalCode.addEventListener("input", validateForm);
city.addEventListener("input", validateForm);

//fonction permettant de valider le formulaire
function validateForm(){
const dateEventOk = validateRequired(dateEvent);
const addressOk = validateRequired(address);
const postalCodeOk = validateRequired(postalCode);
const cityOk = validateRequired(city);

    if(dateEventOk && addressOk && postalCodeOk && cityOk){
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
btnGoConfirmation.addEventListener("click", updateOrder);

validateForm();
loadOrder();