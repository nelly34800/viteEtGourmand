//validation des données
// Implémenter js de ma page
const nameDiet = document.getElementById("nameDiet");
const dietValidation = document.getElementById("dietValidation");
const messageDiv = document.getElementById('creat-message');

const params = new URLSearchParams(window.location.search);
const dietId = params.get('id');


//écoute des événements
nameDiet.addEventListener("input", validateForm);

//fonction permettant de valider le formulaire
function validateForm(){
  const nameDietOk = validateRequired(nameDiet);

    if(nameDietOk){
      dietValidation.disabled = false;
      }
      else{
      dietValidation.disabled = true;
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

// Affichage message
function showMessage(message, type) {
  messageDiv.textContent = message;
  messageDiv.className = `alert alert-${type}`;
}

// Charger un régime alimentaire (modification)
async function loadDiet(id) {
  try {
    const data = await secureFetch(
      `http://localhost:8082/diet/${id}`,
      { method: 'GET' },
      ['employee', 'admin']
    );

    nameDiet.value = data.diet_name;

  } catch (error) {
    showMessage(error.message, "danger");
  }
}
// pré-rempli si edit
if (dietId) {
  loadDiet(dietId);
}

// créer ou modifier en bdd le régime alimentaire (submit)
document.querySelector('form').addEventListener('submit', async (e) => {
  //empêche le rechargement
  e.preventDefault();

  const name = nameDiet.value;
  // envoie au backend 
  try {
    // vérifie si on a un id dans l'URL (si id = modification)
    if (dietId) {
      await secureFetch(
        `http://localhost:8082/diet/${dietId}`,
        {
          method: 'PUT',
          body: JSON.stringify({ diet_name: name })
        },
        ['employee', 'admin']
      );

    // afficher le message
    showMessage("Modification réussie ! Vous allez être redirigé", "success");
    // redirection après 2 secondes
    setTimeout(() => {
      window.location.href = '/diets';
    }, 2000);
  
    } else {
      // sinon pas d'id = création
      await secureFetch(
        `http://localhost:8082/diet`,
        {
          method: 'POST',
          body: JSON.stringify({ diet_name: name })
        },
        ['employee', 'admin']
      );

      // afficher le message
      showMessage("Création réussie ! Vous allez être redirigé", "success");
      // redirection après 2 secondes
      setTimeout(() => {
        window.location.href = '/diets';
      }, 2000);
    }
  } catch (error) {
      // message d'erreur
      showMessage(error.message, "danger");
      console.error(error);
  }
});