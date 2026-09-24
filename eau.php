<?php

$circuits = [
    [
        'nom' => 'Réservoir principal',
        'ph' => 6.8,
        'conductivite' => 1.4,
        'temperature' => 19.5,
        'oxygene' => 7.2,
        'nitrates' => 45,
        'turbidite' => 0.8,
        'statut' => 'Optimal',
        'derniere_mesure' => 'Il y a 3 min',
    ],
    [
        'nom' => 'Circuit - Étage 1',
        'ph' => 6.5,
        'conductivite' => 1.2,
        'temperature' => 18.0,
        'oxygene' => 7.5,
        'nitrates' => 38,
        'turbidite' => 0.6,
        'statut' => 'Optimal',
        'derniere_mesure' => 'Il y a 8 min',
    ],
    [
        'nom' => 'Circuit - Étage 2',
        'ph' => 6.3,
        'conductivite' => 1.5,
        'temperature' => 20.2,
        'oxygene' => 6.8,
        'nitrates' => 52,
        'turbidite' => 1.1,
        'statut' => 'À surveiller',
        'derniere_mesure' => 'Il y a 12 min',
    ],
    [
        'nom' => 'Circuit - Étage 3',
        'ph' => 6.7,
        'conductivite' => 1.3,
        'temperature' => 20.8,
        'oxygene' => 7.0,
        'nitrates' => 41,
        'turbidite' => 0.7,
        'statut' => 'Optimal',
        'derniere_mesure' => 'Il y a 5 min',
    ],
    [
        'nom' => 'Circuit - Étage 4',
        'ph' => 6.2,
        'conductivite' => 1.7,
        'temperature' => 22.5,
        'oxygene' => 6.4,
        'nitrates' => 58,
        'turbidite' => 1.4,
        'statut' => 'À surveiller',
        'derniere_mesure' => 'Il y a 2 min',
    ],
    [
        'nom' => 'Circuit - Étage 5',
        'ph' => 6.6,
        'conductivite' => 1.6,
        'temperature' => 23.1,
        'oxygene' => 6.9,
        'nitrates' => 47,
        'turbidite' => 0.9,
        'statut' => 'Optimal',
        'derniere_mesure' => 'Il y a 6 min',
    ],
];

$moyennes = ['ph' => 0, 'conductivite' => 0, 'oxygene' => 0, 'nitrates' => 0];
foreach ($circuits as $c) {
    $moyennes['ph'] += $c['ph'];
    $moyennes['conductivite'] += $c['conductivite'];
    $moyennes['oxygene'] += $c['oxygene'];
    $moyennes['nitrates'] += $c['nitrates'];
}
$n = count($circuits);
foreach ($moyennes as $key => $val) {
    $moyennes[$key] = round($val / $n, 2);
}

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
    <title>Analyse de l'eau - SpaceFarm</title>
    <link rel="stylesheet" href="plantes.css">
    <style>
        .statut {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: bold;
        }
        .statut-optimal { background: rgba(2, 129, 255, 0.15); color: #6fe3b8; }
        .statut-surveiller { background: rgba(2, 129, 255, 0.15); color: #ffc861; }
    </style>
</head>

<body>

<header class="header">
    <div class="header-content">
        <a href="index.html">Retour à l'accueil</a>
        <h1>Qualité de l'eau</h1>
        <p class="subtitle">
            Suivi de la qualité de l'eau des circuits d'irrigation du vaisseau
        </p>
    </div>
</header>

<main class="container">

    <div class="stats">
        <div class="stat">
            <strong><?= h($moyennes['ph']) ?></strong>
            <span>pH moyen</span>
        </div>
        <div class="stat">
            <strong><?= h($moyennes['conductivite']) ?> mS/cm</strong>
            <span>conductivité moyenne</span>
        </div>
        <div class="stat">
            <strong><?= h($moyennes['oxygene']) ?> mg/L</strong>
            <span>oxygène dissous moyen</span>
        </div>
        <div class="stat">
            <strong><?= h($moyennes['nitrates']) ?> mg/L</strong>
            <span>nitrates moyens</span>
        </div>
    </div>

    <section class="grid">

        <?php foreach ($circuits as $circuit): ?>

            <article class="card">

                <div class="card-top">
                    <h2 class="plant-name" style="font-size: 20px;">
                        <?= h($circuit['nom']) ?>
                    </h2>
                    <div class="scientific">
                        Dernière mesure : <?= h($circuit['derniere_mesure']) ?>
                    </div>
                    <span class="statut <?= $circuit['statut'] === 'Optimal' ? 'statut-optimal' : 'statut-surveiller' ?>">
                        <?= h($circuit['statut']) ?>
                    </span>
                </div>

                <div class="details">

                    <div class="detail">
                        <span class="detail-label">pH</span>
                        <span class="detail-value"><?= h($circuit['ph']) ?></span>
                    </div>

                    <div class="detail">
                        <span class="detail-label">Conductivité</span>
                        <span class="detail-value"><?= h($circuit['conductivite']) ?> mS/cm</span>
                    </div>

                    <div class="detail">
                        <span class="detail-label">Température</span>
                        <span class="detail-value"><?= h($circuit['temperature']) ?> °C</span>
                    </div>

                    <div class="detail">
                        <span class="detail-label">Oxygène dissous</span>
                        <span class="detail-value"><?= h($circuit['oxygene']) ?> mg/L</span>
                    </div>

                    <div class="detail">
                        <span class="detail-label">Nitrates</span>
                        <span class="detail-value"><?= h($circuit['nitrates']) ?> mg/L</span>
                    </div>

                    <div class="detail">
                        <span class="detail-label">Turbidité</span>
                        <span class="detail-value"><?= h($circuit['turbidite']) ?> NTU</span>
                    </div>

                </div>

            </article>

        <?php endforeach; ?>

    </section>

</main>

</body>
</html>