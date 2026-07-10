//validation des données
// Implémenter js de ma page
const note = document.getElementById("note");
const description = document.getElementById("description");
const signature = document.getElementById("signature");
const noticeValidation = document.getElementById("noticeValidation");
// récupère l'id de la commande
const params = new URLSearchParams(window.location.search);
const idOrder = params.get("id_order");

noticeValidation.disabled = true;

//écoute des événements
[note, description, signature].forEach(input => {
  input.addEventListener("input", validateForm);
});

//fonction permettant de valider le formulaire
function validateForm(){
  const noteOk = validateRequired(note);
  const descriptionOk = validateRequired(description);
  const signatureOk = validateRequired(signature);

    if(noteOk && descriptionOk && signatureOk){
      noticeValidation.disabled = false;
    }
    else{
      noticeValidation.disabled = true;
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
// créer en bdd l'avis (submit)
document.querySelector('form').addEventListener('submit', async (e) => {
  //empêche le rechargement
  e.preventDefault();
  // envoie au backend 
  try {
    await secureFetch(
    `${API_URL}/notice`,
    {
      method: "POST",
      body: JSON.stringify({
        id_order: idOrder,
        note: Number(note.value),
        description: description.value.trim(),
        signature: signature.value.trim()
      })
    },
    ['client']
  );

    // afficher le message
    showMessage("Avis créé avec succès ! Vous allez être redirigé", "success");
    // redirection après 2 secondes
    setTimeout(() => {
      window.location.href = '/account';
    }, 2000);
  } catch (error) {
    showMessage("Erreur lors de la création de l'avis.", "danger");
  }
});