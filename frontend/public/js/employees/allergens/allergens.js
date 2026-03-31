// fonction pour charger les allergènes
async function loadAllergens() {
  try {
    // Appel sécurisé vers l'API (GET)
    const data = await secureFetch(
      'http://localhost:8082/allergen',
      { method: 'GET' },
      ['employee', 'admin'] // seuls ces rôles peuvent accéder
    );
    // vide le tableau
    const tbody = document.querySelector('tbody');
    tbody.innerHTML = '';
    // Boucle sur chaque allergène reçu
    data.forEach(allergen => {
      // Création d'une ligne
      const tr = document.createElement('tr');
      //inject dans le DOM
      tr.innerHTML = `
        <td>${allergen.allergen_name}</td>
        <td>
          <button class="btn btn-secondary editBtn m-1" data-id="${allergen.id}">modifier</button>
          <button class="btn btn-danger deleteBtn m-1" data-id="${allergen.id}" aria-label="Supprimer"><i class="bi bi-trash"></i></button>
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
    alert("Allergène supprimé");

    // Recharge la liste
    loadAllergens();

  } catch (error) {
    alert(error.message);
  }
});