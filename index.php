<?php
// --------- CONFIG BDD (XAMPP) ---------
// Par défaut sur XAMPP : utilisateur root, mdp vide
$DB_HOST = 'localhost';
$DB_NAME = 'concession';
$DB_USER = 'root';
$DB_PASS = '';

// Nombre d'articles à afficher
$LIMIT = 12;

// Connexion PDO
try {
    $pdo = new PDO("mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4", $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo '<h1>Erreur de connexion à la base de données</h1>';
    echo '<p>Vérifiez vos paramètres de connexion dans le fichier <code>index.php</code>.</p>';
    echo '<pre>' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</pre>';
    exit;
}

// Récupération des modèles récemment ajoutés
$stmt = $pdo->prepare("SELECT id, marque, modele, annee, prix, image_url, created_at FROM voitures ORDER BY created_at DESC LIMIT :lim");
$stmt->bindValue(':lim', (int)$LIMIT, PDO::PARAM_INT);
$stmt->execute();
$voitures = $stmt->fetchAll();

function e($str) { return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8'); }
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Concession — Nouveautés</title>
  <style>
    :root {
      --bg: #0f172a;        /* slate-900 */
      --panel: #111827;     /* gray-900 */
      --muted: #94a3b8;     /* slate-400 */
      --text: #e5e7eb;      /* gray-200 */
      --brand: #22d3ee;     /* cyan-400 */
      --accent: #a78bfa;    /* violet-400 */
      --ok: #34d399;        /* green-400 */
      --warning: #f59e0b;   /* amber-500 */
      --radius: 16px;
    }
    * { box-sizing: border-box; }
    body { margin:0; font-family: system-ui, -apple-system, Segoe UI, Roboto, Ubuntu, Cantarell, Noto Sans, Helvetica, Arial, "Apple Color Emoji", "Segoe UI Emoji"; background: linear-gradient(180deg, #0b1220 0%, var(--bg) 100%); color: var(--text); }
    a { color: inherit; text-decoration: none; }
    header { position: sticky; top: 0; z-index: 10; backdrop-filter: saturate(120%) blur(6px); background: rgba(15,23,42,.6); border-bottom: 1px solid rgba(148,163,184,.1); }
    .wrap { max-width: 1200px; margin: 0 auto; padding: 16px; }
    .nav { display: flex; align-items: center; gap: 20px; }
    .logo { font-weight: 800; letter-spacing: .3px; font-size: 20px; }
    .logo span { color: var(--brand); }
    .btn { display: inline-flex; align-items: center; gap: 8px; padding: 10px 14px; border-radius: 999px; border: 1px solid rgba(148,163,184,.2); transition: transform .06s ease, background .2s ease; }
    .btn:hover { background: rgba(148,163,184,.08); transform: translateY(-1px); }

    .hero { padding: 36px 16px 8px; }
    .hero h1 { font-size: clamp(28px, 3vw, 36px); margin: 0 0 8px; }
    .hero p { margin: 0; color: var(--muted); }

    .grid { display: grid; grid-template-columns: repeat(12, 1fr); gap: 16px; padding: 16px; }
    .card { grid-column: span 12; background: linear-gradient(180deg, rgba(255,255,255,.02), rgba(255,255,255,.005)); border: 1px solid rgba(148,163,184,.14); border-radius: var(--radius); overflow: hidden; position: relative; box-shadow: 0 10px 30px rgba(0,0,0,.25); }
    @media (min-width: 640px) { .card { grid-column: span 6; } }
    @media (min-width: 1024px) { .card { grid-column: span 4; } }

    .thumb { position: relative; aspect-ratio: 16/9; background: radial-gradient(100% 100% at 50% 0%, rgba(34,211,238,.15), rgba(167,139,250,.08)); display: grid; place-items: center; }
    .thumb img { width: 100%; height: 100%; object-fit: cover; display: block; }
    .noimg { font-size: 42px; opacity: .6; }

    .badge { position: absolute; top: 10px; left: 10px; background: linear-gradient(90deg, var(--brand), var(--accent)); color: #0b1220; font-weight: 700; font-size: 12px; padding: 6px 10px; border-radius: 999px; letter-spacing: .3px; border: 1px solid rgba(0,0,0,.2); }

    .content { padding: 14px; display: grid; gap: 8px; }
    .title { font-weight: 700; font-size: 18px; }
    .meta { color: var(--muted); font-size: 14px; }
    .price { font-size: 18px; font-weight: 800; color: var(--ok); }

    .actions { display: flex; justify-content: space-between; align-items: center; padding-top: 8px; }
    .pill { font-size: 12px; padding: 6px 10px; border-radius: 999px; border: 1px solid rgba(148,163,184,.18); color: var(--muted); }

    footer { padding: 32px 16px; color: var(--muted); border-top: 1px solid rgba(148,163,184,.1); margin-top: 16px; }
    .empty { grid-column: 1 / -1; text-align: center; padding: 48px 16px; border: 2px dashed rgba(148,163,184,.2); border-radius: var(--radius); color: var(--muted); }
  </style>
</head>
<body>
  <header>
    <div class="wrap nav">
      <div class="logo">Auto<span>Concession</span></div>
      <nav style="margin-left:auto; display:flex; gap:10px;">
        <a class="btn" href="#">Accueil</a>
        <a class="btn" href="#stock">Stock</a>
        <a class="btn" href="#contact">Contact</a>
      </nav>
    </div>
  </header>

  <main class="wrap">
    <section class="hero">
      <h1>Nouveautés du parc</h1>
      <p>Les derniers modèles ajoutés à notre base de données, mis à jour en temps réel.</p>
    </section>

    <section class="grid" aria-label="Modèles récemment ajoutés">
      <?php if (!$voitures): ?>
        <div class="empty">Aucun véhicule pour le moment. Ajoutez des enregistrements dans la table <code>voitures</code> puis actualisez la page.</div>
      <?php else: ?>
        <?php foreach ($voitures as $v): ?>
          <?php
            $created = new DateTime($v['created_at']);
            $isNew = $created >= (new DateTime('-14 days'));
            $titre = trim($v['marque'] . ' ' . $v['modele']);
          ?>
          <article class="card">
            <div class="thumb">
              <?php if (!empty($v['image_url'])): ?>
                <img src="<?php echo e($v['image_url']); ?>" alt="<?php echo e($titre); ?>">
              <?php else: ?>
                <div class="noimg" aria-label="Image manquante">🚗</div>
              <?php endif; ?>
              <?php if ($isNew): ?><span class="badge">Nouveau</span><?php endif; ?>
            </div>
            <div class="content">
              <div class="title"><?php echo e($titre); ?></div>
              <div class="meta">Année <?php echo e($v['annee']); ?> • Ajouté le <?php echo e($created->format('d/m/Y')); ?></div>
              <?php if ($v['prix'] !== null): ?>
                <div class="price"><?php echo number_format((float)$v['prix'], 0, ',', ' '); ?> €</div>
              <?php endif; ?>
              <div class="actions">
                <span class="pill">ID #<?php echo e($v['id']); ?></span>
                <a class="btn" href="vehicule.php?id=<?php echo urlencode((string)$v['id']); ?>">Voir la fiche</a>
              </div>
            </div>
          </article>
        <?php endforeach; ?>
      <?php endif; ?>
    </section>
  </main>

  <footer class="wrap">
    © <?php echo date('Y'); ?> AutoConcession — Démo XAMPP. 
  </footer>
</body>
</html>

<?php /*
============================
Guide d'installation rapide
============================
1) Créez la base et la table (phpMyAdmin > SQL) :

CREATE DATABASE IF NOT EXISTS concession CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE concession;

CREATE TABLE IF NOT EXISTS voitures (
  id INT AUTO_INCREMENT PRIMARY KEY,
  marque VARCHAR(80) NOT NULL,
  modele VARCHAR(120) NOT NULL,
  annee INT NOT NULL,
  prix DECIMAL(10,2) NULL,
  image_url VARCHAR(255) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Exemples de véhicules
INSERT INTO voitures (marque, modele, annee, prix, image_url, created_at) VALUES
('Peugeot', '308 GT', 2023, 25990, NULL, NOW()),
('Renault', 'Clio V Intens', 2022, 17990, NULL, NOW() - INTERVAL 3 DAY),
('BMW', '320d xDrive', 2021, 33900, NULL, NOW() - INTERVAL 10 DAY),
('Toyota', 'Yaris Hybride', 2024, 23990, NULL, NOW() - INTERVAL 20 DAY);

2) Placez ce fichier dans :
   C:\\xampp\\htdocs\\concession\\index.php (Windows)
   ou /Applications/XAMPP/htdocs/concession/index.php (macOS)

3) Démarrez Apache & MySQL dans XAMPP, puis ouvrez :
   http://localhost/concession/

Personnalisation :
- Modifiez les variables $DB_* en haut du fichier si nécessaire.
- Remplacez image_url par une URL/chemin d'image (ex: "assets/308.jpg").
- Le badge "Nouveau" s'affiche pour les 14 derniers jours (modifiable via DateTime('-14 days')).
*/ ?>
