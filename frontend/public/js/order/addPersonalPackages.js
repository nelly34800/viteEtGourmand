// fonction pour charger les forfaits de personnel
async function loadPersonalsPackage() {
  try {
    // Appel sécurisé vers l'API (GET: fonction dans api.js)
    const data = await getPersonalPackages();
    // dekstop: vide le tableau
    const tbody = document.querySelector('tbody');
    tbody.innerHTML = '';

    //mobile
    const mobileContainer = document.getElementById('mobile-container');
    mobileContainer.innerHTML = '';

    // Boucle sur chaque forfait de personnel reçu
    data.forEach(personalPackage => {
      //  desktop: Création d'une ligne
      const tr = document.createElement('tr');
      //inject dans le DOM
      // Événement
      const tdEvent = document.createElement("td");
      tdEvent.textContent = personalPackage.event_type;
      tr.appendChild(tdEvent);
      // Ratio personnel
      const tdRatio = document.createElement("td");
      tdRatio.textContent = `1 pour ${personalPackage.staff_ratio} personnes`;
      tr.appendChild(tdRatio);
      // Prix
      const tdPrice = document.createElement("td");
      tdPrice.textContent = `${personalPackage.package_price}€`;
      tr.appendChild(tdPrice);
      // Actions (récupère la fonction dans utils.js pour créer les boutons d'action)
      const tdAction = document.createElement("td");
      const buttonContainer = CreateAddButton(personalPackage.id);
      const addBtn = buttonContainer.querySelector(".addBtn");

      addBtn.addEventListener("click", async () => {
        try {
          // récupère le résultat de l'ajout (true ou false)
          const success = addPersonalPackageToCart(personalPackage.id);

          // Si l'ajout a échoué (pas de menu ou doublon), on s'arrête là.
          if (!success) return;

          // lance directement la redirection ici après 2 secondes.
          setTimeout(() => {
            window.location.replace("/cart");
          }, 2000);

        } catch (error) {
          console.error("Erreur ajout forfait de personnel :", error);
          showMessage("Une erreur est survenue", "danger");
        }
      });

      tdAction.appendChild(buttonContainer);
      tr.appendChild(tdAction);
      // Ajout dans le DOM
      tbody.appendChild(tr);

      //mobile: boucle pour afficher les cartes de tous les forfaits de personnel
      const card = document.createElement('div');
      card.className = 'card mb-3';

      const cardBody = document.createElement('div');
      cardBody.className = 'card-body bgc-secondary text-center';
      // Event
      const cardEvent = document.createElement('p');
      cardEvent.textContent = personalPackage.event_type;
      cardBody.appendChild(cardEvent);
      // Ratio
      const cardRatio = document.createElement('p');
      cardRatio.textContent = `1 pour ${personalPackage.staff_ratio} personnes`;
      cardBody.appendChild(cardRatio);
      // Prix
      const cardPrice = document.createElement('p');
      cardPrice.textContent = `${personalPackage.package_price}€`;
      cardBody.appendChild(cardPrice);
      // Actions (récupère la fonction dans utils.js pour créer les boutons d'action)
      const action = document.createElement('p');
      const buttonCard = CreateAddButton(personalPackage.id);
      const addCartBtn = buttonCard.querySelector(".addBtn");

      addCartBtn.addEventListener("click", async () => {
        try {
          // récupère le résultat de l'ajout (true ou false)
          const success = addPersonalPackageToCart(personalPackage.id);

          // Si l'ajout a échoué (pas de menu ou doublon), on s'arrête là.
          if (!success) return;

          // lance directement la redirection ici après 2 secondes.
          setTimeout(() => {
            window.location.replace("/cart");
          }, 2000);

        } catch (error) {
          console.error("Erreur ajout forfait de personnel :", error);
          showMessage("Une erreur est survenue", "danger");
        }
      });

      action.appendChild(buttonCard);

      cardBody.appendChild(action);
      card.appendChild(cardBody);
      mobileContainer.appendChild(card);

    });
  } catch (error) {
    // Affiche l'erreur si problème API
    showMessage("Une erreur est survenue lors du chargement des forfaits", "danger");
  }
}

// ajout local du forfait de personnel
function addPersonalPackageToCart(id) {
   const LOCAL_STORAGE_KEY = "vgc_cart_raw";

  // récupére le panier existant
  let localCart = JSON.parse(localStorage.getItem(LOCAL_STORAGE_KEY)) || [];

  // vérifie s'il y a au moins un menu dans le panier
  const hasMenu = localCart.some(item => item.type === "menu");

  if (!hasMenu) {
    showMessage("Vous devez d'abord ajouter un menu à votre panier avant de pouvoir choisir des options (forfaits ou matériel).", "warning");
    return false;
  }

  // vérifie si le forfait de personnel est déjà dans le panier
  const existingItem = localCart.find(item => item.id == id && item.type === "personal_package");

  if (existingItem) {
    showMessage("Ce forfait de personnel est déjà présent dans votre panier !", "warning");
    return false; 
  }
    // si tout est ok on ajoute l'option
  localCart.push({
    type: "personal_package",
    id: id,
    quantity: 1
  });

  // sauvegarde dans le navigateur
  localStorage.setItem(LOCAL_STORAGE_KEY, JSON.stringify(localCart));
  // force le rafraîchissement visuel de la barre de navigation
  updateCartNavbar();
  
  showMessage("Forfait de personnel ajouté au panier !", "success");
  return true;
}
// se lance au chargement
loadPersonalsPackage();
updateCartNavbar();