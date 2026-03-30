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

//écoute des événements
lastName.addEventListener("keyup", validateForm);
firstName.addEventListener("keyup", validateForm);
email.addEventListener("keyup", validateForm);
password.addEventListener("keyup", validateForm);
confirmPassword.addEventListener("keyup", validateForm);
address.addEventListener("keyup", validateForm);
postalCode.addEventListener("keyup", validateForm);
city.addEventListener("keyup", validateForm);
phone.addEventListener("keyup", validateForm);

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

// créer en bdd l'utilisateur
document.querySelector('form').addEventListener('submit', async (e) => {
  //empêche le rechargement
  e.preventDefault();
  //récupère l'utilisateur (valeurs des inputs) par son id
  const lastName = document.getElementById('lastName').value;
  const firstName = document.getElementById('firstName').value;
  const email = document.getElementById('email').value;
  const password = document.getElementById('password').value;
  const address = document.getElementById('address').value;
  const postalCode = document.getElementById('postalCode').value;
  const city = document.getElementById('city').value;
  const phone = document.getElementById('phone').value;

  // envoie au backend pour l'entrer dans la bdd
  try {
    const response = await fetch('http://localhost:8082/user', {
      method: 'POST',
      credentials: 'include',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-Token': localStorage.getItem('csrf_token')
      },
      body: JSON.stringify({
        last_name: lastName,
        first_name: firstName,
        email: email,
        password: password,
        postal_address: address,
        city: city,
        postal_code: postalCode,
        phone: phone
      })
    });

    const data = await response.json();

    const messageDiv = document.getElementById('signup-message');

    if (response.ok) {
      // afficher le message
      messageDiv.textContent = "Inscription réussie ! Vous allez être redirigé sur la page de connexion";
      messageDiv.classList.remove("d-none");
      messageDiv.classList.add("alert-success");

      // redirection après 3 secondes
      setTimeout(() => {
        window.location.href = '/signin';
      }, 3000);
    } else {
      // message d'erreur
      messageDiv.textContent = data.error || "Une erreur est survenue";
      messageDiv.classList.remove("d-none");
      messageDiv.classList.add("alert-danger");
    }

  } catch (error) {
    console.error("Erreur fetch :", error);
  }
});
