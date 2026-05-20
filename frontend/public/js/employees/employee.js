// les avis : 
// // fonction pour charger les avis en attente
async function loadNoticeUnvalidated() {
  try {
    // Appel sécurisé vers l'API
    const data = await secureFetch("http://localhost:8082/noticeUnvalidated", {
        method: 'GET' }, 
        ['admin', 'employé']);

    const carouselInner = document.getElementById("carouselInner");

    carouselInner.innerHTML = '';

    // détermine combien d'avis selon la largeur de l'écran
    let noticesPerSlide = 2;

    // Générer les slides
    for (let i = 0; i < data.length; i += noticesPerSlide) {
      // Sélection des avis pour cette slide

  const slideDiv = document.createElement("div");
  slideDiv.classList.add("carousel-item");
  // La première slide prend la classe active
  if (i === 0) slideDiv.classList.add("active");

  // Construit la slide
  const row = document.createElement("div");
  row.classList.add("row", "justify-content-space-between");

  const slideNotices = data.slice(i, i + noticesPerSlide);

    slideNotices.forEach((notice) => {
      // création d'une ligne d'un avis
      const col = document.createElement("div");

      // desktop = 3 colonnes / Mobile = 1 colonne
      col.classList.add("col-12", "col-md-6", "mb-3");

col.innerHTML = `
  <div class="card bgc-primary p-2 w-100" style="min-width: 300px;">
    <div class="d-flex flex-column align-items-center pb-3">
      <div class="note p-2">
        ${renderStars(Number(sanitizeHTML(notice.note)))}
      </div>
      <p class="p-2">
        ${sanitizeHTML(notice.description)}
      </p>
      <p class="signature">
        ${sanitizeHTML(notice.signature)}
      </p>
      <div class="p-2">
        <button class="validateBtn btn btn-secondary btn-sm" data-id="${notice.id}">
          Valider
        </button>
        <button class="deleteBtn btn btn-danger btn-sm" data-id="${notice.id}">
          Supprimer
        </button>
      </div>
    </div>
  </div>
`;
      row.appendChild(col);
    });

    slideDiv.appendChild(row);
    // Ajouter la slide dans le carousel
    carouselInner.appendChild(slideDiv);
    }
  } catch (error) {
      // Affiche l'erreur si problème API
      showMessage("Une erreur est survenue", "danger");
  }
}
// se lance au chargement
loadNoticeUnvalidated();

//supprimer l'avis
document.addEventListener("click", async (e) => {
  // Vérifie si bouton supprimer
  if (!e.target.closest(".deleteBtn")) return;
  const button = e.target.closest(".deleteBtn");
  // Récupère l'id
  const noticeId = button.dataset.id;
  // Confirmation utilisateur
  if (!confirm("Supprimer cet avis ?")) return;

  try {
    // Appel API DELETE
    await secureFetch(
      `http://localhost:8082/notice/${noticeId}`,
      { method: 'DELETE' },
      ['employé', 'admin']
    );
    // Message succès
    alert("Avis supprimé avec succès");
    // Recharge la liste
    loadNoticeUnvalidated();
 
  } catch (error) {
    showMessage("Une erreur est survenue", "danger");
  }
});

// valider l'avis
document.addEventListener("click", async (e) => {
  // Vérifie si bouton valider
  if (!e.target.closest(".validateBtn")) return;
  const button = e.target.closest(".validateBtn");
  // Récupère l'id
  const noticeId = button.dataset.id;
  try {
    // Appel API PUT pour valider
    await secureFetch(
      `http://localhost:8082/notice/${noticeId}/updateStatus`, { 
        method: 'PUT',
        body: JSON.stringify({
          status: "validé"
       })
    }, ['employé', 'admin']);
      // afficher le message
      showMessage("avis validé avec succès", "success");
      loadNoticeUnvalidated();

  } catch (error) {
    console.error("Erreur fetch :", error);
  }
});


// tableau des commandes
// Implémenter js de ma page
const filterName = document.getElementById("filterName");
const filterStatus = document.getElementById("filterStatus");

const tbody = document.querySelector("tbody");
const mobileContainer = document.getElementById("mobile-container");

