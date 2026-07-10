// Implémenter js de ma page
const title = document.getElementById("title");
const description = document.getElementById("description");
const email = document.getElementById("email");
const btnValidation = document.getElementById("validation");

btnValidation.disabled = true;

//écoute des événements
title.addEventListener("keyup", validateForm);
description.addEventListener("keyup", validateForm);
email.addEventListener("keyup", validateForm);

//fonction permettant de valider le formulaire
function validateForm(){
const titleOk = validateRequired(title);
const descriptionOk =validateRequired(description);
const emailOk = validateEmail(email);

 if(titleOk && descriptionOk && emailOk){
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

// créer le message à envoyer
document.querySelector('form').addEventListener('submit', async (e) => {
  //empêche le rechargement
  e.preventDefault();
  //récupère la valeurs des inputs
  const titleValue = document.getElementById('title').value;
  const descriptionValue = document.getElementById('description').value;
  const emailValue = document.getElementById('email').value;

  // envoie au backend pour envoyer le message
  try {
    const response = await fetch(`${API_URL}/contact`, {
      method: 'POST',
      credentials: 'include',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        title: titleValue,
        description: descriptionValue,
        email: emailValue,
      })
    });

    const data = await response.json();

    if (response.ok) {
      // afficher le message
      showMessage("Message envoyé avec succès", "success");

      // redirection après 3 secondes
      setTimeout(() => {
        window.location.href = '/';
      }, 3000);
    } else {
      // message d'erreur
      showMessage( "Une erreur est survenue, danger");
    }

  } catch (error) {
    showMessage("Une erreur est survenue", "danger");
  }
});
