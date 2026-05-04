//validation des données
// Implémenter js de ma page
const lastName = document.getElementById("lastName");
const firstName = document.getElementById("firstName");
const email = document.getElementById("email");
const password = document.getElementById("password");
const confirmPassword = document.getElementById("confirmPassword");
const address = document.getElementById("address");
const postalCode = document.getElementById("postalCode");
const city = document.getElementById("city");
const phone = document.getElementById("phone");
const btnValidation = document.getElementById("validation-inscription");

const params = new URLSearchParams(window.location.search);
const userId = params.get('id');

//écoute des événements
[lastName, firstName, email, password, confirmPassword, address, postalCode, city, phone].forEach(input => {
  input.addEventListener("input", validateForm);
});

//fonction permettant de valider le formulaire
function validateForm(){
const nomOk = validateRequired(lastName);
const prenomOk =validateRequired(firstName);
const mailOk = validateEmail(email);
const passwordOk = validatePassword(password);
const confirmPasswordOk = validateConfirmationPassword(password, confirmPassword);
const addressOk = validateRequired(address);
const postalCodeOk = validateRequired(postalCode);
const cityOk = validateRequired(city);
const phoneOk = validateRequired(phone);

    if(nomOk && prenomOk && mailOk && passwordOk && confirmPasswordOk && addressOk && postalCodeOk && cityOk && phoneOk){
    btnValidation.disabled = false;
    }
    else{
    btnValidation.disabled = true;
    }
}

function validateConfirmationPassword(inputPwd, inputConfirmPwd){
      if (inputConfirmPwd.value === "") {
        // champ vide → on enlève les classes de validation
        inputConfirmPwd.classList.remove("is-valid", "is-invalid");
        return false;
    }
    if(inputPwd.value== inputConfirmPwd.value){
      inputConfirmPwd.classList.add("is-valid");
        inputConfirmPwd.classList.remove("is-invalid");
        return true;
    }
    else{
        inputConfirmPwd.classList.remove("is-valid");
        inputConfirmPwd.classList.add("is-invalid");
        return false;
    }
}

function validatePassword(input){
// définir regex
const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_])[A-Za-z\d\W_]{10,}$/;
const passwordUser = input.value;
if(passwordUser.match(passwordRegex)){
         input.classList.add("is-valid");
        input.classList.remove("is-invalid");
        return true;
    }
    else{
        input.classList.remove("is-valid");
        input.classList.add("is-invalid");
        return false;
    }
}

function validateEmail(input){
// définir regex
const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
const mailUser = input.value;
if(mailUser.match(emailRegex)){
        input.classList.add("is-valid");
        input.classList.remove("is-invalid");
        return true;
    }
    else{
        input.classList.remove("is-valid");
        input.classList.add("is-invalid");
        return false;
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

// créer en bdd l'employé
document.querySelector('form').addEventListener('submit', async (e) => {
  //empêche le rechargement
  e.preventDefault();
  //récupère l'utilisateur (valeurs des inputs) par son id
  const lastNameValue = lastName.value;
  const firstNameValue = firstName.value;
  const emailValue = email.value;
  const passwordValue = password.value;
  const addressValue = address.value;
  const postalCodeValue = postalCode.value;
  const cityValue = city.value;
  const phoneValue = phone.value;

  // envoie au backend pour l'entrer dans la bdd
  try {
    await secureFetch(
      'http://localhost:8082/employee', 
      {
        method: 'POST',
        body: JSON.stringify({
          last_name: lastNameValue,
          first_name: firstNameValue,
          email: emailValue,
          password: passwordValue,
          postal_address: addressValue,
          city: cityValue,
          postal_code: postalCodeValue,
          phone: phoneValue})
        },
        ['admin']
      );

      // afficher le message
      showMessage("Création réussie ! Vous allez être redirigé", "success");
      // redirection après 2 secondes
      setTimeout(() => {
        window.location.href = '/employeesList';
      }, 2000);
  } catch (error) {
    console.error("Erreur fetch :", error);
  }
  });
