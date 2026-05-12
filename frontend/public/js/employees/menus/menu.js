function groupByCategory(dishes) {
  const grouped = {};

  dishes.forEach(dish => {
    const category = dish.categoryName || "Autres";

    if (!grouped[category]) {
      grouped[category] = [];
    }

    grouped[category].push(dish);
  });

  return grouped;
}

function renderDishesByCategory(dishes) {
  const container = document.createElement("div");

  const order = [
    "Entrées",
    "Plats principaux",
    "Fromages",
    "Desserts"
  ];

  const grouped = groupByCategory(dishes);

  order.forEach(category => {
    if (grouped[category]) {

      // titre catégorie
      const title = document.createElement("strong");
      title.textContent = category;
      container.appendChild(title);

      // liste des plats
      const ul = document.createElement("ul");

      grouped[category].forEach(dish => {
        const li = document.createElement("li");
        li.textContent = dish.name; 
        ul.appendChild(li);
      });
      container.appendChild(ul);
    }
  });

  return container;
}

// fonction pour charger les menus
async function loadMenus() {
  try {
    // Appel sécurisé vers l'API (GET: fonction dans api.js)
    const data = await getMenus();

    const container = document.getElementById('container');
    container.innerHTML = '';

    // Boucle sur chaque menu reçu
    data.forEach(menu => {
      // boucle pour afficher les cartes de tous les menus
      const card = document.createElement('div');
      card.className = 'card mb-3';

      const cardBody = document.createElement('div');
      cardBody.className = 'card-body bgc-secondary align_left';
      // Name
      const name = document.createElement('h2');
      name.textContent = menu.menu_name;
      cardBody.appendChild(name);
      // Thème
      const theme = document.createElement('p');
      theme.textContent = menu.theme;
      cardBody.appendChild(theme);
      // Description
      const desc = document.createElement('p');
      desc.textContent = menu.description;
      cardBody.appendChild(desc);
      // image du menu
      const img = document.createElement('img');
      img.src = `/assets/img/${menu.illustration.picture}`;
      img.style.width = "300px";
      cardBody.appendChild(img);
      // nombre de personne minimum
      const minPerson = document.createElement('p');
      minPerson.textContent = `Nombre de personne minimum : ${menu.minimum_people} personnes`;
      cardBody.appendChild(minPerson);
      // prix du menu
      const price = document.createElement('p');
      price.textContent = `Prix par personne : ${menu.price_per_person} €`;
      cardBody.appendChild(price);
      // nombre de commande encore disponible pour ce menu
      const remaining = document.createElement('p');
      remaining.textContent = `Nombre de commande encore disponible : ${menu.remaining_quantity} commandes disponibles`;
      cardBody.appendChild(remaining);
      // plats
      const dishesContainer = document.createElement("div");
      // titre plats
      const title = document.createElement("strong");
      title.textContent = "Plats :";
      dishesContainer.appendChild(title);
      // affiche les plats regroupés par catégorie (fonction créée plus haut)
      const dishesGrouped = renderDishesByCategory(menu.dishes || []);
      dishesContainer.appendChild(dishesGrouped);
      // ajoute le conteneur des plats à la card
      cardBody.appendChild(dishesContainer);
      // conditions du menu
      const conditions = document.createElement("div");
      // titre conditions
      const titleCondition = document.createElement("strong");
      titleCondition.textContent = "Conditions :";
      conditions.appendChild(titleCondition);
      // affiche les conditions du menu
      const ul = document.createElement("ul");
      // boucle sur les conditions du menu et les affiche dans une liste
      menu.conditions?.forEach(c => {
        const li = document.createElement("li");
        li.textContent = `${c.type} : ${c.description}`;
        ul.appendChild(li);
      });
      // ajoute la liste des conditions à la card
      conditions.appendChild(ul);
      cardBody.appendChild(conditions);
      // Actions (récupère la fonction dans utils.js pour créer les boutons d'action)
      const cardAction = document.createElement('p');
      cardAction.appendChild(createActionButtons(menu.id));
      cardBody.appendChild(cardAction);

      card.appendChild(cardBody);
      container.appendChild(card);
    });
    } catch (error) {
      // Affiche l'erreur si problème API
      alert(error.message);
  }
}
loadMenus();

// modifier: aller sur la page createMenu pour modifier le menu
document.addEventListener("click", (e) => {
  // Vérifie si on a cliqué sur un bouton "modifier"
  if (e.target.closest(".editBtn")) {
    const button = e.target.closest(".editBtn");
    // Récupère l'id du menu
    const menuId = button.dataset.id;
    // redirection avec id dans l'URL
    window.location.href = `/createMenu?id=${menuId}`;
  }
});

//supprimer le menu
document.addEventListener("click", async (e) => {

  // Vérifie si bouton supprimer
  if (!e.target.closest(".deleteBtn")) return;

  const button = e.target.closest(".deleteBtn");

  // Récupère l'id
  const menuId = button.dataset.id;

  // Confirmation utilisateur
  if (!confirm("Supprimer ce menu ?")) return;

  try {
    // Appel API DELETE
    await secureFetch(
      `http://localhost:8082/menu/${menuId}`,
      { method: 'DELETE' },
      ['employee', 'admin']
    );

    // Message succès
    alert("Menu supprimé avec succès");

    // Recharge la liste
    loadMenus();

  } catch (error) {
    alert(error.message);
  }
});