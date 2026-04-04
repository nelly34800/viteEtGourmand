// fonction pour charger les forfaits de boissons
async function loadDrinksPackage() {
  try {
    // Appel sécurisé vers l'API (GET)
    const data = await secureFetch(
      'http://localhost:8082/drinkPackage',
      { method: 'GET' },
      ['employee', 'admin'] // seuls ces rôles peuvent accéder
    );
    // vide le tableau
    const tbody = document.querySelector('tbody');
    tbody.innerHTML = '';
    // Boucle sur chaque forfait de boissons reçu
    data.forEach(drinkPackage => {
      // Création d'une ligne
      const tr = document.createElement('tr');
      //inject dans le DOM
      // Nom
      const tdName = document.createElement("td");
      tdName.textContent = drinkPackage.drink_package_name;
      tr.appendChild(tdName);
      // Prix
      const tdPrice = document.createElement("td");
      tdPrice.textContent = `${drinkPackage.price_per_person}€`;
      tr.appendChild(tdPrice);
      // Actions
      const tdAction = document.createElement("td");
      tdAction.appendChild(createActionButtons(drinkPackage.id));
      tr.appendChild(tdAction);

      // Ajout dans le DOM
      tbody.appendChild(tr);
    });
    } catch (error) {
      // Affiche l'erreur si problème API
      alert(error.message);
  }
}
loadDrinksPackage();

// modifier: aller sur la page createDrinkPackage pour modifier le forfait de boissons
document.addEventListener("click", (e) => {
  // Vérifie si on a cliqué sur un bouton "modifier"
  if (e.target.closest(".editBtn")) {
    const button = e.target.closest(".editBtn");
    // Récupère l'id du régime alimentaire
    const drinkPackageId = button.dataset.id;
    // redirection avec id dans l'URL
    window.location.href = `/createDrinkPackage?id=${drinkPackageId}`;
  }
});

//supprimer le forfait de boisson
document.addEventListener("click", async (e) => {

  // Vérifie si bouton supprimer
  if (!e.target.closest(".deleteBtn")) return;

  const button = e.target.closest(".deleteBtn");

  // Récupère l'id
  const drinkPackageId = button.dataset.id;

  // Confirmation
  if (!confirm("Supprimer ce forfait de boisson ?")) return;

  try {
    // Appel API DELETE
    await secureFetch(
      `http://localhost:8082/drinkPackage/${drinkPackageId}`,
      { method: 'DELETE' },
      ['employee', 'admin']
    );

    // Message succès
    alert("forfait de boisson supprimé avec succès");

    // Recharge la liste
    loadDrinksPackage();

  } catch (error) {
    alert(error.message);
  }
});