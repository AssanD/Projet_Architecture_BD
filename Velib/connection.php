<?php
    require 'vendor/autoload.php';

    // Connexion à MongoDB
    $client = new MongoDB\Client("mongodb+srv://assan:assan@cluster0.hhu4cwj.mongodb.net/test");

    // Création de la base de données
    $db = $client->db_velib;

    // Création de la collection
    //$db->createCollection("collection_velib");

    $velib = $db->collection_velib;

?>