<?php
require_once 'db.php';

// Recherche et filtre par catégorie
$recherche = trim($_GET['recherche'] ?? '');
$categorie = trim($_GET['categorie'] ?? '');

$sqlCategories = "SELECT DISTINCT categorie
                  FROM plantes
                  WHERE categorie IS NOT NULL AND categorie <> ''
                  ORDER BY categorie";
$categories = $pdo->query($sqlCategories)->fetchAll();

$sql = "SELECT *
        FROM plantes
        WHERE 1=1";
$params = [];

if ($recherche !== '') {
    $sql .= " AND (
        nom LIKE :recherche
        OR nom_scientifique LIKE :recherche
        OR categorie LIKE :recherche
        OR description LIKE :recherche
    )";
    $params[':recherche'] = '%' . $recherche . '%';
}

if ($categorie !== '') {
    $sql .= " AND categorie = :categorie";
    $params[':categorie'] = $categorie;
}

$sql .= " ORDER BY nom ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$plantes = $stmt->fetchAll();

$totalPlantes = (int) $pdo->query("SELECT COUNT(*) FROM plantes")->fetchColumn();

function h($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function formatNumber($value, int $decimals = 1): string
{
    if ($value === null || $value === '') {
        return '—';
    }
    return number_format((float)$value, $decimals, ',', ' ');
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Encyclopédie — SpaceFarm</title>
    <link rel="stylesheet" href="plantes.css">
   
</head>

<body>

<header class="header">
    <div class="header-content">
        <a href="index.html">Retour à l'accueil</a>
        <h1>Encyclopédie des plantes</h1>
        <p class="subtitle">
            Découvrez les espèces cultivables à bord du vaisseau :
            croissance, température, humidité, pH, consommation d'eau et besoins en CO₂.
        </p>
    </div>
</header>

<main class="container">

    <form class="toolbar" method="GET">
        <input
            type="search"
            name="recherche"
            placeholder="Rechercher une plante..."
            value="<?= h($recherche) ?>"
        >

        <select name="categorie">
            <option value="">Toutes les catégories</option>
            <?php foreach ($categories as $cat): ?>
                <option
                    value="<?= h($cat['categorie']) ?>"
                    <?= $categorie === $cat['categorie'] ? 'selected' : '' ?>
                >
                    <?= h($cat['categorie']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Rechercher</button>
    </form>

    <div class="stats">
        <div class="stat">
            <strong><?= $totalPlantes ?></strong>
            <span>plantes dans la base</span>
        </div>

        <div class="stat">
            <strong><?= count($plantes) ?></strong>
            <span>résultat(s) affiché(s)</span>
        </div>
    </div>

    <?php if (count($plantes) > 0): ?>

        <section class="grid">

            <?php foreach ($plantes as $plante): ?>

                <article class="card">

                    <div class="card-top">

                        <?php if (!empty($plante['categorie'])): ?>
                            <span class="category">
                                <?= h($plante['categorie']) ?>
                            </span>
                        <?php endif; ?>

                        <h2 class="plant-name">
                            <?= h($plante['nom']) ?>
                        </h2>

                        <div class="scientific">
                            <?= h($plante['nom_scientifique']) ?>
                        </div>

                        <?php if (!empty($plante['description'])): ?>
                            <p class="description">
                                <?= h($plante['description']) ?>
                            </p>
                        <?php endif; ?>

                    </div>

                    <div class="details">

                        <div class="detail">
                            <span class="detail-label">Croissance</span>
                            <span class="detail-value">
                                <?= h($plante['temps_croissance_jours']) ?> jours
                            </span>
                        </div>

                        <div class="detail">
                            <span class="detail-label">Température</span>
                            <span class="detail-value">
                                <?= formatNumber($plante['temperature_min']) ?> °C
                                —
                                <?= formatNumber($plante['temperature_max']) ?> °C
                            </span>
                        </div>

                        <div class="detail">
                            <span class="detail-label">Humidité</span>
                            <span class="detail-value">
                                <?= formatNumber($plante['humidite_min'], 0) ?> %
                                —
                                <?= formatNumber($plante['humidite_max'], 0) ?> %
                            </span>
                        </div>

                        <div class="detail">
                            <span class="detail-label">pH</span>
                            <span class="detail-value">
                                <?= formatNumber($plante['ph_min']) ?>
                                —
                                <?= formatNumber($plante['ph_max']) ?>
                            </span>
                        </div>

                        <div class="detail">
                            <span class="detail-label">Eau</span>
                            <span class="detail-value">
                                <?= formatNumber($plante['eau_l_jour_m2'], 2) ?>
                                L / jour / m²
                            </span>
                        </div>

                        <div class="detail">
                            <span class="detail-label">CO₂</span>
                            <span class="detail-value">
                                <?= $plante['co2_ppm'] !== null
                                    ? h(number_format((int)$plante['co2_ppm'], 0, ',', ' ')) . ' ppm'
                                    : '—'
                                ?>
                            </span>
                        </div>

                    </div>

                </article>

            <?php endforeach; ?>

        </section>

    <?php else: ?>

        <div class="empty">
            <h2>Aucune plante trouvée</h2>
            <p>Essayez une autre recherche ou sélectionnez une autre catégorie.</p>
        </div>

    <?php endif; ?>

    <div class="footer">
        SpaceFarm — Encyclopédie botanique du vaisseau
    </div>

</main>

</body>
</html>
