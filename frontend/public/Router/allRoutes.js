import Route from "./Route.js";
//Définir ici vos routes
export const allRoutes = [
    new Route("/", "Accueil", "/pages/home.html", [], "/js/home.js"),
    new Route("/contact", "Contact", "/pages/contact.html", []),
    new Route("/cgv", "CGV", "/pages/cgv.html", []),
    new Route("/mentions", "Mentions légales", "/pages/mentions.html", []),
    new Route("/menus", "Les menus", "/pages/menus.html", []),
    new Route("/menuDetails", "Détails du menu", "/pages/menuDetails.html", [], "/js/menuDetails.js"),
    new Route("/notice", "Laisser un avis", "/pages/notice.html", ["client"]),
    // auth
    new Route("/signin", "Connexion", "/pages/auth/signin.html", ["disconnected"], "/js/auth/signin.js"),
    new Route("/signup", "Inscription", "/pages/auth/signup.html", ["disconnected"], "/js/auth/signup.js"),
    new Route("/account", "Mon compte","/pages/auth/account.html", ["client", "admin", "employee"]),
    new Route("/editUserInfo", "Modifier mes informations", "/pages/auth/editUserInfo.html", ["client"], "/js/auth/editUserInfo.js"),
    new Route("/editPassword", "Modifier mon mot de passe", "/pages/auth/editPassword.html", ["client"], "/js/auth/editPassword.js"),
    // order
    new Route("/cart", "Panier", "/pages/order/cart.html", ["client"], "/js/order/cart.js"),
    new Route("/order", "Livraison commande", "/pages/order/order.html", ["client"], "/js/order/order.js"), 
    new Route("/orderConfirmation", "Confirmation de commande", "/pages/order/orderConfirmation.html", ["client"]),
    // admin
    new Route("/admin", "Admin","/pages/admin/admin.html", ["admin"]),
    new Route("/createEmployee", "Créer un compte employé", "/pages/admin/createEmployee.html", ["admin"], "/js/admin/createEmployee.js"),
    new Route("/employeesList", "liste des employés", "/pages/admin/employeesList.html", ["admin"], "/js/admin/employeesList.js"),
    // employé
    new Route("/employee", "Espace employé", "/pages/employees/employee.html", ["admin", "employee"], "/js/employees/employee.js"),
              // allergènes
    new Route("/allergens", "Les allergènes", "/pages/employees/allergens/allergens.html", ["admin", "employee"], "/js/employees/allergens/allergens.js"),
    new Route("/createAllergen", "Créer/modifier un allergène", "/pages/employees/allergens/createAllergen.html", ["admin", "employee"], "/js/employees/allergens/createAllergen.js"),
              // régimes alimentaires
    new Route("/diets", "Les régimes alimentaires", "/pages/employees/diets/diets.html", ["admin", "employee"], "/js/employees/diets/diets.js"),
    new Route("/createDiet", "Créer/modifier un régime alimentaire", "/pages/employees/diets/createDiet.html", ["admin", "employee"], "/js/employees/diets/createDiet.js"),
];
//Le titre s'affiche comme ceci : Route.titre - websitename
export const websiteName = "Vite & Gourmand";