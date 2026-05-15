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
      alert(error.message);
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
    alert(error.message);
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
    alert(error.message);
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

  tr.innerHTML = `
    <td>${order.service_date}</td>
    <td>${order.user_first_name} ${order.user_last_name}</td>
    <td>${order.user_email}</td>
    <td>${order.user_phone}</td>
    <td>${order.number_of_people} pers</td>
    <td>${order.total_amount} €</td>
    <td>${booleanIcon(order.equipment_loan)}</td>
    <td>${booleanIcon(order.equipment_return)}</td>
    <td>
      ${
        order.status === "annulée"
          ? `<span class="badge bg-danger">Commande annulée</span>`
          : order.status
      }
    </td>
    <td></td>
  `;

  const tdAction = tr.querySelector("td:last-child");
  tdAction.appendChild(createActionButton(order));

  tbody.appendChild(tr);
}
// affichage des lignes mobile
function displayOrderCard(order) {
  const card = document.createElement("div");
  card.className = "card mb-3";

  card.innerHTML = `
    <div class="card-body bgc-secondary text-center">
      <p>Date évènement : ${order.service_date}</p>
      <p>Nom : ${order.user_first_name} ${order.user_last_name}</p>
      <p>Email : ${order.user_email}</p>
      <p>Téléphone : ${order.user_phone}</p>
      <p>Nombre de personnes : ${order.number_of_people} pers</p>
      <p>Prix : ${order.total_amount} €</p>
      <p>Matériel loué : ${booleanIcon(order.equipment_loan)}</p>
      <p>Retour matériel : ${booleanIcon(order.equipment_return)}</p>
      <p>
        Statut :
        ${
          order.status === "annulée"
            ? `<span class="badge bg-danger">Commande annulée</span>`
            : order.status
        }
      </p>
      <div class="card-action"></div>
    </div>
  `;

  const actionContainer = card.querySelector(".card-action");
  actionContainer.appendChild(createActionButton(order));

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