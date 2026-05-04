import Route from "./Route.js";
import { allRoutes, websiteName } from "./allRoutes.js";

// Route 404
const route404 = new Route("404", "Page introuvable", "/pages/404.html", []);

// Récupérer la route depuis l'URL
const getRouteByUrl = (url) => {
  let currentRoute = null;

  allRoutes.forEach((element) => {
    if (element.url == url) {
      currentRoute = element;
    }
  });

  return currentRoute || route404;
};

// Fonction de protection des routes
function checkAccess(route) {
  const roles = route.authorize;

  // page publique
  if (roles.length === 0) return true;

  // page visiteurs uniquement
  if (roles.includes("disconnected")) {
    if (isConnected()) {
      showMessage("Vous êtes déjà connecté", "warning");

      setTimeout(() => {
        window.location.replace("/");
      }, 3000);
      return false;
    }
    return true;
  }

  // page protégée (connexion requise)
  if (!isConnected()) {
    localStorage.setItem("redirectAfterLogin", window.location.pathname);
    showMessage("Cette page est seulement accessible après connexion, merci de vous connecter s'il vous plait", "warning");

    setTimeout(() => {
      window.location.replace("/signin");
    }, 3000);
    return false;
  }

  // vérification du rôle
  const roleUser = getRole();

  if (!roles.includes(roleUser)) {
    showMessage("Accès refusé : vous n'avez pas les droits nécessaires", "danger");

    setTimeout(() => {
      window.location.replace("/");
    }, 3000);
    return false;
  }

  return true;
}

// Charger le contenu de la page
const LoadContentPage = async () => {
  const path = window.location.pathname;
  const actualRoute = getRouteByUrl(path);

  // Vérification des accès AVANT chargement
  if (!checkAccess(actualRoute)) return;

  // Charger le HTML
  const html = await fetch(actualRoute.pathHtml).then((data) => data.text());
  document.getElementById("main-page").innerHTML = html;

  // Charger le JS de la page
  if (actualRoute.pathJS != "") {
    const scriptTag = document.createElement("script");
    scriptTag.setAttribute("type", "text/javascript");
    scriptTag.setAttribute("src", actualRoute.pathJS);
    document.body.appendChild(scriptTag);
  }

  // Titre de la page
  document.title = actualRoute.title + " - " + websiteName;

  // Mise à jour UI (navbar etc.)
  showAndHideElementForRole();
};

// Gestion clics navigation
const routeEvent = (event) => {
  event = event || window.event;
  event.preventDefault();

  // Chargement du contenu de la nouvelle page
  const url = event.currentTarget.href;

  window.history.pushState({}, "", url);
  LoadContentPage();
};

// Navigation arrière
window.onpopstate = LoadContentPage;

// Exposer la fonction globalement
window.route = routeEvent;

// Chargement initial
LoadContentPage();