//validation des données
// Implémenter js de ma page
const nameAllergen = document.getElementById("nameAllergen");
const allergenValidation = document.getElementById("allergenValidation");

const params = new URLSearchParams(window.location.search);
const allergenId = params.get('id');

//écoute des événements
nameAllergen.addEventListener("input", validateForm);

//fonction permettant de valider le formulaire
function validateForm(){
  const nameAllergenOk = validateRequired(nameAllergen);

    if(nameAllergenOk){
      allergenValidation.disabled = false;
      }
      else{
      allergenValidation.disabled = true;
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

// Charger un allergène (modification)
async function loadAllergen(id) {
  try {
    const data = await secureFetch(
      `http://localhost:8082/allergen/${id}`,
      { method: 'GET' },
      ['employee', 'admin']
    );

    nameAllergen.value = data.allergen_name;

  } catch (error) {
    showMessage(error.message, "danger");
  }
}
// pré-rempli si edit
if (allergenId) {
  loadAllergen(allergenId);
}

// créer ou modifier en bdd l'allergène (submit)
document.querySelector('form').addEventListener('submit', async (e) => {
  //empêche le rechargement
  e.preventDefault();

  const name = nameAllergen.value;
  // envoie au backend 
  try {
    // vérifie si on a un id dans l'URL (si id = modification)
    if (allergenId) {
      await secureFetch(
        `http://localhost:8082/allergen/${allergenId}`,
        {
          method: 'PUT',
          body: JSON.stringify({ allergen_name: name })
        },
        ['employee', 'admin']
      );

    // afficher le message
    showMessage("Modification réussie ! Vous allez être redirigé", "success");
    // redirection après 2 secondes
    setTimeout(() => {
      window.location.href = '/allergens';
    }, 2000);
  
    } else {
      // sinon pas d'id = création
      await secureFetch(
        `http://localhost:8082/allergen`,
        {
          method: 'POST',
          body: JSON.stringify({ allergen_name: name })
        },
        ['employee', 'admin']
      );

      // afficher le message
      showMessage("Création réussie ! Vous allez être redirigé", "success");
      // redirection après 2 secondes
      setTimeout(() => {
        window.location.href = '/allergens';
      }, 2000);
    }
  } catch (error) {
      // message d'erreur
      showMessage(error.message, "danger");
      console.error(error);
  }
});