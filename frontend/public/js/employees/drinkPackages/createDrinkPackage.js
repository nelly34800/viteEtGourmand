//validation des données
// Implémenter js de ma page
const nameDrinkPackage = document.getElementById("nameDrinkPackage");
const priceDrinkPackage = document.getElementById("priceDrinkPackage");
const drinkPackageValidation = document.getElementById("drinkPackageValidation");

drinkPackageValidation.disabled = true;

const params = new URLSearchParams(window.location.search);
const drinkPackageId = params.get('id');

//écoute des événements
[nameDrinkPackage, priceDrinkPackage].forEach(input => {
  input.addEventListener("input", validateForm);
});

//fonction permettant de valider le formulaire
function validateForm(){
  const nameDrinkPackageOk = validateRequired(nameDrinkPackage);
  const priceDrinkPackageOk = validateRequired(priceDrinkPackage);

    if(nameDrinkPackageOk && priceDrinkPackageOk){
      drinkPackageValidation.disabled = false;
      }
      else{
      drinkPackageValidation.disabled = true;
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

// Charger un forfait de boissons (modification)
async function loadDrinkPackage(id) {
  try {
    const data = await secureFetch(
      `http://localhost:8082/drinkPackage/${id}`,
      { method: 'GET' },
      ['employee', 'admin']
    );

    nameDrinkPackage.value = data.drink_package_name;
    priceDrinkPackage.value = data.price_per_person;

  } catch (error) {
    showMessage("Une erreur est survenue", "danger");
  }
}
// pré-rempli si edit
if (drinkPackageId) {
  loadDrinkPackage(drinkPackageId);
}

// créer ou modifier en bdd le forfait de boissons (submit)
document.querySelector('form').addEventListener('submit', async (e) => {
  //empêche le rechargement
  e.preventDefault();

  const name = nameDrinkPackage.value;
  const price = priceDrinkPackage.value;
  // envoie au backend 
  try {
    // vérifie si on a un id dans l'URL (si id = modification)
    if (drinkPackageId) {
      await secureFetch(
        `http://localhost:8082/drinkPackage/${drinkPackageId}`,
        {
          method: 'PUT',
          body: JSON.stringify({ 
            drink_package_name: name ,
            price_per_person: price })
        },
        ['employee', 'admin']
      );

    // afficher le message
    showMessage("Modification réussie ! Vous allez être redirigé", "success");
    // redirection après 2 secondes
    setTimeout(() => {
      window.location.href = '/drinkPackages';
    }, 2000);
  
    } else {
      // sinon pas d'id = création
      await secureFetch(
        `http://localhost:8082/drinkPackage`,
        {
          method: 'POST',
          body: JSON.stringify({ 
            drink_package_name: name ,
            price_per_person: price })
        },
        ['employee', 'admin']
      );

      // afficher le message
      showMessage("Création réussie ! Vous allez être redirigé", "success");
      // redirection après 2 secondes
      setTimeout(() => {
        window.location.href = '/drinkPackages';
      }, 2000);
    }
  } catch (error) {
      // message d'erreur
      showMessage("Une erreur est survenue", "danger");
  }
});