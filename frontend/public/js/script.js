// Nom du cookie contenant le token d'authentification (JWT ou autre)
const tokenCookieName  = "accesstoken";
// nom du rôle(admin, employe, client)
const roleCookieName = "role";
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
// Retourne le rôle stocké dans le cookie
function getRole(){
    return getCookie(roleCookieName);
}

// Déconnecte l'utilisateur
function signout(){
    // Supprime le cookie du token
    eraseCookie(tokenCookieName);
    // Supprime le cookie du rôle
    eraseCookie(roleCookieName);
    // Recharge la page pour réinitialiser l'affichage
    globalThis.location.reload();
}
// Stocke le token dans un cookie pour 7 jours
function setToken (token){
    setCookie(tokenCookieName, token, 7);
}
// Récupère le token depuis le cookie
function getToken (){
    return getCookie(tokenCookieName);
}
// Crée ou modifie un cookie
function setCookie(name,value,days) {
    let expires = "";
      // Si une durée est définie
    if (days) {
        let date = new Date();
        // Convertit les jours en millisecondes
        date.setTime(date.getTime() + (days*24*60*60*1000));
        // Formatage de la date d'expiration
        expires = "; expires=" + date.toUTCString();
    }
    // Création du cookie sur tout le site
    document.cookie = name + "=" + (value || "")  + expires + "; path=/";
}
// Récupère la valeur d'un cookie à partir de son nom
function getCookie(name) {
    let nameEQ = name + "=";
    // Sépare les cookies dans un tableau
    let ca = document.cookie.split(';');
    for(const element of ca) {
        let c = element;
             // Supprime les espaces au début
        while (c.startsWith(' ')) c = c.substring(1,c.length);
        // Si le cookie correspond au nom recherché
        if (c.startsWith(nameEQ)) 
            // Retourne uniquement la valeur
            return c.substring(nameEQ.length,c.length);
    }
    // Retourne null si le cookie n'existe pas
    return null;
}
// Supprime un cookie en définissant une date d'expiration passée
function eraseCookie(name) {
    document.cookie = name +'=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
}
//vérifie si l'utilisateur est connecté
function isConnected(){
  // Retourne true si un token est présent, sinon false
  return getToken() !== null;
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
