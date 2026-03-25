# supervision

Mini interface de supervision convertie en **PHP**.

## Lancer en local

```bash
php -S 127.0.0.1:8000
```

Puis ouvrir <http://127.0.0.1:8000/index.php>.

## Préparation MQTT

La page `index.php` contient :
- une configuration de broker (`host`, `port`, `topic`, `client_id`),
- une zone d'affichage des dernières données machine,
- un formulaire de simulation pour tester un payload JSON en attendant la connexion MQTT réelle.
