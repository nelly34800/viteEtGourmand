// Implémenter js de ma page
const lastName = document.getElementById("lastName");
const firstName = document.getElementById("firstName");
const address = document.getElementById("address");
const postalCode = document.getElementById("postalCode");
const city = document.getElementById("city");
const phone = document.getElementById("phone");
const btnModification = document.getElementById("validationModification");
const deleteAccount = document.getElementById("deleteAccount");

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

  if(nomOk && prenomOk && addressOk && postalCodeOk && cityOk && phoneOk){
  btnModification.disabled = false;
  }
  else{
  btnModification.disabled = true;
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
//fonction pour remplir le formulaire avec les données de l'utilisateur
function fillForm(user) {
  document.getElementById('lastName').value = user.last_name;
  document.getElementById('firstName').value = user.first_name;
  document.getElementById('email').value = user.email;
  document.getElementById('address').value = user.postal_address;
  document.getElementById('postalCode').value = user.postal_code;
  document.getElementById('city').value = user.city;
  document.getElementById('phone').value = user.phone;
  // bloquer le champ email
  const emailInput = document.getElementById('email');
  emailInput.disabled = true;
  emailInput.title = "L'adresse email n'est pas modifiable";
}
//récupération de l'utilisateur par son id
const user = JSON.parse(localStorage.getItem('user'));
if (!user) {
  alert("Utilisateur non connecté !");
  window.location.href = "/signin";
}
const userId = user.id;

// charger les données de l'utilisateur
async function loadUserData() {
  const csrfToken = localStorage.getItem('csrf_token');

  try {
    const response = await fetch(`http://localhost:8082/user/${userId}`, {
      method: 'GET',
      credentials: 'include',
      headers: {
        'X-CSRF-Token': csrfToken
      }
    });

    const data = await response.json();

    if (!response.ok) {
      console.error(data);
      return;
    }
    // remplissage des champs
    fillForm(data);

  } catch (error) {
    console.error("Erreur fetch :", error);
  }
}
// modifier l'utilisateur
async function editUserInfo(e){
  e.preventDefault();

  const updatedData = {
  last_name: document.getElementById('lastName').value,
  first_name: document.getElementById('firstName').value,
  postal_address: document.getElementById('address').value,
  city: document.getElementById('city').value,
  postal_code: document.getElementById('postalCode').value,
  phone: document.getElementById('phone').value
  };

  try {
    const response = await fetch(`http://localhost:8082/user/${userId}`, {
      method: 'PUT',
      credentials: 'include',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-Token': localStorage.getItem('csrf_token')
      },
      body: JSON.stringify(updatedData)
    });

    const data = await response.json();
    const messageDiv = document.getElementById('edit-user-message');

    if (response.ok) {
      // afficher le message
      messageDiv.textContent = "Modification réussie ! Vous allez être redirigé sur la page d'accueil";
      messageDiv.classList.remove("d-none");
      messageDiv.classList.add("alert-success");

      // redirection après 3 secondes
      setTimeout(() => {
        window.location.href = '/';
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
}
// ecoute de l'événement submit du formulaire
document.querySelector('form').addEventListener('submit', editUserInfo);
// charger les données de l'utilisateur à l'ouverture de la page
loadUserData();

//supprimer l'utilisateur
deleteAccount.addEventListener("click", async function() {
  if (!confirm("Êtes-vous sûr de vouloir supprimer votre compte ? Cette action est irréversible.")) {
    return;
  }
  const user = JSON.parse(localStorage.getItem('user'));
  const userId = user?.id;
  const csrfToken = localStorage.getItem('csrf_token');

  try {
    const response = await fetch(`http://localhost:8082/user/${userId}`, {
      method: 'DELETE',
      credentials: 'include',
      headers: {
        'X-CSRF-Token': csrfToken
      }
    });

    const data = await response.json();
    const messageDiv = document.getElementById('delete-user-message');

    if (response.ok) {
      // afficher le message
      messageDiv.textContent = "Suppression réussie ! Vous allez être redirigé sur la page d'accueil'";
      messageDiv.classList.remove("d-none");
      messageDiv.classList.add("alert-success");

      // nettoyage côté front
      localStorage.removeItem('user');
      localStorage.removeItem('csrf_token');

      // redirection après 3 secondes
      setTimeout(() => {
        window.location.href = '/';
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