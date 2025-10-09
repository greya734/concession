<?php
require 'config.php';

// Récupération de tous les véhicules
$stmt = $pdo->query("SELECT * FROM voitures ORDER BY created_at DESC");
$voitures = $stmt->fetchAll();

function e($str) { return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8'); }

// Recherche
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
if ($search !== '') {
    $stmt = $pdo->prepare("SELECT * FROM voitures 
                           WHERE marque LIKE :s OR modele LIKE :s 
                           ORDER BY created_at DESC");
    $stmt->execute(['s' => "%$search%"]);
} else {
    $stmt = $pdo->query("SELECT * FROM voitures ORDER BY created_at DESC");
}
$voitures = $stmt->fetchAll();

?>

<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="style.css"/>
  <title>Stock de véhicules</title>
  
</head>
<body>
  <header>
    <div class="wrap nav">
      <h1>Stock de véhicules disponibles</h1>
      <nav style="margin-left:auto; display:flex; gap:10px;">
          
          <a class="btn" href="index.php">Accueil</a>
          <a class="btn" href="#contact">Contact</a>
          <a class="btn suppr" href="admin.php">admin</a>  
          </nav>
    </div>
    
    <style>
      img {width: 300px; height: 200px; object-fit: cover; /* conserve le ratio sans déformer */;}
    </style>
  </header>

  <main class="wrap">
    <?php if (!$voitures): ?>
      <p>Aucun véhicule en stock pour le moment.</p>
    <?php else: ?>
    <form method="get" class="search">
          <input type="text" name="search" placeholder="Rechercher par marque ou modèle..." value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>">
          <button class="submit" type="submit">Rechercher</button>
    </form>
      <div class="grid">
        <?php foreach ($voitures as $v): ?>
          <div class="card">
            <?php if ($v['image_url']): ?>
              <img src="<?php echo e($v['image_url']); ?>" alt="<?php echo e($v['marque'] . ' ' . $v['modele']); ?>">
            <?php else: ?>
              <img src="https://via.placeholder.com/400x200?text=Image+non+disponible" alt="placeholder">
            <?php endif; ?>

            <div class="title"><?php echo e($v['marque'] . ' ' . $v['modele']); ?></div>
            <div class="price"><?php echo number_format((float)$v['prix'], 0, ',', ' '); ?> €</div>
            <div class="meta">Année <?php echo e($v['annee']); ?> • Ajouté le <?php echo (new DateTime($v['created_at']))->format('d/m/Y'); ?></div>

            <a href="detail.php?id=<?php echo e($v['id']); ?>" class="btn ok">Voir le détail</a>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </main>
</body>
</html>
