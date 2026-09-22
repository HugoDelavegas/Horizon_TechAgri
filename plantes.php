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

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: #0b1220;
            color: #e8eef7;
        }

        .header {
            padding: 42px 20px 55px;
            background:
                radial-gradient(circle at 20% 20%, rgba(70, 120, 255, .20), transparent 30%),
                radial-gradient(circle at 80% 10%, rgba(60, 220, 170, .15), transparent 25%),
                #111b2e;
            border-bottom: 1px solid #26344c;
        }

        .header-content {
            max-width: 1200px;
            margin: auto;
        }

        .badge {
            display: inline-block;
            padding: 7px 12px;
            border: 1px solid #385070;
            border-radius: 999px;
            color: #a9c7ff;
            font-size: 13px;
            margin-bottom: 15px;
        }

        h1 {
            margin: 0 0 12px;
            font-size: clamp(32px, 5vw, 54px);
        }

        .subtitle {
            max-width: 700px;
            margin: 0;
            color: #aebbd0;
            font-size: 17px;
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: auto;
            padding: 28px 20px 60px;
        }

        .toolbar {
            display: grid;
            grid-template-columns: 1fr 230px auto;
            gap: 12px;
            margin-bottom: 30px;
        }

        input, select, button {
            width: 100%;
            padding: 13px 15px;
            border-radius: 10px;
            border: 1px solid #30415d;
            background: #111b2e;
            color: #fff;
            font-size: 15px;
        }

        button {
            width: auto;
            min-width: 110px;
            cursor: pointer;
            background: #274a8a;
            border-color: #3c66ad;
            font-weight: bold;
        }

        button:hover {
            background: #315da5;
        }

        .stats {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            margin-bottom: 25px;
        }

        .stat {
            background: #111b2e;
            border: 1px solid #26344c;
            border-radius: 12px;
            padding: 14px 18px;
        }

        .stat strong {
            display: block;
            font-size: 24px;
        }

        .stat span {
            color: #8e9db4;
            font-size: 13px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(330px, 1fr));
            gap: 20px;
        }

        .card {
            background: #111b2e;
            border: 1px solid #26344c;
            border-radius: 16px;
            overflow: hidden;
            transition: transform .2s, border-color .2s;
        }

        .card:hover {
            transform: translateY(-3px);
            border-color: #48678f;
        }

        .card-top {
            padding: 22px 22px 18px;
            border-bottom: 1px solid #26344c;
        }

        .category {
            display: inline-block;
            padding: 5px 9px;
            border-radius: 999px;
            background: #1b2d49;
            color: #9fc1ff;
            font-size: 12px;
            margin-bottom: 12px;
        }

        .plant-name {
            margin: 0;
            font-size: 26px;
        }

        .scientific {
            margin-top: 5px;
            color: #8999b1;
            font-style: italic;
        }

        .description {
            color: #b8c4d5;
            line-height: 1.55;
            margin: 15px 0 0;
        }

        .details {
            padding: 18px 22px 22px;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }

        .detail {
            background: #0d1728;
            border-radius: 10px;
            padding: 12px;
        }

        .detail-label {
            display: block;
            color: #7f8fa7;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .5px;
            margin-bottom: 5px;
        }

        .detail-value {
            font-weight: bold;
            font-size: 14px;
        }

        .empty {
            text-align: center;
            padding: 60px 20px;
            background: #111b2e;
            border: 1px solid #26344c;
            border-radius: 16px;
            color: #9aa9bd;
        }

        .footer {
            text-align: center;
            color: #66758c;
            font-size: 13px;
            margin-top: 40px;
        }

        @media (max-width: 760px) {
            .toolbar {
                grid-template-columns: 1fr;
            }

            button {
                width: 100%;
            }

            .grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
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
