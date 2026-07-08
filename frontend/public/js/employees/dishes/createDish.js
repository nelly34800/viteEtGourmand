//validation des données
// Implémenter js de ma page
const dishTitle = document.getElementById("dishTitle");
const description = document.getElementById("description");
const picture = document.getElementById("picture");
const category = document.getElementById("category");
const allergens = document.getElementById("allergens-container");
const diets = document.getElementById("diets-container");
const dishValidation = document.getElementById("dishValidation");
// Récupère l'id dans l'URL pour savoir si on est en création ou modification
const params = new URLSearchParams(window.location.search);
const dishId = params.get('id');

dishValidation.disabled = true;

//écoute des événements
[dishTitle, description, picture].forEach(input => {
  input.addEventListener("input", validateForm);
});

// validation image
function validateImageName(input) {
  // vérifie que le champ n'est pas vide
  const value = input.value.trim();
  if (!value) return false;
  // vérifie si l'extension est valide
  const validExtensions = /\.(jpg|jpeg|png|webp)$/i;
  // test de l'extension
  return validExtensions.test(value);
}

// validation des checkbox
function validateCheckboxGroup(containerId) {
  return document.querySelectorAll(`#${containerId} input:checked`).length > 0;
}

// validation du select
function validateSelect(select) {
  return select.value !== "";
} 

//fonction permettant de valider le formulaire
function validateForm(){
  const dishTitleOk = validateRequired(dishTitle);
  const descriptionOk = validateRequired(description);
  const pictureOk = validateImageName(picture);
  const categoryOk = validateSelect(category);
  // validation de l'image
  if (pictureOk) {
    picture.classList.add("is-valid");
    picture.classList.remove("is-invalid");
  } else {
    picture.classList.add("is-invalid");
    picture.classList.remove("is-valid");
  }

  if(dishTitleOk && descriptionOk && pictureOk && categoryOk){
    dishValidation.disabled = false;
  } else{
    dishValidation.disabled = true;
  }
}

function validateRequired(input){
  if(input.value != ''){
    // c'est ok
    input.classList.add("is-valid");
    input.classList.remove("is-invalid");
    return true;
  }
  else{
      //c'est pas ok
      input.classList.remove("is-valid");
      input.classList.add("is-invalid");
      return false;
  }
}

// charger les catégories de plats dans le select
async function loadCategoriesDish() {
  try {
    const data = await getCategoriesDish();
    // boucle pour créer les options du select
    data.forEach(c => {
      const option = document.createElement("option");
      option.value = c.id;        // Id envoyé
      option.textContent = c.category_name; // nom affiché
      category.appendChild(option);
    });
    category.addEventListener("change", validateForm);

  } catch (error) {
    showMessage("Une erreur est survenue", "danger");
  }
}
// charger les allergènes
async function loadAllergens() {
  try {
    const data = await getAllergens();
    data.forEach(a => {
      const label = document.createElement("label");
      label.style.display = "block"; 

      const checkbox = document.createElement("input");
      checkbox.type = "checkbox";
      checkbox.value = a.id;
      checkbox.addEventListener("change", validateForm);

      label.appendChild(checkbox);
      label.appendChild(document.createTextNode(" " + a.allergen_name));

      document.getElementById("allergens-container").appendChild(label);
    });
  } catch (error) {
    showMessage("Une erreur est survenue", "danger");
  }
}
// charger les régimes alimentaires
async function loadDiets() {
  try {
    const data = await getDiets();
    data.forEach(d => {
      const label = document.createElement("label");
      label.style.display = "block"; 

      const checkbox = document.createElement("input");
      checkbox.type = "checkbox";
      checkbox.value = d.id;
      checkbox.addEventListener("change", validateForm);

      label.appendChild(checkbox);
      label.appendChild(document.createTextNode(" " + d.diet_name));

      document.getElementById("diets-container").appendChild(label);
    });
  } catch (error) {
    showMessage("Une erreur est survenue", "danger");
  }
}
 
// Charger un plat (modification)
async function loadDish(id) {
  try {
    const data = await secureFetch(
      `http://localhost:8082/dish/${id}`,
      { method: 'GET' },
      ['employee', 'admin']
    );
    // pré-rempli les champs du formulaire
    dishTitle.value = data.dish_title;
    description.value = data.description;
    picture.value = data.picture;
    category.value = data.id_category_dish;
    // pré-rempli les allergènes
    // vérifie que data.allergens existe et est un tableau avant de faire le forEach
    if (data.allergens && Array.isArray(data.allergens)) {
      // boucle sur les allergènes du plat et coche les cases correspondantes
      data.allergens.forEach(a => {
        const checkbox = document.querySelector(
          `#allergens-container input[value="${a.id}"]`);
        if (checkbox) {
          checkbox.checked = true;
        } 
      });
    }
    // pré-rempli les régimes alimentaires
    // vérifie que data.diets existe et est un tableau avant de faire le forEach
    if (data.diets && Array.isArray(data.diets)) {
      // boucle sur les régimes alimentaires du plat et coche les cases correspondantes
      data.diets.forEach(d => {
        const checkbox = document.querySelector(
          `#diets-container input[value="${d.id}"]`
        );
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
  await loadCategoriesDish();
  await loadAllergens();
  await loadDiets();

  if (dishId) {
    await loadDish(dishId);
  }
}
init();

// créer ou modifier en bdd le plat (submit)
document.querySelector('form').addEventListener('submit', async (e) => {
  //empêche le rechargement
  e.preventDefault();
  // récupère les valeurs du formulaire
  const title = dishTitle.value;
  const descriptionValue = description.value;
  const pictureValue = picture.value;
  const categoryValue = category.value;
  // récupère les allergènes cochés
  const allergensValue = Array.from(
    document.querySelectorAll('#allergens-container input:checked')
  ).map(cb => cb.value);
  // récupère les régimes alimentaires cochés
  const dietsValue = Array.from(
    document.querySelectorAll('#diets-container input:checked')
  ).map(cb => cb.value);

  // envoie au backend 
  try {
    // vérifie si on a un id dans l'URL (si id = modification)
    if (dishId) {
      await secureFetch(
        `http://localhost:8082/dish/${dishId}`,
        {
          method: 'PUT',
          // envoie les données du formulaire dans le body de la requête
          body: JSON.stringify({ 
            dish_title: title,
            description: descriptionValue,
            picture: pictureValue,
            id_category_dish: categoryValue,
            allergen_id: allergensValue,
            diet_id: dietsValue })
        },
        ['employee', 'admin']
      );

    // afficher le message
    showMessage("Modification réussie ! Vous allez être redirigé", "success");
    // redirection après 2 secondes
    setTimeout(() => {
      window.location.href = '/dishes';
    }, 2000);
  
    } else {
      // sinon pas d'id = création
      await secureFetch(
        `http://localhost:8082/dish`,
        {
          method: 'POST',
          // envoie les données du formulaire dans le body de la requête
          body: JSON.stringify({ 
            dish_title: title,
            description: descriptionValue,
            picture: pictureValue,
            id_category_dish: categoryValue,
            allergen_id: allergensValue,
            diet_id: dietsValue })
        },
        ['employee', 'admin']
      );

      // afficher le message
      showMessage("Création réussie ! Vous allez être redirigé", "success");
      // redirection après 2 secondes
      setTimeout(() => {
        window.location.href = '/dishes';
      }, 2000);
    }
  } catch (error) {
      // message d'erreur
      showMessage("Une erreur est survenue", "danger");
  }
});