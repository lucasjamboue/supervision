<?php
$brokerConfig = [
    'host' => '127.0.0.1',
    'port' => 1883,
    'topic' => 'machines/etat',
    'client_id' => 'supervision-web',
];

$machineData = [
    'machine' => 'cuve',
    'etat' => 'En attente de données MQTT',
    'temperature' => '--',
    'pression' => '--',
    'updated_at' => date('Y-m-d H:i:s'),
];

$payloadError = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payload = trim($_POST['payload'] ?? '');

    if ($payload !== '') {
        $decodedPayload = json_decode($payload, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($decodedPayload)) {
            $machineData = array_merge($machineData, $decodedPayload);
            $machineData['updated_at'] = date('Y-m-d H:i:s');
        } else {
            $payloadError = 'Le JSON fourni est invalide.';
        }
    }
}

/*
 * Étape suivante pour brancher un broker MQTT réel :
 * 1) Installer un client MQTT PHP (ex: composer require php-mqtt/client)
 * 2) Se connecter au broker avec $brokerConfig
 * 3) Souscrire au topic $brokerConfig['topic']
 * 4) Mettre à jour $machineData avec le payload reçu
 */
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
            <p>Application prête à recevoir des données depuis un broker MQTT.</p>

            <h3>Configuration broker</h3>
            <ul>
                <li><strong>Hôte :</strong> <?= htmlspecialchars($brokerConfig['host']) ?></li>
                <li><strong>Port :</strong> <?= htmlspecialchars((string) $brokerConfig['port']) ?></li>
                <li><strong>Topic :</strong> <?= htmlspecialchars($brokerConfig['topic']) ?></li>
                <li><strong>Client ID :</strong> <?= htmlspecialchars($brokerConfig['client_id']) ?></li>
            </ul>

            <h3>Dernières données machine</h3>
            <ul>
                <li><strong>Machine :</strong> <?= htmlspecialchars((string) $machineData['machine']) ?></li>
                <li><strong>État :</strong> <?= htmlspecialchars((string) $machineData['etat']) ?></li>
                <li><strong>Température :</strong> <?= htmlspecialchars((string) $machineData['temperature']) ?></li>
                <li><strong>Pression :</strong> <?= htmlspecialchars((string) $machineData['pression']) ?></li>
                <li><strong>Mise à jour :</strong> <?= htmlspecialchars((string) $machineData['updated_at']) ?></li>
            </ul>

        </section>
    </main>

    <hr>

    <footer>
        <p>&copy; 2026 - Supervision prod</p>
    </footer>

</body>
</html>
