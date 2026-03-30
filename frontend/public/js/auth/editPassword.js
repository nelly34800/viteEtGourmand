const password = document.getElementById("password");
const confirmPassword = document.getElementById("confirmPassword");
const btnModification = document.getElementById("validationModification");

//écoute des événements
password.addEventListener("keyup", validateForm);
confirmPassword.addEventListener("keyup", validateForm);

//fonction permettant de valider le formulaire
function validateForm(){
const passwordOk = validatePassword(password);
const confirmPasswordOk = validateConfirmationPassword(password, confirmPassword);

    if(passwordOk && confirmPasswordOk) {
        btnModification.disabled = false;
    }
    else{
        btnModification.disabled = true;
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