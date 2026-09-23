<?php
$host = 'localhost';
$dbname = 'plantes_spacefarm';
$user = 'root';
$password = '';

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $user,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    die("Erreur de connexion : " . htmlspecialchars($e->getMessage()));
}

$plantes = $pdo->query("SELECT * FROM plantes ORDER BY nom ASC")->fetchAll();

function h($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

/*
 * Répartition de l'étage.
 * Une plante est placée dans une zone selon ses propriétés.
 */
$zones = [
    'zone-a' => [
        'title' => 'Zone A',
        'subtitle' => 'Croissance rapide',
        'condition' => '≤ 45 jours',
        'plants' => []
    ],
    'zone-b' => [
        'title' => 'Zone B',
        'subtitle' => 'Cultures chaudes',
        'condition' => '≥ 18 °C',
        'plants' => []
    ],
    'zone-c' => [
        'title' => 'Zone C',
        'subtitle' => 'Cycles longs',
        'condition' => '> 45 jours',
        'plants' => []
    ]
];

foreach ($plantes as $plante) {
    $jours = (int)$plante['temps_croissance_jours'];
    $minTemp = (float)$plante['temperature_min'];
    $maxTemp = (float)$plante['temperature_max'];

    if ($jours <= 45 && $maxTemp <= 24) {
        $zones['zone-a']['plants'][] = $plante;
    } elseif ($minTemp >= 18 || $maxTemp >= 28) {
        $zones['zone-b']['plants'][] = $plante;
    } elseif ($jours > 45) {
        $zones['zone-c']['plants'][] = $plante;
    }
}

function plantIcon(string $categorie): string {
    $categorie = strtolower($categorie);

    if (str_contains($categorie, 'fruit')) return '🍅';
    if (str_contains($categorie, 'racine') || str_contains($categorie, 'tubercule')) return '🥕';
    if (str_contains($categorie, 'céréale') || str_contains($categorie, 'cereale')) return '🌾';
    if (str_contains($categorie, 'herbe')) return '🌿';
    return '🥬';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Horizon TechAgri — Étage 1</title>

<style>
* { box-sizing: border-box; }

body {
    margin: 0;
    font-family: Arial, Helvetica, sans-serif;
    background: #060b13;
    color: #e8f0fa;
}

.header {
    height: 82px;
    background: #0c1522;
    border-bottom: 1px solid #26374c;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 35px;
}

.brand {
    font-size: 14px;
    letter-spacing: 2px;
    color: #86b6ff;
    font-weight: bold;
}

.header h1 {
    margin: 3px 0 0;
    font-size: 25px;
}

.floor-counter {
    padding: 10px 17px;
    border: 1px solid #395574;
    border-radius: 10px;
    background: #101e2f;
    text-align: center;
}

.floor-counter strong {
    display: block;
    font-size: 20px;
}

.floor-counter span {
    color: #8295ad;
    font-size: 11px;
}

.page {
    max-width: 1350px;
    margin: auto;
    padding: 25px;
}

.intro {
    display: flex;
    justify-content: space-between;
    gap: 20px;
    align-items: center;
    margin-bottom: 20px;
}

.intro p {
    color: #8fa1b8;
    margin: 6px 0 0;
}

.legend {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
    color: #9eafc4;
    font-size: 12px;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 7px;
}

.dot {
    width: 11px;
    height: 11px;
    border-radius: 50%;
    display: inline-block;
}

.dot-a { background: #6e9de5; }
.dot-b { background: #e59c62; }
.dot-c { background: #72b89b; }

.station {
    position: relative;
    background: #0d1724;
    border: 2px solid #30445d;
    border-radius: 22px;
    min-height: 680px;
    padding: 25px;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(0,0,0,.35);
}

/* Lignes du sol */
.station::before {
    content: "";
    position: absolute;
    inset: 0;
    opacity: .18;
    background-image:
        linear-gradient(#53708f 1px, transparent 1px),
        linear-gradient(90deg, #53708f 1px, transparent 1px);
    background-size: 45px 45px;
    pointer-events: none;
}

.wall {
    position: relative;
    z-index: 1;
    border: 2px solid #4b6380;
    border-radius: 15px;
    height: 100%;
    min-height: 625px;
    padding: 22px;
    background: rgba(8, 15, 25, .65);
}

.room {
    position: absolute;
    border: 2px solid;
    border-radius: 15px;
    background: rgba(15, 29, 45, .95);
    padding: 16px;
    overflow: auto;
}

.room-a {
    left: 2.5%;
    top: 4%;
    width: 44%;
    height: 42%;
    border-color: #416da8;
}

.room-b {
    right: 2.5%;
    top: 4%;
    width: 44%;
    height: 42%;
    border-color: #a66b3d;
}

.room-c {
    left: 2.5%;
    bottom: 4%;
    width: 44%;
    height: 42%;
    border-color: #477e68;
}

.control-room {
    right: 2.5%;
    bottom: 4%;
    width: 44%;
    height: 42%;
    border-color: #59677b;
    background: rgba(13, 22, 35, .98);
}

.room-title {
    display: flex;
    justify-content: space-between;
    gap: 10px;
    align-items: flex-start;
    margin-bottom: 15px;
}

.room-title h2 {
    margin: 0;
    font-size: 21px;
}

.room-title p {
    margin: 5px 0 0;
    color: #8295ac;
    font-size: 12px;
}

.room-number {
    font-size: 11px;
    color: #8298b2;
    border: 1px solid #354b66;
    padding: 5px 8px;
    border-radius: 7px;
}

.plants {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 9px;
}

.plant {
    padding: 11px;
    border-radius: 10px;
    background: #101e2e;
    border: 1px solid #263c55;
    cursor: default;
}

.plant:hover {
    border-color: #7091b7;
}

.plant-name {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: bold;
    font-size: 14px;
}

.plant-category {
    margin-top: 5px;
    color: #7f93ab;
    font-size: 10px;
}

.plant-data {
    display: flex;
    gap: 10px;
    margin-top: 8px;
    color: #aab8ca;
    font-size: 10px;
}

.empty {
    color: #6f8198;
    font-size: 12px;
    padding: 15px 0;
}

.control-content {
    display: grid;
    gap: 10px;
}

.control-box {
    background: #0a1420;
    border: 1px solid #26384e;
    border-radius: 10px;
    padding: 13px;
}

.control-box strong {
    display: block;
    margin-bottom: 5px;
}

.control-box span {
    color: #8092a8;
    font-size: 12px;
}

.corridor {
    position: absolute;
    z-index: 2;
    left: 46%;
    right: 46%;
    top: 42%;
    bottom: 42%;
    background: #172536;
    border-left: 1px solid #405873;
    border-right: 1px solid #405873;
    display: flex;
    align-items: center;
    justify-content: center;
}

.corridor span {
    writing-mode: vertical-rl;
    transform: rotate(180deg);
    color: #687e98;
    font-size: 10px;
    letter-spacing: 2px;
}

.elevator {
    position: absolute;
    z-index: 4;
    left: 47.5%;
    top: 46%;
    width: 5%;
    height: 8%;
    min-width: 45px;
    background: #182a3d;
    border: 2px solid #6c8099;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #b8c6d6;
    font-size: 10px;
}

.bottom-info {
    margin-top: 18px;
    display: flex;
    justify-content: space-between;
    gap: 15px;
    color: #71839b;
    font-size: 12px;
}

@media (max-width: 900px) {
    .station {
        min-height: auto;
    }

    .wall {
        min-height: auto;
        display: grid;
        grid-template-columns: 1fr;
        gap: 15px;
        padding: 15px;
    }

    .room, .control-room {
        position: relative;
        inset: auto;
        width: 100%;
        height: auto;
        min-height: 190px;
    }

    .corridor, .elevator {
        display: none;
    }

    .intro {
        flex-direction: column;
        align-items: flex-start;
    }
}

@media (max-width: 550px) {
    .header {
        padding: 0 15px;
    }

    .page {
        padding: 15px;
    }

    .plants {
        grid-template-columns: 1fr;
    }
}
</style>
</head>

<body>

<header class="header">
    <div>
        <div class="brand">HORIZON TECHAGRI</div>
        <h1>Ferme spatiale — Plan de l'étage</h1>
    </div>

    <div class="floor-counter">
        <strong>01 / 05</strong>
        <span>ÉTAGE 1</span>
    </div>
</header>

<main class="page">

    <div class="intro">
        <div>
            <h2>Plan de culture</h2>
            <p>
                Les zones sont organisées selon les propriétés des plantes
                enregistrées dans la base de données.
            </p>
        </div>

        <div class="legend">
            <div class="legend-item"><i class="dot dot-a"></i> Croissance rapide</div>
            <div class="legend-item"><i class="dot dot-b"></i> Cultures chaudes</div>
            <div class="legend-item"><i class="dot dot-c"></i> Cycles longs</div>
        </div>
    </div>

    <section class="station">

        <div class="wall">

            <!-- ZONE A -->
            <section class="room room-a">
                <div class="room-title">
                    <div>
                        <h2>🌱 Zone A</h2>
                        <p>Croissance rapide · ≤ 45 jours</p>
                    </div>
                    <span class="room-number">
                        <?= count($zones['zone-a']['plants']) ?> espèces
                    </span>
                </div>

                <div class="plants">
                    <?php if (count($zones['zone-a']['plants'])): ?>
                        <?php foreach ($zones['zone-a']['plants'] as $plante): ?>
                            <div class="plant">
                                <div class="plant-name">
                                    <?= plantIcon($plante['categorie']) ?>
                                    <?= h($plante['nom']) ?>
                                </div>

                                <div class="plant-category">
                                    <?= h($plante['categorie']) ?>
                                </div>

                                <div class="plant-data">
                                    <span>⏱ <?= h($plante['temps_croissance_jours']) ?> j</span>
                                    <span>🌡 <?= h($plante['temperature_min']) ?>–<?= h($plante['temperature_max']) ?>°</span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty">Aucune plante dans cette zone.</div>
                    <?php endif; ?>
                </div>
            </section>

            <!-- ZONE B -->
            <section class="room room-b">
                <div class="room-title">
                    <div>
                        <h2>☀️ Zone B</h2>
                        <p>Cultures chaudes · température élevée</p>
                    </div>
                    <span class="room-number">
                        <?= count($zones['zone-b']['plants']) ?> espèces
                    </span>
                </div>

                <div class="plants">
                    <?php if (count($zones['zone-b']['plants'])): ?>
                        <?php foreach ($zones['zone-b']['plants'] as $plante): ?>
                            <div class="plant">
                                <div class="plant-name">
                                    <?= plantIcon($plante['categorie']) ?>
                                    <?= h($plante['nom']) ?>
                                </div>

                                <div class="plant-category">
                                    <?= h($plante['categorie']) ?>
                                </div>

                                <div class="plant-data">
                                    <span>⏱ <?= h($plante['temps_croissance_jours']) ?> j</span>
                                    <span>🌡 <?= h($plante['temperature_min']) ?>–<?= h($plante['temperature_max']) ?>°</span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty">Aucune plante dans cette zone.</div>
                    <?php endif; ?>
                </div>
            </section>

            <!-- ZONE C -->
            <section class="room room-c">
                <div class="room-title">
                    <div>
                        <h2>🌿 Zone C</h2>
                        <p>Cycles longs · croissance &gt; 45 jours</p>
                    </div>
                    <span class="room-number">
                        <?= count($zones['zone-c']['plants']) ?> espèces
                    </span>
                </div>

                <div class="plants">
                    <?php if (count($zones['zone-c']['plants'])): ?>
                        <?php foreach ($zones['zone-c']['plants'] as $plante): ?>
                            <div class="plant">
                                <div class="plant-name">
                                    <?= plantIcon($plante['categorie']) ?>
                                    <?= h($plante['nom']) ?>
                                </div>

                                <div class="plant-category">
                                    <?= h($plante['categorie']) ?>
                                </div>

                                <div class="plant-data">
                                    <span>⏱ <?= h($plante['temps_croissance_jours']) ?> j</span>
                                    <span>pH <?= h($plante['ph_min']) ?>–<?= h($plante['ph_max']) ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty">Aucune plante dans cette zone.</div>
                    <?php endif; ?>
                </div>
            </section>

            <!-- SALLE TECHNIQUE -->
            <section class="room control-room">
                <div class="room-title">
                    <div>
                        <h2>⚙️ Contrôle de l'étage</h2>
                        <p>Système de gestion de la ferme</p>
                    </div>
                    <span class="room-number">SYS-01</span>
                </div>

                <div class="control-content">
                    <div class="control-box">
                        <strong>💧 Gestion de l'eau</strong>
                        <span>Distribution automatique par zone</span>
                    </div>

                    <div class="control-box">
                        <strong>🌡 Contrôle climatique</strong>
                        <span>Température et humidité contrôlées</span>
                    </div>

                    <div class="control-box">
                        <strong>🧪 Atmosphère</strong>
                        <span>Surveillance du CO₂</span>
                    </div>

                    <div class="control-box">
                        <strong>📡 Réseau</strong>
                        <span>Connexion au système central du vaisseau</span>
                    </div>
                </div>
            </section>

        </div>

        <div class="corridor">
            <span>COULOIR CENTRAL</span>
        </div>

        <div class="elevator">ASC.</div>

    </section>

    <div class="bottom-info">
        <span>Horizon TechAgri · Module agricole · Niveau 01</span>
        <span><?= count($plantes) ?> plante(s) disponibles dans la base</span>
    </div>

</main>

</body>
</html>