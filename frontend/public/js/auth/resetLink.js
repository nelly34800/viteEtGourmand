// Implémenter js de ma page
const email = document.getElementById("email");
const btnValidation = document.getElementById("validation-inscription");

btnValidation.disabled = true;

//écoute des événements
email.addEventListener("keyup", validateForm);

//fonction permettant de valider le formulaire
function validateForm(){
    const mailOk = validateEmail(email);

    if (mailOk) {
      btnValidation.disabled = false;
    }
    else{
    btnValidation.disabled = true;
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

document.getElementById('resetPasswordForm').addEventListener('submit', async (e) => {
  //empêche le rechargement
  e.preventDefault();

   const email = document.getElementById('email').value;

   // envoie au backend pour récupérer l'adresse mail
  try {
    const response = await fetch(`${API_URL}/passwordReset`, {
      method: 'POST',
      credentials: 'include',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        email: email,
      })
    });

    const data = await response.json();


    if (response.ok) {
      // afficher le message
      showMessage("Un mail vous à été envoyé pour modifier votre mot de passe", "success");

      // redirection après 3 secondes
      setTimeout(() => {
        window.location.href = '/';
      }, 2000);
    } else {
      // message d'erreur
      showMessage(data.error || "Une erreur est survenue, danger");
    }

  } catch (error) {
    console.error("Erreur fetch :", error);
  }
});
