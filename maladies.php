<?php
require_once 'db.php';

$recherche = trim($_GET['recherche'] ?? '');

$sql = "SELECT * FROM maladies WHERE 1=1";
$params = [];

if ($recherche !== '') {
    $sql .= " AND (nom LIKE :recherche OR symptomes LIKE :recherche OR cause LIKE :recherche)";
    $params[':recherche'] = '%' . $recherche . '%';
}

$sql .= " ORDER BY nom ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$maladies = $stmt->fetchAll();

$totalMaladies = (int) $pdo->query("SELECT COUNT(*) FROM maladies")->fetchColumn();

function h($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maladies — SpaceFarm</title>
    <link rel="stylesheet" href="plantes.css">
</head>

<body>

<header class="header">
    <div class="header-content">
        <a href="index.html">Retour à l'accueil</a>
        <h1>Maladies des plantes</h1>
        <p class="subtitle">
            Symptômes, causes et traitements des principales maladies
        </p>
    </div>
</header>

<main class="container">

    <form class="toolbar" method="GET" style="grid-template-columns: 1fr auto;">
        <input
            type="search"
            name="recherche"
            placeholder="Rechercher une maladie..."
            value="<?= h($recherche) ?>"
        >
        <button type="submit">Rechercher</button>
    </form>

    <div class="stats">
        <div class="stat">
            <strong><?= $totalMaladies ?></strong>
            <span>maladies dans la base</span>
        </div>
        <div class="stat">
            <strong><?= count($maladies) ?></strong>
            <span>résultat(s) affiché(s)</span>
        </div>
    </div>

    <?php if (count($maladies) > 0): ?>

        <section class="grid">

            <?php foreach ($maladies as $maladie): ?>

                <article class="card">

                    <div class="card-top">
                        <h2 class="plant-name">
                            <?= h($maladie['nom']) ?>
                        </h2>
                    </div>

                    <div class="details" style="grid-template-columns: 1fr;">
                        
                        <?php if (!empty($maladie['plantes_liees'])): ?>
                            <div class="scientific">
                                Plantes concernées : <?= h($maladie['plantes_liees']) ?>
                            </div>
                        <?php endif; ?>

                        <div class="detail">
                            <span class="detail-label">Symptômes</span>
                            <span class="detail-value" style="font-weight: normal;">
                                <?= h($maladie['symptomes']) ?>
                            </span>
                        </div>

                        <div class="detail">
                            <span class="detail-label">Cause</span>
                            <span class="detail-value" style="font-weight: normal;">
                                <?= h($maladie['cause']) ?>
                            </span>
                        </div>

                        <div class="detail">
                            <span class="detail-label">Traitement</span>
                            <span class="detail-value" style="font-weight: normal;">
                                <?= h($maladie['traitement']) ?>
                            </span>
                        </div>

                    </div>

                </article>

            <?php endforeach; ?>

        </section>

    <?php else: ?>

        <div class="empty">
            <h2>Aucune maladie trouvée</h2>
            <p>Essayez une autre recherche.</p>
        </div>

    <?php endif; ?>

</main>

</body>
</html>