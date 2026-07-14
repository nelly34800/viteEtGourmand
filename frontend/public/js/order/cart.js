// Implémenter js de ma page
const cartBody = document.getElementById("cart-body");
const cartTotal = document.getElementById("cart-total");
const dateEvent = document.getElementById("dateEvent");
const btnValidation = document.getElementById("btnValidation");

let localCart = [];

// Clé unique pour stocker notre panier dans le LocalStorage
const LOCAL_STORAGE_KEY = "vgc_cart_raw";

// fonction pour charger le panier brut local et demander les calculs au Backend PHP
async function loadCart() {
  try {
    localCart = JSON.parse(localStorage.getItem(LOCAL_STORAGE_KEY)) || [];

    if (localCart.length === 0) {
      renderEmptyCart();
      return;
    }

    // envoi du panier brut à PHP pour calcul des remises et ratios métiers
    const response = await secureFetch(`${API_URL}/cart/details`, {
      method: "POST",
      body: JSON.stringify({ cart: localCart })
    }, ["client"]);

    renderCart(response.detailed_cart, response.total_general);

  } catch (error) {
    showMessage("Une erreur est survenue lors du calcul du panier", "danger");
    renderEmptyCart();
  }
}

function renderCart(detailedCart, totalGeneral) {
  cartBody.innerHTML = ""; // On vide le tableau avant de le reconstruire

  detailedCart.forEach(item => {
    const tr = document.createElement("tr");

    //nom de l'article avec type en petit dessous
    const tdName = document.createElement("td");
    const strongName = document.createElement("strong");
    strongName.textContent = item.name; 
    tdName.appendChild(strongName);

    const br = document.createElement("br");
    tdName.appendChild(br);

    const smallType = document.createElement("small");
    smallType.textContent = getTypeLabel(item.type);
    tdName.appendChild(smallType);
    tr.appendChild(tdName);

     // quantité
    const tdQty = document.createElement("td");
    const inputQty = document.createElement("input");
    inputQty.type = "number";
    inputQty.className = "input-qty";
    inputQty.min = item.minimum_people ?? 1;
    inputQty.value = item.quantity;
    inputQty.dataset.id = item.id;
    inputQty.dataset.type = item.type
    tdQty.appendChild(inputQty);

    const feedbackDiv = document.createElement("div");
    feedbackDiv.className = "invalid-feedback";
    feedbackDiv.textContent = `Quantité minimum : ${item.minimum_people ?? 1}`;
    tdQty.appendChild(feedbackDiv);

    const labelQty = document.createElement("label");
    labelQty.textContent = item.type === 'material' ? " unités" : " personnes";
    tdQty.appendChild(labelQty);
    tr.appendChild(tdQty);

    // prix unitaire
    const tdPrice = document.createElement("td");
    tdPrice.textContent = `${item.price_per_person} €`;
    tr.appendChild(tdPrice);

    // total ligne avec remise si applicable
    const tdLineTotal = document.createElement("td");
    const discount = Number(item.discount) || 0;

    if (discount > 0) {
      const smallDiscount = document.createElement("small");
      smallDiscount.className = "text-success";
      smallDiscount.textContent = `Remise: -${discount.toFixed(2)} €`;
      tdLineTotal.appendChild(smallDiscount);
      tdLineTotal.appendChild(document.createElement("br"));
    }

    const textTotal = document.createTextNode(`${Number(item.line_total).toFixed(2)} €`);
    tdLineTotal.appendChild(textTotal);
    tr.appendChild(tdLineTotal);

    // bouton supprimer
    const tdDelete = document.createElement("td");
    const btnDelete = document.createElement("button");
    btnDelete.type = "button";
    btnDelete.className = "btn btn-danger delete-btn";
    btnDelete.textContent = "X";
    btnDelete.dataset.id = item.id;

    tdDelete.appendChild(btnDelete);
    tr.appendChild(tdDelete);

    // Ajout de la ligne complète au corps du tableau
    cartBody.appendChild(tr);
  });

  cartTotal.textContent = `${Number(totalGeneral).toFixed(2)} €`;

  bindCartEvents();
  validateForm();
}

// gestion des événements sur le panier
function bindCartEvents() {
  document.querySelectorAll(".input-qty").forEach(input => {
    input.addEventListener("change", async (event) => {
      const itemId = event.target.dataset.id;
      const quantity = parseInt(event.target.value, 10);  // 10 : convertir la valeur en nombre entier
      const minPeople = parseInt(event.target.min, 10) || 1;  // || 1 : prend 1 par défaut

      const item = localCart.find(i => i.id == itemId);
      if (item) {
        if (quantity >= minPeople) {
          item.quantity = quantity;
        } else {
          event.target.value = minPeople;
          item.quantity = minPeople;
        }
        localStorage.setItem(LOCAL_STORAGE_KEY, JSON.stringify(localCart));
        await loadCart();
        // force le rafraîchissement visuel de la barre de navigation
        updateCartNavbar();
      }
    });
  });
  document.querySelectorAll(".delete-btn").forEach(button => {
    button.addEventListener("click", async (event) => {
      const itemId = event.target.dataset.id;
      // Supprime l'article du panier local, puis recharge le panier : si i correspond à itemId, on le supprime sinon on le garde
      localCart = localCart.filter(i => i.id != itemId);

      // vérifie s'il reste au moins un menu dans le panier
    const hasMenu = localCart.some(i => i.type === "menu");
    
    if (!hasMenu && localCart.length > 0) {
      // S'il n'y a plus de menu mais qu'il reste des options, on vide tout
      localCart = [];
      showMessage("Le menu principal a été retiré. Les options associées ont également été supprimées.", "warning");
    }
    // sauvegarde et mise à jour
    localStorage.setItem(LOCAL_STORAGE_KEY, JSON.stringify(localCart));
    await loadCart();
    // force le rafraîchissement visuel de la barre de navigation
    updateCartNavbar();
    });
  });
}
// fonction pour afficher un message lorsque le panier est vide
function renderEmptyCart() {
  cartBody.innerHTML = "";
  const tr = document.createElement("tr");
  const td = document.createElement("td");
  td.colSpan = 5;
  td.className = "text-center text-muted";
  td.textContent = "Votre panier est vide.";
  
  tr.appendChild(td);
  cartBody.appendChild(tr);
  
  cartTotal.textContent = "0.00 €";
  btnValidation.disabled = true;
}

// recupère le type d'article pour l'afficher dans le panier
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
// écouteur d'événement pour valider le formulaire lorsque la date change
dateEvent.addEventListener("change", validateForm);

// fonction pour valider le formulaire
function validateForm() {
  const dateOk = dateEvent.value !== "";
  const cartOk = localCart.length > 0;
  btnValidation.disabled = !(dateOk && cartOk);
}
// écouteur d'événement pour le bouton de validation
btnValidation.addEventListener("click", (event) => {
  event.preventDefault();
  localStorage.setItem("service_date", dateEvent.value);
  window.location.href = "/order";
});

// Chargement initial
const savedDate = localStorage.getItem("service_date");
if (savedDate) {
  dateEvent.value = savedDate;
}
loadCart();
updateCartNavbar();