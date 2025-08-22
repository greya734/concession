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
  <style>
    :root { --bg:#0b1220; --panel:#111827; --muted:#94a3b8; --text:#e5e7eb; --brand:#22d3ee; }
    *{box-sizing:border-box}
    body{margin:0;font-family:system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,Cantarell,Noto Sans,Helvetica,Arial;background:linear-gradient(180deg,#0b1220,#111827);color:var(--text)}
    .wrap{max-width:900px;margin:0 auto;padding:24px}
    header{position:sticky;top:0;background:rgba(17,24,39,.7);backdrop-filter:blur(6px);border-bottom:1px solid rgba(148,163,184,.15)}
    h1{margin:0;padding:16px 24px}
    .card{background:linear-gradient(180deg,rgba(255,255,255,.03),rgba(255,255,255,.01));border:1px solid rgba(148,163,184,.15);border-radius:16px;padding:20px;box-shadow:0 10px 30px rgba(0,0,0,.25)}
    .login{max-width:360px;margin:56px auto}
    label{display:block;margin-bottom:6px;font-weight:700}
    input{width:100%;padding:10px;border-radius:10px;border:1px solid rgba(148,163,184,.25);background:#0f172a;color:var(--text)}
    .row{display:grid;gap:12px}
    button,.btn{display:inline-block;padding:10px 14px;border-radius:999px;border:1px solid rgba(148,163,184,.2);background:#ef4444;color:#fff;font-weight:700;text-decoration:none}
    button:hover,.btn:hover{filter:brightness(1.05)}
    .muted{color:var(--muted)}
    nav.actions{display:flex;gap:10px;flex-wrap:wrap;margin-top:12px}
    .ok{background:#22c55e;border-color:transparent}
    .ghost{background:transparent;color:var(--text)}
    .error{color:#fecaca;margin:8px 0}
  </style>
</head>
<body>
  <header>
    <h1>Back‑office — AutoConcession</h1>
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
            <button type="submit">Se connecter</button>
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
          <a class="btn ghost" href="stock.php">Voir le stock</a>
          <a class="btn" href="admin.php?logout=1">Se déconnecter</a>
        </nav>
      </div>
    <?php endif; ?>
  </main>
</body>
</html>
