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
      <div class"p-2">
        <button class="btn btn-secondary btn-sm">Valider</button>
        <button class="btn btn-primary btn-sm">Supprimer</button>
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

// tableau des commandes
// fonction pour charger les commnades
async function loadOrders() {
  try {
    // Appel sécurisé vers l'API (GET: fonction dans api.js)
    const data = await getOrders();
    // dekstop: vide le tableau
    const tbody = document.querySelector('tbody');
    tbody.innerHTML = '';

    //mobile
    const mobileContainer = document.getElementById('mobile-container');
    mobileContainer.innerHTML = '';

    // Boucle sur chaque commande reçu
    data.forEach(order => {
      // Création d'une ligne
      const tr = document.createElement('tr');
      //inject dans le DOM
      // Date
      const tdDate = document.createElement("td");
      tdDate.textContent = order.service_date;
      tr.appendChild(tdDate);
      // nom
      const tdName = document.createElement("td");
      tdName.textContent = `${order.user_first_name} ${order.user_last_name}`;
      tr.appendChild(tdName);
      // email
      const tdMail = document.createElement("td");
      tdMail.textContent = order.user_email;
      tr.appendChild(tdMail);
      // téléphone
      const tdPhone = document.createElement("td");
      tdPhone.textContent = order.user_phone;
      tr.appendChild(tdPhone);
      // Nombre de persones
      const tdPeople = document.createElement("td");
      tdPeople.textContent = `${order.number_of_people} pers`;
      tr.appendChild(tdPeople);
      // prix
      const tdPrice = document.createElement("td");
      tdPrice.textContent = `${order.total_amount} €`;
      tr.appendChild(tdPrice);
      // pret matériel
      const tdEquipmentLoan = document.createElement("td");
      tdEquipmentLoan.innerHTML = order.equipment_loan
      ? '<i class="bi bi-check-circle-fill text-success"></i>'
      : '<i class="bi bi-x-circle-fill text-danger"></i>';
      tr.appendChild(tdEquipmentLoan);
      // retour matériel
      const tdEquipmentReturn = document.createElement("td");
      tdEquipmentReturn.innerHTML = order.equipment_return
        ? '<i class="bi bi-check-circle-fill text-success"></i>'
        : '<i class="bi bi-x-circle-fill text-danger"></i>';
      tr.appendChild(tdEquipmentReturn);
      // Statut
      const tdStatus = document.createElement("td");
      if (order.status === "annulée") {
        tdStatus.innerHTML = `<span class="badge bg-danger">Commande annulée</span>`;
      } else {
        tdStatus.innerHTML = order.status;
      }
      tr.appendChild(tdStatus);
      // Action 
      const tdAction = document.createElement("td");
      if (order.status === "annulée") {
        const detailBtn = document.createElement("button");
        detailBtn.type = "button";
        detailBtn.className = "btn btn-secondary btn-sm cancellationDetailBtn";
        detailBtn.dataset.reason = order.cancellation_reason ?? "";
        detailBtn.dataset.contact = order.contact_mode ?? "";
        detailBtn.textContent = "Voir motif";

        tdAction.appendChild(detailBtn);
      } else {
        const statusBtn = document.createElement("button");
        statusBtn.type = "button";
        statusBtn.className = "btn btn-primary btn-sm statusChangeBtn";
        statusBtn.dataset.id = order.id;
        statusBtn.dataset.status = order.status;
        statusBtn.textContent = "Changer statut";

        tdAction.appendChild(statusBtn);
      }

      tr.appendChild(tdAction);
      // Ajout dans le DOM
      tbody.appendChild(tr);

      //mobile: boucle pour afficher les cartes de toutes les commandes
      const card = document.createElement('div');
      card.className = 'card mb-3';

      const cardBody = document.createElement('div');
      cardBody.className = 'card-body bgc-secondary text-center';
      // Date
      const cardDate = document.createElement('p');
      cardDate.textContent = `Date évènement : ${order.service_date}`;
      cardBody.appendChild(cardDate);
      // nom
      const cardName = document.createElement("p");
      cardName.textContent = `nom : ${order.user_first_name} ${order.user_last_name}`;
      cardBody.appendChild(cardName);
      // email
      const cardMail = document.createElement("p");
      cardMail.textContent = `email : ${order.user_email}`;
      cardBody.appendChild(cardMail);
      // téléphone
      const cardPhone = document.createElement("p");
      cardPhone.textContent = `téléphone : ${order.user_phone}`;
      cardBody.appendChild(cardPhone);
      // Nombre de personnes
      const cardPeople = document.createElement('p');
      cardPeople.textContent = `Nombre de personnes : ${order.number_of_people} pers`;
      cardBody.appendChild(cardPeople);
      // Prix
      const cardPrice = document.createElement('p');
      cardPrice.textContent = `Prix : ${order.total_amount} €`;
      cardBody.appendChild(cardPrice);
      // pret matériel
      const cardEquipmentLoan = document.createElement("p");
      cardEquipmentLoan.innerHTML = `matériel loué : ${booleanIcon(order.equipment_loan)}`;
      cardBody.appendChild(cardEquipmentLoan);
      // retour matériel
      const cardEquipmentReturn = document.createElement("p");
      cardEquipmentReturn.innerHTML = `retour matériel : ${booleanIcon(order.equipment_return)}`;
      cardBody.appendChild(cardEquipmentReturn);
         // Statut
      const cardStatus = document.createElement("p");
       if (order.status === "annulée") {
        cardStatus.innerHTML = `statut : <span class="badge bg-danger">Commande annulée</span>`;
      } else {
        cardStatus.innerHTML = `statut : ${order.status}`;
      }
      cardBody.appendChild(cardStatus);
      //action
      const cardAction = document.createElement("p");

      if (order.status === "annulée") {
        const cardDetailBtn = document.createElement("button");
        cardDetailBtn.type = "button";
        cardDetailBtn.className = "btn btn-secondary btn-sm cancellationDetailBtn";
        cardDetailBtn.dataset.reason = order.cancellation_reason ?? "";
        cardDetailBtn.dataset.contact = order.contact_mode ?? "";
        cardDetailBtn.textContent = "Voir motif";

        cardAction.appendChild(cardDetailBtn);
      } else {
        const cardStatusBtn = document.createElement("button");
        cardStatusBtn.type = "button";
        cardStatusBtn.className = "btn btn-primary btn-sm statusChangeBtn";
        cardStatusBtn.dataset.id = order.id;
        cardStatusBtn.dataset.status = order.status;
        cardStatusBtn.textContent = "Changer statut";

        cardAction.appendChild(cardStatusBtn);
      }
      cardBody.appendChild(cardAction);
      card.appendChild(cardBody);
      mobileContainer.appendChild(card);
    });
  } catch (error) {
    // Affiche l'erreur si problème API
    alert(error.message);
  }
}
loadOrders();
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