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
      const buttonContainer = CreateAddButton(material.id);
      const addBtn = buttonContainer.querySelector(".addBtn");

      addBtn.addEventListener("click", async () => {
        try {
          // récupère le résultat de l'ajout (true ou false)
          const success = addMaterialToCart(material.id);

          // Si l'ajout a échoué (pas de menu ou doublon), on s'arrête là.
          if (!success) return;

          // lance directement la redirection ici après 2 secondes.
          setTimeout(() => {
            window.location.replace("/cart");
          }, 2000);

        } catch (error) {
          console.error("Erreur ajout matériel :", error);
          showMessage("Une erreur est survenue", "danger");
        }
      });

      tdAction.appendChild(buttonContainer);
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
      const action = document.createElement('p');
      const buttonCard = CreateAddButton(material.id);
      const addCartBtn = buttonCard.querySelector(".addBtn");

      addCartBtn.addEventListener("click", async () => {
        try {
          // récupère le résultat de l'ajout (true ou false)
          const success = addMaterialToCart(material.id);

          // Si l'ajout a échoué (pas de menu ou doublon), on s'arrête là.
          if (!success) return;

          // lance directement la redirection ici après 2 secondes.
          setTimeout(() => {
            window.location.replace("/cart");
          }, 2000);

        } catch (error) {
          console.error("Erreur ajout matériel :", error);
          showMessage("Une erreur est survenue", "danger");
        }
      });

      action.appendChild(buttonCard);
      cardBody.appendChild(action);
      card.appendChild(cardBody);

      mobileContainer.appendChild(card);
    });

    } catch (error) {
      // affiche l'erreur si problème API
      showMessage("Une erreur est survenue lors du chargement du matériel", "danger");
  }
}
// ajout local du matériel
function addMaterialToCart(id) {
   const LOCAL_STORAGE_KEY = "vgc_cart_raw";

  // récupére le panier existant
  let localCart = JSON.parse(localStorage.getItem(LOCAL_STORAGE_KEY)) || [];

  // vérifie s'il y a au moins un menu dans le panier
  const hasMenu = localCart.some(item => item.type === "menu");

  if (!hasMenu) {
    showMessage("Vous devez d'abord ajouter un menu à votre panier avant de pouvoir choisir des options (forfaits ou matériel).", "warning");
    return false;
  }

  // vérifie si le matériel est déjà dans le panier
  const existingItem = localCart.find(item => item.id == id && item.type === "material");

  if (existingItem) {
    showMessage("Ce matériel est déjà présent dans votre panier !", "warning");
    return false; 
  }
    // si tout est ok on ajoute l'option
  localCart.push({
    type: "material",
    id: id,
    quantity: 1
  });

  // sauvegarde dans le navigateur
  localStorage.setItem(LOCAL_STORAGE_KEY, JSON.stringify(localCart));
  
  showMessage("Matériel ajouté au panier !", "success");
  return true;
}
// se lance au chargement
loadMaterial();
