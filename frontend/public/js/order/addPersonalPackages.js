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
          await addPersonalPackageToCart(personalPackage.id);
          showMessage("Forfait de personnel ajouté au panier", "success");

          setTimeout(() => {
            window.location.replace("/cart");
          }, 2000);

        } catch (error) {
          console.error("Erreur ajout forfait de personnel :", error);
          alert(error.message);
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
          await addPersonalPackageToCart(personalPackage.id);
          showMessage("Forfait de personnel ajouté au panier", "success");

          setTimeout(() => {
            window.location.replace("/cart");
          }, 2000);

        } catch (error) {
          console.error("Erreur ajout forfait de personnel :", error);
          alert(error.message);
        }
      });

      action.appendChild(buttonCard);

      cardBody.appendChild(action);
      card.appendChild(cardBody);
      mobileContainer.appendChild(card);

    });
    } catch (error) {
      // Affiche l'erreur si problème API
      alert(error.message);
  }
}

async function addPersonalPackageToCart(id) {

  // crée le forfait boisson dans panier  
  return await secureFetch(
    'http://localhost:8082/cart', 
      {
        method: "POST", 
        body: JSON.stringify({ 
          type: "personal_package",
          id: id,
          quantity: 1
        })
    },
    ['client']
  );
}
// se lance au chargement
loadPersonalsPackage();
