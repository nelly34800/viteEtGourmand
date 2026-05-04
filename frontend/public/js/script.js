// Récupération du bouton de déconnexion
const signoutBtn = document.getElementById("signout-btn");

// Si bouton
if (signoutBtn) {
  // On écoute l'événement "click"
  signoutBtn.addEventListener("click", (e) => {
    // Empêche le comportement par défaut du lien
    e.preventDefault();
    // Appelle la fonction de déconnexion
    signout();
  });
}
// Retourne le rôle 
function getRole() {
  const user = JSON.parse(localStorage.getItem('user'));
  return user ? user.role : null;
}

//vérifie si l'utilisateur est connecté
function isConnected() {
  const user = JSON.parse(localStorage.getItem('user'));
  return user !== null;
}

// Déconnecte l'utilisateur
async function signout() {
  const csrfToken = localStorage.getItem('csrf_token');

  try {
    const response = await fetch('http://localhost:8082/logout', {
      method: 'POST',
      credentials: 'include',
      headers: {
        'X-CSRF-Token': csrfToken
      }
    });
    if (!response.ok) {
        console.error("Erreur logout");
      }

  } catch (error) {
    console.error("Erreur fetch :", error);
  }

  localStorage.removeItem('user');
  localStorage.removeItem('csrf_token');

  window.location.href = "/";
}

//afficher et masquer les élément en fonction du role
function showAndHideElementForRole(){
    // Vérifie si un utilisateur est connecté
    const userConnected = isConnected();
    // Récupère son rôle
    const role = getRole();
    // Sélectionne tous les éléments ayant un attribut data-show
    let allEllementsToEdit = document.querySelectorAll('[data-show]');
    // Pour chaque élément trouvé
    allEllementsToEdit.forEach(element =>{
        // On regarde la valeur de data-show
        switch(element.dataset.show){
            case "disconnected":
              // Visible uniquement si l'utilisateur est déconnecté
                if(userConnected){
                    element.classList.add("d-none");
                }
                break;
            case "connected":
              // Visible uniquement si l'utilisateur est connecté
                if(!userConnected){
                    element.classList.add("d-none");
                }
                break;
            case "admin":
              // Visible uniquement si connecté ET rôle admin
                if(!userConnected || role!="admin"){
                    element.classList.add("d-none");
                }
                break; 
                case "employé":
                  // Visible uniquement si connecté ET rôle employe
                     if(!userConnected || (role!="employé")){ 
                        element.classList.add("d-none"); 
                    }
                break;
            case "client":
              // Visible uniquement si connecté ET rôle client
                if(!userConnected || role!="client"){
                    element.classList.add("d-none");
                }
                break;
        }
    })
}

document.addEventListener('DOMContentLoaded', showAndHideElementForRole);

function loadProfileByRole(role) {
  switch (role) {
    case "client": 
    window.location.href = "/account";
    break;

    case "employé": 
    window.location.href = "/employee";
    break;

    case "admin": 
    window.location.href = "/admin";
    break;

    default: 
    showMessage("Cette page est seulement accessible après connexion, merci de vous connecter s'il vous plait", "warning");
    // redirection après 2 secondes
    setTimeout(() => {
      window.location.href = "/signin";
    }, 2000);
  }
}
// 
document.addEventListener("click", (e) => {
  if (e.target.closest("#account")) {
    e.preventDefault();
    const role = getRole();
    loadProfileByRole(role);
  }
});
