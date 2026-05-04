const params = new URLSearchParams(window.location.search);
const menuId = params.get("id");

async function loadMenu(id) {
  try {
    const response = await fetch(`http://localhost:8082/menu/${id}`);
    const data = await response.json();

    renderMenu(data);
    renderCarousel(data.dishes);
    renderDishes(data.dishes);
    renderConditions(data.conditions);
    renderInfos(data);

    // lier l'id du backend
    addOrderButton(data.id);

  } catch (error) {
    console.error(error);
  }
}

function renderMenu(menu) {
  document.getElementById("menu-title").textContent = menu.menu_name;
  document.getElementById("menu-description").textContent = menu.description;
}

function renderCarousel(dishes) {
  const carouselInner = document.getElementById("carouselInner");
  carouselInner.innerHTML = "";

  const imagesPerSlide = 3;

  // Générer les slides 
  for (let i = 0; i < dishes.length; i += imagesPerSlide) {
    // Sélection des images pour cette slide  
    // Création de la div de slide 
    const slideDiv = document.createElement("div");
    slideDiv.classList.add("carousel-item");
     // La première slide prend la classe active 
    if (i === 0) slideDiv.classList.add("active");
     // Construit la slide avec les images
    const row = document.createElement("div");
    row.className = "row";

    const slice = dishes.slice(i, i + imagesPerSlide);

    slice.forEach(dish => {
      const col = document.createElement("div");
      col.className = "col-12 col-md-4";

      const img = document.createElement("img");
      img.src = `/assets/img/${dish.picture}`;
      img.className = "d-block w-100";
      img.alt = dish.dish_title;

      col.appendChild(img);
      row.appendChild(col);
    });

    slideDiv.appendChild(row);
    carouselInner.appendChild(slideDiv);
  }
}

const order = ["Entrées", "Plats principaux", "Fromages", "Desserts"];

function groupByCategory(dishes) {
  const grouped = {};

  dishes.forEach(dish => {
    const cat = dish.categoryName;
    if (!grouped[cat]) grouped[cat] = [];
    grouped[cat].push(dish);
  });

  return grouped;
}

function renderDishes(dishes) {
  const container = document.getElementById("dishes-container");
  container.innerHTML = "";

  const grouped = groupByCategory(dishes);

  order.forEach(category => {
    if (!grouped[category]) return;

    const title = document.createElement("h4");
    title.textContent = category;
    container.appendChild(title);

    grouped[category].forEach(dish => {
      const p1 = document.createElement("p");
      p1.textContent = dish.name;
      container.appendChild(p1);

      const p2 = document.createElement("p");
      p2.textContent = dish.description;
      container.appendChild(p2);

      const details = document.createElement("p");
      const diets = dish.diets?.map(d => d.name).join(", ") || "Aucun";
      const allergens = dish.allergens?.map(a => a.name).join(", ") || "Aucun";

      details.textContent = `Régimes alimentaires : ${diets} | Allergènes : ${allergens}`;

      container.appendChild(details);
    });
  });
}

function renderConditions(conditions) {
  const container = document.getElementById("menu-conditions");
  container.innerHTML = "<h3>Conditions</h3>";

  conditions.forEach(cond => {
    const p = document.createElement("p");
    p.textContent = `${cond.type} : ${cond.description}`;
    container.appendChild(p);
  });
}

function renderInfos(menu) {
  const container = document.getElementById("menu-infos");

  container.innerHTML = `
    <p>Nombre de personnes minimum : ${menu.minimum_people} personnes</p>
    <p>Disponibilité : il reste ${menu.remaining_quantity} commandes disponible pour ce menu</p>
    <p>Prix : ${menu.price_per_person} € / personne</p>
  `;
}
async function addToCart(menuId) {
  const user = isConnected();

  if (!user) {
      localStorage.setItem("redirectAfterLogin", window.location.href);
      showMessage("Cette page est seulement accessible après connexion, merci de vous connecter s'il vous plait", "warning");

      setTimeout(() => {
        window.location.replace("/signin");
      }, 2000);
      return false;
    }

  // envoie le menu au backend et ajoute le panier dans la session php  
  return await secureFetch(
    'http://localhost:8082/cart', 
      {
        method: "POST", 
        body: JSON.stringify({ 
          type: "menu",
          id: menuId,
          quantity: 1
        })
    },
    ['client']
  );
}

function addOrderButton(menuId) {
  // récupère le bouton HTML
  const btn = document.getElementById("reserve_btn");
  // vérifie si le bouton existe dans le DOM
  if (btn) {
    btn.addEventListener("click", async () => {
      try {
          await addToCart(menuId);
          const modal = new bootstrap.Modal(document.getElementById('cartModal'));
          modal.show();
      } catch (error) {
        console.error("Erreur ajout panier :", error);
      }
    });
  }
}
// vérifie qu’un menu est sélectionné
if (menuId) {
  //charge les données du menu
  loadMenu(menuId);
}