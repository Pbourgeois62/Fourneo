# Fourneo

<img src="logo.png" alt="Logo Fourneo" width="150" height="150"> 

Fourneo est une **application web moderne** conçue pour être le compagnon numérique des **artisans boulangers**. Elle vise à simplifier, optimiser et moderniser la gestion quotidienne de la boulangerie, de la production à la vente, en passant par la gestion.

---

## Fonctionnalités principales

Fourneo vous aide à structurer et à gérer efficacement votre activité grâce aux modules suivants :

* **Gestion des produits :** Enregistrez, cataloguez et organisez facilement tous vos produits de boulangerie.
* **Création d'événements de vente :** Planifiez et suivez vos ventes spéciales, marchés ou événements directement depuis l'application.
* **Gestion des livraisons et des documents associés**: Photographiez facilement les étiquettes produits ou les bons de livraison. Associez-les immédiatement à un produit ou enregistrez-les pour les traiter plus tard.
* **WIP !** Restez à l'écoute, de nouvelles fonctionnalités sont en cours de développement pour enrichir l'application.

---

## Environnement technique

Fourneo est bâti sur une stack technologique robuste et moderne, garantissant performance et maintenabilité :

* **Backend :** [Symfony 7.3](https://symfony.com/) - Un framework PHP puissant pour une logique métier solide et sécurisée.
* **Frontend :** [Tailwind CSS](https://tailwindcss.com/) & [Flowbite](https://flowbite.com/) - Pour une interface utilisateur réactive, moderne et facilement personnalisable.
* **Environnement de développement :** [Docker](https://www.docker.com/) - Assure un environnement de développement cohérent et isolé pour tous les collaborateurs.
* **Langage :** [PHP 8.2](https://www.php.net/) - Intégré directement dans l'environnement Docker pour des performances optimales.

---

## Installation et démarrage

Pour faire tourner Fourneo en local, suivez ces étapes simples.

### Prérequis

Avoir [Docker](https://www.docker.com/get-started) node.js et npm d'installés sur votre machine.

### Commandes utiles

Une fois les prérequis satisfaits:

* Executer le script **docker-start** pour monter les images
* Afin d'exécuter les migrations, exécuter:
```
php bin/console d:m:m
```
* Executer le script **tailwind** pour compiler les assets tailwind CSS
* Pour stopper les containers et les supprimer, executer le script **docker-stop**
* Pour travailler directement dans l'image php, executer **frankenphp**



