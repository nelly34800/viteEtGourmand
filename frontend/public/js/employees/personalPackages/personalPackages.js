// fonction pour charger les forfaits de personnel
async function loadPersonalsPackage() {
  try {
    // Appel sécurisé vers l'API (GET: fonction dans api.js)
    const data = await getPersonalPackages();
    // dekstop: vide le tableau
    const tbody = document.querySelector('tbody');
    tbody.innerHTML = '';

    //mobile
    const mobileContainer = document.getElementById('mobile-container');
    mobileContainer.innerHTML = '';

    // Boucle sur chaque forfait de personnel reçu
    data.forEach(personalPackage => {
      //  desktop: Création d'une ligne
      const tr = document.createElement('tr');
      //inject dans le DOM
      // Événement
      const tdEvent = document.createElement("td");
      tdEvent.textContent = personalPackage.event_type;
      tr.appendChild(tdEvent);
      // Ratio personnel
      const tdRatio = document.createElement("td");
      tdRatio.textContent = `1 pour ${personalPackage.staff_ratio} personnes`;
      tr.appendChild(tdRatio);
      // Prix
      const tdPrice = document.createElement("td");
      tdPrice.textContent = `${personalPackage.package_price}€`;
      tr.appendChild(tdPrice);
      // Actions
      const tdAction = document.createElement("td");
      tdAction.appendChild(createActionButtons(personalPackage.id));
       tr.appendChild(tdAction);

      // Ajout dans le DOM
      tbody.appendChild(tr);

      //mobile: boucle pour afficher les cartes de tous les forfaits de personnel
      const card = document.createElement('div');
      card.className = 'card mb-3';

      const cardBody = document.createElement('div');
      cardBody.className = 'card-body bgc-secondary text-center';
      // Event
      const cardEvent = document.createElement('p');
      cardEvent.textContent = personalPackage.event_type;
      cardBody.appendChild(cardEvent);
      // Ratio
      const cardRatio = document.createElement('p');
      cardRatio.textContent = `1 pour ${personalPackage.staff_ratio} personnes`;
      cardBody.appendChild(cardRatio);
      // Prix
      const cardPrice = document.createElement('p');
      cardPrice.textContent = `${personalPackage.package_price}€`;
      cardBody.appendChild(cardPrice);
      // Actions (récupère la fonction dans utils.js pour créer les boutons d'action)
      const action = document.createElement('p');
      action.appendChild(createActionButtons(personalPackage.id));
      cardBody.appendChild(action);

      card.appendChild(cardBody);
      mobileContainer.appendChild(card);
    });

    } catch (error) {
      // Affiche l'erreur si problème API
      showMessage("Une erreur est survenue", "danger");
  }
}
// se lance au chargement
loadPersonalsPackage();

// modifier: aller sur la page createPersonalPackage pour modifier le forfait de personnel
document.addEventListener("click", (e) => {
  // Vérifie si on a cliqué sur un bouton "modifier"
  if (e.target.closest(".editBtn")) {
    const button = e.target.closest(".editBtn");
    // Récupère l'id de le forfait de personnel
    const personalPackageId = button.dataset.id;
    // redirection avec id dans l'URL
    window.location.href = `/createPersonalPackage?id=${personalPackageId}`;
  }
});

//supprimer le forfait de personnel
document.addEventListener("click", async (e) => {

  // Vérifie si bouton supprimer
  if (!e.target.closest(".deleteBtn")) return;

  const button = e.target.closest(".deleteBtn");

  // Récupère l'id
  const personalPackageId = button.dataset.id;

  // Confirmation
  if (!confirm("Supprimer ce forfait de personnel ?")) return;

  try {
    // Appel API DELETE
    await secureFetch(
      `${API_URL}/personalPackage/${personalPackageId}`,
      { method: 'DELETE' },
      ['employee', 'admin']
    );

    // Message succès
    showMessage("forfait de personnel supprimé avec succès");

    // Recharge la liste
    loadPersonalsPackage();

  } catch (error) {
    showMessage("Une erreur est survenue", "danger");
  }
});