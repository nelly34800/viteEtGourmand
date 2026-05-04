// Implémenter js de ma page
const address = document.getElementById("address");
const postalCode = document.getElementById("postalCode");
const city = document.getElementById("city");
const btnDeliveryCharges = document.getElementById("btnDeliveryCharges");

// fonction pour charger le panier
async function loadOrder() {
  try {
    // Appel sécurisé vers l'API (GET: fonction dans api.js)
    cart = await secureFetch("http://localhost:8082/cart", {
      method: "GET"
    }, ["client"]);


  } catch (error) {
    console.error("Erreur chargement panier :", error);
  }
}

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

loadOrder();