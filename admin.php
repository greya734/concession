<?php
session_start();

// ⚠️ Démo : à remplacer par un vrai système d'utilisateurs / mots de passe hashés
const ADMIN_USER = 'admin';
const ADMIN_PASS = '1234';

// Déconnexion
if (isset($_GET['logout'])) {
    $_SESSION = [];
    session_destroy();
    header('Location: admin.php');
    exit;
}

// Tentative de connexion
$error = '';
if (!isset($_SESSION['logged']) || $_SESSION['logged'] !== true) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $u = $_POST['user'] ?? '';
        $p = $_POST['pass'] ?? '';
        if ($u === ADMIN_USER && $p === ADMIN_PASS) {
            $_SESSION['logged'] = true;
            header('Location: admin.php');
            exit;
        } else {
            $error = 'Identifiants incorrects';
        }
    }
}

$isLogged = !empty($_SESSION['logged']);
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Back-office — AutoConcession</title>
  <link rel="stylesheet" href="style.css"/>
</head>
  
<body>
  <header>
    <h1>Back‑office — AutoConcession</h1>
    <a class="btn ok" href="index.php">Accueuil</a>
  </header>

  <main class="wrap">
    <?php if (!$isLogged): ?>
      <div class="card login">
        <h2>Connexion</h2>
        <?php if (!empty($error)): ?>
          <p class="error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>
        <form method="post" class="row">
          <div>
            <label for="user">Utilisateur</label>
            <input id="user" name="user" type="text" autocomplete="username" required>
          </div>
          <div>
            <label for="pass">Mot de passe</label>
            <input id="pass" name="pass" type="password" autocomplete="current-password" required>
          </div>
          <div>
            <button class="submit" type="submit">Se connecter</button>
          </div>
        </form>
        <p class="muted" style="margin-top:10px">Démo : <code>admin</code> / <code>1234</code></p>
      </div>
    <?php else: ?>
      <div class="card">
        <h2 style="margin-top:0">Tableau de bord</h2>
        <p class="muted">Bienvenue ! Utilisez les actions ci‑dessous pour gérer le parc.</p>
        <nav class="actions">
          <a class="btn ok" href="ajouter.php">Ajouter un véhicule</a>
          <a class="btn error" href="gestion_stock.php">modifier le stock</a>
          <a class="btn ghost" href="stock.php">voir le stock</a>
          <a class="btn error" href="admin.php?logout=1">Se déconnecter</a>
        </nav>
      </div>
    <?php endif; ?>
  </main>
</body>
</html>
