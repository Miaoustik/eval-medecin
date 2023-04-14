# Readme
**Référence : GDDWWMECFENTRIII3A_296117_20230216122200**

Adresse en ligne: https://sandrine-coupart-diet.herokuapp.com/

## Deploiement en local: 

### Prérequis:
- php 8.1 avec les extensions (ainsi que l'extension pdo correspondant à votre SGBDR.)
- un SGBDR
- composer
- Node.js
- symfony cli  https://symfony.com/download
    
### Instructions: 
- cloner le projet de github
```bash
git clone https://github.com/Miaoustik/eval-medecin
```
- créer un fichier .env et définir les variables APP_SECRET, APP_ENV,  DATABASE_URL, MESSENGER_TRANSPORT_DSN et CORS_ALLOW_ORIGIN.
- (CORS pour autoriser votre localhost : exemple '^https?://(localhost|127\.0\.0\.1)(:[0-9]+)?$')
- (MESSENGER_TRANSPORT_DSN exemple : 'doctrine://default?auto_setup=0')
- se rendre à la racine du projet et faire un composer install et un npm install.
```bash
cd project_dir
composer install
npm install
```
- faire symfony console doctrine:database create si vous n'avez pas encore créer votre base de donnée.

```bash
symfony console doctrine:database:create
```

- faire un symfony console doctrine:migrations:migrate pour préparer votre base de données à recevoir les données.
```bash
symfony console doctrine:migrations:migrate
```
- tester le serveur en faisant symfony serve

```bash
symfony serve
```

### Un admin peut être créé avec :
- symfony console app:add-admin
- une adresse mail et un mot de passe en clair vous sera demandé.

```bash
symfony console app:add-admin
```

## Deploiement en ligne: 

### prérequis : 
- un compte heroku avec dyno eco plan minimum et un addon postgres pour la base de données.
- avoir git.
- installer heroku CLI sur votre machine

### installation: 
- cloner le projet.
```bash
git clone https://github.com/Miaoustik/eval-medecin
```
- sur rendre dans le dossier de votre projet.
```bash
cd project_dir
```
- faire un heroku login pour vous connecter à votre compte. 
```bash
heroku login
```
- heroku create pour créer votre appli dans heroku.
```bash
heroku create
```
- déclarer toute les variables d'environnement dans heroku config:set ou dans la page settings de votre app sur heroku.
- Ajouter les buildpacks Node.js puis php.
- faire git push heroku main pour build votre app locale dans heroku.
```bash
git push heroku main
```

- heroku ps:scale web=1 pour démarrer une instance de votre application.
```bash
heroku ps:scale web=1
```
- lancer les migrations avec console doctrine:migrations:migrate pour préparer votre base de donnée.
```bash
symfony console doctrine:migrations:migrate
```
- heroku open pour tester votre app dans le navigateur.
```bash
heroku open
```


**Pour la création d'admin, la commande console app:add-admin a été ajouté à l'application. Un email et un mot de passe en clair qui sera encrypté par la commande vous seront demandés par la suite.**
```bash
symfony console app:add-admin
```

**ou une fois sur heroku**
```bash
console app:add-admin
```

