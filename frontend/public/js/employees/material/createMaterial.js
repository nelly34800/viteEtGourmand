//validation des données
// Implémenter js de ma page
const nameMaterial = document.getElementById("nameMaterial");
const quantityAvailable = document.getElementById("quantityAvailable");
const price = document.getElementById("price");
const category = document.getElementById("category");
const materialValidation = document.getElementById("materialValidation");
// Récupère l'id dans l'URL pour savoir si on est en création ou modification
const params = new URLSearchParams(window.location.search);
const materialId = params.get('id');

//écoute des événements
[nameMaterial, quantityAvailable, price].forEach(input => {
  input.addEventListener("input", validateForm);
});

// validation du select
function validateSelect(select) {
  return select.value !== "";
} 
//fonction permettant de valider le formulaire
function validateForm(){
  const nameMaterialOk = validateRequired(nameMaterial);
  const quantityAvailableOk = validateRequired(quantityAvailable);
  const priceOk = validateRequired(price);
  const categoryOk = validateSelect(category);

    if(nameMaterialOk && quantityAvailableOk && priceOk && categoryOk){
      materialValidation.disabled = false;
      }
      else{
      materialValidation.disabled = true;
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

// charger les catégories de matériel dans le select
async function loadCategoriesMaterial() {
  try {
    const data = await getCategoriesMaterial();
    // boucle pour créer les options du select
    data.forEach(c => {
      const option = document.createElement("option");
      option.value = c.id;        // Id envoyé
      option.textContent = c.material_category_name; // nom affiché
      category.appendChild(option);
    });
    category.addEventListener("change", validateForm);

  } catch (error) {
    showMessage(error.message, "danger");
  }
}

// Charger un matériel (modification)
async function loadMaterial(id) {
  try {
    const data = await secureFetch(
      `http://localhost:8082/material/${id}`,
      { method: 'GET' },
      ['employee', 'admin']
    );

    nameMaterial.value = data.material_name;
    quantityAvailable.value = data.quantity_available;
    price.value = data.price;
    category.value = data.id_material_category;

  } catch (error) {
    showMessage(error.message, "danger");
  }
}
// pré-rempli si edit
async function init() {
  await loadCategoriesMaterial();

  if (materialId) {
    await loadMaterial(materialId);
  }
}
init();

// créer ou modifier en bdd le matériel (submit)
document.querySelector('form').addEventListener('submit', async (e) => {
  //empêche le rechargement
  e.preventDefault();

  const name = nameMaterial.value;
  const quantity = quantityAvailable.value;
  const priceValue = price.value;
  const categoryValue = category.value;
  // envoie au backend 
  try {
    // vérifie si on a un id dans l'URL (si id = modification)
    if (materialId) {
      await secureFetch(
        `http://localhost:8082/material/${materialId}`,
        {
          method: 'PUT',
          body: JSON.stringify({ 
            material_name: name,
            quantity_available: quantity,
            price: priceValue,
            id_material_category: categoryValue })
        },
        ['employee', 'admin']
      );

    // afficher le message
    showMessage("Modification réussie ! Vous allez être redirigé", "success");
    // redirection après 2 secondes
    setTimeout(() => {
      window.location.href = '/material';
    }, 2000);
  
    } else {
      // sinon pas d'id = création
      await secureFetch(
        `http://localhost:8082/material`,
        {
          method: 'POST',
          body: JSON.stringify({ 
            material_name: name,
            quantity_available: quantity,
            price: priceValue,
            id_material_category: categoryValue })
        },
        ['employee', 'admin']
      );

      // afficher le message
      showMessage("Création réussie ! Vous allez être redirigé", "success");
      // redirection après 2 secondes
      setTimeout(() => {
        window.location.href = '/material';
      }, 2000);
    }
  } catch (error) {
      // message d'erreur
      showMessage(error.message, "danger");
      console.error(error);
  }
});