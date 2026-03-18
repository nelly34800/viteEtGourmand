// Implémenter js de ma page
const address = document.getElementById("address");
const postalCode = document.getElementById("postalCode");
const city = document.getElementById("city");
const btnDeliveryCharges = document.getElementById("btnDeliveryCharges");

//écoute des événements
address.addEventListener("keyup", validateForm);
postalCode.addEventListener("keyup", validateForm);
city.addEventListener("keyup", validateForm);

//fonction permettant de valider le formulaire
function validateForm(){
const addressOk = validateRequired(address);
const postalCodeOk = validateRequired(postalCode);
const cityOk = validateRequired(city);

    if(addressOk && postalCodeOk && cityOk){
    btnDeliveryCharges.disabled = false;
    }
    else{
    btnDeliveryCharges.disabled = true;
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