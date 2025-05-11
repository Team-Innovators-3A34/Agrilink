**# Projet Agrilink**
Gestion complète d'une exploitation agricole sous Symfony 6.4, intégrant 6 modules de gestion (utilisateurs, ressources, points de recyclage, événements, posts, réclamations) ainsi qu'une multitude d'API et de modèles d'IA.

---

## Table des matières

1. [Description du projet](#description-du-projet)
2. [Technologies & Topics](#technologies--topics)
3. [Installation](#installation)
4. [Configuration](#configuration)
5. [Modules de gestion](#modules-de-gestion)

   * [Utilisateurs](#utilisateurs)
   * [Ressources](#ressources)
   * [Points de recyclage](#points-de-recyclage)
   * [Événements](#événements)
   * [Posts](#posts)
   * [Réclamations](#réclamations)
6. [APIs & Modèles d'IA](#apis--modèles-dia)
7. [Utilisation](#utilisation)
8. [Contribution](#contribution)
9. [Licence](#licence)

---

## Description du projet

AgriGestion est une application web développée en Symfony 6.4, conçue pour optimiser la gestion d'une exploitation agricole. Elle centralise les processus de gestion des utilisateurs, des ressources (matériel, semences, engrais), des points de recyclage, des événements (fêtes de la moisson, ateliers), de la publication de contenus (posts) et des réclamations.

Chaque module dispose d'une API RESTful dédiée, enrichie par des modèles d'intelligence artificielle pour l'analyse et la recommandation (par exemple : classification d'image de récolte, prédiction de rendement) citeturn0file0.

---

## Technologies & Topics

* **Backend** : Symfony 6.4, PHP 8.1+
* **Base de données** : MySQL / MariaDB
* **API** : REST, JSON
* **IA** : intégration de modèles TensorFlow & PyTorch via API internes
* **Front-end** : Twig, Bootstrap
* **Outils** : Composer, Symfony CLI, Docker (optionnel)

**Topics GitHub** : symfony, agriculture, api, ai, recyclage, gestion citeturn0file1.

---

## Installation

1. **Cloner le dépôt** :

   ```bash
   git clone https://github.com/votre-organisation/agrilink.git  
   cd agrilink  
   ```
2. **Installer les dépendances PHP** :

   ```bash
   composer install  
   ```
3. **Installer les dépendances front-end** (si nécessaire) :

   ```bash
   npm install  # ou yarn install  
   ```
4. **Préparer l'environnement** :

   * Dupliquer le fichier `.env` en `.env.local`
   * Ajuster les variables :

     ```dotenv
     DATABASE_URL="mysql://user:pass@127.0.0.1:3306/agri_db"  
     AI_API_KEY="votre_cle_api_ia"  
     ```

---

## Configuration

* Démarrer le serveur local Symfony :

  ```bash
  symfony server:start  
  ```
* Accéder à l'application : `http://127.0.0.1:8000`
* Importer la base de données :

  ```bash
  php bin/console doctrine:migrations:migrate  
  ```

---

## Modules de gestion

Chaque module expose des endpoints CRUD et des fonctionnalités spécifiques :

### Utilisateurs

* Inscription, authentification JWT
* Gestion des rôles (agriculteur, administrateur)

### Ressources

* Création & suivi du matériel agricole, semences, engrais
* Statistiques d'utilisation et réapprovisionnement

### Points de recyclage

* Géolocalisation des points sur carte OpenStreetMap
* Ajout / modification / suppression de points

### Événements

* Planification et gestion d'événements agricoles
* Envoi de notifications aux participants

### Posts

* Publication d'articles et conseils agricoles
* Modération et commentaires

### Réclamations

* Soumission et suivi des réclamations des utilisateurs
* Statistiques et rapports

---

## APIs & Modèles d'IA

* **Endpoints REST** sécurisés par JWT
* **IA d'analyse d'images** : classification des cultures via TensorFlow Serving
* **IA prédictive** : estimation de rendement basée sur historique et conditions météo
* **Chatbot agricole** : réponse aux questions des utilisateurs via GPT-API interne

---

## Utilisation

1. Se connecter via `/login` (JWT)
2. Appeler les endpoints API avec un outil comme Postman ou cURL
3. Consulter le front-office pour visualiser les données

---

## Contribution

Vos contributions sont les bienvenues !

1. Fork ce dépôt
2. Créez une branche feature :

   ```bash
   git checkout -b feature/ma-fonctionnalite  
   ```
3. Committez vos changements :

   ```bash
   git commit -m "Ajout de X fonctionnalité"  
   ```
4. Poussez votre branche :

   ```bash
   git push origin feature/ma-fonctionnalite  
   ```
5. Ouvrez une Pull Request

---

## Licence

Ce projet est sous licence MIT. Voir le fichier [LICENSE](LICENSE) pour plus de détails.
