SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

CREATE TABLE role(
  id CHAR(36) NOT NULL PRIMARY KEY DEFAULT (UUID()),
  role_name enum('client', 'employé', 'admin') NOT NULL DEFAULT 'client'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `role` ( `role_name`) VALUES 
('client'),
('employé'),
('admin');

CREATE TABLE schedule(
  id CHAR(36) NOT NULL PRIMARY KEY DEFAULT (UUID()),
  schedule_name VARCHAR(255) NOT NULL,
  first_day VARCHAR(50) NOT NULL,
  last_day VARCHAR(50) NOT NULL,
  opening_time TIME NOT NULL,
  closing_time TIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `schedule` (`schedule_name`,`first_day`, `last_day`, `opening_time`, `closing_time`) VALUES
('horaires classiques','Mardi', 'Dimanche', '11:00:00', '22:00:00');

CREATE TABLE diet(
  id CHAR(36) NOT NULL PRIMARY KEY DEFAULT (UUID()),
  diet_name VARCHAR(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `diet` (`diet_name`) VALUES
('Végétarien'),
('Vegan'),
('Sans gluten'),
('Sans lactose'),
('Sans porc');

CREATE TABLE allergen(
  id CHAR(36) NOT NULL PRIMARY KEY DEFAULT (UUID()),
  allergen_name VARCHAR(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `allergen` (`allergen_name`) VALUES
('Gluten'),
('Lactose'),
('Fruits à coque'),
('Sésame'),
('Oeufs'),
('Crustacés'),
('Sulfites'),
('Moutarde'),
('Mollusques');

CREATE TABLE category_dish(
  id CHAR(36) NOT NULL PRIMARY KEY DEFAULT (UUID()),
  category_name enum('Entrées', 'Plats principaux', 'Desserts', 'Fromages') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `category_dish` (`category_name`) VALUES
('Entrées'),
('Plats principaux'),
('Desserts'),
('Fromages');

CREATE TABLE dish(
  id CHAR(36) NOT NULL PRIMARY KEY DEFAULT (UUID()),
  dish_title VARCHAR(250) NOT NULL,
  description TEXT NOT NULL,
  picture VARCHAR(250) NOT NULL,
  id_category_dish CHAR(36) NOT NULL,
  FOREIGN KEY(id_category_dish) REFERENCES category_dish(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `dish` (`dish_title`, `description`, `picture`, `id_category_dish`) VALUES
('Velouté de potimarron', 'Velouté onctueux de potimarron rôti, relevé d’une pointe de crème et parsemé de noisettes torréfiées.', 'veloute_potimarron_noisette.jpeg', (SELECT id FROM category_dish WHERE category_name = 'Entrées')),
('Salade de chèvre chaud sur toast', 'Jeunes pousses, toasts croustillants au chèvre fondant, vinaigrette maison au miel.', 'salade_chevre.jpg', (SELECT id FROM category_dish WHERE category_name = 'Entrées')),
('Houmous de pois chiches et légumes croquants', 'Purée de pois chiches au sésame, citron et huile d’olive, accompagnée de légumes frais.', 'Houmous_legume.jpg', (SELECT id FROM category_dish WHERE category_name = 'Entrées')),
('Foie gras de canard mi-cuit', 'Foie gras de canard mi-cuit accompagné d’un chutney de figues maison.', 'foie_gras_mi_cuit.png', (SELECT id FROM category_dish WHERE category_name = 'Entrées')),
('Noix de Saint-Jacques poêlées, crème légère au citron vert', 'Saint-Jacques poêlées, servies avec une crème citronnée délicate.', 'saint_jacques.jpg', (SELECT id FROM category_dish WHERE category_name = 'Entrées')),
('Asperges vertes, vinaigrette citronnée', 'Asperges fraîches accompagnées d’une vinaigrette légère au citron.', 'asperge.jpg', (SELECT id FROM category_dish WHERE category_name = 'Entrées')),
('Œuf parfait, crème de champignons', 'Œuf cuit à basse température accompagné d’une crème de champignons.', 'oeuf_champi.jpg', (SELECT id FROM category_dish WHERE category_name = 'Entrées')),
('Planche apéritive à partager', 'Sélection de charcuteries, fromages et pains régionaux.', 'planche_aperitive.jpg', (SELECT id FROM category_dish WHERE category_name = 'Entrées')),
('Salade fraîcheur tomates et concombre', 'Salade fraîche aux herbes.', 'salade_fraicheur.jpg', (SELECT id FROM category_dish WHERE category_name = 'Entrées')),
('Velouté de légumes de saison', 'Velouté de légumes avec crème légère, croûtons dorés.', 'veloute_potimarron_noisette.jpeg', (SELECT id FROM category_dish WHERE category_name = 'Entrées')),
('Œuf mimosa revisité', 'mayonnaise légère, herbes fraîches', 'oeuf_mimosa.jpg', (SELECT id FROM category_dish WHERE category_name = 'Entrées')),
('Salades composées (au choix)', 'Salade de pâtes, légumes grillés et pesto et/ou Taboulé de quinoa aux herbes fraîches et/ou Salade de pois chiches, concombre et citron', 'salade.jpg', (SELECT id FROM category_dish WHERE category_name = 'Entrées')),
('Verrines & tartinables', 'Houmous de pois chiches au sésame et/ou Fromage frais aux herbes et/ou Tapenade d’olives noires', 'verrine.jpg', (SELECT id FROM category_dish WHERE category_name = 'Entrées')),
('Suprême de volaille fermière, sauce forestière', 'Suprême de volaille rôtie, sauce crémeuse aux champignons, gratin dauphinois maison.', 'Supreme_volaille.jpeg', (SELECT id FROM category_dish WHERE category_name = 'Plats principaux')),
('Gambas flambées au pastis, riz basmati parfumé', 'Gambas sautées et flambées au pastis, servies avec un riz basmati délicatement parfumé.', 'gambas.jpg', (SELECT id FROM category_dish WHERE category_name = 'Plats principaux')),
('Curry de légumes de saison au lait de coco', 'Mélange de légumes frais mijotés dans un curry doux au lait de coco.', 'curry.jpg', (SELECT id FROM category_dish WHERE category_name = 'Plats principaux')),
('Ballotin de chapon aux marrons, purée de patates douces', 'Chapon farci aux marrons, servi avec une purée de patates douces aux épices douces.', 'puree_patates_douces.jpg', (SELECT id FROM category_dish WHERE category_name = 'Plats principaux')),
('Gigot d’agneau rôti aux herbes', 'Gigot d’agneau rôti, servi avec des pommes grenaille rôties.', 'gigot_agneau.jpg', (SELECT id FROM category_dish WHERE category_name = 'Plats principaux')),
('Filet de veau rôti, jus corsé au romarin', 'Filet de veau servi avec un écrasé de pommes de terre à l’huile d’olive.', 'filet.jpg', (SELECT id FROM category_dish WHERE category_name = 'Plats principaux')),
('Suprême de poulet rôti, riz pilaf aux légumes', 'Poulet rôti servi avec un riz pilaf aux légumes.', 'supreme_poulet.jpg', (SELECT id FROM category_dish WHERE category_name = 'Plats principaux')),
('Burger gourmet au bœuf, cheddar affiné', 'Burger maison accompagné de frites.', 'burger.jpg', (SELECT id FROM category_dish WHERE category_name = 'Plats principaux')),
('Burger végétarien gourmet, pommes grenaille', 'Burger végétarien maison accompagné de pommes grenaille.', 'burger_vege.jpg', (SELECT id FROM category_dish WHERE category_name = 'Plats principaux')),
('Brochettes de bœuf marinées', 'Brochettes de bœuf marinées, servies avec des frites maison.', 'brochette_boeuf.jpg', (SELECT id FROM category_dish WHERE category_name = 'Plats principaux')),
('Suprême de volaille fermière pané maison et ses frites', 'Filet de poulet avec sa panure fine aux herbes.', 'supreme_volaille_pane.jpg', (SELECT id FROM category_dish WHERE category_name = 'Plats principaux')),
('Steak de bœuf Charolais, frites maison', 'Steak de bœuf grillé, frites croustillantes maison.', 'steak1.jpg', (SELECT id FROM category_dish WHERE category_name = 'Plats principaux')),
('Viandes & poissons', 'Brochettes de poulet mariné aux herbes et/ou Mini brochettes de bœuf marinées et/ou Gambas rôties au paprika doux', 'mini_brochette.jpg', (SELECT id FROM category_dish WHERE category_name = 'Plats principaux')),
('Options végétariennes & vegan', 'Falafels de pois chiches, sauce tahini et/ou Légumes rôtis de saison et/ou Mini quiches aux légumes', 'buffet.jpg', (SELECT id FROM category_dish WHERE category_name = 'Plats principaux')),
('Plateau de fromages affinés', 'Sélection de fromages régionaux affinés.', 'assiette_fromage.jpeg', (SELECT id FROM category_dish WHERE category_name = 'Fromages')),
('Alternative végétale aux noix de cajou', 'Préparation végétale crémeuse à base de noix de cajou.', 'preparation_fromagere.jpg', (SELECT id FROM category_dish WHERE category_name = 'Fromages')),
('Tarte fine aux pommes caramélisées', 'Pommes fondantes caramélisées sur pâte croustillante.', 'tarte.jpg', (SELECT id FROM category_dish WHERE category_name = 'Desserts')),
('Brownie vegan au chocolat', 'Brownie fondant au chocolat noir, sans ingrédient d’origine animale.', 'brownie_vegan.png', (SELECT id FROM category_dish WHERE category_name = 'Desserts')),
('Bûche chocolat praliné', 'Bûche de Noël au chocolat et praliné.', 'buche_chocolat_praline.png', (SELECT id FROM category_dish WHERE category_name = 'Desserts')),
('Fraisier traditionnel', 'Génoise, crème mousseline et fraises fraîches.', 'fraisier.jpg', (SELECT id FROM category_dish WHERE category_name = 'Desserts')),
('Entremets framboise & chocolat blanc', 'Entremets délicat aux fruits rouges et chocolat blanc.', 'entremet_framb_choc.jpg', (SELECT id FROM category_dish WHERE category_name = 'Desserts')),
('Brochettes de fruits frais', 'Fruits frais de saison.', 'brochette_fruits.jpg', (SELECT id FROM category_dish WHERE category_name = 'Desserts')),
('Moelleux au chocolat, crème anglaise', 'Moelleux au chocolat servi avec une crème anglaise.', 'moelleux_choco.jpg', (SELECT id FROM category_dish WHERE category_name = 'Desserts')),
('Brownie chocolat & caramel beurre salé', 'Brownie au chocolat et caramel beurre salé.', 'brownie_caramel.jpg', (SELECT id FROM category_dish WHERE category_name = 'Desserts')),
('Desserts format mini', 'Brownie chocolat & caramel beurre salé et/ou Mini tartelettes aux fruits de saison et/ou Brochettes de fruits frais et/ou Brownie vegan au chocolat', 'brownie_caramel.jpg', (SELECT id FROM category_dish WHERE category_name = 'Desserts'));

CREATE TABLE menu(
  id CHAR(36) NOT NULL PRIMARY KEY DEFAULT (UUID()),
  menu_name VARCHAR(250) NOT NULL,
  theme VARCHAR(250) NOT NULL,
  description TEXT NOT NULL,
  illustration_dish_id CHAR(36) NOT NULL,
  minimum_people INT NOT NULL,
  price_per_person DECIMAL(10,2) NOT NULL, 
  remaining_quantity INT NOT NULL,
  FOREIGN KEY(illustration_dish_id) REFERENCES dish(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `menu` (`menu_name`, `theme`, `description`, `illustration_dish_id`, `minimum_people`, `price_per_person`, `remaining_quantity`) VALUES
('Menu Classique (toute saison)', 'Classique', 'Un menu gourmand, équilibré et généreux, mettant à l’honneur des recettes traditionnelles revisitées, adaptées à tous vos événements tout au long de l’année.', (SELECT id FROM dish WHERE dish_title = 'Velouté de potimarron'), 6, 32.00, 20),
('Menu Festif de Noël', 'Noël',  'Un menu raffiné aux saveurs festives, pensé pour sublimer vos repas de fin d’année avec des produits emblématiques et gourmands.', (SELECT id FROM dish WHERE dish_title = 'Bûche chocolat praliné'), 10, 49.00, 8),
('Menu Mariage – Élégance & Raffinement', 'Mariage', 'Un menu d’exception, élégant et sophistiqué, conçu pour accompagner les moments uniques de votre réception de mariage.', (SELECT id FROM dish WHERE dish_title = 'Noix de Saint-Jacques poêlées, crème légère au citron vert'), 40, 35.00, 5),
('Option Buffet – Convivial & Modulable', 'Buffet', 'Une formule flexible et variée, idéale pour tout événement, offrant un large choix de plats à partager adaptés à tous les régimes alimentaires.', (SELECT id FROM dish WHERE dish_title = 'Options végétariennes & vegan'), 20, 15.00, 20),
('Menu Printanier / Pâques', 'Pâques', 'Un menu frais et de saison, aux notes printanières, idéal pour célébrer Pâques et les repas conviviaux autour de produits délicats.', (SELECT id FROM dish WHERE dish_title = 'Gigot d’agneau rôti aux herbes'), 8, 35.00, 12),
('Menu Communion – Tradition & Douceur', 'Communion', 'Un menu familial et réconfortant, mêlant tradition et douceur, parfaitement adapté aux repas de communion et aux rassemblements intergénérationnels.', (SELECT id FROM dish WHERE dish_title = 'Suprême de poulet rôti, riz pilaf aux légumes'), 15, 35.00, 10),
('Menu EVC – Convivial & Gourmand', 'Enterrement de célibat', 'Un menu convivial et généreux, pensé pour le partage et la bonne humeur lors de vos événements festifs entre amis.', (SELECT id FROM dish WHERE dish_title = 'Planche apéritive à partager'), 10, 19.00, 18),
('Menu Enfant – « Petit Gourmet »', 'Enfant', 'Un menu ludique et élaboré avec des plats simples et savoureux pour satisfaire les plus jeunes gourmands.', (SELECT id FROM dish WHERE dish_title = 'Steak de bœuf Charolais, frites maison'), 1, 12.00, 50);

CREATE TABLE material_category(
  id CHAR(36) NOT NULL PRIMARY KEY DEFAULT (UUID()),
  material_category_name enum('vaisselle', 'linge de table', 'verrerie', 'décoration de table', 'équipement') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `material_category` (`material_category_name`) VALUES
('vaisselle'),
('linge de table'),
('verrerie'),
('décoration de table'),
('équipement');

CREATE TABLE material(
  id CHAR(36) NOT NULL PRIMARY KEY DEFAULT (UUID()),
  material_name VARCHAR(250) NOT NULL,
  quantity_available INT NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  id_material_category CHAR(36) NOT NULL,
  FOREIGN KEY(id_material_category) REFERENCES material_category(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `material` (`material_name`, `quantity_available`, `price`, `id_material_category`) VALUES
('assiette plate :', 500, 1.00, (SELECT id FROM material_category WHERE material_category_name = 'vaisselle')),
('assiette à dessert', 500, 1.00, (SELECT id FROM material_category WHERE material_category_name = 'vaisselle')),
('plat de service', 100, 5.00, (SELECT id FROM material_category WHERE material_category_name = 'vaisselle')),
('verre à vin', 500, 1.50, (SELECT id FROM material_category WHERE material_category_name = 'verrerie')),
('verre à eau', 500, 1.50, (SELECT id FROM material_category WHERE material_category_name = 'verrerie')),
('coupe à champagne', 500, 1.50, (SELECT id FROM material_category WHERE material_category_name = 'verrerie')),
('carafe à eau', 50, 5.00, (SELECT id FROM material_category WHERE material_category_name = 'verrerie')),
('carafe à vin', 50, 5.00, (SELECT id FROM material_category WHERE material_category_name = 'verrerie')),
('couverts en inox', 500, 1.50, (SELECT id FROM material_category WHERE material_category_name = 'vaisselle')),
('nappe en coton blanche', 50, 5.00, (SELECT id FROM material_category WHERE material_category_name = 'linge de table')),
('chemin de table en lin', 50, 5.00, (SELECT id FROM material_category WHERE material_category_name = 'linge de table')),
('centre de table floral', 20, 7.00, (SELECT id FROM material_category WHERE material_category_name = 'décoration de table')),
('décoration de chaise', 300, 2.00, (SELECT id FROM material_category WHERE material_category_name = 'décoration de table')),
('machine à café professionnelle', 2, 15.00, (SELECT id FROM material_category WHERE material_category_name = 'équipement')),
('machine à glaçons', 2, 15.00, (SELECT id FROM material_category WHERE material_category_name = 'équipement')),
('plancha à gaz', 2, 20.00, (SELECT id FROM material_category WHERE material_category_name = 'équipement')),
('barbecue à gaz', 2, 20.00, (SELECT id FROM material_category WHERE material_category_name = 'équipement')),
('glacière électrique', 2, 15.00, (SELECT id FROM material_category WHERE material_category_name = 'équipement')),
('armoire froide pour buffet', 2, 35.00, (SELECT id FROM material_category WHERE material_category_name = 'équipement'));

CREATE TABLE drink_package(
  id CHAR(36) NOT NULL PRIMARY KEY DEFAULT (UUID()),
  drink_package_name VARCHAR(255) NOT NULL,
  price_per_person INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `drink_package` (`drink_package_name`, `price_per_person`) VALUES
('Forfait vin d’honneur (vin blanc, rosé et rouge, bière, pastis et whisky)', 25),
('Forfait vin repas (blanc, rosé et rouge)', 12),
('Forfait soft (eau plates,  gazeuse, soda, jus de fruit)', 8),
('Forfait champagne', 20);

CREATE TABLE personal_package(
  id CHAR(36) NOT NULL PRIMARY KEY DEFAULT (UUID()),
  event_type VARCHAR(250) NOT NULL,
  staff_ratio INT NOT NULL,
  package_price INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `personal_package` (`event_type`, `staff_ratio`, `package_price`) VALUES
('Mariage', 15, 30),
('Buffet', 30, 20),
('Classique à table', 20, 30);

CREATE TABLE condition_menu(
  id CHAR(36) NOT NULL PRIMARY KEY DEFAULT (UUID()),
  condition_type VARCHAR(255) NOT NULL,
  description TEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `condition_menu` (`condition_type`, `description`) VALUES
('Délai de commande', '5 jours avant l’événement.'),
('Délai de commande', 'ajustement du nombre jusqu’à 72h avant l’événement.'),
('Délai de commande', '10 jours avant l’événement.'),
('Délai de commande', '7 jours avant l’événement.'),
('Produits frais', 'foie gras, Saint-Jacques → stock limité.'),
('Confirmation définitive du nombre d''invités', '48h avant l’événement.'),
('Régimes alimentaires', 'tous nos menus comprennent des options vegan, donc sans lactose et sans porc.'),
('Conservation', 'plats froids : +4°C.'),
('Conservation', 'plats chauds : à consommer sous 2h après livraison.'),
('Produits saisonniers', 'disponibilité variable.'),
('Délai de commande', 'réservation minimum 1 mois à l’avance.');

CREATE TABLE user(
  id CHAR(36) NOT NULL PRIMARY KEY DEFAULT (UUID()),
  last_name VARCHAR(50) NOT NULL,
  first_name VARCHAR(50) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  postal_address VARCHAR(255) NOT NULL,
  city VARCHAR(50) NOT NULL,
  postal_code VARCHAR(50) NOT NULL,
  phone VARCHAR(50) NOT NULL,
  id_role CHAR(36) NOT NULL,
  FOREIGN KEY(id_role) REFERENCES role(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `user` (`last_name`, `first_name`, `email`, `password`, `postal_address`, `city`, `postal_code`, `phone`, `id_role`) VALUES
('Test', 'Mathieu', 'mathieu@test.com', '$2y$10$mzgmNoiO8EsFNmKcKKp.jeJze4qd64bpRHsVKcGTFmv7Qu0ETkVHK', '123 Rue de la Paix', 'Bordeaux', '33000', '06 12 34 56 78', (SELECT id FROM role WHERE role_name = 'client')),
('Test', 'Marie', 'marie@test.com', '$2y$10$mzgmNoiO8EsFNmKcKKp.jeJze4qd64bpRHsVKcGTFmv7Qu0ETkVHK', '456 Avenue des Champs', 'Bordeaux', '33000', '06 12 34 56 79', (SELECT id FROM role WHERE role_name = 'client')),
('Test', 'Elise', 'elise@test.com', '$2y$10$mzgmNoiO8EsFNmKcKKp.jeJze4qd64bpRHsVKcGTFmv7Qu0ETkVHK', '789 Boulevard Saint-Michel', 'Bordeaux', '33000', '06 12 34 56 80', (SELECT id FROM role WHERE role_name = 'client')),
('Test', 'Sophie', 'sophie.empl1@test.com', '$2y$10$SoGpgkGNrlvfJiKU9CpLgOPPL9HaCV4e3tSbjhOI391tZI7nlsNGy', '12 rue des Saveurs', 'Bordeaux', '33000', '06 12 34 56 78', (SELECT id FROM role WHERE role_name = 'employé')),
('Admin', 'José', 'jose.admin@test.com', '$2y$10$GxFGCM7acUe9BrMooaIxXeiAhLy10UuuS9KuGGdBQe/ATMaQTHtWu', '12 rue des Saveurs', 'Bordeaux', '33000', '06 12 34 56 81', (SELECT id FROM role WHERE role_name = 'admin'));

CREATE TABLE password_reset (
    id CHAR(36) NOT NULL PRIMARY KEY DEFAULT (UUID()),
    token CHAR(64) NOT NULL,
    expires_at DATETIME NOT NULL,
    id_user CHAR(36) NOT NULL,
    FOREIGN KEY (id_user) REFERENCES user(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE orders(
  id CHAR(36) NOT NULL PRIMARY KEY DEFAULT (UUID()),
  order_date DATE NOT NULL,
  service_date DATETIME NOT NULL,
  delivery_address VARCHAR(255) NOT NULL,
  city VARCHAR(50) NOT NULL,
  postal_code VARCHAR(10) NOT NULL,
  latitude DECIMAL(10,8) NOT NULL,
  longitude DECIMAL(11,8) NOT NULL,
  distance_km DECIMAL(10,2) NOT NULL,
  number_of_people INT NOT NULL,
  delivery_charges DECIMAL(10,2) NOT NULL,
  total_amount DECIMAL(10,2) NOT NULL,
  status enum('en attente', 'accepté', 'en préparation', 'en cours de livraison', 'livrée', 'attente retour matériel', 'terminée', 'annulée') NOT NULL DEFAULT 'en attente',
  status_changed_at DATETIME NULL,
  equipment_loan TINYINT(1) NOT NULL,
  equipment_return TINYINT(1) NOT NULL,
  cancellation_reason TEXT NULL,
  contact_mode VARCHAR(50) NULL,
  id_user CHAR(36) NOT NULL,
  FOREIGN KEY(id_user) REFERENCES user(id)
  ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `orders` (`order_date`, `service_date`, `delivery_address`, `city`, `postal_code`, `latitude`, `longitude`, `distance_km`, `number_of_people`, `delivery_charges`, `total_amount`, `status`, `equipment_loan`, `equipment_return`, `id_user`) VALUES
('2026-01-15', '2026-01-30 19:00:00', '123 Rue de la Paix', 'Bordeaux', '33000', 44.837789, -0.579180, 0, 50, 120.00, 1940.00,  'terminée', 1, 0, (SELECT id FROM user WHERE email = 'mathieu@test.com')),
('2026-01-16', '2026-04-14 12:00:00', '456 Avenue des Champs', 'Bordeaux', '33000',  44.837789, -0.579180, 0, 30, 80.00, 1050.00, 'terminée', 0, 0, (SELECT id FROM user WHERE email = 'marie@test.com')),
('2026-01-17', '2026-06-20 18:30:00', '789 Boulevard Saint-Michel', 'Bordeaux', '33888', 44.837789, -0.579180, 0, 25, 55.55, 875.00, 'terminée', 1, 1, (SELECT id FROM user WHERE email = 'elise@test.com'));

CREATE TABLE notice(
  id CHAR(36) NOT NULL PRIMARY KEY DEFAULT (UUID()),
  note INT NOT NULL,
  description TEXT NOT NULL,
  signature VARCHAR(50) NOT NULL,
  status  enum('en attente', 'validé') NOT NULL DEFAULT 'en attente',
  date DATE NOT NULL,
  id_order CHAR(36) NOT NULL UNIQUE,
  FOREIGN KEY(id_order) REFERENCES orders(id)
  ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO notice (note, description, signature, status, date, id_order) values
  (5, 'Mon mariage s’est très bien passé grâce au sérieux de l’équipe.', 'Mathieu33', 'validé', '2026-01-15', (SELECT id FROM orders WHERE order_date = '2026-01-15')),
  (4, 'Traiteur très sérieux.', 'RobinDesBois', 'en attente', '2026-01-16', (SELECT id FROM orders WHERE order_date = '2026-01-16')),
  (5, 'Merci encore pour la qualité et la présentation. Merci à Julie, José et toute la team.', 'EliseB', 'validé', '2026-01-17', (SELECT id FROM orders WHERE order_date = '2026-01-17'));

CREATE TABLE order_material(
  id_order CHAR(36) NOT NULL,
  id_material CHAR(36) NOT NULL,
  PRIMARY KEY (id_order, id_material),
  quantity INT NOT NULL,
  unit_price DECIMAL(10,2) NOT NULL,
  subtotal DECIMAL(10,2) NOT NULL,
  FOREIGN KEY(id_order) REFERENCES orders(id)
  ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY(id_material) REFERENCES material(id)
  ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE order_menu(
  id_order CHAR(36)  NOT NULL,
  id_menu CHAR(36) NOT NULL,
  PRIMARY KEY (id_order, id_menu),
  number_people INT NOT NULL,
  price_person DECIMAL(10,2) NOT NULL,
  discount_amount DECIMAL(10,2) NOT NULL,
  subtotal DECIMAL(10,2) NOT NULL,
  FOREIGN KEY(id_order) REFERENCES orders(id)
  ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY(id_menu) REFERENCES menu(id)
  ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO order_menu (id_order, id_menu, number_people, price_person, discount_amount, subtotal) VALUES
((SELECT id FROM orders WHERE order_date = '2026-01-15'),
  (SELECT id FROM menu WHERE menu_name='Menu Classique (toute saison)'), 30, 32.00, 0.00, 960.00),
((SELECT id FROM orders WHERE order_date = '2026-01-15'),
  (SELECT id FROM menu WHERE menu_name='Menu Festif de Noël'), 20, 49.00, 0.00, 980.00),
((SELECT id FROM orders WHERE order_date = '2026-01-16'),
  (SELECT id FROM menu WHERE menu_name='Menu Printanier / Pâques'), 30, 35.00, 0.00, 1050.00),
((SELECT id FROM orders WHERE order_date = '2026-01-17'),
  (SELECT id FROM menu WHERE menu_name='Menu Mariage – Élégance & Raffinement'), 25, 35.00, 0.00, 875.00);

CREATE TABLE menu_dish(
  id_menu CHAR(36) NOT NULL,
  id_dish CHAR(36) NOT NULL,
  PRIMARY KEY (id_menu, id_dish),
  FOREIGN KEY(id_dish) REFERENCES dish(id)
  ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY(id_menu) REFERENCES menu(id)
  ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO menu_dish (id_menu, id_dish) VALUES
((SELECT id FROM menu WHERE menu_name='Menu Classique (toute saison)'),
  (SELECT id FROM dish WHERE dish_title='Velouté de potimarron')),
((SELECT id FROM menu WHERE menu_name='Menu Classique (toute saison)'),
  (SELECT id FROM dish WHERE dish_title='Salade de chèvre chaud sur toast')),
((SELECT id FROM menu WHERE menu_name='Menu Classique (toute saison)'),
  (SELECT id FROM dish WHERE dish_title='Houmous de pois chiches et légumes croquants')),
((SELECT id FROM menu WHERE menu_name='Menu Classique (toute saison)'),
  (SELECT id FROM dish WHERE dish_title='Suprême de volaille fermière, sauce forestière')),
((SELECT id FROM menu WHERE menu_name='Menu Classique (toute saison)'),
  (SELECT id FROM dish WHERE dish_title='Gambas flambées au pastis, riz basmati parfumé')),
((SELECT id FROM menu WHERE menu_name='Menu Classique (toute saison)'),
  (SELECT id FROM dish WHERE dish_title='Curry de légumes de saison au lait de coco')),
((SELECT id FROM menu WHERE menu_name='Menu Classique (toute saison)'),
  (SELECT id FROM dish WHERE dish_title='Plateau de fromages affinés')),
((SELECT id FROM menu WHERE menu_name='Menu Classique (toute saison)'),
  (SELECT id FROM dish WHERE dish_title='Alternative végétale aux noix de cajou')),
((SELECT id FROM menu WHERE menu_name='Menu Classique (toute saison)'),
  (SELECT id FROM dish WHERE dish_title='Tarte fine aux pommes caramélisées')),
((SELECT id FROM menu WHERE menu_name='Menu Classique (toute saison)'),
  (SELECT id FROM dish WHERE dish_title='Brownie vegan au chocolat')),
((SELECT id FROM menu WHERE menu_name='Menu Festif de Noël'),
  (SELECT id FROM dish WHERE dish_title='Foie gras de canard mi-cuit')),
((SELECT id FROM menu WHERE menu_name='Menu Festif de Noël'),
  (SELECT id FROM dish WHERE dish_title='Noix de Saint-Jacques poêlées, crème légère au citron vert')),
((SELECT id FROM menu WHERE menu_name='Menu Festif de Noël'),
  (SELECT id FROM dish WHERE dish_title='Houmous de pois chiches et légumes croquants')),
((SELECT id FROM menu WHERE menu_name='Menu Festif de Noël'),
  (SELECT id FROM dish WHERE dish_title='Ballotin de chapon aux marrons, purée de patates douces')),
((SELECT id FROM menu WHERE menu_name='Menu Festif de Noël'),
  (SELECT id FROM dish WHERE dish_title='Curry de légumes de saison au lait de coco')),
((SELECT id FROM menu WHERE menu_name='Menu Festif de Noël'),
  (SELECT id FROM dish WHERE dish_title='Plateau de fromages affinés')),
((SELECT id FROM menu WHERE menu_name='Menu Festif de Noël'),
  (SELECT id FROM dish WHERE dish_title='Alternative végétale aux noix de cajou')),
((SELECT id FROM menu WHERE menu_name='Menu Festif de Noël'),
  (SELECT id FROM dish WHERE dish_title='Bûche chocolat praliné')),
((SELECT id FROM menu WHERE menu_name='Menu Festif de Noël'),
  (SELECT id FROM dish WHERE dish_title='Brownie vegan au chocolat')),
((SELECT id FROM menu WHERE menu_name='Menu Printanier / Pâques'),
  (SELECT id FROM dish WHERE dish_title='Asperges vertes, vinaigrette citronnée')),
((SELECT id FROM menu WHERE menu_name='Menu Printanier / Pâques'),
  (SELECT id FROM dish WHERE dish_title='Salade de chèvre chaud sur toast')),
((SELECT id FROM menu WHERE menu_name='Menu Printanier / Pâques'),
  (SELECT id FROM dish WHERE dish_title='Gigot d’agneau rôti aux herbes')),
((SELECT id FROM menu WHERE menu_name='Menu Printanier / Pâques'),
  (SELECT id FROM dish WHERE dish_title='Gambas flambées au pastis, riz basmati parfumé')),
((SELECT id FROM menu WHERE menu_name='Menu Printanier / Pâques'),
  (SELECT id FROM dish WHERE dish_title='Curry de légumes de saison au lait de coco')),
((SELECT id FROM menu WHERE menu_name='Menu Printanier / Pâques'),
  (SELECT id FROM dish WHERE dish_title='Plateau de fromages affinés')),
((SELECT id FROM menu WHERE menu_name='Menu Printanier / Pâques'),
  (SELECT id FROM dish WHERE dish_title='Alternative végétale aux noix de cajou')),
((SELECT id FROM menu WHERE menu_name='Menu Printanier / Pâques'),
  (SELECT id FROM dish WHERE dish_title='Fraisier traditionnel')),
((SELECT id FROM menu WHERE menu_name='Menu Printanier / Pâques'),
  (SELECT id FROM dish WHERE dish_title='Brownie vegan au chocolat')),
((SELECT id FROM menu WHERE menu_name='Menu Mariage – Élégance & Raffinement'),
  (SELECT id FROM dish WHERE dish_title='Noix de Saint-Jacques poêlées, crème légère au citron vert')),
((SELECT id FROM menu WHERE menu_name='Menu Mariage – Élégance & Raffinement'),
  (SELECT id FROM dish WHERE dish_title='Foie gras de canard mi-cuit')),
((SELECT id FROM menu WHERE menu_name='Menu Mariage – Élégance & Raffinement'),
  (SELECT id FROM dish WHERE dish_title='Curry de légumes de saison au lait de coco')),
((SELECT id FROM menu WHERE menu_name='Menu Mariage – Élégance & Raffinement'),
  (SELECT id FROM dish WHERE dish_title='Filet de veau rôti, jus corsé au romarin')),
((SELECT id FROM menu WHERE menu_name='Menu Mariage – Élégance & Raffinement'),
  (SELECT id FROM dish WHERE dish_title='Gambas flambées au pastis, riz basmati parfumé')),
((SELECT id FROM menu WHERE menu_name='Menu Mariage – Élégance & Raffinement'),
  (SELECT id FROM dish WHERE dish_title='Plateau de fromages affinés')),
((SELECT id FROM menu WHERE menu_name='Menu Mariage – Élégance & Raffinement'),
  (SELECT id FROM dish WHERE dish_title='Alternative végétale aux noix de cajou')),
((SELECT id FROM menu WHERE menu_name='Menu Mariage – Élégance & Raffinement'),
  (SELECT id FROM dish WHERE dish_title='Entremets framboise & chocolat blanc')),
((SELECT id FROM menu WHERE menu_name='Menu Mariage – Élégance & Raffinement'),
  (SELECT id FROM dish WHERE dish_title='Brochettes de fruits frais')),
((SELECT id FROM menu WHERE menu_name='Menu Communion – Tradition & Douceur'),
  (SELECT id FROM dish WHERE dish_title='Œuf parfait, crème de champignons')),
((SELECT id FROM menu WHERE menu_name='Menu Communion – Tradition & Douceur'),
  (SELECT id FROM dish WHERE dish_title='Salade de chèvre chaud sur toast')),
((SELECT id FROM menu WHERE menu_name='Menu Communion – Tradition & Douceur'),
  (SELECT id FROM dish WHERE dish_title='Houmous de pois chiches et légumes croquants')),
((SELECT id FROM menu WHERE menu_name='Menu Communion – Tradition & Douceur'),
  (SELECT id FROM dish WHERE dish_title='Suprême de poulet rôti, riz pilaf aux légumes')),
((SELECT id FROM menu WHERE menu_name='Menu Communion – Tradition & Douceur'),
  (SELECT id FROM dish WHERE dish_title='Gambas flambées au pastis, riz basmati parfumé')),
((SELECT id FROM menu WHERE menu_name='Menu Communion – Tradition & Douceur'),
  (SELECT id FROM dish WHERE dish_title='Curry de légumes de saison au lait de coco')),
((SELECT id FROM menu WHERE menu_name='Menu Communion – Tradition & Douceur'),
  (SELECT id FROM dish WHERE dish_title='Plateau de fromages affinés')),
((SELECT id FROM menu WHERE menu_name='Menu Communion – Tradition & Douceur'),
  (SELECT id FROM dish WHERE dish_title='Alternative végétale aux noix de cajou')),
((SELECT id FROM menu WHERE menu_name='Menu Communion – Tradition & Douceur'),
  (SELECT id FROM dish WHERE dish_title='Moelleux au chocolat, crème anglaise')),
((SELECT id FROM menu WHERE menu_name='Menu Communion – Tradition & Douceur'),
  (SELECT id FROM dish WHERE dish_title='Brochettes de fruits frais')),
((SELECT id FROM menu WHERE menu_name='Menu EVC – Convivial & Gourmand'),
  (SELECT id FROM dish WHERE dish_title='Alternative végétale aux noix de cajou')),
((SELECT id FROM menu WHERE menu_name='Menu EVC – Convivial & Gourmand'),
  (SELECT id FROM dish WHERE dish_title='Plateau de fromages affinés')),
((SELECT id FROM menu WHERE menu_name='Menu EVC – Convivial & Gourmand'),
  (SELECT id FROM dish WHERE dish_title='Planche apéritive à partager')),
((SELECT id FROM menu WHERE menu_name='Menu EVC – Convivial & Gourmand'),
  (SELECT id FROM dish WHERE dish_title='Salade fraîcheur tomates et concombre')),
((SELECT id FROM menu WHERE menu_name='Menu EVC – Convivial & Gourmand'),
  (SELECT id FROM dish WHERE dish_title='Burger gourmet au bœuf, cheddar affiné')),
((SELECT id FROM menu WHERE menu_name='Menu EVC – Convivial & Gourmand'),
  (SELECT id FROM dish WHERE dish_title='Burger végétarien gourmet, pommes grenaille')),
((SELECT id FROM menu WHERE menu_name='Menu EVC – Convivial & Gourmand'),
  (SELECT id FROM dish WHERE dish_title='Brochettes de bœuf marinées')),
((SELECT id FROM menu WHERE menu_name='Menu EVC – Convivial & Gourmand'),
  (SELECT id FROM dish WHERE dish_title='Brownie chocolat & caramel beurre salé')),
((SELECT id FROM menu WHERE menu_name='Menu EVC – Convivial & Gourmand'),
  (SELECT id FROM dish WHERE dish_title='Brochettes de fruits frais')),
((SELECT id FROM menu WHERE menu_name='Menu Enfant – « Petit Gourmet »'),
  (SELECT id FROM dish WHERE dish_title='Velouté de légumes de saison')),
((SELECT id FROM menu WHERE menu_name='Menu Enfant – « Petit Gourmet »'),
  (SELECT id FROM dish WHERE dish_title='Œuf mimosa revisité')),
((SELECT id FROM menu WHERE menu_name='Menu Enfant – « Petit Gourmet »'),
  (SELECT id FROM dish WHERE dish_title='Suprême de volaille fermière pané maison et ses frites')),
((SELECT id FROM menu WHERE menu_name='Menu Enfant – « Petit Gourmet »'),
  (SELECT id FROM dish WHERE dish_title='Steak de bœuf Charolais, frites maison')),
((SELECT id FROM menu WHERE menu_name='Menu Enfant – « Petit Gourmet »'),
  (SELECT id FROM dish WHERE dish_title='Brownie chocolat & caramel beurre salé')),
((SELECT id FROM menu WHERE menu_name='Menu Enfant – « Petit Gourmet »'),
  (SELECT id FROM dish WHERE dish_title='Brochettes de fruits frais')),
((SELECT id FROM menu WHERE menu_name='Option Buffet – Convivial & Modulable'),
  (SELECT id FROM dish WHERE dish_title='Salades composées (au choix)')),
((SELECT id FROM menu WHERE menu_name='Option Buffet – Convivial & Modulable'),
  (SELECT id FROM dish WHERE dish_title='Verrines & tartinables')),
((SELECT id FROM menu WHERE menu_name='Option Buffet – Convivial & Modulable'),
  (SELECT id FROM dish WHERE dish_title='Viandes & poissons')),
((SELECT id FROM menu WHERE menu_name='Option Buffet – Convivial & Modulable'),
  (SELECT id FROM dish WHERE dish_title='Options végétariennes & vegan')),
((SELECT id FROM menu WHERE menu_name='Option Buffet – Convivial & Modulable'),
  (SELECT id FROM dish WHERE dish_title='Desserts format mini'));

CREATE TABLE allergen_dish(
  id_allergen CHAR(36) NOT NULL,
  id_dish CHAR(36) NOT NULL,
  PRIMARY KEY (id_allergen, id_dish),
  FOREIGN KEY(id_allergen) REFERENCES allergen(id)
  ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY(id_dish) REFERENCES dish(id)
  ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO allergen_dish (id_allergen, id_dish) VALUES
((SELECT id FROM allergen WHERE allergen_name='Lactose'),
 (SELECT id FROM dish WHERE dish_title='Velouté de potimarron')),
((SELECT id FROM allergen WHERE allergen_name='Fruits à coque'),
 (SELECT id FROM dish WHERE dish_title='Velouté de potimarron')),
((SELECT id FROM allergen WHERE allergen_name='Gluten'),
 (SELECT id FROM dish WHERE dish_title='Salade de chèvre chaud sur toast')),
((SELECT id FROM allergen WHERE allergen_name='Lactose'),
 (SELECT id FROM dish WHERE dish_title='Salade de chèvre chaud sur toast')),
((SELECT id FROM allergen WHERE allergen_name='Lactose'),
 (SELECT id FROM dish WHERE dish_title='Suprême de volaille fermière, sauce forestière')),
((SELECT id FROM allergen WHERE allergen_name='Crustacés'),
 (SELECT id FROM dish WHERE dish_title='Gambas flambées au pastis, riz basmati parfumé')),
((SELECT id FROM allergen WHERE allergen_name='Lactose'),
 (SELECT id FROM dish WHERE dish_title='Plateau de fromages affinés')),
((SELECT id FROM allergen WHERE allergen_name='Fruits à coque'),
 (SELECT id FROM dish WHERE dish_title='Alternative végétale aux noix de cajou')),
((SELECT id FROM allergen WHERE allergen_name='Gluten'),
 (SELECT id FROM dish WHERE dish_title='Tarte fine aux pommes caramélisées')),
((SELECT id FROM allergen WHERE allergen_name='Lactose'),
 (SELECT id FROM dish WHERE dish_title='Tarte fine aux pommes caramélisées')),
((SELECT id FROM allergen WHERE allergen_name='Sésame'),
 (SELECT id FROM dish WHERE dish_title='Houmous de pois chiches et légumes croquants')),
((SELECT id FROM allergen WHERE allergen_name='Gluten'),
 (SELECT id FROM dish WHERE dish_title='Brownie vegan au chocolat')),
((SELECT id FROM allergen WHERE allergen_name='Fruits à coque'),
 (SELECT id FROM dish WHERE dish_title='Brownie vegan au chocolat')),
((SELECT id FROM allergen WHERE allergen_name='Moutarde'),
 (SELECT id FROM dish WHERE dish_title='Foie gras de canard mi-cuit')),
((SELECT id FROM allergen WHERE allergen_name='Sulfites'),
 (SELECT id FROM dish WHERE dish_title='Foie gras de canard mi-cuit')),
((SELECT id FROM allergen WHERE allergen_name='Lactose'),
 (SELECT id FROM dish WHERE dish_title='Noix de Saint-Jacques poêlées, crème légère au citron vert')),
((SELECT id FROM allergen WHERE allergen_name='Mollusques'),
 (SELECT id FROM dish WHERE dish_title='Noix de Saint-Jacques poêlées, crème légère au citron vert')),
((SELECT id FROM allergen WHERE allergen_name='Lactose'),
 (SELECT id FROM dish WHERE dish_title='Ballotin de chapon aux marrons, purée de patates douces')),
((SELECT id FROM allergen WHERE allergen_name='Fruits à coque'),
 (SELECT id FROM dish WHERE dish_title='Bûche chocolat praliné')),
((SELECT id FROM allergen WHERE allergen_name='Lactose'),
 (SELECT id FROM dish WHERE dish_title='Bûche chocolat praliné')),
((SELECT id FROM allergen WHERE allergen_name='Gluten'),
 (SELECT id FROM dish WHERE dish_title='Bûche chocolat praliné')),
((SELECT id FROM allergen WHERE allergen_name='Gluten'),
 (SELECT id FROM dish WHERE dish_title='Fraisier traditionnel')),
((SELECT id FROM allergen WHERE allergen_name='Lactose'),
 (SELECT id FROM dish WHERE dish_title='Fraisier traditionnel')),
((SELECT id FROM allergen WHERE allergen_name='Oeufs'),
 (SELECT id FROM dish WHERE dish_title='Fraisier traditionnel')),
((SELECT id FROM allergen WHERE allergen_name='Lactose'),
 (SELECT id FROM dish WHERE dish_title='Entremets framboise & chocolat blanc')),
((SELECT id FROM allergen WHERE allergen_name='Oeufs'),
 (SELECT id FROM dish WHERE dish_title='Entremets framboise & chocolat blanc')),
((SELECT id FROM allergen WHERE allergen_name='Lactose'),
 (SELECT id FROM dish WHERE dish_title='Œuf parfait, crème de champignons')),
((SELECT id FROM allergen WHERE allergen_name='Oeufs'),
 (SELECT id FROM dish WHERE dish_title='Œuf parfait, crème de champignons')),
((SELECT id FROM allergen WHERE allergen_name='Lactose'),
 (SELECT id FROM dish WHERE dish_title='Suprême de poulet rôti, riz pilaf aux légumes')),
((SELECT id FROM allergen WHERE allergen_name='Gluten'),
 (SELECT id FROM dish WHERE dish_title='Moelleux au chocolat, crème anglaise')),
((SELECT id FROM allergen WHERE allergen_name='Lactose'),
 (SELECT id FROM dish WHERE dish_title='Moelleux au chocolat, crème anglaise')),
((SELECT id FROM allergen WHERE allergen_name='Oeufs'),
 (SELECT id FROM dish WHERE dish_title='Moelleux au chocolat, crème anglaise')),
((SELECT id FROM allergen WHERE allergen_name='Gluten'),
 (SELECT id FROM dish WHERE dish_title='Planche apéritive à partager')),
((SELECT id FROM allergen WHERE allergen_name='Lactose'),
 (SELECT id FROM dish WHERE dish_title='Planche apéritive à partager')),
((SELECT id FROM allergen WHERE allergen_name='Gluten'),
 (SELECT id FROM dish WHERE dish_title='Burger gourmet au bœuf, cheddar affiné')),
((SELECT id FROM allergen WHERE allergen_name='Lactose'),
 (SELECT id FROM dish WHERE dish_title='Burger gourmet au bœuf, cheddar affiné')),
((SELECT id FROM allergen WHERE allergen_name='Gluten'),
 (SELECT id FROM dish WHERE dish_title='Burger végétarien gourmet, pommes grenaille')),
((SELECT id FROM allergen WHERE allergen_name='Lactose'),
 (SELECT id FROM dish WHERE dish_title='Burger végétarien gourmet, pommes grenaille')),
((SELECT id FROM allergen WHERE allergen_name='Gluten'),
 (SELECT id FROM dish WHERE dish_title='Brownie chocolat & caramel beurre salé')),
((SELECT id FROM allergen WHERE allergen_name='Lactose'),
 (SELECT id FROM dish WHERE dish_title='Brownie chocolat & caramel beurre salé')),
((SELECT id FROM allergen WHERE allergen_name='Gluten'),
 (SELECT id FROM dish WHERE dish_title='Velouté de légumes de saison')),
((SELECT id FROM allergen WHERE allergen_name='Lactose'),
 (SELECT id FROM dish WHERE dish_title='Œuf mimosa revisité')),
((SELECT id FROM allergen WHERE allergen_name='Oeufs'),
 (SELECT id FROM dish WHERE dish_title='Œuf mimosa revisité')),
((SELECT id FROM allergen WHERE allergen_name='Gluten'),
 (SELECT id FROM dish WHERE dish_title='Suprême de volaille fermière pané maison et ses frites')),
((SELECT id FROM allergen WHERE allergen_name='Lactose'),
 (SELECT id FROM dish WHERE dish_title='Suprême de volaille fermière pané maison et ses frites')),
((SELECT id FROM allergen WHERE allergen_name='Oeufs'),
 (SELECT id FROM dish WHERE dish_title='Suprême de volaille fermière pané maison et ses frites')),
((SELECT id FROM allergen WHERE allergen_name='Gluten'),
 (SELECT id FROM dish WHERE dish_title='Salades composées (au choix)')),
((SELECT id FROM allergen WHERE allergen_name='Fruits à coque'),
 (SELECT id FROM dish WHERE dish_title='Salades composées (au choix)')),
((SELECT id FROM allergen WHERE allergen_name='Lactose'),
 (SELECT id FROM dish WHERE dish_title='Verrines & tartinables')),
((SELECT id FROM allergen WHERE allergen_name='Sésame'),
 (SELECT id FROM dish WHERE dish_title='Verrines & tartinables')),
((SELECT id FROM allergen WHERE allergen_name='Crustacés'),
 (SELECT id FROM dish WHERE dish_title='Viandes & poissons')),
((SELECT id FROM allergen WHERE allergen_name='Gluten'),
 (SELECT id FROM dish WHERE dish_title='Options végétariennes & vegan')),
((SELECT id FROM allergen WHERE allergen_name='Oeufs'),
 (SELECT id FROM dish WHERE dish_title='Options végétariennes & vegan')),
((SELECT id FROM allergen WHERE allergen_name='Sésame'),
 (SELECT id FROM dish WHERE dish_title='Options végétariennes & vegan')),
((SELECT id FROM allergen WHERE allergen_name='Lactose'),
 (SELECT id FROM dish WHERE dish_title='Options végétariennes & vegan')),
((SELECT id FROM allergen WHERE allergen_name='Lactose'),
 (SELECT id FROM dish WHERE dish_title='Desserts format mini')),
((SELECT id FROM allergen WHERE allergen_name='Gluten'),
 (SELECT id FROM dish WHERE dish_title='Desserts format mini')),
((SELECT id FROM allergen WHERE allergen_name='Fruits à coque'),
 (SELECT id FROM dish WHERE dish_title='Desserts format mini'));

CREATE TABLE diet_dish(
  id_diet CHAR(36) NOT NULL,
  id_dish CHAR(36) NOT NULL,
  PRIMARY KEY (id_diet, id_dish),
  FOREIGN KEY(id_diet) REFERENCES diet(id)
  ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY(id_dish) REFERENCES dish(id)
  ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


INSERT INTO diet_dish (id_diet, id_dish) VALUES
((SELECT id FROM diet WHERE diet_name='Végétarien'),
 (SELECT id FROM dish WHERE dish_title='Velouté de potimarron')),
((SELECT id FROM diet WHERE diet_name='Vegan'),
 (SELECT id FROM dish WHERE dish_title='Houmous de pois chiches et légumes croquants')),
((SELECT id FROM diet WHERE diet_name='Végétarien'),
 (SELECT id FROM dish WHERE dish_title='Salade de chèvre chaud sur toast')),
((SELECT id FROM diet WHERE diet_name='Sans porc'),
 (SELECT id FROM dish WHERE dish_title='Suprême de volaille fermière, sauce forestière')),
((SELECT id FROM diet WHERE diet_name='Sans lactose'),
 (SELECT id FROM dish WHERE dish_title='Gambas flambées au pastis, riz basmati parfumé')),
((SELECT id FROM diet WHERE diet_name='Sans gluten'),
 (SELECT id FROM dish WHERE dish_title='Gambas flambées au pastis, riz basmati parfumé')),
((SELECT id FROM diet WHERE diet_name='Vegan'),
 (SELECT id FROM dish WHERE dish_title='Curry de légumes de saison au lait de coco')),
((SELECT id FROM diet WHERE diet_name='Végétarien'),
 (SELECT id FROM dish WHERE dish_title='Plateau de fromages affinés')),
((SELECT id FROM diet WHERE diet_name='Vegan'),
 (SELECT id FROM dish WHERE dish_title='Alternative végétale aux noix de cajou')),
((SELECT id FROM diet WHERE diet_name='Végétarien'),
 (SELECT id FROM dish WHERE dish_title='Tarte fine aux pommes caramélisées')),
((SELECT id FROM diet WHERE diet_name='Vegan'),
 (SELECT id FROM dish WHERE dish_title='Brownie vegan au chocolat')),
((SELECT id FROM diet WHERE diet_name='Sans lactose'),
 (SELECT id FROM dish WHERE dish_title='Foie gras de canard mi-cuit')),
((SELECT id FROM diet WHERE diet_name='Sans gluten'),
 (SELECT id FROM dish WHERE dish_title='Foie gras de canard mi-cuit')),
((SELECT id FROM diet WHERE diet_name='Sans gluten'),
 (SELECT id FROM dish WHERE dish_title='Noix de Saint-Jacques poêlées, crème légère au citron vert')),
((SELECT id FROM diet WHERE diet_name='Sans porc'),
 (SELECT id FROM dish WHERE dish_title='Ballotin de chapon aux marrons, purée de patates douces')),
((SELECT id FROM diet WHERE diet_name='Vegan'),
 (SELECT id FROM dish WHERE dish_title='Asperges vertes, vinaigrette citronnée')),
((SELECT id FROM diet WHERE diet_name='Sans gluten'),
 (SELECT id FROM dish WHERE dish_title='Asperges vertes, vinaigrette citronnée')),
((SELECT id FROM diet WHERE diet_name='Sans lactose'),
 (SELECT id FROM dish WHERE dish_title='Gigot d’agneau rôti aux herbes')),
((SELECT id FROM diet WHERE diet_name='Sans porc'),
 (SELECT id FROM dish WHERE dish_title='Gigot d’agneau rôti aux herbes')),
((SELECT id FROM diet WHERE diet_name='Sans gluten'),
 (SELECT id FROM dish WHERE dish_title='Gigot d’agneau rôti aux herbes')),
((SELECT id FROM diet WHERE diet_name='Sans porc'),
 (SELECT id FROM dish WHERE dish_title='Filet de veau rôti, jus corsé au romarin')),
((SELECT id FROM diet WHERE diet_name='Vegan'),
 (SELECT id FROM dish WHERE dish_title='Brochettes de fruits frais')),
((SELECT id FROM diet WHERE diet_name='Végétarien'),
 (SELECT id FROM dish WHERE dish_title='Œuf parfait, crème de champignons')),
((SELECT id FROM diet WHERE diet_name='Sans porc'),
 (SELECT id FROM dish WHERE dish_title='Suprême de poulet rôti, riz pilaf aux légumes')),
((SELECT id FROM diet WHERE diet_name='Vegan'),
 (SELECT id FROM dish WHERE dish_title='Salade fraîcheur tomates et concombre')),
((SELECT id FROM diet WHERE diet_name='Sans porc'),
 (SELECT id FROM dish WHERE dish_title='Burger gourmet au bœuf, cheddar affiné')),
((SELECT id FROM diet WHERE diet_name='Sans porc'),
 (SELECT id FROM dish WHERE dish_title='Burger végétarien gourmet, pommes grenaille')),
((SELECT id FROM diet WHERE diet_name='Végétarien'),
 (SELECT id FROM dish WHERE dish_title='Burger végétarien gourmet, pommes grenaille')),
((SELECT id FROM diet WHERE diet_name='Sans porc'),
 (SELECT id FROM dish WHERE dish_title='Brochettes de bœuf marinées')),
((SELECT id FROM diet WHERE diet_name='Sans gluten'),
 (SELECT id FROM dish WHERE dish_title='Brochettes de bœuf marinées')),
((SELECT id FROM diet WHERE diet_name='Sans lactose'),
 (SELECT id FROM dish WHERE dish_title='Brochettes de bœuf marinées')),
((SELECT id FROM diet WHERE diet_name='Vegan'),
 (SELECT id FROM dish WHERE dish_title='Velouté de légumes de saison')),
((SELECT id FROM diet WHERE diet_name='Végétarien'),
 (SELECT id FROM dish WHERE dish_title='Œuf mimosa revisité')),
((SELECT id FROM diet WHERE diet_name='Sans porc'),
 (SELECT id FROM dish WHERE dish_title='Suprême de volaille fermière pané maison et ses frites')),
((SELECT id FROM diet WHERE diet_name='Sans porc'),
 (SELECT id FROM dish WHERE dish_title='Steak de bœuf Charolais, frites maison')),
((SELECT id FROM diet WHERE diet_name='Vegan'),
 (SELECT id FROM dish WHERE dish_title='Salades composées (au choix)')),
((SELECT id FROM diet WHERE diet_name='Vegan'),
 (SELECT id FROM dish WHERE dish_title='Verrines & tartinables')),
((SELECT id FROM diet WHERE diet_name='Végétarien'),
 (SELECT id FROM dish WHERE dish_title='Verrines & tartinables')),
((SELECT id FROM diet WHERE diet_name='Sans porc'),
 (SELECT id FROM dish WHERE dish_title='Viandes & poissons')),
((SELECT id FROM diet WHERE diet_name='Sans gluten'),
 (SELECT id FROM dish WHERE dish_title='Viandes & poissons')),
((SELECT id FROM diet WHERE diet_name='Sans lactose'),
 (SELECT id FROM dish WHERE dish_title='Viandes & poissons')),
((SELECT id FROM diet WHERE diet_name='Vegan'),
 (SELECT id FROM dish WHERE dish_title='Options végétariennes & vegan')),
((SELECT id FROM diet WHERE diet_name='Végétarien'),
 (SELECT id FROM dish WHERE dish_title='Options végétariennes & vegan')),
((SELECT id FROM diet WHERE diet_name='Végétarien'),
 (SELECT id FROM dish WHERE dish_title='Desserts format mini')),
((SELECT id FROM diet WHERE diet_name='Vegan'),
 (SELECT id FROM dish WHERE dish_title='Desserts format mini'));

CREATE TABLE order_drink_package(
  id_order CHAR(36) NOT NULL,
  id_drink_package CHAR(36) NOT NULL,
  PRIMARY KEY (id_order, id_drink_package),
  number_people INT NOT NULL,
  price_person DECIMAL(10,2) NOT NULL,
  subtotal DECIMAL(10,2) NOT NULL,
  FOREIGN KEY(id_order) REFERENCES orders(id)
  ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY(id_drink_package) REFERENCES drink_package(id)
  ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE order_personal_package(
  id_order CHAR(36) NOT NULL,
  id_personal_package CHAR(36) NOT NULL,
  PRIMARY KEY (id_order, id_personal_package),
  number_people INT NOT NULL,
  price_package DECIMAL(10,2) NOT NULL,
  subtotal DECIMAL(10,2) NOT NULL,
  FOREIGN KEY(id_order) REFERENCES orders(id)
  ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY(id_personal_package) REFERENCES personal_package(id)
  ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE menu_condition_menu(
  id_menu CHAR(36) NOT NULL,
  id_condition_menu CHAR(36) NOT NULL,
  PRIMARY KEY (id_menu, id_condition_menu),
  FOREIGN KEY(id_menu) REFERENCES menu(id)
  ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY(id_condition_menu) REFERENCES condition_menu(id)
  ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO menu_condition_menu (id_menu, id_condition_menu) VALUES
((SELECT id FROM menu WHERE menu_name='Menu Classique (toute saison)'),
 (SELECT id FROM condition_menu WHERE description='5 jours avant l’événement.')),
((SELECT id FROM menu WHERE menu_name='Menu Classique (toute saison)'),
 (SELECT id FROM condition_menu WHERE description='plats froids : +4°C.')),
((SELECT id FROM menu WHERE menu_name='Menu Classique (toute saison)'),
 (SELECT id FROM condition_menu WHERE description='plats chauds : à consommer sous 2h après livraison.')),
((SELECT id FROM menu WHERE menu_name='Menu Classique (toute saison)'),
 (SELECT id FROM condition_menu WHERE description='tous nos menus comprennent des options vegan, donc sans lactose et sans porc.')),
((SELECT id FROM menu WHERE menu_name='Menu Festif de Noël'),
 (SELECT id FROM condition_menu WHERE description='10 jours avant l’événement.')),
((SELECT id FROM menu WHERE menu_name='Menu Festif de Noël'),
 (SELECT id FROM condition_menu WHERE description='foie gras, Saint-Jacques → stock limité.')),
((SELECT id FROM menu WHERE menu_name='Menu Festif de Noël'),
 (SELECT id FROM condition_menu WHERE description='tous nos menus comprennent des options vegan, donc sans lactose et sans porc.')),
((SELECT id FROM menu WHERE menu_name='Menu Printanier / Pâques'),
 (SELECT id FROM condition_menu WHERE description='plats froids : +4°C.')),
((SELECT id FROM menu WHERE menu_name='Menu Printanier / Pâques'),
 (SELECT id FROM condition_menu WHERE description='disponibilité variable.')),
((SELECT id FROM menu WHERE menu_name='Menu Printanier / Pâques'),
 (SELECT id FROM condition_menu WHERE description='tous nos menus comprennent des options vegan, donc sans lactose et sans porc.')),
((SELECT id FROM menu WHERE menu_name='Menu Printanier / Pâques'),
 (SELECT id FROM condition_menu WHERE description='7 jours avant l’événement.')),
((SELECT id FROM menu WHERE menu_name='Menu Mariage – Élégance & Raffinement'),
 (SELECT id FROM condition_menu WHERE description='réservation minimum 1 mois à l’avance.')),
((SELECT id FROM menu WHERE menu_name='Menu Mariage – Élégance & Raffinement'),
 (SELECT id FROM condition_menu WHERE description='ajustement du nombre jusqu’à 72h avant l’événement.')),
((SELECT id FROM menu WHERE menu_name='Menu Mariage – Élégance & Raffinement'),
 (SELECT id FROM condition_menu WHERE description='tous nos menus comprennent des options vegan, donc sans lactose et sans porc.')),
((SELECT id FROM menu WHERE menu_name='Menu Communion – Tradition & Douceur'),
 (SELECT id FROM condition_menu WHERE description='7 jours avant l’événement.')),
((SELECT id FROM menu WHERE menu_name='Menu Communion – Tradition & Douceur'),
 (SELECT id FROM condition_menu WHERE description='tous nos menus comprennent des options vegan, donc sans lactose et sans porc.')),
((SELECT id FROM menu WHERE menu_name='Menu EVC – Convivial & Gourmand'),
 (SELECT id FROM condition_menu WHERE description='5 jours avant l’événement.')),
((SELECT id FROM menu WHERE menu_name='Menu EVC – Convivial & Gourmand'),
 (SELECT id FROM condition_menu WHERE description='tous nos menus comprennent des options vegan, donc sans lactose et sans porc.')),
((SELECT id FROM menu WHERE menu_name='Menu Enfant – « Petit Gourmet »'),
 (SELECT id FROM condition_menu WHERE description='5 jours avant l’événement.')),
((SELECT id FROM menu WHERE menu_name='Option Buffet – Convivial & Modulable'),
 (SELECT id FROM condition_menu WHERE description='7 jours avant l’événement.')),
((SELECT id FROM menu WHERE menu_name='Option Buffet – Convivial & Modulable'),
 (SELECT id FROM condition_menu WHERE description='plats froids : +4°C.')),
((SELECT id FROM menu WHERE menu_name='Option Buffet – Convivial & Modulable'),
 (SELECT id FROM condition_menu WHERE description='tous nos menus comprennent des options vegan, donc sans lactose et sans porc.'));

