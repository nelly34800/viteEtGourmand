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
      // Nombre de persones
      const tdPeople = document.createElement("td");
      tdPeople.textContent = `${order.number_of_people} pers`;
      tr.appendChild(tdPeople);
      // prix
      const tdPrice = document.createElement("td");
      tdPrice.textContent = `${order.total_amount} €`;
      tr.appendChild(tdPrice);
      // Statut
      const tdStatus = document.createElement("td");
      tdStatus.textContent = order.status;
      tr.appendChild(tdStatus);
      // détails
      const tdDetails = document.createElement("td");
      const detailsBtn = document.createElement("button");
        detailsBtn.type = "button";
        detailsBtn.className = "btn btn-primary btn-sm orderDetailModalBtn m-1";
        detailsBtn.dataset.id = order.id;
        detailsBtn.textContent = "Détails";

        tdDetails.appendChild(detailsBtn);
      tr.appendChild(tdDetails);
      // Actions (récupère la fonction dans utils.js pour créer les boutons d'action)
      const tdAction = document.createElement("td");
      tdAction.appendChild(createActionButtons(order.id));
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
      // Nombre de personnes
      const cardPeople = document.createElement('p');
      cardPeople.textContent = `Nombre de personnes : ${order.number_of_people} pers`;
      cardBody.appendChild(cardPeople);
      // Prix
      const cardPrice = document.createElement('p');
      cardPrice.textContent = `Prix : ${order.total_amount} €`;
      cardBody.appendChild(cardPrice);
      // détails
      const cardDetails = document.createElement("p");
      const cardDetailsBtn = document.createElement("button");
        cardDetailsBtn.type = "button";
        cardDetailsBtn.className = "btn btn-primary btn-sm orderDetailModalBtn m-1";
        detailsBtn.dataset.id = order.id;
        cardDetailsBtn.textContent = "Détail";

        cardDetails.appendChild(cardDetailsBtn);
      cardBody.appendChild(cardDetails);
         // Statut
      const cardStatus = document.createElement("p");
      cardStatus.textContent = order.status;
      cardBody.appendChild(cardStatus);
      // Actions (récupère la fonction dans utils.js pour créer les boutons d'action)
      if (order.status === "en attente") {
          // bouton modifier + supprimer seulement si statut en attente
          const action = document.createElement('p');
          action.appendChild(createActionButtons(order.id));
          cardBody.appendChild(action);
      }

      card.appendChild(cardBody);
      mobileContainer.appendChild(card);
    });
 
    } catch (error) {
      // Affiche l'erreur si problème API
      alert(error.message);
  }
}
loadOrders();
// ouvrir la modale avec les infos de la commande
document.addEventListener("click", async (event) => {
  const button = event.target.closest(".orderDetailModalBtn");
  if (!button) return;

  const orderId = button.dataset.id;

  try {
    const order = await secureFetch(`http://localhost:8082/order/${orderId}`, {
      method: "GET"
    }, ["client", "employé", "admin"]);

    document.getElementById("OrderdateDetail").textContent = order.order_date;
    document.getElementById("serviceDateDetail").textContent = order.service_date;
    document.getElementById("deliveryAddressDetail").textContent =
      `${order.delivery_address}, ${order.postal_code} ${order.city}`;

    document.getElementById("numberOfPeopleDetail").textContent =
      `${order.number_of_people} personnes`;

    document.getElementById("deliveryChargesDetail").textContent =
      `${order.delivery_charges} €`;

    document.getElementById("totalAmountDetail").textContent =
      `${order.total_amount} €`;

    renderDetailItems(order);

    const modal = new bootstrap.Modal(document.getElementById("orderDetailModal"));
    modal.show();

  } catch (error) {
    console.error("Erreur détail commande :", error);
    showMessage("Impossible de charger le détail de la commande", "error");
  }
});
// affiche les menus, forfaits, matériel dans la modale
function renderDetailItems(order) {
  const container = document.getElementById("orderItemsDetail");
  container.innerHTML = "";

  const sections = [
    { title: "Menus", items: order.menus || [] },
    { title: "Forfaits boissons", items: order.drink_packages || [] },
    { title: "Forfaits personnel", items: order.personal_packages || [] },
    { title: "Matériel", items: order.materials || [] }
  ];

  sections.forEach(section => {
    if (section.items.length === 0) return;

    const title = document.createElement("h4");
    title.textContent = section.title;
    container.appendChild(title);

    section.items.forEach(item => {
      const p = document.createElement("p");

      p.innerHTML = `
        <strong>${item.name}</strong><br>
        Quantité : ${item.number}<br>
        Prix unitaire : ${item.price} €<br>
        ${item.discount ? `Remise : -${item.discount} €<br>` : ""}
        Sous-total : ${item.subtotal} €
      `;

      container.appendChild(p);
    });
  });
}

// modifier: aller sur la page editOrder pour modifier la commande
document.addEventListener("click", (e) => {
  // Vérifie si on a cliqué sur un bouton "modifier"
  if (e.target.closest(".editBtn")) {
    const button = e.target.closest(".editBtn");
    // Récupère l'id de la commande
    const orderId = button.dataset.id;
    // redirection avec id dans l'URL
    window.location.href = `/editOrder?id=${orderId}`;
  }
});

//supprimer la commande
document.addEventListener("click", async (e) => {

  // Vérifie si bouton supprimer
  if (!e.target.closest(".deleteBtn")) return;

  const button = e.target.closest(".deleteBtn");

  // Récupère l'id
  const orderId = button.dataset.id;

  // Confirmation utilisateur
  if (!confirm("Souhaitez-vous vraiment supprimer cette commande ?")) return;

  try {
    // Appel API DELETE
    await secureFetch(
      `http://localhost:8082/order/${orderId}`,
      { method: 'DELETE' },
      ['client']
    );

    // Message succès
    alert("Commande supprimé avec succès");

    // Recharge la liste
    loadOrders();

  } catch (error) {
    alert(error.message);
  }
});