<?php
require 'config.php';

// Vérifie l'ID passé en paramètre
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$stmt = $pdo->prepare("SELECT * FROM voitures WHERE id = :id");
$stmt->execute(['id' => $id]);
$voiture = $stmt->fetch();

function e($str) { return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8'); }
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="style.css"/> 
  <title>Détail du véhicule</title>

</head>
<body>
  <header>
    <div class="wrap">
      <h2>AutoConcession</h2>
    </div>
    <a class="btn" href="admin.php" style="background:#ef4444;">Backend</a>
  </header>

  <main class="wrap">
    <?php if (!$voiture): ?>
      <p>Véhicule introuvable.</p>
    <?php else: ?>
      <div class="card">
        <?php if ($voiture['image_url']): ?>
          <img src="<?php echo e($voiture['image_url']); ?>" alt="<?php echo e($voiture['marque'] . ' ' . $voiture['modele']); ?>">
        <?php else: ?>
          <img src="https://via.placeholder.com/800x400?text=Image+non+disponible" alt="placeholder">
        <?php endif; ?>

        <h1><?php echo e($voiture['marque'] . ' ' . $voiture['modele']); ?></h1>
        <div class="price"><?php echo number_format((float)$voiture['prix'], 0, ',', ' '); ?> €</div>
        <div class="meta">Année <?php echo e($voiture['annee']); ?> • Ajouté le <?php echo (new DateTime($voiture['created_at']))->format('d/m/Y'); ?></div>

        <p><strong>Description :</strong> Ce véhicule est proposé par AutoConcession. Pour plus d'informations, contactez-nous ou passez commande via le formulaire d'achat.</p>

        <div class="actions">
          <a href="achat.html" class="btn ok">Acheter ce véhicule</a>
          <a href="stock.php" class="btn">Retour au stock</a>
        </div>
      </div>
    <?php endif; ?>
  </main>
</body>
</html>
