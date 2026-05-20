//validation des données
// Implémenter js de ma page
const eventType = document.getElementById("eventType");
const staffRatio = document.getElementById("staffRatio");
const packagePrice = document.getElementById("packagePrice");
const personalPackageValidation = document.getElementById("personalPackageValidation");

personalPackageValidation.disabled = true;
// Récupère l'id dans l'URL pour savoir si on est en création ou modification
const params = new URLSearchParams(window.location.search);
const personalPackageId = params.get('id');

//écoute des événements
[eventType, staffRatio, packagePrice].forEach(input => {
  input.addEventListener("input", validateForm);
});

//fonction permettant de valider le formulaire
function validateForm(){
  const eventTypeOk = validateRequired(eventType);
  const staffRatioOk = validateRequired(staffRatio);
  const packagePriceOk = validateRequired(packagePrice);

    if(eventTypeOk && staffRatioOk && packagePriceOk){
      personalPackageValidation.disabled = false;
      }
      else{
      personalPackageValidation.disabled = true;
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

// Charger un forfait de personnel (modification)
async function loadPersonalPackage(id) {
  try {
    const data = await secureFetch(
      `http://localhost:8082/personalPackage/${id}`,
      { method: 'GET' },
      ['employee', 'admin']
    );

    eventType.value = data.event_type;
    staffRatio.value = data.staff_ratio;
    packagePrice.value = data.package_price;

  } catch (error) {
    showMessage("Une erreur est survenue", "danger");
  }
}
// pré-rempli si edit
if (personalPackageId) {
  loadPersonalPackage(personalPackageId);
}

// créer ou modifier en bdd le forfait de personnel (submit)
document.querySelector('form').addEventListener('submit', async (e) => {
  //empêche le rechargement
  e.preventDefault();

  const event = eventType.value;
  const ratio = staffRatio.value;
  const package = packagePrice.value;
  // envoie au backend 
  try {
    // vérifie si on a un id dans l'URL (si id = modification)
    if (personalPackageId) {
      await secureFetch(
        `http://localhost:8082/personalPackage/${personalPackageId}`,
        {
          method: 'PUT',
          body: JSON.stringify({
            event_type: event ,
            staff_ratio: ratio ,
            package_price: package })
        },
        ['employee', 'admin']
      );

    // afficher le message
    showMessage("Modification réussie ! Vous allez être redirigé", "success");
    // redirection après 2 secondes
    setTimeout(() => {
      window.location.href = '/personalPackages';
    }, 2000);
  
    } else {
      // sinon pas d'id = création
      await secureFetch(
        `http://localhost:8082/personalPackage`,
        {
          method: 'POST',
          body: JSON.stringify({
            event_type: event ,
            staff_ratio: ratio ,
            package_price: package })
        },
        ['employee', 'admin']
      );

      // afficher le message
      showMessage("Création réussie ! Vous allez être redirigé", "success");
      // redirection après 2 secondes
      setTimeout(() => {
        window.location.href = '/personalPackages';
      }, 2000);
    }
  } catch (error) {
      // message d'erreur
      showMessage("Une erreur est survenue", "danger");
  }
});