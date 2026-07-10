// fonction pour charger les régimes alimentaires
async function loadDiets() {
  try {
    // Appel sécurisé vers l'API (GET: fonction dans api.js)
    const data = await getDiets();
    // vide le tableau
    const tbody = document.querySelector('tbody');
    tbody.innerHTML = '';
    // Boucle sur chaque régime alimentaire reçu
    data.forEach(diet => {
      // Création d'une ligne
      const tr = document.createElement('tr');
      //inject dans le DOM
      // Nom
      const tdName = document.createElement("td");
      tdName.textContent = diet.diet_name;
      tr.appendChild(tdName);
      // Actions (récupère la fonction dans utils.js pour créer les boutons d'action)
      const tdAction = document.createElement("td");
      tdAction.appendChild(createActionButtons(diet.id));
      tr.appendChild(tdAction);
      // Ajout dans le DOM
      tbody.appendChild(tr);
    });
    } catch (error) {
      // Affiche l'erreur si problème API
      showMessage("Une erreur est survenue", "danger");
  }
}
loadDiets();

// modifier: aller sur la page createDiet pour modifier le régime alimentaire
document.addEventListener("click", (e) => {
  // Vérifie si on a cliqué sur un bouton "modifier"
  if (e.target.closest(".editBtn")) {
    const button = e.target.closest(".editBtn");
    // Récupère l'id du régime alimentaire
    const dietId = button.dataset.id;
    // redirection avec id dans l'URL
    window.location.href = `/createDiet?id=${dietId}`;
  }
});

//supprimer le régime alimentaire
document.addEventListener("click", async (e) => {

  // Vérifie si bouton supprimer
  if (!e.target.closest(".deleteBtn")) return;

  const button = e.target.closest(".deleteBtn");

  // Récupère l'id
  const dietId = button.dataset.id;

  // Confirmation utilisateur
  if (!confirm("Supprimer ce régime alimentaire ?")) return;

  try {
    // Appel API DELETE
    await secureFetch(
      `${API_URL}/diet/${dietId}`,
      { method: 'DELETE' },
      ['employee', 'admin']
    );

    // Message succès
    showMessage("Régime alimentaire supprimé avec succès", "success");

    // Recharge la liste
    loadDiets();

  } catch (error) {
    showMessage("Une erreur est survenue", "danger");
  }
});