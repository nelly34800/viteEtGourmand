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
          await addDrinkPackageToCart(drinkPackage.id);
          showMessage("Forfait boisson ajouté au panier", "success");

          setTimeout(() => {
            window.location.replace("/cart");
          }, 2000);
          return false;

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
      // Affiche l'erreur si problème API
      showMessage("Une erreur est survenue", "danger");
  }
}

async function addDrinkPackageToCart(id) {

  // crée le forfait boisson dans panier  
  return await secureFetch(
    'http://localhost:8082/cart', 
      {
        method: "POST", 
        body: JSON.stringify({ 
          type: "drink_package",
          id: id,
          quantity: 1
        })
    },
    ['client']
  );
}
loadDrinksPackage();
