<?php
session_start();

// Vérifier que le client est connecté
if (empty($_SESSION['client_id'])) {
    header("Location: client_login.php");
    exit;
}

// Connexion à la base de données
require 'config.php';
$client_id = $_SESSION['client_id'];

// Vérifier qu’un véhicule est choisi via l’URL
if (!empty($_GET['id'])) {
    $id_voiture = (int)$_GET['id'];

    // Vérifier que la voiture existe
    $stmt = $pdo->prepare("SELECT id FROM voitures WHERE id = :id");
    $stmt->execute(['id' => $id_voiture]);
    $voiture = $stmt->fetch();

    if ($voiture) {
        // Ajouter au panier
        $insert = $pdo->prepare("INSERT INTO panier (id_client, id_voiture) VALUES (?, ?)");
        $insert->execute([$client_id, $id_voiture]);
    }
}

// Redirection vers la page du panier
header("Location: panier.php");
exit;
?>