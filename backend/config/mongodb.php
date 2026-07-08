<?php
// import de la classe Client de la bibliothèque MongoDB
use MongoDB\Client;
// création de la connexion
$mongoUrl = $_ENV['MONGODB_URL'] ?? 'mongodb://mongo:27017';

$mongoClient = new Client($mongoUrl);
// création de la base au 1er insert
$mongoDb = $mongoClient->selectDatabase("app_statistics");