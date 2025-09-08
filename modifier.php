<?php
session_start();

// (optionnel) vérifier si l’admin est connecté
if (empty($_SESSION['logged'])) {
    header("Location: admin.php");
    exit;
}

// Connexion BDD
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
    die("Erreur connexion BDD");
}

// Récupérer ID du véhicule
$id = isset($_GET['id']) ? (int)$_GET['id'] : 1;

// Charger véhicule
$stmt = $pdo->prepare("SELECT * FROM voitures WHERE id = :id");
$stmt->execute(['id' => $id]);
$voiture = $stmt->fetch();

if (!$voiture) {
    die("Véhicule introuvable");
}

// Si formulaire envoyé
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $marque   = $_POST['marque'] ?? '';
    $modele   = $_POST['modele'] ?? '';
    $annee    = $_POST['annee'] ?? '';
    $couleur  = $_POST['couleur'] ?? '';
    $prix     = $_POST['prix'] ?? 0;

    // Image upload si modifiée
    $image_url = $voiture['image_url'];
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "uploads/";
        if (!is_dir($targetDir)) mkdir($targetDir);

        $filename = time() . "_" . basename($_FILES['image']['name']);
        $targetFile = $targetDir . $filename;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            $image_url = $targetFile;
        }
    }

    $stmt = $pdo->prepare("UPDATE voitures 
        SET marque = :marque, modele = :modele, annee = :annee, prix = :prix, image_url = :image_url
        WHERE id = :id");

    $stmt->execute([
        'marque'   => $marque,
        'modele'   => $modele,
        'annee'    => $annee,
        'prix'     => $prix,
        'image_url'=> $image_url,
        'id'       => $id
    ]);

    header("Location: gestion_stock.php?success=modif"); // tu peux adapter
    exit;
}
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Modifier un véhicule</title>
  <style>
    body { font-family: Arial, sans-serif; background:#f5f5f5; margin:0; }
    .wrap { max-width:800px; margin:20px auto; background:#fff; padding:20px; border-radius:12px; box-shadow:0 4px 10px rgba(0,0,0,.1); }
    label { display:block; margin-top:12px; }
    input { width:100%; padding:10px; margin-top:6px; border:1px solid #ccc; border-radius:6px; }
    button { margin-top:20px; padding:10px 15px; background:#007bff; color:#fff; border:none; border-radius:6px; cursor:pointer; }
    button:hover { background:#0056b3; }
    img { max-width:200px; margin-top:10px; display:block; }
  </style>
</head>
<body>
  <div class="wrap">
    <h2>Modifier le véhicule</h2>

    <form method="post" enctype="multipart/form-data">
      <label>Marque :</label>
      <input type="text" name="marque" value="<?= htmlspecialchars($voiture['marque']) ?>" required>

      <label>Modèle :</label>
      <input type="text" name="modele" value="<?= htmlspecialchars($voiture['modele']) ?>" required>

      <label>Année :</label>
      <input type="number" name="annee" value="<?= htmlspecialchars($voiture['annee']) ?>">


      <label>Prix :</label>
      <input type="number" name="prix" value="<?= htmlspecialchars($voiture['prix']) ?>" required>

      <label>Image :</label>
      <input type="file" name="image">
      <?php if ($voiture['image_url']): ?>
        <img src="<?= htmlspecialchars($voiture['image_url']) ?>" alt="Aperçu">
      <?php endif; ?>

      <button type="submit">Enregistrer les modifications</button>
    </form>
  </div>
</body>
</html>
