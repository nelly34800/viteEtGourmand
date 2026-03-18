import Route from "./Route.js";
//Définir ici vos routes
export const allRoutes = [
    new Route("/", "Accueil", "/pages/home.html", [], "/js/home.js"),
    new Route("/contact", "Contact", "/pages/contact.html", []),
    new Route("/cgv", "CGV", "/pages/cgv.html", []),
    new Route("/mentions", "Mentions légales", "/pages/mentions.html", []),
    new Route("/signin", "Connexion", "/pages/auth/signin.html", ["disconnected"], "/js/auth/signin.js"),
    new Route("/signup", "Inscription", "/pages/auth/signup.html", ["disconnected"], "/js/auth/signup.js"),
    new Route("/account", "Mon compte","/pages/auth/account.html",  ["client"]),
    new Route("/editUserInfo", "Modifier mes informations", "/pages/auth/editUserInfo.html", ["client"], "/js/auth/editUserInfo.js"),
    new Route("/editPassword", "Modifier mon mot de passe", "/pages/auth/editPassword.html", ["client"], "/js/auth/editPassword.js"),
    new Route("/menus", "Les menus", "/pages/menus.html", []),
    new Route("/menuDetails", "Détails du menu", "/pages/menuDetails.html", [], "/js/menuDetails.js"),
    new Route("/notice", "Laisser un avis", "/pages/notice.html", ["client"]),
    new Route("/cart", "Panier", "/pages/order/cart.html", ["client"], "/js/order/cart.js"),
    new Route("/order", "Livraison commande", "/pages/order/order.html", ["client"], "/js/order/order.js"), 
    new Route("/orderConfirmation", "Confirmation de commande", "/pages/order/orderConfirmation.html", ["client"]),
    new Route("/modifyOrder", "Modifier ma commande", "/pages/order/modifyOrder.html", ["client"])

];
//Le titre s'affiche comme ceci : Route.titre - websitename
export const websiteName = "Vite & Gourmand";