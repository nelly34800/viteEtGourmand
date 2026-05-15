import Route from "./Route.js";
//Définir ici vos routes
export const allRoutes = [
    new Route("/", "Accueil", "/pages/home.html", [], "/js/home.js"),
    new Route("/contact", "Contact", "/pages/contact.html", [], "/js/contact.js"),
    new Route("/cgv", "CGV", "/pages/cgv.html", []),
    new Route("/mentions", "Mentions légales", "/pages/mentions.html", []),
    new Route("/menus", "Les menus", "/pages/menus.html", [], "/js/menus.js"),
    new Route("/menuDetails", "Détails du menu", "/pages/menuDetails.html", [], "/js/menuDetails.js"),
    new Route("/createNotice", "Laisser un avis", "/pages/createNotice.html", ["client"], "/js/createNotice.js"),
    // auth
    new Route("/signin", "Connexion", "/pages/auth/signin.html", ["disconnected"], "/js/auth/signin.js"),
    new Route("/signup", "Inscription", "/pages/auth/signup.html", ["disconnected"], "/js/auth/signup.js"),
    new Route("/account", "Mon compte","/pages/auth/account.html", ["client", "admin", "employé"], "/js/auth/account.js"),
    new Route("/editUserInfo", "Modifier mes informations", "/pages/auth/editUserInfo.html", ["client"], "/js/auth/editUserInfo.js"),
    new Route("/editPassword", "Récupération de mon email", "/pages/auth/editPassword.html", [], "/js/auth/editPassword.js"),
    new Route("/resetLink", "Modifier mon mot de passe", "/pages/auth/resetLink.html", [], "/js/auth/resetLink.js"),
    new Route("/changePassword", "Récupération de mon email", "/pages/auth/changePassword.html", [], "/js/auth/changePassword.js"),

    // order
    new Route("/cart", "Panier", "/pages/order/cart.html", ["client"], "/js/order/cart.js"),
    new Route("/addMaterial", "Ajouter du matériel", "/pages/order/addMaterial.html", ["client"], "/js/order/addMaterial.js"),
    new Route("/addPersonalPackages", "Ajouter un forfait de personnel", "/pages/order/addPersonalPackages.html", ["client"], "/js/order/addPersonalPackages.js"),
    new Route("/addDrinkPackages", "Ajouter un forfait de boisson", "/pages/order/addDrinkPackages.html", ["client"], "/js/order/addDrinkPackages.js"),
    new Route("/order", "Livraison commande", "/pages/order/order.html", ["client"], "/js/order/order.js"), 
    new Route("/orderConfirmation", "Confirmation de commande", "/pages/order/orderConfirmation.html", ["client"], "/js/order/orderConfirmation.js"),
    new Route("/editOrder", "Livraison commande", "/pages/order/editOrder.html", ["client"], "/js/order/editOrder.js"),
    // admin
    new Route("/admin", "Admin","/pages/admin/admin.html", ["admin"]),
    new Route("/createEmployee", "Créer un compte employé", "/pages/admin/createEmployee.html", ["admin"], "/js/admin/createEmployee.js"),
    new Route("/employeesList", "liste des employés", "/pages/admin/employeesList.html", ["admin"], "/js/admin/employeesList.js"),
    // employé
    new Route("/employee", "Espace employé", "/pages/employees/employee.html", ["admin", "employé"], "/js/employees/employee.js"),
              // allergènes
    new Route("/allergens", "Les allergènes", "/pages/employees/allergens/allergens.html", ["admin", "employé"], "/js/employees/allergens/allergens.js"),
    new Route("/createAllergen", "Créer/modifier un allergène", "/pages/employees/allergens/createAllergen.html", ["admin", "employé"], "/js/employés/allergens/createAllergen.js"),
              // régimes alimentaires
    new Route("/diets", "Les régimes alimentaires", "/pages/employees/diets/diets.html", ["admin", "employé"], "/js/employees/diets/diets.js"),
    new Route("/createDiet", "Créer/modifier un régime alimentaire", "/pages/employees/diets/createDiet.html", ["admin", "employé"], "/js/employees/diets/createDiet.js"),
              // plats
    new Route("/dishes", "Les plats", "/pages/employees/dishes/dishes.html", ["admin", "employé"], "/js/employees/dishes/dishes.js"),
    new Route("/createDish", "Créer/modifier un plat", "/pages/employees/dishes/createDish.html", ["admin", "employé"], "/js/employees/dishes/createDish.js"),
              // menus
    new Route("/menu", "Les menus", "/pages/employees/menus/menu.html", ["admin", "employé"], "/js/employees/menus/menu.js"),
    new Route("/createMenu", "Créer/modifier un menu", "/pages/employees/menus/createMenu.html", ["admin", "employé"], "/js/employees/menus/createMenu.js"),
              // horaires
    new Route("/schedules", "Les horaires", "/pages/employees/schedules/schedules.html", ["admin", "employé"], "/js/employees/schedules/schedules.js"),
    new Route("/createSchedule", "Créer/modifier un horaire", "/pages/employees/schedules/createSchedule.html", ["admin", "employé"], "/js/employees/schedules/createSchedule.js"),
              // conditions du menu
    new Route("/conditionsMenu", "Les conditions des menus", "/pages/employees/conditionsMenu/conditionsMenu.html", ["admin", "employé"], "/js/employees/conditionsMenu/conditionsMenu.js"),
    new Route("/createConditionMenu", "Créer/modifier une condition de menu", "/pages/employees/conditionsMenu/createConditionMenu.html", ["admin", "employé"], "/js/employees/conditionsMenu/createConditionMenu.js"),
              // forfaits boissons
    new Route("/drinkPackages", "Les forfaits de boissons", "/pages/employees/drinkPackages/drinkPackages.html", ["admin", "employé"], "/js/employees/drinkPackages/drinkPackages.js"),
    new Route("/createDrinkPackage", "Créer/modifier un forfait de boissons", "/pages/employees/drinkPackages/createDrinkPackage.html", ["admin", "employé"], "/js/employees/drinkPackages/createDrinkPackage.js"),
              // forfaits de personnel
    new Route("/personalPackages", "Les forfaits de personnel", "/pages/employees/personalPackages/personalPackages.html", ["admin", "employé"], "/js/employees/personalPackages/personalPackages.js"),
    new Route("/createPersonalPackage", "Créer/modifier un forfait de personnel", "/pages/employees/personalPackages/createPersonalPackage.html", ["admin", "employé"], "/js/employees/personalPackages/createPersonalPackage.js"),
              // materiel
    new Route("/material", "Le materiel", "/pages/employees/material/material.html", ["admin", "employé"], "/js/employees/material/material.js"),
    new Route("/createMaterial", "Créer/modifier du materiel", "/pages/employees/material/createMaterial.html", ["admin", "employé"], "/js/employees/material/createMaterial.js")
];
//Le titre s'affiche comme ceci : Route.titre - websitename
export const websiteName = "Vite & Gourmand";