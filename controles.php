<?php
require_once 'db.php';

$numero = (int) ($_GET['numero'] ?? 1);
if ($numero < 1 || $numero > 5) {
    $numero = 1;
}

$sql = "
    SELECT
        MIN(temperature_min) AS temp_min, MAX(temperature_max) AS temp_max,
        MIN(humidite_min) AS hum_min, MAX(humidite_max) AS hum_max,
        MIN(co2_ppm) AS co2_min, MAX(co2_ppm) AS co2_max,
        AVG(eau_l_jour_m2) AS eau_moy
    FROM (
        SELECT p.*,
            NTILE(5) OVER (
                ORDER BY (p.temperature_min + p.temperature_max) / 2, p.nom
            ) AS etage_calcule
        FROM plantes p
        WHERE p.temperature_min IS NOT NULL AND p.temperature_max IS NOT NULL
    ) AS classement
    WHERE etage_calcule = :numero
";
$stmt = $pdo->prepare($sql);
$stmt->execute([':numero' => $numero]);
$bornes = $stmt->fetch();

$noms_etages = [
    1 => 'Zone fraîche', 2 => 'Zone tempérée', 3 => 'Zone intermédiaire',
    4 => 'Zone chaude modérée', 5 => 'Zone chaude',
];

// Valeurs de consigne "actuelles" — simulées au milieu des plages tolérées
$consigne_temp = round(($bornes['temp_min'] + $bornes['temp_max']) / 2, 1);
$consigne_hum  = round(($bornes['hum_min'] + $bornes['hum_max']) / 2);
$consigne_co2  = round(($bornes['co2_min'] + $bornes['co2_max']) / 2);
$consigne_eau  = round($bornes['eau_moy'], 2);

$message = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Non fonctionnel : rien n'est persisté, c'est une maquette.
    $message = "Consignes mises à jour (simulation — non enregistré en base).";
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
    <title>Contrôles — Étage <?= h($numero) ?> — SpaceFarm</title>
    <link rel="stylesheet" href="plantes.css">
    <style>
        .control-form { display: flex; flex-direction: column; gap: 1.5rem; max-width: 480px; margin: 2rem 0; }
        .control-group { display: flex; flex-direction: column; gap: 0.4rem; }
        .control-group label { display: flex; justify-content: space-between; font-size: 0.95rem; }
        .control-group output { font-weight: bold; }
        .control-actions { display: flex; gap: 0.75rem; margin-top: 1rem; }
        .badge-fictif {
            display: inline-block; padding: 0.2rem 0.6rem; border-radius: 6px;
            background: rgba(255,180,0,0.15); border: 1px solid rgba(255,180,0,0.4);
            font-size: 0.8rem; margin-bottom: 1rem;
        }
    </style>
</head>

<body>

<header class="header">
    <div class="header-content">
        <a href="etage.php?numero=<?= h($numero) ?>">Retour à l'étage <?= h($numero) ?></a>
        <h1>Contrôles — Étage <?= h($numero) ?> — <?= h($noms_etages[$numero]) ?></h1>
        <p class="subtitle">Ajustement des consignes environnementales.</p>
    </div>
</header>

<main class="container">

    <span class="badge-fictif">⚠️ Maquette — les réglages ne sont pas encore enregistrés en base</span>

    <?php if ($message): ?>
        <div class="stat"><span><?= h($message) ?></span></div>
    <?php endif; ?>

    <form class="control-form" method="POST">

        <div class="control-group">
            <label for="temp">
                Température cible
                <output id="temp-val"><?= h($consigne_temp) ?> °C</output>
            </label>
            <input type="range" id="temp" name="temperature"
                   min="<?= h($bornes['temp_min']) ?>" max="<?= h($bornes['temp_max']) ?>" step="0.5"
                   value="<?= h($consigne_temp) ?>"
                   oninput="document.getElementById('temp-val').textContent = this.value + ' °C'">
        </div>

        <div class="control-group">
            <label for="hum">
                Humidité cible
                <output id="hum-val"><?= h($consigne_hum) ?> %</output>
            </label>
            <input type="range" id="hum" name="humidite"
                   min="<?= h($bornes['hum_min']) ?>" max="<?= h($bornes['hum_max']) ?>" step="1"
                   value="<?= h($consigne_hum) ?>"
                   oninput="document.getElementById('hum-val').textContent = this.value + ' %'">
        </div>

        <div class="control-group">
            <label for="eau">
                Eau (L / jour / m²)
                <output id="eau-val"><?= h($consigne_eau) ?> L</output>
            </label>
            <input type="range" id="eau" name="eau"
                   min="0" max="6" step="0.1"
                   value="<?= h($consigne_eau) ?>"
                   oninput="document.getElementById('eau-val').textContent = this.value + ' L'">
        </div>

        <div class="control-group">
            <label for="co2">
                CO₂ cible
                <output id="co2-val"><?= h($consigne_co2) ?> ppm</output>
            </label>
            <input type="range" id="co2" name="co2"
                   min="<?= h($bornes['co2_min']) ?>" max="<?= h($bornes['co2_max']) ?>" step="10"
                   value="<?= h($consigne_co2) ?>"
                   oninput="document.getElementById('co2-val').textContent = this.value + ' ppm'">
        </div>

        <div class="control-actions">
            <button type="submit">Appliquer</button>
            <a href="etage.php?numero=<?= h($numero) ?>">Annuler</a>
        </div>

    </form>

</main>

</body>
</html>