//validation des données
// Implémenter js de ma page
const conditionType = document.getElementById("conditionType");
const description = document.getElementById("description");
const conditionValidation = document.getElementById("conditionValidation");
const messageDiv = document.getElementById('creat-message');
// Récupère l'id dans l'URL pour savoir si on est en création ou modification
const params = new URLSearchParams(window.location.search);
const conditionId = params.get('id');

//écoute des événements
[conditionType, description].forEach(input => {
  input.addEventListener("input", validateForm);
});

//fonction permettant de valider le formulaire
function validateForm(){
  const conditionTypeOk = validateRequired(conditionType);
  const descriptionOk = validateRequired(description);

    if(conditionTypeOk && descriptionOk){
      conditionValidation.disabled = false;
      }
      else{
      conditionValidation.disabled = true;
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

// Charger une condition (modification)
async function loadCondition(id) {
  try {
    const data = await secureFetch(
      `http://localhost:8082/condition/${id}`,
      { method: 'GET' },
      ['employee', 'admin']
    );

    conditionType.value = data.condition_type;
    description.value = data.description;

  } catch (error) {
    showMessage(error.message, "danger");
  }
}
// pré-rempli si edit
if (conditionId) {
  loadCondition(conditionId);
}

// créer ou modifier en bdd l'allergène (submit)
document.querySelector('form').addEventListener('submit', async (e) => {
  //empêche le rechargement
  e.preventDefault();

  const type = conditionType.value;
  const descriptionValue = description.value;
  // envoie au backend 
  try {
    // vérifie si on a un id dans l'URL (si id = modification)
    if (conditionId) {
      await secureFetch(
        `http://localhost:8082/condition/${conditionId}`,
        {
          method: 'PUT',
          body: JSON.stringify({
            condition_type: type,
            description: descriptionValue })
        },
        ['employee', 'admin']
      );

    // afficher le message
    showMessage("Modification réussie ! Vous allez être redirigé", "success");
    // redirection après 2 secondes
    setTimeout(() => {
      window.location.href = '/conditionsMenu';
    }, 2000);
  
    } else {
      // sinon pas d'id = création
      await secureFetch(
        `http://localhost:8082/condition`,
        {
          method: 'POST',
          body: JSON.stringify({
            condition_type: type,
            description: descriptionValue })
        },
        ['employee', 'admin']
      );

      // afficher le message
      showMessage("Création réussie ! Vous allez être redirigé", "success");
      // redirection après 2 secondes
      setTimeout(() => {
        window.location.href = '/conditionsMenu';
      }, 2000);
    }
  } catch (error) {
      // message d'erreur
      showMessage(error.message, "danger");
      console.error(error);
  }
});