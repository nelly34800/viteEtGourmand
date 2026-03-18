// Implémenter js de ma page
const dateEvent = document.getElementById("dateEvent");
const numberPeople = document.getElementById("numberPeople");
const btnValidation = document.getElementById("btnValidation");

//écoute des événements
numberPeople.addEventListener("keyup", validateForm);

//fonction permettant de valider le formulaire
function validateForm(){
  const dateOk = validateRequired(dateEvent);
  const nombrePersOk = validateRequired(numberPeople);

    if(dateEvent && nombrePersOk){
      btnValidation.disabled = false;
      }
      else{
      btnValidation.disabled = true;
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