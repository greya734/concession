<?php
session_start();

if (empty($_SESSION['client_id'])) {
    header("Location: client_login.php");
    exit;
}

$client_id = $_SESSION['client_id'];
$panier_id = (int)($_GET['id'] ?? 0);

require 'config.php';

// Supprimer uniquement si le panier appartient au client connecté
$stmt = $pdo->prepare("DELETE FROM panier WHERE id = :id AND id_client = :client_id");
$stmt->execute(['id' => $panier_id, 'client_id' => $client_id]);

header("Location: panier.php");
exit;
?>
