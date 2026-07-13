// fonction pour charger les forfaits de boissons
async function loadDrinksPackage() {
  try {
    // Appel sécurisé vers l'API (GET: fonction dans api.js)
    const data = await getDrinkPackages();
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

      const buttonContainer = CreateAddButton(drinkPackage.id);
      const addBtn = buttonContainer.querySelector(".addBtn");

      addBtn.addEventListener("click", async () => {
        try {
          // récupère le résultat de l'ajout (true ou false)
          const success = addDrinkPackageToCart(drinkPackage.id);

          // Si l'ajout a échoué (pas de menu ou doublon), on s'arrête là.
          if (!success) return;

          // lance directement la redirection ici après 2 secondes.
          setTimeout(() => {
            window.location.replace("/cart");
          }, 2000);

        } catch (error) {
          console.error("Erreur ajout forfait boisson :", error);
          showMessage("Une erreur est survenue", "danger");
        }
      });

      tdAction.appendChild(buttonContainer);
      tr.appendChild(tdAction);
      // Ajout dans le DOM
      tbody.appendChild(tr);
    });
  } catch (error) {
    showMessage("Une erreur est survenue lors du chargement des forfaits", "danger");
  }
}

// ajout local du forfait boisson
function addDrinkPackageToCart(id) {
  const LOCAL_STORAGE_KEY = "vgc_cart_raw";
  
  // récupére le panier existant
  let localCart = JSON.parse(localStorage.getItem(LOCAL_STORAGE_KEY)) || [];

  // vérifie s'il y a au moins un menu dans le panier
  const hasMenu = localCart.some(item => item.type === "menu");

  if (!hasMenu) {
    showMessage("Vous devez d'abord ajouter un menu à votre panier avant de pouvoir choisir des options (forfaits ou matériel).", "warning");
    return false;
  }

  // vérifie si le forfait boisson est déjà dans le panier
  const existingItem = localCart.find(item => item.id == id && item.type === "drink_package");

  if (existingItem) {
    showMessage("Ce forfait boisson est déjà présent dans votre panier !", "warning");
    return false; 
  }
    // si tout est ok on ajoute l'option
  localCart.push({
    type: "drink_package",
    id: id,
    quantity: 1
  });

  // sauvegarde dans le navigateur
  localStorage.setItem(LOCAL_STORAGE_KEY, JSON.stringify(localCart));
  
  showMessage("Forfait boisson ajouté au panier !", "success");
  return true;
}
loadDrinksPackage();