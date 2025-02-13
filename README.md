# IntraConnect - Outil de Collaboration en Entreprise

## Description
IntraConnect est une plateforme web développée en JavaScript, HTML, CSS et PHP permettant aux travailleurs d'une structure de collaborer efficacement. Cet outil offre plusieurs fonctionnalités facilitant le partage de connaissances, la gestion de formations et le suivi des projets.

## Fonctionnalités
- **Publication d'annonces de formation** : Les collaborateurs peuvent publier et consulter des annonces de formation.
- **Inscription aux formations** : Possibilité de s'inscrire aux formations proposées.
- **Gestion de projets** : Les utilisateurs peuvent renseigner les projets sur lesquels ils travaillent.
- **Partage de connaissances** : Les collaborateurs peuvent publier et consulter des ressources et articles.
- **Interface utilisateur intuitive** : Un design ergonomique pour une utilisation fluide.

## Prérequis
Pour exécuter cette application, vous devez disposer des éléments suivants :
- Un serveur web (Apache, Nginx, etc.)
- PHP installé (version 7.4 ou supérieure recommandée)
- Une base de données MySQL ou MariaDB

### Installation de l'environnement (Linux)
```sh
sudo apt update
sudo apt install apache2 php libapache2-mod-php mysql-server php-mysql
```

### Installation de l'environnement (Windows)
Vous pouvez utiliser XAMPP ou WAMP pour exécuter l'application.

## Installation et exécution
1. Clonez le dépôt :
```sh
git clone https://github.com/votre-utilisateur/intraconnect.git
cd intraconnect
```

2. Configurez la base de données en important le fichier `database.sql` dans MySQL.

3. Placez les fichiers du projet dans le répertoire de votre serveur web.

4. Démarrez Apache et MySQL, puis accédez à l'application via :
```
http://localhost/intraconnect
```


## Améliorations futures
- Implémentation d'un chat interne pour les collaborateurs.
- Intégration de notifications en temps réel.
- Ajout d'un système de gestion des droits d'accès.
- Amélioration du design et de l'expérience utilisateur.

## Contribution
Les contributions sont les bienvenues ! N'hésitez pas à proposer des améliorations via une pull request ou à signaler des bugs via les issues.

## Licence
Ce projet est sous licence MIT. Voir le fichier `LICENSE` pour plus d'informations.

