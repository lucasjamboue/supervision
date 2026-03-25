<?php
$brokerConfig = [
    'host' => '127.0.0.1',
    'port' => 1883,
    'topic' => 'machines/etat',
    'client_id' => 'supervision-web',
];

$machines = [
    ['id' => 'cmzta20m-1', 'nom' => 'cmzta20m #1', 'en_ligne' => true, 'lubrifiant' => 82],
    ['id' => 'cmzta20m-2', 'nom' => 'cmzta20m #2', 'en_ligne' => false, 'lubrifiant' => 35],
    ['id' => 'transmab-350', 'nom' => 'transmab 350', 'en_ligne' => true, 'lubrifiant' => 71],
    ['id' => 'arrow500', 'nom' => 'arrow500', 'en_ligne' => true, 'lubrifiant' => 64],
    ['id' => 'doosaw-dnm-5700', 'nom' => 'doosaw dnm 5700', 'en_ligne' => false, 'lubrifiant' => 22],
    ['id' => 'doosaw-dnm-350-5ax', 'nom' => 'doosaw dnm 350/5ax', 'en_ligne' => true, 'lubrifiant' => 58],
    ['id' => 'cmzta20y', 'nom' => 'cmzta20y', 'en_ligne' => true, 'lubrifiant' => 77],
    ['id' => 'cmztc20ty', 'nom' => 'cmztc20ty', 'en_ligne' => true, 'lubrifiant' => 69],
    ['id' => 'mori-seiki-nh4000-dcg', 'nom' => 'mori seiki nh4000 dcg', 'en_ligne' => false, 'lubrifiant' => 40],
    ['id' => 'puma-2600y', 'nom' => 'puma 2600y', 'en_ligne' => true, 'lubrifiant' => 90],
    ['id' => 'svm4100', 'nom' => 'svm4100', 'en_ligne' => false, 'lubrifiant' => 28],
    ['id' => 'seiki', 'nom' => 'seiki', 'en_ligne' => true, 'lubrifiant' => 67],
    ['id' => 'nhx4000', 'nom' => 'nhx4000', 'en_ligne' => true, 'lubrifiant' => 73],
];

$payloadError = null;
$payloadApplied = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payload = trim($_POST['payload'] ?? '');

    if ($payload !== '') {
        $decodedPayload = json_decode($payload, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($decodedPayload)) {
            $machinesById = [];
            foreach ($machines as $machine) {
                $machinesById[$machine['id']] = $machine;
            }

            $updates = $decodedPayload['machines'] ?? [];
            if (is_array($updates)) {
                foreach ($updates as $update) {
                    if (!is_array($update) || empty($update['id']) || !isset($machinesById[$update['id']])) {
                        continue;
                    }

                    if (isset($update['en_ligne'])) {
                        $machinesById[$update['id']]['en_ligne'] = (bool) $update['en_ligne'];
                    }

                    if (isset($update['lubrifiant']) && is_numeric($update['lubrifiant'])) {
                        $machinesById[$update['id']]['lubrifiant'] = max(0, min(100, (int) $update['lubrifiant']));
                    }
                }

                $machines = array_values($machinesById);
                $payloadApplied = true;
            }
        } else {
            $payloadError = 'Le JSON fourni est invalide.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Supervision Machines</title>
    <link rel="stylesheet" href="style/style.css">
</head>
<body>

<header>
    <div>
        <img src="photo/logo-uimm.png" alt="Logo UIMM" width="40" style="vertical-align: middle;">
        <strong>Supervision</strong>
    </div>

    <nav>
        <a href="index.php">Accueil</a> |
        <a href="#">Machines</a> |
        <a href="#">Alertes</a> |
        <a href="#">Rapports</a>
    </nav>

    <div>
        <a href="#">Connexion</a>
    </div>
</header>

<hr>

<main>
    <aside>
        <h3>Graphiques</h3>
        <hr>
        <ul>
            <li><a href="#">Tableau de bord</a></li>
            <li><a href="#">Circuit pneumatique</a></li>
            <li><a href="#">Electricité</a></li>
            <li><a href="#">Assignation</a></li>
        </ul>
    </aside>

    <section>
        <h2>Supervision des machines (PHP)</h2>
        <p>Tableau central des machines supervisées avec état en ligne et jauge simple de lubrifiant.</p>

        <h3>Configuration broker MQTT</h3>
        <ul>
            <li><strong>Hôte :</strong> <?= htmlspecialchars($brokerConfig['host']) ?></li>
            <li><strong>Port :</strong> <?= htmlspecialchars((string) $brokerConfig['port']) ?></li>
            <li><strong>Topic :</strong> <?= htmlspecialchars($brokerConfig['topic']) ?></li>
            <li><strong>Client ID :</strong> <?= htmlspecialchars($brokerConfig['client_id']) ?></li>
        </ul>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Machine</th>
                        <th>Statut</th>
                        <th>Lubrifiant</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($machines as $machine): ?>
                        <tr>
                            <td><?= htmlspecialchars($machine['nom']) ?></td>
                            <td>
                                <?php if ($machine['en_ligne']): ?>
                                    <span class="status online">En ligne</span>
                                <?php else: ?>
                                    <span class="status offline">Hors ligne</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="gauge">
                                    <div class="gauge-fill" style="width: <?= (int) $machine['lubrifiant'] ?>%"></div>
                                </div>
                                <small><?= (int) $machine['lubrifiant'] ?>%</small>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <h3>Simulation payload MQTT</h3>
        <p>Format attendu: <code>{"machines":[{"id":"puma-2600y","en_ligne":true,"lubrifiant":55}]}</code></p>
        <?php if ($payloadError !== null): ?>
            <p style="color: #b00020;"><?= htmlspecialchars($payloadError) ?></p>
        <?php elseif ($payloadApplied): ?>
            <p style="color: #0a7b33;">Payload appliqué avec succès.</p>
        <?php endif; ?>

        <form method="post" action="index.php">
            <textarea name="payload" rows="6" cols="80" placeholder='{"machines":[{"id":"cmzta20m-1","en_ligne":true,"lubrifiant":78}]}'></textarea>
            <br>
            <button type="submit">Appliquer le payload</button>
        </form>
    </section>
</main>

<hr>

<footer>
    <p>&copy; 2026 - Supervision prod</p>
</footer>

</body>
</html>
