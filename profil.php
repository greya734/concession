<?php
session_start();

// Vérifier que le client est connecté
if (empty($_SESSION['client_id'])) {
    header("Location: client_login.php");
    exit;
}

// Connexion BDD
require 'config.php';

// Récupération des infos du client
$stmt = $pdo->prepare("SELECT * FROM clients WHERE id = :id");
$stmt->execute(['id' => $_SESSION['client_id']]);
$client = $stmt->fetch();
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Mon Profil</title>
    <link rel="stylesheet" href="style.css"/>
</head>
<body>
  <header>
    <h2>AutoConcession — Mon Profil</h2>
  </header>

  <div class="wrap">
    <h2>Bienvenue, <?= htmlspecialchars($client['nom']) ?></h2>

    <p class="info"><b>Nom :</b> <?= htmlspecialchars($client['nom']) ?></p>
    <p class="info"><b>Email :</b> <?= htmlspecialchars($client['email']) ?></p>
    <p class="info"><b>Téléphone :</b> <?= htmlspecialchars($client['telephone']) ?></p>
    <p class="info"><b>Date d’inscription :</b> <?= htmlspecialchars($client['created_at']) ?></p>
    
    <a class="btn" href="index.php">Accueil</a>
    <a class="btn suppr" href="profil_edit.php">Modifier</a>
    <a class="btn error" href="logout.php">Se déconnecter</a>
  </div>
</body>
</html>
