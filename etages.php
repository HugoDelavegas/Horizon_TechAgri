<?php
require_once 'db.php';

$numero = (int) ($_GET['numero'] ?? 1);
if ($numero < 1 || $numero > 5) {
    $numero = 1;
}

$sql = "
    SELECT *
    FROM (
        SELECT
            p.*,
            (p.temperature_min + p.temperature_max) / 2 AS temp_moyenne,
            NTILE(5) OVER (
                ORDER BY (p.temperature_min + p.temperature_max) / 2, p.nom
            ) AS etage_calcule
        FROM plantes p
        WHERE p.temperature_min IS NOT NULL
          AND p.temperature_max IS NOT NULL
    ) AS classement
    WHERE etage_calcule = :numero
    ORDER BY nom ASC
";

$stmt = $pdo->prepare($sql);
$stmt->execute([':numero' => $numero]);
$plantes = $stmt->fetchAll();

$bornes = [
    'temperature_min' => null,
    'temperature_max' => null,
    'humidite_min'    => null,
    'humidite_max'    => null,
    'co2_min'         => null,
    'co2_max'         => null,
];

foreach ($plantes as $p) {
    $bornes['temperature_min'] = $bornes['temperature_min'] === null
        ? $p['temperature_min'] : min($bornes['temperature_min'], $p['temperature_min']);
    $bornes['temperature_max'] = $bornes['temperature_max'] === null
        ? $p['temperature_max'] : max($bornes['temperature_max'], $p['temperature_max']);
    $bornes['humidite_min'] = $bornes['humidite_min'] === null
        ? $p['humidite_min'] : min($bornes['humidite_min'], $p['humidite_min']);
    $bornes['humidite_max'] = $bornes['humidite_max'] === null
        ? $p['humidite_max'] : max($bornes['humidite_max'], $p['humidite_max']);
    $bornes['co2_min'] = $bornes['co2_min'] === null
        ? $p['co2_ppm'] : min($bornes['co2_min'], $p['co2_ppm']);
    $bornes['co2_max'] = $bornes['co2_max'] === null
        ? $p['co2_ppm'] : max($bornes['co2_max'], $p['co2_ppm']);
}

$noms_etages = [
    1 => 'Zone fraîche',
    2 => 'Zone tempérée',
    3 => 'Zone intermédiaire',
    4 => 'Zone chaude modérée',
    5 => 'Zone chaude',
];

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
    <title>Étage <?= h($numero) ?> - <?= h($noms_etages[$numero]) ?> - SpaceFarm</title>
    <link rel="stylesheet" href="plantes.css">
</head>

<body>

<header class="header">
    <div class="header-content">
        <a href="index.html">Retour à l'accueil</a>
        <h1>Étage <?= h($numero) ?> - <?= h($noms_etages[$numero]) ?></h1>
        <p class="subtitle">
            Répartition selon la température moyenne des espèces
        </p>

        <nav class="etage-nav" style="margin-top: 1rem;">
            <?php for ($i = 1; $i <= 5; $i++): ?>
                <a href="?numero=<?= $i ?>" class="etage-pill <?= $i === $numero ? 'active' : '' ?>">
                    Étage <?= $i ?>
                </a>
            <?php endfor; ?>
        
        <a href="controles.php?numero=<?= $numero ?>" class="etage-pill etage-control">
        Contrôles systèmes
        </a>
        </nav>
    </div>
</header>

<main class="container">

    <div class="stats">
        <div class="stat">
            <strong><?= count($plantes) ?></strong>
            <span>espèces sur cet étage</span>
        </div>
        <div class="stat">
            <strong><?= formatNumber($bornes['temperature_min'], 0) ?>–<?= formatNumber($bornes['temperature_max'], 0) ?> °C</strong>
            <span>plage de température</span>
        </div>
        <div class="stat">
            <strong><?= formatNumber($bornes['humidite_min'], 0) ?>–<?= formatNumber($bornes['humidite_max'], 0) ?> %</strong>
            <span>plage d'humidité</span>
        </div>
        <div class="stat">
            <strong><?= h(number_format((int)$bornes['co2_min'], 0, ',', ' ')) ?>–<?= h(number_format((int)$bornes['co2_max'], 0, ',', ' ')) ?> ppm</strong>
            <span>plage de CO₂</span>
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
            <h2>Aucune plante sur cet étage</h2>
        </div>

    <?php endif; ?>

</main>

</body>
</html>