let allOrders = [];
// Charger les commandes
async function loadOrders() {
  try {
    // Appel sécurisé vers l'API (GET: fonction dans api.js)
    allOrders = await getOrders();

    fillStatusFilter(allOrders);
    displayOrders(allOrders);

  } catch (error) {
    showMessage("Une erreur est survenue", "danger");
  }
}
loadOrders();
// appliquer filtres 
function applyFilters() {
  const nameValue = filterName.value.toLowerCase();
  const statusValue = filterStatus.value;

  const filteredOrders = allOrders.filter(order => {
    const matchName = !nameValue || order.user_last_name.toLowerCase().includes(nameValue);
    const matchStatus = !statusValue || order.status === statusValue;

    return matchName && matchStatus;
  });
  displayOrders(filteredOrders);
}
//écoute des changements
filterName.addEventListener("input", applyFilters);
filterStatus.addEventListener("change", applyFilters);
// rempli le select des statuts
function fillStatusFilter(orders) {
  filterStatus.innerHTML = `<option value="">Tous les statuts</option>`;

  const status = [...new Set(orders.map(order => order.status))];

  status.forEach(status => {
    const option = document.createElement("option");
    option.value = status;
    option.textContent = status;
    filterStatus.appendChild(option);
  });
}
// affichage desktop + mobile
function displayOrders(orders) {
  tbody.innerHTML = "";
  mobileContainer.innerHTML = "";

  if (orders.length === 0) {
    tbody.innerHTML = `
      <tr>
        <td colspan="10" class="text-center">Aucune commande ne correspond aux filtres.</td>
      </tr>
    `;

    mobileContainer.innerHTML = `
      <p class="text-center">Aucune commande ne correspond aux filtres.</p>
    `;
    return;
  }
  orders.forEach(order => {
    displayOrderRow(order);
    displayOrderCard(order);
  });
}
// affichage des ligne desktop
function displayOrderRow(order) {
  const tr = document.createElement("tr");

  // Date
  const tdDate = document.createElement("td");
  tdDate.textContent = order.service_date;
  tr.appendChild(tdDate);

  // Nom prénom
  const tdName = document.createElement("td");
  tdName.textContent = `${order.user_first_name} ${order.user_last_name}`;
  tr.appendChild(tdName);

  // Email
  const tdEmail = document.createElement("td");
  tdEmail.textContent = order.user_email;
  tr.appendChild(tdEmail);

  // Téléphone
  const tdPhone = document.createElement("td");
  tdPhone.textContent = order.user_phone;
  tr.appendChild(tdPhone);

  // Nombre de personnes
  const tdPeople = document.createElement("td");
  tdPeople.textContent = `${order.number_of_people} pers`;
  tr.appendChild(tdPeople);

  // Prix total
  const tdTotal = document.createElement("td");
  tdTotal.textContent = `${order.total_amount} €`;
  tr.appendChild(tdTotal);

  // Prêt matériel
  const tdLoan = document.createElement("td");
  tdLoan.appendChild(booleanIcon(order.equipment_loan));
  tr.appendChild(tdLoan);

  // Retour matériel
  const tdReturn = document.createElement("td");
  tdReturn.appendChild(booleanIcon(order.equipment_return));
  tr.appendChild(tdReturn);

  // Statut
  const tdStatus = document.createElement("td");

  if (order.status === "annulée") {
    const badge = document.createElement("span");
    badge.className = "badge bg-danger";
    badge.textContent = "Commande annulée";

    tdStatus.appendChild(badge);
  } else {
    tdStatus.textContent = order.status;
  }
  tr.appendChild(tdStatus);

  const tdAction = document.createElement("td");
  tdAction.appendChild(createActionButton(order));
  tr.appendChild(tdAction);

  tbody.appendChild(tr);
}
// affichage des lignes mobile
function displayOrderCard(order) {
  const card = document.createElement("div");
  card.className = "card mb-3";

  const cardBody = document.createElement("div");
  cardBody.className = "card-body bgc-secondary text-center";

  // Date
  const date = document.createElement("p");
  date.textContent = `Date évènement : ${order.service_date}`;
  cardBody.appendChild(date);

  // Nom prénom
  const name = document.createElement("p");
  name.textContent = `Nom : ${order.user_first_name} ${order.user_last_name}`;
 cardBody.appendChild(name);

  // Email
  const email = document.createElement("p");
  email.textContent = `Email : ${order.user_email}`;
  cardBody.appendChild(email);

  // Téléphone
  const phone = document.createElement("p");
  phone.textContent = `Téléphone : ${order.user_phone}`;
 cardBody.appendChild(phone);

  // Nombre de personnes
  const people = document.createElement("p");
  people.textContent = `Nombre de personnes : ${order.number_of_people} pers`;
  cardBody.appendChild(people);

  // Prix total
  const total = document.createElement("p");
  total.textContent = `Prix : ${order.total_amount} €`;
  cardBody.appendChild(total);

  // Prêt matériel
  const loan = document.createElement("p");
  loan.append("Matériel loué : ");
  loan.appendChild(booleanIcon(order.equipment_loan));
  cardBody.appendChild(loan);

  // Retour matériel
  const returnMaterial = document.createElement("p");
  returnMaterial.append("Retour matériel : ");
  returnMaterial.appendChild(booleanIcon(order.equipment_return));
  cardBody.appendChild(returnMaterial);

  // Statut
  const status = document.createElement("p");

  if (order.status === "annulée") {
    const badge = document.createElement("span");
    badge.className = "badge bg-danger";
    badge.textContent = "Commande annulée";

    status.appendChild(badge);
  } else {
    status.textContent = order.status;
  }
  cardBody.appendChild(status);

  const actionContainer = document.createElement("div");
  actionContainer.className = "card-action";
  actionContainer.appendChild(createActionButton(order));
  cardBody.appendChild (actionContainer)
  
  card.appendChild(cardBody);
  mobileContainer.appendChild(card);
}
// création du bouton d'action
function createActionButton(order) {
  const button = document.createElement("button");
  button.type = "button";
  button.className = "btn btn-sm";

  if (order.status === "annulée") {
    button.classList.add("btn-secondary", "cancellationDetailBtn");
    button.dataset.reason = order.cancellation_reason ?? "";
    button.dataset.contact = order.contact_mode ?? "";
    button.textContent = "Voir motif";
  } else {
    button.classList.add("btn-primary", "statusChangeBtn");
    button.dataset.id = order.id;
    button.dataset.status = order.status;
    button.textContent = "Changer statut";
  }
  return button;
}
// ouvrir la modale statut
document.addEventListener("click", (event) => {
  const button = event.target.closest(".statusChangeBtn");
  if (!button) return;

  document.getElementById("statusOrderId").value = button.dataset.id;
  document.getElementById("newStatus").value = button.dataset.status;

  document.getElementById("statusContactMode").value = "";
  document.getElementById("statusCancellationReason").value = "";
  document.getElementById("cancellationFields").classList.add("d-none");

  const modal = new bootstrap.Modal(document.getElementById("StatusChangeModal"));
  modal.show();
});
// afficher les champs seulement si annulée
document.getElementById("newStatus").addEventListener("change", (event) => {
  const cancellationFields = document.getElementById("cancellationFields");

  if (event.target.value === "annulée") {
    cancellationFields.classList.remove("d-none");
  } else {
    cancellationFields.classList.add("d-none");
  }
});
// ouvrir la modale détail annulation
document.addEventListener("click", (event) => {
  const button = event.target.closest(".cancellationDetailBtn");
  if (!button) return;

  document.getElementById("cancellationReason").textContent =
    button.dataset.reason || "Non renseigné";

  document.getElementById("cancellationContact").textContent =
    button.dataset.contact || "Non renseigné";

  const modal = new bootstrap.Modal(document.getElementById("cancellationModal"));
  modal.show();
});
// envoyer le changement de statut en BDD
document.getElementById("confirmStatusChangeBtn").addEventListener("click", async () => {
const orderId = document.getElementById("statusOrderId").value;
const status = document.getElementById("newStatus").value;
const contactMode = document.getElementById("statusContactMode").value;
const reason = document.getElementById("statusCancellationReason").value.trim();

if (status === "annulée" && (!contactMode || !reason)) {
  showMessage("Le motif et le mode de contact sont obligatoires", "warning");
  return;
}

try {
  await secureFetch(`http://localhost:8082/order/${orderId}/updateStatus`, {
    method: "PUT",
    body: JSON.stringify({
      status,
      cancellation_reason: status === "annulée" ? reason : null,
      contact_mode: status === "annulée" ? contactMode : null
    })
  }, ["admin", "employé"]);

  showMessage("Statut mis à jour", "success");

  const modal = bootstrap.Modal.getInstance(document.getElementById("StatusChangeModal"));
  modal.hide();

  loadOrders();

    } catch (error) {
      showMessage(error.message, "error");
    }
  });