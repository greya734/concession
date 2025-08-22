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
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $marque = trim($_POST['marque'] ?? '');
    $modele = trim($_POST['modele'] ?? '');
    $annee = (int)($_POST['annee'] ?? 0);
    $prix = (float)($_POST['prix'] ?? 0);

    $image_path = null;
    if (!empty($_FILES['image']['name'])) {
        $targetDir = __DIR__ . "/uploads/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $filename = time() . "_" . basename($_FILES['image']['name']);
        $targetFile = $targetDir . $filename;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            $image_path = "uploads/" . $filename; // chemin relatif stocké en BDD
        } else {
            $message = "❌ Erreur lors du téléchargement de l'image.";
        }
    }

    if ($marque && $modele && $annee && $prix) {
        $stmt = $pdo->prepare("INSERT INTO voitures (marque, modele, annee, prix, image_url, created_at)
                               VALUES (:marque,:modele,:annee,:prix,:image_url,NOW())");
        $stmt->execute([
            'marque' => $marque,
            'modele' => $modele,
            'annee' => $annee,
            'prix' => $prix,
            'image_url' => $image_path,
        ]);
        $message = '✅ Véhicule ajouté avec succès !';
    } else {
        $message = '❌ Merci de remplir tous les champs requis.';
    }
}
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Ajouter un véhicule</title>
  <style>
    body{margin:0;font-family:Arial, sans-serif;background:#f5f5f5;color:#111}
    header{background:#222;color:#fff;padding:16px}
    .wrap{max-width:700px;margin:0 auto;padding:16px}
    .card{background:#fff;border-radius:12px;padding:24px;box-shadow:0 4px 12px rgba(0,0,0,.1)}
    form{display:grid;gap:16px}
    label{font-weight:bold}
    input{padding:8px;border:1px solid #ccc;border-radius:6px;width:100%}
    button{padding:10px 16px;border:none;border-radius:6px;background:#007bff;color:#fff;font-size:16px;cursor:pointer}
    button:hover{background:#0056b3}
    .msg{margin:10px 0;font-weight:bold}
    .back{display:inline-block;margin-top:12px;text-decoration:none;color:#007bff}
  </style>
</head>
<body>
  <header>
    <h2>Back-office — Ajouter un véhicule</h2>
  </header>
  <main class="wrap">
    <div class="card">
      <?php if ($message): ?>
        <p class="msg"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>
      <?php endif; ?>

      <form method="post" enctype="multipart/form-data">
        <div>
          <label for="marque">Marque *</label>
          <input type="text" name="marque" id="marque" required>
        </div>
        <div>
          <label for="modele">Modèle *</label>
          <input type="text" name="modele" id="modele" required>
        </div>
        <div>
          <label for="annee">Année *</label>
          <input type="number" name="annee" id="annee" required>
        </div>

        <div>
          <label for="prix">Prix (€) *</label>
          <input type="number" name="prix" id="prix" required>
        </div>
        <div>
          <label for="image">Image du véhicule</label>
          <input type="file" name="image" id="image" accept="image/*">
        </div>
        <button type="submit">Ajouter</button>
      </form>

      <a href="admin.php" class="back">← Retour au tableau de bord</a>
    </div>
  </main>
</body>
</html>
