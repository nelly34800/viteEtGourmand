//validation des données
// Implémenter js de ma page
const menuName = document.getElementById("menuName");
const theme = document.getElementById("theme");
const description = document.getElementById("description");
const minPeople = document.getElementById("minPeople");
const price = document.getElementById("price");
const remainingQuantity = document.getElementById("remainingQuantity");
const dishes = document.getElementById("dishes-container");
const conditions= document.getElementById("conditions-container");
const menuValidation = document.getElementById("menuValidation");

menuValidation.disabled = true;
// Récupère l'id dans l'URL pour savoir si on est en création ou modification
const params = new URLSearchParams(window.location.search);
const menuId = params.get('id');

//écoute des événements
[menuName, theme, description, minPeople, price, remainingQuantity].forEach(input => {
  input.addEventListener("input", validateForm);
});

// validation des checkbox
function validateCheckboxGroup(containerId) {
  return document.querySelectorAll(`#${containerId} input:checked`).length > 0;
}

//fonction permettant de valider le formulaire
function validateForm(){
  const menuNameOk = validateRequired(menuName);
  const themeOk = validateRequired(theme);
  const descriptionOk = validateRequired(description);
  const minPeopleOk = validateRequired(minPeople);
  const priceOk = validateRequired(price);
  const remainingQuantityOk = validateRequired(remainingQuantity);

  if(menuNameOk && themeOk && descriptionOk && minPeopleOk && priceOk && remainingQuantityOk){
    menuValidation.disabled = false;
  } else{
    menuValidation.disabled = true;
  }
}

function validateRequired(input){
  if(input.value != ''){
    // c'est ok
    input.classList.add("is-valid");
    input.classList.remove("is-invalid");
    return true;
  } else{
    //c'est pas ok
    input.classList.remove("is-valid");
    input.classList.add("is-invalid");
    return false;
  }
}

// ordre d'affichage des plats
const order = ["Entrées", "Plats principaux", "Fromages", "Desserts"];

// fonction pour grouper les plats par catégorie
function groupByCategory(dishes) {
  // Crée un objet vide pour stocker les catégories
  const grouped = {};
  // Parcourt chaque plat 
  dishes.forEach(dish => {
    // Récupère le nom de la catégorie du plat
    const category = dish.category_name;
    // vérifie catégorie existe, 
    if (!grouped[category]) {
      // sinon la crée avec un tableau vide
      grouped[category] = [];
    }
    // ajoute le plat dans la bonne catégorie
    grouped[category].push(dish);
  });
  //retourne l'objet avec les catégories et leurs plats
  return grouped;
}
// fonction pour afficher les plats groupés par catégorie
function renderDishesByCategory(dishes) {
  // Récupère le conteneur où afficher les plats
  const container = document.getElementById("dishes-container");
  // vide le conteneur avant d'afficher les plats (évite les doublons)
  container.innerHTML = "";
  // appele la fonction pour grouper les plats par catégorie
  const grouped = groupByCategory(dishes);
  // Parcourt chaque catégorie dans l'ordre défini par le tableau "order"
  order.forEach(categoryName => {
    if (grouped[categoryName]) {

      // titre catégorie
      const title = document.createElement("h4");
      title.textContent = categoryName;
      // ajoute le titre de la catégorie
      container.appendChild(document.createTextNode( "Choisir les plats qui compose le menu et ajouter une photo pour illustrer le menu (une seule) :"));
      container.appendChild(title);

      // parcours les plats de la catégorie
      grouped[categoryName].forEach(dish => {

        // crée un label pour chaque plat avec une checkbox
        const label = document.createElement("label");
        // met  à la ligne
        label.style.display = "block";
        // crée une checkbox pour sélectionner le plat
        const checkbox = document.createElement("input");
        checkbox.type = "checkbox";
        // stocke l'id du plat dans la checkbox
        checkbox.value = dish.id;
        //crée un radio pour l'illustration du menu
        const radio = document.createElement("input");
        radio.type = "radio";
        // stocke l'id du plat dans le radio, une seule illustration possible par menu
        radio.name = "menu-image";
        radio.value = dish.id;
        radio.disabled = true;  // Maintenant ça fonctionne
        checkbox.addEventListener("change", () => {
          radio.disabled = !checkbox.checked;
          if (!checkbox.checked) radio.checked = false;
        });
        // image
        const img = document.createElement("img");
        img.src = `/assets/img/${dish.picture}`;
        img.style.width = "80px";
        // ajoute la checkbox et le nom du plat dans label
        label.appendChild(checkbox);
        // ajoute un espace et le nom du plat après checkbox
        label.appendChild(document.createTextNode(" " + dish.dish_title + " "));
        label.appendChild(img);
        label.appendChild(document.createTextNode(" -> "));
        label.appendChild(radio);
        // ajoute le label dans le conteneur
        container.appendChild(label);
      });
    }
  });
}
// Charger les plats
async function loadDishes() {
  try {
    const data = await getDishes();
    renderDishesByCategory(data);

  } catch (error) {
    showMessage("Une erreur est survenue", "danger");
  }
}

