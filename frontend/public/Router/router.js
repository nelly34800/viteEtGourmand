import Route from "./Route.js";
import { allRoutes, websiteName } from "./allRoutes.js";

// Route 404
const route404 = new Route("/404", "Page introuvable", "/pages/404.html");

// Trouver la bonne route
const getRouteByUrl = (url) => {
  const route = allRoutes.find(
    (r) => r.url.toLowerCase() === url.toLowerCase()
  );
  return route || route404;
};

// Charger la page
const LoadContentPage = async () => {
  const path = window.location.pathname;
  const actualRoute = getRouteByUrl(path);

  console.log("Chemin chargé :", actualRoute.pathHtml); // ← ajoute ça

  try {
    const res = await fetch(actualRoute.pathHtml);
    if (!res.ok) {
      throw new Error("Page introuvable");
    }
    const html = await res.text();
    document.getElementById("main-page").innerHTML = html;
  } catch (err) {
    console.error("Erreur de chargement :", err);
    const res404 = await fetch("/pages/404.html");
    const html404 = await res404.text();
    document.getElementById("main-page").innerHTML = html404;
  }

  // Charger JS associé
  if (actualRoute.pathJS) {
    const scriptTag = document.createElement("script");
    scriptTag.type = "text/javascript";
    scriptTag.src = actualRoute.pathJS;
    document.body.appendChild(scriptTag);
  }

  // Modifier le titre
  document.title = `${actualRoute.title} - ${websiteName}`;
};

// Gérer clics internes
const routeEvent = (event) => {
  event.preventDefault();
  const href = event.currentTarget.href;
  window.history.pushState({}, "", href);
  LoadContentPage();
};

// Gérer retour arrière/avant
window.onpopstate = LoadContentPage;

// Rendre accessible globalement
window.route = routeEvent;

// Charger la page au démarrage
LoadContentPage();
