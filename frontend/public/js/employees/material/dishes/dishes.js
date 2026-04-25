// fonction pour charger les plats
async function loadDishes() {
  try {
    // Appel sécurisé vers l'API (GET: fonction dans api.js)
    const data = await getDishes();
    // dekstop: vide le tableau
    const tbody = document.querySelector('tbody');
    tbody.innerHTML = '';

    //mobile
    const mobileContainer = document.getElementById('mobile-container');
    mobileContainer.innerHTML = '';

    // boucle sur chaque plat reçu
    data.forEach(dish => {
      // desktop: création d'une ligne
      const tr = document.createElement('tr');
      //inject dans le DOM
      // Titre
      const tdTitle = document.createElement("td");
      tdTitle.textContent = dish.dish_title;
      tr.appendChild(tdTitle);
      // jour de description
      const tdDescription = document.createElement("td");
      tdDescription.textContent = dish.description;
      tr.appendChild(tdDescription);
      // image
      const tdPicture = document.createElement("td");
      const img = document.createElement("img");
      img.src = `/assets/img/${dish.picture}`;
      img.alt = dish.dish_title;
      img.style.width = "auto";
      img.style.height = "150px";
      tdPicture.style.display = "flex";
      tdPicture.style.justifyContent = "center";
      tdPicture.appendChild(img);
      tr.appendChild(tdPicture);
      // category
      const tdCategory = document.createElement("td");
      tdCategory.textContent = `catégorie: ${dish.category_name}`;
      tr.appendChild(tdCategory);
      // allergènes
      const tdAllergens = document.createElement("td");
      tdAllergens.textContent = `allergène(s): ${dish.allergens.map(a => a.name).join(", ")}`;
      tr.appendChild(tdAllergens);
      // régimes alimentaires
      const tdDiets = document.createElement("td");
      tdDiets.textContent = `régimes(s) alimentaire(s): ${dish.diets.map(a => a.name).join(", ")}`;
      tr.appendChild(tdDiets);
      // Actions (récupère la fonction dans utils.js pour créer les boutons d'action)
      const tdAction = document.createElement("td");
      tdAction.appendChild(createActionButtons(dish.id));
      tr.appendChild(tdAction);

      // ajout dans le DOM
      tbody.appendChild(tr);

      //mobile: boucle pour afficher les cartes de tous les plats
      const card = document.createElement('div');
      card.className = 'card mb-3';

      const cardBody = document.createElement('div');
      cardBody.className = 'card-body bgc-secondary text-center';
      // Titre
      const title = document.createElement('h2');
      title.textContent = dish.dish_title;
      cardBody.appendChild(title);
      // description
      const description = document.createElement('p');
      description.textContent = dish.description;
      cardBody.appendChild(description);
      // image
      const picture = document.createElement('td');
      const cardImg = document.createElement("img");
      cardImg.src = `/assets/img/${dish.picture}`;
      cardImg.alt = dish.dish_title;
      cardImg.style.width = "auto";
      cardImg.style.height = "150px";
      cardBody.style.display = "flex";
      cardBody.style.flexDirection = "column";
      cardBody.style.alignItems = "center";
      picture.appendChild(cardImg );
      cardBody.appendChild(picture);
      // category
      const category = document.createElement('p');
      category.textContent = `catégorie: ${dish.category_name}`;
      cardBody.appendChild(category);
      // allergènes
      const allergens = document.createElement('p');
      allergens.textContent = `allergène(s): ${dish.allergens.map(a => a.name).join(", ")}`;
      cardBody.appendChild(allergens);
      // régimes alimentaires
      const diets = document.createElement('p');
      diets.textContent = `régime(s) alimentaire(s): ${dish.diets.map(a => a.name).join(", ")}`;
      cardBody.appendChild(diets);
      // Actions (récupère la fonction dans utils.js pour créer les boutons d'action)
      const cardAction = document.createElement('p');
      cardAction.appendChild(createActionButtons(dish.id));
      cardBody.appendChild(cardAction);

      card.appendChild(cardBody);
      mobileContainer.appendChild(card);
    });

    } catch (error) {
      // affiche l'erreur si problème API
      alert(error.message);
  }
}
// se lance au chargement
loadDishes();

// modifier: aller sur la page createDish pour modifier le plat
document.addEventListener("click", (e) => {
  // vérifie si on a cliqué sur un bouton "modifier"
  if (e.target.closest(".editBtn")) {
    const button = e.target.closest(".editBtn");
    // récupère l'id du plat
    const dishId = button.dataset.id;
    // redirection avec id dans l'URL
    window.location.href = `/createDish?id=${dishId}`;
  }
});

//supprimer le plt
document.addEventListener("click", async (e) => {

  // vérifie si bouton supprimer
  if (!e.target.closest(".deleteBtn")) return;

  const button = e.target.closest(".deleteBtn");

  // récupère l'id
  const dishId = button.dataset.id;

  // confirmation utilisateur
  if (!confirm("Êtes-vous sûr de vouloir supprimer ce plat ?")) return;

  try {
    // appel API DELETE
    await secureFetch(
      `http://localhost:8082/dish/${dishId}`,
      { method: 'DELETE' },
      ['employee', 'admin']
    );

    // message succès
    alert("Plat supprimé avec succès");

    // recharge la liste
    loadDishes();

  } catch (error) {
    alert(error.message);
  }
});