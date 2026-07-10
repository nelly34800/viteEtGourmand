// fonction pour charger les conditions du menu
async function loadConditions() {
  try {

    // Appel sécurisé vers l'API (GET: fonction dans api.js)
    const data = await getConditions();
    // dekstop: vide le tableau
    const tbody = document.querySelector('tbody');
    tbody.innerHTML = '';

    //mobile
    const mobileContainer = document.getElementById('mobile-container');
    mobileContainer.innerHTML = '';

    // Boucle sur chaque condition reçu
    data.forEach(condition => {
      // desktop: Création d'une ligne
      const tr = document.createElement('tr');
      //inject dans le DOM
      // Type
      const tdType = document.createElement("td");
      tdType.textContent = condition.condition_type;
      tr.appendChild(tdType);
      // Description
      const tdDescription = document.createElement("td");
      tdDescription.textContent = condition.description;
      tr.appendChild(tdDescription);
      // Actions (récupère la fonction dans utils.js pour créer les boutons d'action)
      const tdAction = document.createElement("td");
      tdAction.appendChild(createActionButtons(condition.id));
      tr.appendChild(tdAction);
      // Ajout dans le DOM
      tbody.appendChild(tr);

      //mobile: boucle pour afficher les cartes de toutes les conditions
      const card = document.createElement('div');
      card.className = 'card mb-3';

      const cardBody = document.createElement('div');
      cardBody.className = 'card-body bgc-secondary text-center';
      // Type
      const type = document.createElement('h2');
      type.textContent = condition.condition_type;
      cardBody.appendChild(type);
      // Description
      const description = document.createElement('p');
      description.textContent = condition.description;
      cardBody.appendChild(description);
      // Actions (récupère la fonction dans utils.js pour créer les boutons d'action)
      const cardAction = document.createElement('p');
      cardAction.appendChild(createActionButtons(condition.id));
      cardBody.appendChild(cardAction);

      card.appendChild(cardBody);
      mobileContainer.appendChild(card);
    });
    } catch (error) {
      // Affiche l'erreur si problème API
      showMessage("Une erreur est survenue", "danger");
  }
}
loadConditions();

// modifier: aller sur la page createConditionMenu pour modifier la condition
document.addEventListener("click", (e) => {
  // Vérifie si on a cliqué sur un bouton "modifier"
  if (e.target.closest(".editBtn")) {
    const button = e.target.closest(".editBtn");
    // Récupère l'id de la condition
    const conditionId = button.dataset.id;
    // redirection avec id dans l'URL
    window.location.href = `/createConditionMenu?id=${conditionId}`;
  }
});

//supprimer la condition
document.addEventListener("click", async (e) => {

  // Vérifie si bouton supprimer
  if (!e.target.closest(".deleteBtn")) return;

  const button = e.target.closest(".deleteBtn");

  // Récupère l'id
  const conditionId = button.dataset.id;

  // Confirmation utilisateur
  if (!confirm("Supprimer cette condition ?")) return;

  try {
    // Appel API DELETE
    await secureFetch(
      `${API_URL}/condition/${conditionId}`,
      { method: 'DELETE' },
      ['employee', 'admin']
    );

    // Message succès
    alert("Condition supprimée avec succès");

    // Recharge la liste
    loadConditions();

  } catch (error) {
    showMessage("Une erreur est survenue", "danger");
  }
});