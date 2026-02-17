SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

CREATE TABLE role(
   id CHAR(36) NOT NULL PRIMARY KEY DEFAULT (UUID()),
   role_name VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `role` ( `role_name`) VALUES 
('client'),
('employé'),
('admin');

CREATE TABLE schedule(
   id CHAR(36) NOT NULL PRIMARY KEY DEFAULT (UUID()),
   first_day VARCHAR(50) NOT NULL,
   last_day VARCHAR(50) NOT NULL,
   opening_time TIME NOT NULL,
   closing_time TIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `schedule` (`first_day`, `last_day`, `opening_time`, `closing_time`) VALUES
('Mardi', 'Dimanche', '11:00:00', '22:00:00');

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
('Oeufs'),
('Sulfites');

CREATE TABLE category_dish(
   id CHAR(36) NOT NULL PRIMARY KEY DEFAULT (UUID()),
   category_name VARCHAR(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `category_dish` (`category_name`) VALUES
('Entrées'),
('Plats principaux'),
('Accompagnements'),
('Desserts'),
('Fromage');

CREATE TABLE dish(
   id CHAR(36) NOT NULL PRIMARY KEY DEFAULT (UUID()),
   dish_title VARCHAR(50) NOT NULL,
   description TEXT NOT NULL,
   picture VARCHAR(50) NOT NULL,
   id_Category_dish CHAR(36) NOT NULL,
   FOREIGN KEY(id_Category_dish) REFERENCES category_dish(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `dish` (`dish_title`, `description`, `picture`, `id_Category_dish`) VALUES
('Velouté de potimarron', 'Velouté onctueux de potimarron rôti, relevé d’une pointe de crème et parsemé de noisettes torréfiées.', 'velouté_potimarron_noisette.jpg', (SELECT id FROM category_dish WHERE category_name = 'Entrées')),
('Salade de chèvre chaud sur toast', 'Jeunes pousses, toasts croustillants au chèvre fondant, vinaigrette maison au miel.', 'salade_chevre.jpg', (SELECT id FROM category_dish WHERE category_name = 'Entrées')),
('Houmous de pois chiches et légumes croquants', 'Purée de pois chiches au sésame, citron et huile d’olive, accompagnée de légumes frais.', 'Houmous_legume.jpg', (SELECT id FROM category_dish WHERE category_name = 'Entrées')),
('Suprême de volaille fermière, sauce forestière', 'Suprême de volaille rôtie, sauce crémeuse aux champignons, gratin dauphinois maison.', 'Supreme_volaille.jpg', (SELECT id FROM category_dish WHERE category_name = 'Plats principaux')),
('Gambas flambées au pastis, riz basmati parfumé', 'Gambas sautées et flambées au pastis, servies avec un riz basmati délicatement parfumé.', 'gambas.jpg', (SELECT id FROM category_dish WHERE category_name = 'Plats principaux')),
('Curry de légumes de saison au lait de coco', 'Mélange de légumes frais mijotés dans un curry doux au lait de coco.', 'curry.jpg', (SELECT id FROM category_dish WHERE category_name = 'Plats principaux')),
('Plateau de fromages affinés', 'Sélection de fromages régionaux affinés.', 'assiette_fromage.jpg', (SELECT id FROM category_dish WHERE category_name = 'Fromage')),
('Alternative végétale aux noix de cajou', 'Préparation végétale crémeuse à base de noix de cajou.', 'preparation_vegetale.jpg', (SELECT id FROM category_dish WHERE category_name = 'Fromage')),
('Tarte fine aux pommes caramélisées', 'Pommes fondantes caramélisées sur pâte croustillante.', 'tarte.jpg', (SELECT id FROM category_dish WHERE category_name = 'Desserts')),
('Brownie végan au chocolat', 'Brownie fondant au chocolat noir, sans ingrédient d’origine animale.', 'brownie_vegan.jpg', (SELECT id FROM category_dish WHERE category_name = 'Desserts'));

CREATE TABLE menu(
   id CHAR(36) NOT NULL PRIMARY KEY DEFAULT (UUID()),
   menu_name VARCHAR(50) NOT NULL,
   description TEXT NOT NULL,
   minimum_people INT NOT NULL,
   price_per_person DECIMAL(5,2) NOT NULL, 
   remaining_quantity INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `menu` (`menu_name`, `description`, `minimum_people`, `price_per_person`, `remaining_quantity`) VALUES
('Menu Classique', 'Un menu équilibré et généreux, mettant à l’honneur des recettes traditionnelles revisitées, adaptées à tous vos événements tout au long de l’année.', 6, 32.00, 20),
('Menu Noël', 'Un menu raffiné aux saveurs festives, pensé pour sublimer vos repas de fin d’année avec des produits emblématiques et gourmands.', 10, 49.00, 8),
('Menu Mariage', 'Un menu d’exception, élégant et sophistiqué, conçu pour accompagner les moments uniques de votre réception de mariage.', 40, 35.00, 5),
('Menu Buffet', 'Une formule flexible et variée, idéale pour tout événement, offrant un large choix de plats à partager adaptés à tous les régimes alimentaires.', 20, 15.00, 20);

CREATE TABLE material_category(
   id CHAR(36) NOT NULL PRIMARY KEY DEFAULT (UUID()),
   material_category_name VARCHAR(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `material_category` (`material_category_name`) VALUES
('vaisselle'),
('linge de table'),
('verrerie'),
('décoration de table'),
('équipement');

CREATE TABLE material(
   id CHAR(36) NOT NULL PRIMARY KEY DEFAULT (UUID()),
   material_name VARCHAR(50) NOT NULL,
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
('Forfait vin d’honneur (vin blanc, rosé et rouge, bière, pastis et whiskey)', 25),
('Forfait vin repas (blanc, rosé et rouge)', 12),
('Forfait soft (eau plates,  gazeuse, soda, jus de fruit)', 8),
('Forfait champagne', 20);

CREATE TABLE personal_package(
   id CHAR(36) NOT NULL PRIMARY KEY DEFAULT (UUID()),
   event_type VARCHAR(50) NOT NULL,
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
('Délai de commande :', '5 jours avant l’événement'),
('Délai de commande :', 'ajustement du nombre jusqu’à 72h avant l’événement'),
('Délai de commande :', '10 jours avant l’événement'),
('Délai de commande :', '7 jours avant l’événement'),
('Produits frais :', ' foie gras, Saint-Jacques → stock limité'),
('Confirmation définitive du nombre d''invités :', '48h avant l’événement.'),
('Régimes alimentaires :', 'Tous nos menus comprennent des options végan, donc sans lactose et sans porc.'),
('Conservation :', 'plats froids : +4°C'),
('Conservation :', ' plats chauds : à consommer sous 2h après livraison'),
('Produits saisonniers :', 'disponibilité variable');

CREATE TABLE user(
   id CHAR(36) NOT NULL PRIMARY KEY DEFAULT (UUID()),
   last_name VARCHAR(50) NOT NULL,
   first_name VARCHAR(50) NOT NULL,
   email VARCHAR(255) NOT NULL UNIQUE,
   password VARCHAR(255) NOT NULL,
   postal_address VARCHAR(50) NOT NULL,
   city VARCHAR(50) NOT NULL,
   postal_code VARCHAR(50) NOT NULL,
   telephone VARCHAR(50) NOT NULL,
   id_role CHAR(36) NOT NULL,
   FOREIGN KEY(id_role) REFERENCES role(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `user` (`last_name`, `first_name`, `email`, `password`, `postal_address`, `city`, `postal_code`, `telephone`, `id_role`) VALUES
('Test', 'Mathieu', 'mathieu@test.com', 'Test12345!', '123 Rue de la Paix', 'Bordeaux', '33000', '06 12 34 56 78', (SELECT id FROM role WHERE role_name = 'client')),
('Test', 'Marie', 'marie@test.com', 'Test12345!', '456 Avenue des Champs', 'Bordeaux', '33000', '06 12 34 56 79', (SELECT id FROM role WHERE role_name = 'client')),
('Test', 'Elise', 'elise@test.com', 'Test12345!', '789 Boulevard Saint-Michel', 'Bordeaux', '33000', '06 12 34 56 80', (SELECT id FROM role WHERE role_name = 'client')),
('Admin', 'José', 'jose.admin@test.com', 'Admin12345!', '12 rue des Saveurs', 'Bordeaux', '33000', '06 12 34 56 81', (SELECT id FROM role WHERE role_name = 'admin'));

CREATE TABLE orders(
   id CHAR(36) NOT NULL PRIMARY KEY DEFAULT (UUID()),
   order_date DATE NOT NULL,
   service_date DATETIME NOT NULL,
   delivery_address VARCHAR(50) NOT NULL,
   number_of_people INT NOT NULL,
   total_order_price DECIMAL(10,2) NOT NULL,
   status VARCHAR(50) NOT NULL,
   equipment_loan TINYINT(1) NOT NULL,
   equipment_return TINYINT(1) NOT NULL,
   id_user CHAR(36) NOT NULL,
   FOREIGN KEY(id_user) REFERENCES user(id)
   ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `orders` (`order_date`, `service_date`, `delivery_address`, `number_of_people`, `total_order_price`, `status`, `equipment_loan`, `equipment_return`, `id_user`) VALUES
('2026-01-15', '2026-01-30 19:00:00', '123 Rue de la Paix, Bordeaux', 50, 1750.00, 'Confirmé', 1, 0, (SELECT id FROM user WHERE email = 'mathieu@test.com')),
('2026-01-16', '2026-02-14 12:00:00', '456 Avenue des Champs, Bordeaux', 30, 900.00, 'Confirmé', 0, 0, (SELECT id FROM user WHERE email = 'marie@test.com')),
('2026-01-17', '2026-02-20 18:30:00', '789 Boulevard Saint-Michel, Bordeaux', 20, 600.00, 'Confirmé', 1, 0, (SELECT id FROM user WHERE email = 'elise@test.com'));

CREATE TABLE notice(
   id CHAR(36) NOT NULL PRIMARY KEY DEFAULT (UUID()),
   note INT NOT NULL,
   description TEXT NOT NULL,
   signature VARCHAR(50) NOT NULL,
   status VARCHAR(50) NOT NULL,
   date DATE NOT NULL,
   id_order CHAR(36) NOT NULL,
   FOREIGN KEY(id_order) REFERENCES orders(id)
   ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

insert into notice (note, description, signature, status, date, id_order) values
  (5, 'Mon mariage s’est très bien passé grâce au sérieux de l’équipe.', 'Mathieu33', 'Validé', '2026-01-15', (SELECT id FROM orders WHERE order_date = '2026-01-15')),
  (4, 'Traiteur très sérieux.', 'RobinDesBois', 'Validé', '2026-01-16', (SELECT id FROM orders WHERE order_date = '2026-01-16')),
  (5, 'Merci encore pour la qualité et la présentation. Merci à Julie, José et toute la team.', 'EliseB', 'Validé', '2026-01-17', (SELECT id FROM orders WHERE order_date = '2026-01-17'));

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
   subtotal DECIMAL(10,2) NOT NULL,
   FOREIGN KEY(id_order) REFERENCES orders(id)
   ON DELETE CASCADE ON UPDATE CASCADE,
   FOREIGN KEY(id_menu) REFERENCES menu(id)
   ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE menu_dish(
   id_menu CHAR(36) NOT NULL,
   id_dish CHAR(36) NOT NULL,
   PRIMARY KEY (id_menu, id_dish),
   FOREIGN KEY(id_dish) REFERENCES dish(id)
   ON DELETE CASCADE ON UPDATE CASCADE,
   FOREIGN KEY(id_menu) REFERENCES menu(id)
   ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE allergen_dish(
   id_allergen CHAR(36) NOT NULL,
   id_dish CHAR(36) NOT NULL,
   PRIMARY KEY (id_allergen, id_dish),
   FOREIGN KEY(id_allergen) REFERENCES allergen(id)
   ON DELETE CASCADE ON UPDATE CASCADE,
   FOREIGN KEY(id_dish) REFERENCES dish(id)
   ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE diet_dish(
   id_diet CHAR(36) NOT NULL,
   id_dish CHAR(36) NOT NULL,
   PRIMARY KEY (id_diet, id_dish),
   FOREIGN KEY(id_diet) REFERENCES diet(id)
   ON DELETE CASCADE ON UPDATE CASCADE,
   FOREIGN KEY(id_dish) REFERENCES dish(id)
   ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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