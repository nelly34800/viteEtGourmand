# Vite et Gourmand
application pour un traiteur bordelais.

# Prérequis
Avant d’installer le projet, assurez-vous d’avoir :
Docker
Docker Compose
Git

# Installation et lancement 
Cloner le projet
git clone https://github.com/votre-compte/vite-et-gourmand.git
Lancer les conteneurs Docker
docker compose up --build

# Accès au projet
Front-end : http://localhost:8086

Back-end : http://localhost:8082

Variables d’environnement : 
créer un fichier .env à la racine du projet, en suivant le .env.example fourni.

# tests
Les routes API ont été testées avec : Postman

# déploiement
Pour l’instant, le projet est prévu pour un déploiement local grâce à Docker.
Le déploiement futur pourra se faire sur un hébergeur mutualisé (PHP/MariaDB) ou sur un serveur cloud (AWS, OVH, Azure) avec Apache/Nginx, PHP et MongoDB.

# Auteur
Projet réalisé par Nelly Boussekhane dans le cadre de la formation DWWM.