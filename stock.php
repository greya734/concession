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
  <title>Stock de véhicules</title>
  <style>
    body { margin:0; font-family: Arial, sans-serif; background: #f5f5f5; color: #111; }
    header { background: #222; color: #fff; padding: 16px; }
    .wrap { max-width: 1200px; margin: 0 auto; padding: 16px; }
    h1 { margin-top: 0; }
    .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px; }
    .card { background: #fff; border-radius: 12px; padding: 16px; box-shadow: 0 4px 12px rgba(0,0,0,.1); display:flex; flex-direction:column; }
    .card img { width: 100%; height:180px; object-fit:cover; border-radius: 8px; margin-bottom: 12px; }
    .title { font-weight: bold; font-size: 18px; margin-bottom: 6px; }
    .price { font-size: 18px; font-weight: bold; color: #2c7; margin: 8px 0; }
    .meta { color: #555; font-size:14px; margin-bottom: 12px; }
    a.btn { display: inline-block; padding: 8px 14px; background: #007bff; color: #fff; border-radius: 6px; text-decoration: none; text-align:center; margin-top:auto; }
    a.btn:hover { background: #0056b3; }
  </style>
</head>
<body>
  <header>
    <div class="wrap">
      <h1>Stock de véhicules disponibles</h1>
    </div>
  </header>

  <main class="wrap">
    <?php if (!$voitures): ?>
      <p>Aucun véhicule en stock pour le moment.</p>
    <?php else: ?>
    <form method="get" class="search">
          <input type="text" name="search" placeholder="Rechercher par marque ou modèle..." value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>">
          <button type="submit">Rechercher</button>
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

            <a href="detail.php?id=<?php echo e($v['id']); ?>" class="btn">Voir le détail</a>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </main>
</body>
</html>
