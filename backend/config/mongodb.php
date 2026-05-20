<?php
// import de la classe Client de la bibliothèque MongoDB
use MongoDB\Client;
// création de la connexion
$mongo = $_ENV['MONGODB_PORT'] ?? null;

$mongoClient = new Client("mongodb://mongo:27017");
// création de la base au 1er insert
$mongoDb = $mongoClient->selectDatabase("app_statistics");