Guide d'installation

Pour mettre ce projet en marche sur son ordinateur, il faut cloner le repository en local et initialiser une base de données pour le lier au projet

Ce guide utilise une base de données MySQL en Docker
Si vous ne disposez pas de Docker ou Symfony, il faudrait les télécharger et les installer
Docker: https://www.docker.com/get-started
Symfony: https://symfony.com/download

Clonez le repository en local avec la commande: git clone https://github.com/John-Rep/Symfony-Reseau-Social.git

Créer un container en Docker pour stocker la base de données avec cette commande:
  docker run --name symfony-reseau -p 3309:3306 -e MYSQL_DATABASE=reseau -e MYSQL_ROOT_PASSWORD=root -d mysql

Si le port 3309 n'est pas disponible, vous pouvez changer cette valeur à un port qui est disponible, et aussi la changer dans le fichier .env dans le dossier du projet
  (Il faudrait changer ça dans le fichier .env pour le projet API aussi pour connecter à la même base de données)

Une fois que le Docker container est prêt, vous pouvez run la commande suivante dans le directory du projet:
  symfony console doctrine:migrations:migrate

Si cette commande ne marche pas, vous devez possiblement installer le paquet ORM avec la commande:
  composer require orm

Cette étape ajoute toutes les tables à la base de données

Et maintenant il faut juste démarrer le serveur avec la commande:
  Symfony server:start

Si vous allez à l'URL http://localhost:8000/post, il doit afficher le Dashboard du site
Vous pouvez maintenant créer des utilisateurs, et une fois enregistré, créer des posts et des commentaires

Je vous conseille de créer un utilisateur avec l'email jeremy@icloud.com si vous voulez faire les tests unitaires dans le projet
  Les tests unitaires se font avec la commande:
    php bin/phpunit 
  Si ça ne marche pas, assurez-vous que vous disposez du paquet test:
    composer require symfony/test-pack
