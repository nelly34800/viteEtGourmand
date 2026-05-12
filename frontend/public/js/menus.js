//filtre JS 
// Implémenter js de ma page
const minPrice = document.getElementById("minPrice");
const maxPrice = document.getElementById("maxPrice");
const minPeople = document.getElementById("minPeople");
const menuTheme = document.getElementById("menuTheme");
const container = document.getElementById("container");

let allMenus = [];

function applyFilters() {
  const minPriceValue = Number(minPrice.value);
  const maxPriceValue = Number(maxPrice.value);
  const minPeopleValue = Number(minPeople.value);
  const themeValue = menuTheme.value;

  const filteredMenus = allMenus.filter(menu => {
    const price = Number(menu.price_per_person);
    const people = Number(menu.minimum_people);

    const matchMinPrice = !minPrice.value || price >= minPriceValue;
    const matchMaxPrice = !maxPrice.value || price <= maxPriceValue;
    const matchMinPeople = !minPeople.value || people <= minPeopleValue;
    const matchTheme = !themeValue || menu.theme === themeValue;

    return matchMinPrice && matchMaxPrice && matchMinPeople && matchTheme;
  });

  displayMenus(filteredMenus);
}
//affichage des menus
function displayMenus(menus) {
  container.innerHTML = "";

  if (menus.length === 0) {
    container.innerHTML = "<p>Aucun menu ne correspond aux filtres.</p>";
    return;
  }
  // Boucle sur chaque menu reçu
  menus.forEach(menu => {
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
}
// charger les menus
async function loadMenus() {
  try {
    const response = await fetch("http://localhost:8082/menu");

    const text = await response.text();

    if (!response.ok) {
      throw new Error("Erreur API");
    }

    const data = JSON.parse(text);

    allMenus = data;

    fillThemeSelect(data);
    displayMenus(allMenus);

  } catch (error) {
    console.error(error);
    alert(error.message);
  }
}
// remplir le select avec les thèmes des menus
function fillThemeSelect(menus) {
  menuTheme.innerHTML = `<option value="">Tous les thèmes</option>`;

  const themes = [...new Set(menus.map(menu => menu.theme).filter(Boolean))];

  themes.forEach(theme => {
    const option = document.createElement("option");
    option.value = theme;
    option.textContent = theme;
    menuTheme.appendChild(option);
  });
}
// écoute des chamgements
minPrice.addEventListener("input", applyFilters);
maxPrice.addEventListener("input", applyFilters);
minPeople.addEventListener("input", applyFilters);
menuTheme.addEventListener("change", applyFilters);

loadMenus().then(() => {
  updateCartNavbar();
});