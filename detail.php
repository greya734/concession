<?php
// --------- CONFIG BDD (XAMPP) ---------
$DB_HOST = 'localhost';
$DB_NAME = 'concession';
$DB_USER = 'root';
$DB_PASS = '';

try {
    $pdo = new PDO("mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4", $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo '<h1>Erreur de connexion à la base de données</h1>';
    exit;
}

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
  <title>Détail du véhicule</title>
  <style>
    body { margin:0; font-family: Arial, sans-serif; background: #f5f5f5; color: #111; }
    header { background: #222; color: #fff; padding: 16px; }
    .wrap { max-width: 900px; margin: 0 auto; padding: 16px; }
    .card { background: #fff; border-radius: 12px; padding: 24px; box-shadow: 0 4px 12px rgba(0,0,0,.1); }
    .card img { width: 100%; border-radius: 12px; margin-bottom: 16px; }
    h1 { margin-top: 0; }
    .price { font-size: 22px; font-weight: bold; color: #2c7; margin: 12px 0; }
    .meta { color: #555; margin-bottom: 20px; }
    .actions { display:flex; gap:10px; }
    a.btn { display: inline-block; padding: 10px 18px; background: #007bff; color: #fff; border-radius: 6px; text-decoration: none; }
    a.btn:hover { background: #0056b3; }
    a.back { background:#666; }
  </style>
</head>
<body>
  <header>
    <div class="wrap">
      <h2>AutoConcession</h2>
    </div>
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
          <a href="achat.html" class="btn">Acheter ce véhicule</a>
          <a href="stock.php" class="btn back">Retour au stock</a>
        </div>
      </div>
    <?php endif; ?>
  </main>
</body>
</html>
