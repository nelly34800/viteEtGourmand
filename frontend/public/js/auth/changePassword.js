const currentPassword = document.getElementById("currentPassword");
const newPassword = document.getElementById("newPassword");
const confirmPassword = document.getElementById("confirmPassword");
const btnModification = document.getElementById("validationModification");

btnModification.disabled = true;

//écoute des événements
currentPassword.addEventListener("keyup", validateForm);
newPassword.addEventListener("keyup", validateForm);
confirmPassword.addEventListener("keyup", validateForm);

//fonction permettant de valider le formulaire
function validateForm(){
    const currentPasswordOk = validatePassword(currentPassword);
    const newPasswordOk = validatePassword(newPassword);
    const confirmPasswordOk = validateConfirmationPassword(newPassword, confirmPassword);

    if(currentPasswordOk && newPasswordOk && confirmPasswordOk) {
        btnModification.disabled = false;
    }
    else{
        btnModification.disabled = true;
    }
}

function invalidateCurrentPassword(inputPpassword){
    if(error) {
      currentPassword.classList.add("is-invalid");
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

document.querySelector('form').addEventListener('submit', async (e) => {
  //empêche le rechargement
  e.preventDefault();

   const currentPasswordValue = currentPassword.value;
  const newPasswordValue = newPassword.value;

  //récupération de l'utilisateur par son id
  const user = JSON.parse(localStorage.getItem('user'));
  if (!user) {
    alert("Utilisateur non connecté !");
    window.location.href = "/signin";
  }
  const userId = user.id;

   // envoie au backend
  try {
    const csrfToken = localStorage.getItem('csrf_token');

    const response = await fetch(`http://localhost:8082/user/${userId}/changePassword`, {
      method: 'POST',
      credentials: 'include',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-Token': csrfToken
      },
      body: JSON.stringify({
        currentPassword: currentPasswordValue,
        newPassword: newPasswordValue,
      })
    });

    const data = await response.json();


    if (response.ok) {
      // afficher le message
      showMessage("Modification du mot de passe réalisé avec succès", "success");

      // redirection après 3 secondes
      setTimeout(() => {
        window.location.href = '/';
      }, 3000);
    } else {
      currentPassword.classList.add("is-invalid");
      currentPassword.classList.remove("is-valid");

      // message d'erreur
      showMessage(data.error || "Une erreur est survenue, danger");
    }

  } catch (error) {
    console.error("Erreur fetch :", error);
    showMessage("Erreur de connexion au serveur", "danger");
  }
});