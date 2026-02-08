// Implémenter js de ma page
const lastName = document.getElementById("lastName");
const firstName = document.getElementById("firstName");
const address = document.getElementById("address");
const postalCode = document.getElementById("postalCode");
const city = document.getElementById("city");
const phone = document.getElementById("phone");
const btnModification = document.getElementById("validation-modification");

//écoute des événements
lastName.addEventListener("keyup", validateForm);
firstName.addEventListener("keyup", validateForm);
address.addEventListener("keyup", validateForm);
postalCode.addEventListener("keyup", validateForm);
city.addEventListener("keyup", validateForm);
phone.addEventListener("keyup", validateForm);

//fonction permettant de valider le formulaire
function validateForm(){
const nomOk = validateRequired(lastName);
const prenomOk = validateRequired(firstName);
const addressOk = validateRequired(address);
const postalCodeOk = validateRequired(postalCode);
const cityOk = validateRequired(city);
const phoneOk = validateRequired(phone);

    if(nomOk && prenomOk && mailOk && addressOk && postalCodeOk && cityOk && phoneOk){
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