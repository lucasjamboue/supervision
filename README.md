# supervision

Mini interface de supervision en **PHP** avec un tableau central des machines.

## Lancer en local

```bash
php -S 127.0.0.1:8000
```

Puis ouvrir <http://127.0.0.1:8000/index.php>.

## Fonctionnalités

- tableau des machines supervisées,
- indicateur d'état (**En ligne / Hors ligne**),
- jauge simple du niveau de lubrifiant,
- simulation d'un payload MQTT en JSON pour mettre à jour les machines.

Exemple de payload :

```json
{"machines":[{"id":"puma-2600y","en_ligne":true,"lubrifiant":55}]}
```
