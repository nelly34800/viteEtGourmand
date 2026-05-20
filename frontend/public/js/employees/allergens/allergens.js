// fonction pour charger les allergènes
async function loadAllergens() {
  try {
    // Appel sécurisé vers l'API (GET: fonction dans api.js)
    const data = await getAllergens();
    // vide le tableau
    const tbody = document.querySelector('tbody');
    tbody.innerHTML = '';
    // Boucle sur chaque allergène reçu
    data.forEach(allergen => {
      // Création d'une ligne
      const tr = document.createElement('tr');
      //inject dans le DOM
      // Nom
      const tdName = document.createElement("td");
      tdName.textContent = allergen.allergen_name;
      tr.appendChild(tdName);
      // Actions (récupère la fonction dans utils.js pour créer les boutons d'action)
      const tdAction = document.createElement("td");
      tdAction.appendChild(createActionButtons(allergen.id));
      tr.appendChild(tdAction);
      // Ajout dans le DOM
      tbody.appendChild(tr);
    });
    } catch (error) {
      // Affiche l'erreur si problème API
      showMessage("Une erreur est survenue", "danger");
  }
}
loadAllergens();

// modifier: aller sur la page createAllergen pour modifier l'allergène
document.addEventListener("click", (e) => {
  // Vérifie si on a cliqué sur un bouton "modifier"
  if (e.target.closest(".editBtn")) {
    const button = e.target.closest(".editBtn");
    // Récupère l'id de l'allergène
    const allergenId = button.dataset.id;
    // redirection avec id dans l'URL
    window.location.href = `/createAllergen?id=${allergenId}`;
  }
});

//supprimer l'allergène
document.addEventListener("click", async (e) => {

  // Vérifie si bouton supprimer
  if (!e.target.closest(".deleteBtn")) return;

  const button = e.target.closest(".deleteBtn");

  // Récupère l'id
  const allergenId = button.dataset.id;

  // Confirmation utilisateur
  if (!confirm("Supprimer cet allergène ?")) return;

  try {
    // Appel API DELETE
    await secureFetch(
      `http://localhost:8082/allergen/${allergenId}`,
      { method: 'DELETE' },
      ['employee', 'admin']
    );

    // Message succès
    alert("Allergène supprimé avec succès");

    // Recharge la liste
    loadAllergens();
 
  } catch (error) {
    showMessage("Une erreur est survenue", "danger");
  }
});