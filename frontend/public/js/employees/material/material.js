// fonction pour charger le matériel
async function loadMaterial() {
  try {
    // Appel sécurisé vers l'API (GET: fonction dans api.js)
    const data = await getMaterial();
    // dekstop: vide le tableau
    const tbody = document.querySelector('tbody');
    tbody.innerHTML = '';

    //mobile
    const mobileContainer = document.getElementById('mobile-container');
    mobileContainer.innerHTML = '';

    // boucle sur chaque matériel reçu
    data.forEach(material => {
      // desktop: création d'une ligne
      const tr = document.createElement('tr');
      //inject dans le DOM
      // Nom
      const tdName = document.createElement("td");
      tdName.textContent = material.material_name;
      tr.appendChild(tdName);
      // quantité disponible
      const tdQuantity = document.createElement("td");
      tdQuantity.textContent = material.quantity_available;
      tr.appendChild(tdQuantity);
      // prix
      const tdPrice = document.createElement("td");
      tdPrice.textContent = `${material.price} €`;
      tr.appendChild(tdPrice);
      // catégorie
      const tdCategory = document.createElement("td");
      tdCategory.textContent =material.material_category_name;
      tr.appendChild(tdCategory);
      // Actions (récupère la fonction dans utils.js pour créer les boutons d'action)
      const tdAction = document.createElement("td");
      tdAction.appendChild(createActionButtons(material.id));
      tr.appendChild(tdAction);

      // ajout dans le DOM
      tbody.appendChild(tr);

      //mobile: boucle pour afficher les cartes de tous le matériel
      const card = document.createElement('div');
      card.className = 'card mb-3';

      const cardBody = document.createElement('div');
      cardBody.className = 'card-body bgc-secondary text-center';
      // nom
      const name = document.createElement('h2');
      name.textContent = material.material_name;
      cardBody.appendChild(name);
      // quantité disponible
      const quantity = document.createElement('p');
      quantity.textContent = `${material.quantity_available} disponible(s)`;
      cardBody.appendChild(quantity);
      // prix
      const price = document.createElement('p');
      price.textContent = `${material.price} €`;
      cardBody.appendChild(price);
      // catégorie
      const category = document.createElement('p');
      category.textContent = `Catégorie: ${material.material_category_name}`;
      cardBody.appendChild(category);
      // Actions (récupère la fonction dans utils.js pour créer les boutons d'action)
      const cardAction = document.createElement('p');
      cardAction.appendChild(createActionButtons(material.id));
      cardBody.appendChild(cardAction);

      card.appendChild(cardBody);
      mobileContainer.appendChild(card);
    });

    } catch (error) {
      // affiche l'erreur si problème API
      showMessage("Une erreur est survenue", "danger");
  }
}
// se lance au chargement
loadMaterial();

// modifier: aller sur la page createMaterial pour modifier le matériel
document.addEventListener("click", (e) => {
  // vérifie si on a cliqué sur un bouton "modifier"
  if (e.target.closest(".editBtn")) {
    const button = e.target.closest(".editBtn");
    // récupère l'id du matériel
    const materialId = button.dataset.id;
    // redirection avec id dans l'URL
    window.location.href = `/createMaterial?id=${materialId}`;
  }
});

//supprimer le matériel
document.addEventListener("click", async (e) => {

  // vérifie si bouton supprimer
  if (!e.target.closest(".deleteBtn")) return;

  const button = e.target.closest(".deleteBtn");

  // récupère l'id
  const materialId = button.dataset.id;

  // confirmation utilisateur
  if (!confirm("Êtes-vous sûr de vouloir supprimer ce matériel ?")) return;

  try {
    // appel API DELETE
    await secureFetch(
      `${API_URL}/material/${materialId}`,
      { method: 'DELETE' },
      ['employee', 'admin']
    );

    // message succès
    showMessage("Matériel supprimé avec succès", "success");

    // recharge la liste
    loadMaterial();

  } catch (error) {
    showMessage("Une erreur est survenue", "danger");
  }
});