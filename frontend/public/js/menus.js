// fonction pour charger les menus
async function loadMenus() {
  try {
    // Appel sécurisé vers l'API
    const response = await fetch(`http://localhost:8082/menu`);

    if (!response.ok) {
      throw new Error("Erreur API");
    }

    const data = await response.json();

    const container = document.getElementById('container');
    container.innerHTML = '';

    // Boucle sur chaque menu reçu
    data.forEach(menu => {
      // boucle pour afficher les cartes de tous les menus
      const cardColumn = document.createElement('div');
      cardColumn.className = 'col-md-12 col-lg-6  mb-3';

      const cardGold = document.createElement('div');
      cardGold.className = 'card-gold';
      cardGold.classList.add('h-100');

      const card = document.createElement('div');
      card.className = 'card-content';

      const cardBody = document.createElement('div');
      cardBody.className = 'card-body bgc-secondary';

      const cardTitle = document.createElement('div');
      cardTitle.className = 'card-title text-center';

      //titre du menu
      const name = document.createElement('h3');
      name.textContent = menu.menu_name;
      cardTitle.appendChild(name);

       // image du menu
      const img = document.createElement('img');
      if (menu.illustration && menu.illustration.picture) {
        img.src = `/assets/img/${menu.illustration.picture}`;
      } else {
        img.src = `/assets/img/default.png`;
      }
      img.style.width = "200px";
      img.style.height = "150px";
      cardTitle.appendChild(img);

      cardBody.appendChild(cardTitle);

      // Description
      const desc = document.createElement('p');
      desc.innerHTML = `<i class="bi bi-star-fill"> </i> ${menu.description}`;
      cardBody.appendChild(desc);

      // nombre de personne minimum
      const minPerson = document.createElement('p');
      minPerson.innerHTML = `<i class="bi bi-star-fill"> </i>Nombre minimum : ${menu.minimum_people} personnes`;
      cardBody.appendChild(minPerson);

      // prix du menu
      const price = document.createElement('p');
      price.innerHTML = `<i class="bi bi-star-fill"> </i>Prix par personne : ${menu.price_per_person} €`;
      cardBody.appendChild(price);

      const cardFooter = document.createElement('div');
      cardFooter.className = 'card-footer text-center';

      // Bouton Détails
      const link = document.createElement("button");
      link.textContent = "Détails";
      link.className = "btn btn-primary playfair";
      link.dataset.id = menu.id;

      link.addEventListener("click", () => {
        window.location.href = `/menuDetails?id=${menu.id}`;
      });

      cardFooter.appendChild(link);
      cardBody.appendChild(cardFooter);

      card.appendChild(cardBody);
      cardGold.appendChild(card);
      cardColumn.appendChild(cardGold);

      container.appendChild(cardColumn);
    });
    } catch (error) {
      // Affiche l'erreur si problème API
      alert(error.message);
  }
}
loadMenus().then(() => {
  updateCartNavbar();
});