// Charger les conditions du menu
async function loadConditions() {
  try {
    const data = await getConditions();
    data.forEach(c => {
      const label = document.createElement("label");
      label.style.display = "block";

      const checkbox = document.createElement("input");
      checkbox.type = "checkbox";
      checkbox.value = c.id;
      checkbox.addEventListener("change", validateForm);

      label.appendChild(checkbox);
      label.appendChild(document.createTextNode(" " + c.condition_type));
      label.appendChild(document.createTextNode(" " + c.description));

      document.getElementById("conditions-container").appendChild(label);
    });
  } catch (error) {
    showMessage("Une erreur est survenue", "danger");
  }
}

// Charger un menu (modification)
async function loadMenu(id) {
  try {
    const data = await secureFetch(
      `http://localhost:8082/menu/${id}`,
      { method: 'GET' },
      ['employee', 'admin']
    );
    // pré-rempli les champs du formulaire
    menuName.value = data.menu_name;
    theme.value = data.theme;
    description.value = data.description;
    minPeople.value = data.minimum_people;
    price.value = data.price_per_person;
    remainingQuantity.value = data.remaining_quantity;
    // pré-rempli les plats
    // vérifie que data.dishes existe et est un tableau avant de faire le forEach
    if (data.dishes && Array.isArray(data.dishes)) {
      // boucle sur les plats et coche les cases correspondantes
      data.dishes.forEach(d => {
        const checkbox = document.querySelector(
          `#dishes-container input[value="${d.id}"]`);
        if (checkbox) {
          checkbox.checked = true;
        }
      });
    }
    // pré-rempli les conditions du menu
    // vérifie que data.conditions existe et est un tableau avant de faire le forEach
    if (data.conditions && Array.isArray(data.conditions)) {
      // boucle sur les conditions du menu et coche les cases correspondantes
      data.conditions.forEach(c => {
        const checkbox = document.querySelector(
          `#conditions-container input[value="${c.id}"]`);
        if (checkbox) {
          checkbox.checked = true;
        }
      });
    }
  } catch (error) {
    showMessage("Une erreur est survenue", "danger");
  }
}
// pré-rempli si edit
async function init() {
  await loadDishes();
  await loadConditions();

  if (menuId) {
    await loadMenu(menuId);
  }
}
init();

// créer ou modifier en bdd le menu (submit)
document.querySelector('form').addEventListener('submit', async (e) => {
  //empêche le rechargement
  e.preventDefault();

  const name = menuName.value;
  const themeValue = theme.value;
  const descriptionValue = description.value;
  const illustrationDishId = document.querySelector(
    'input[name="menu-image"]:checked'
  )?.value;
  const minPeopleValue = minPeople.value;
  const priceValue = price.value;
  const remainingQuantityValue = remainingQuantity.value;
  //récupère les plats cochés
  
  if (!illustrationDishId) {
    showMessage("Tu dois choisir une image pour le menu", "danger");
    return;
  }
  const dishesValue = Array.from(
    document.querySelectorAll('#dishes-container input[type="checkbox"]:checked')
  ).map(cb => cb.value);
  //récupère les conditions cochées
  const conditionsValue = Array.from(
    document.querySelectorAll('#conditions-container input:checked')
  ).map(cb => cb.value);

  // envoie au backend 
  try {
    // vérifie si on a un id dans l'URL (si id = modification)
    if (menuId) {
      await secureFetch(
        `http://localhost:8082/menu/${menuId}`,
        {
          method: 'PUT',
          body: JSON.stringify({
            menu_name: name,
            description: descriptionValue,
            theme: themeValue,
            illustration_dish_id: illustrationDishId,
            minimum_people: minPeopleValue,
            price_per_person: priceValue,
            remaining_quantity: remainingQuantityValue,
            dish_id: dishesValue,
            condition_id: conditionsValue})
        },
        ['employee', 'admin']
      );

    // afficher le message
    showMessage("Modification réussie ! Vous allez être redirigé", "success");
    // redirection après 2 secondes
    setTimeout(() => {
      window.location.href = '/menu';
    }, 2000);
  
    } else {
      // sinon pas d'id = création
      await secureFetch(
        `http://localhost:8082/menu`,
        {
          method: 'POST',
          body: JSON.stringify({
            menu_name: name,
            description: descriptionValue,
            theme: themeValue,
            illustration_dish_id: illustrationDishId,
            minimum_people: minPeopleValue,
            price_per_person: priceValue,
            remaining_quantity: remainingQuantityValue,
            dish_id: dishesValue,
            condition_id: conditionsValue})
        },
        ['employee', 'admin']
      );

      // afficher le message
      showMessage("Création réussie ! Vous allez être redirigé", "success");
      // redirection après 2 secondes
      setTimeout(() => {
        window.location.href = '/menu';
      }, 2000);
    }
  } catch (error) {
      // message d'erreur
        showMessage("Une erreur est survenue", "danger");
  }
});