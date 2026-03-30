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

  window.location.reload();
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
                case "employee":
                  // Visible uniquement si connecté ET rôle employe
                     if(!userConnected || (role!="employee")){ 
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
