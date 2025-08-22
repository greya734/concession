<?php
session_start();
if (empty($_SESSION['logged'])) {
    header('Location: admin.php');
    exit;
}

// Config BDD
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

$message = '';

// Suppression si un ID est passé
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM voitures WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $message = "✅ Véhicule ID $id supprimé avec succès.";
}

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
  <title>Supprimer un véhicule</title>
  <style>
    body { margin:0; font-family:Arial,sans-serif; background:#f5f5f5; color:#111 }
    header { background:#222; color:#fff; padding:16px }
    .wrap { max-width:900px; margin:0 auto; padding:16px }
    .card { background:#fff; border-radius:12px; padding:24px; box-shadow:0 4px 12px rgba(0,0,0,.1) }
    table { width:100%; border-collapse:collapse; margin-top:16px }
    th, td { padding:10px; border-bottom:1px solid #ccc; text-align:left }
    th { background:#eee }
    img { max-width:100px; border-radius:8px }
    a.btn { padding:6px 10px; background:#c00; color:#fff; border-radius:6px; text-decoration:none }
    a.btn:hover { background:#900 }
    .msg { margin:10px 0; font-weight:bold }
    .back { display:inline-block; margin-top:12px; text-decoration:none; color:#007bff }
    form.search { margin-bottom:16px; }
    input[type="text"] { padding:8px; border:1px solid #ccc; border-radius:6px; width:250px; }
    button { padding:8px 12px; border:none; border-radius:6px; background:#007bff; color:#fff; cursor:pointer; }
    button:hover { background:#0056b3; }
  </style>
</head>
<body>
  <header>
    <h2>Back-office — Supprimer un véhicule</h2>
  </header>
  <main class="wrap">
    <div class="card">
      <?php if ($message): ?>
        <p class="msg"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>
      <?php endif; ?>

      <form method="get" class="search">
        <input type="text" name="search" placeholder="Rechercher par marque ou modèle..." value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>">
        <button type="submit">Rechercher</button>
      </form>

      <?php if (!$voitures): ?>
        <p>Aucun véhicule trouvé.</p>
      <?php else: ?>
        <table>
          <tr>
            <th>ID</th>
            <th>Image</th>
            <th>Marque & Modèle</th>
            <th>Année</th>
            <th>Couleur</th>
            <th>Prix (€)</th>
            <th>Action</th>
          </tr>
          <?php foreach ($voitures as $v): ?>
          <tr>
            <td><?= $v['id'] ?></td>
            <td>
              <?php if ($v['image_url']): ?>
                <img src="<?= htmlspecialchars($v['image_url']) ?>" alt="">
              <?php else: ?>
                <img src="https://via.placeholder.com/100x60?text=No+Image" alt="">
              <?php endif; ?>
            </td>
            <td><?= htmlspecialchars($v['marque'].' '.$v['modele']) ?></td>
            <td><?= htmlspecialchars($v['annee']) ?></td>
            <td><?= number_format($v['prix'], 0, ',', ' ') ?> €</td>
            <td>
              <a class="btn" href="supprimer.php?delete=<?= $v['id'] ?>" 
                 onclick="return confirm('Supprimer ce véhicule ?')">Supprimer</a>
            </td>
          </tr>
          <?php endforeach; ?>
        </table>
      <?php endif; ?>

      <a href="admin.php" class="back">← Retour au tableau de bord</a>
    </div>
  </main>
</body>
</html>
