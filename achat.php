<?php
session_start();

// Vérifier si client connecté
$isClient = !empty($_SESSION['client_id']);

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

$client = null;
if ($isClient) {
    $stmt = $pdo->prepare("SELECT * FROM clients WHERE id = :id");
    $stmt->execute(['id' => $_SESSION['client_id']]);
    $client = $stmt->fetch();
}

// Si un véhicule est choisi (via id dans l’URL)
$vehicule = null;
if (!empty($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM voitures WHERE id = :id");
    $stmt->execute(['id' => (int)$_GET['id']]);
    $vehicule = $stmt->fetch();
}
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Achat véhicule</title>
  <link rel="stylesheet" href="style.css"/>
</head>
<body>
  <header>
    <div class="wrap nav">
        
        <nav style="margin-left:auto; display:flex; gap:10px;">
          
          <a class="btn suppr" href="admin.php">admin</a>   
          <a class="btn" href="index.php">Accueil</a>
          <a class="btn" href="stock.php">Stock</a>
          <a class="btn" href="#contact">Contact</a>
      </nav>
        
      </div>  
  </header>

  <div class="wrap">
    <h1>Finaliser votre achat</h1>

    <?php if ($vehicule): ?>
      <div class="vehicule">
        <strong>Véhicule choisi :</strong><br>
        <?= htmlspecialchars($vehicule['marque'] . " " . $vehicule['modele']) ?> — 
        <?= number_format($vehicule['prix'], 0, ',', ' ') ?> €
      </div>
    <?php endif; ?>

    <form method="post" action="achat_traitement.php">
      <label>Nom :</label>
      <input type="text" name="nom" value="<?= $client ? htmlspecialchars($client['nom']) : '' ?>" required>

      <label>Email :</label>
      <input type="email" name="email" value="<?= $client ? htmlspecialchars($client['email']) : '' ?>" required>

      <label>Téléphone :</label>
      <input type="text" name="telephone" value="<?= $client ? htmlspecialchars($client['telephone']) : '' ?>">

      <label for="adresse">Adresse de livraison</label>
      <input type="text" id="adresse" placeholder="12 rue des Lilas, 75000 Paris"> 
        
      <label for="modepaiement">Mode de paiement</label>
        <select id="modepaiement" required>
          <option value="">-- Sélectionner --</option>
          <option>Carte bancaire</option>
          <option>Virement bancaire</option>
          <option>Chèque</option>
        </select>
        
      
        
      <?php if ($vehicule): ?>
        <input type="hidden" name="voiture_id" value="<?= $vehicule['id'] ?>">
      <?php endif; ?>

      <button class="submit" type="submit">Valider l’achat</button>
    </form>
  </div>
</body>
</html>
