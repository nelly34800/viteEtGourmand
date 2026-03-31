// fonction pour charger les régimes alimentaires
async function loadDiets() {
  try {
    // Appel sécurisé vers l'API (GET)
    const data = await secureFetch(
      'http://localhost:8082/diet',
      { method: 'GET' },
      ['employee', 'admin'] // seuls ces rôles peuvent accéder
    );
    // vide le tableau
    const tbody = document.querySelector('tbody');
    tbody.innerHTML = '';
    // Boucle sur chaque régime alimentaire reçu
    data.forEach(diet => {
      // Création d'une ligne
      const tr = document.createElement('tr');
      //inject dans le DOM
      tr.innerHTML = `
        <td>${diet.diet_name}</td>
        <td>
          <button class="btn btn-secondary editBtn m-1" data-id="${diet.id}">modifier</button>
          <button class="btn btn-danger deleteBtn m-1" data-id="${diet.id}" aria-label="Supprimer"><i class="bi bi-trash"></i></button>
        </td>
      `;
      // Ajout dans le DOM
      tbody.appendChild(tr);
    });
    } catch (error) {
      // Affiche l'erreur si problème API
      alert(error.message);
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
      `http://localhost:8082/diet/${dietId}`,
      { method: 'DELETE' },
      ['employee', 'admin']
    );

    // Message succès
    alert("Régime alimentaire supprimé");

    // Recharge la liste
    loadDiets();

  } catch (error) {
    alert(error.message);
  }
});