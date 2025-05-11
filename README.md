# Projet Agrilink Symfony

Gestion web avancée pour l'agriculture via Symfony 6.4, avec 6 modules de gestion, de nombreux métiers API et une intégration étendue de modèles IA.

---

## Table des matières

1. [Description du projet](#description-du-projet)
2. [Technologies & Topics](#technologies--topics)
3. [Installation](#installation)
4. [Configuration](#configuration)
5. [Fonctionnalités Métiers & APIs](#fonctionnalités-métiers--apis)
6. [Modules de gestion](#modules-de-gestion)
7. [IA & Modèles](#ia--modèles)
8. [Utilisation](#utilisation)
9. [Contribution](#contribution)
10. [Licence](#licence)

---

## Description du projet

Agrilink Symfony est une plateforme web développée en Symfony 6.4, connectée à une base de données MySQL via Doctrine ORM. Elle permet une gestion agricole complète et intègre de multiples métiers avancés, APIs intelligentes et modèles d’IA pour l'optimisation de la productivité.

---

## Technologies & Topics

* **Backend** : PHP 8.1+, Symfony 6.4
* **Base de données** : MySQL (XAMPP/phpMyAdmin)
* **API** : REST/JSON
* **Front-end** : Twig, Bootstrap
* **Outils** : Composer, Symfony CLI, Webpack Encore
* **IA** : TensorFlow, PyTorch, GPT2, RoBERTa

**Topics** : symfony, agriculture, api, ia, recyclage, gestion, chatbot, calendar, map

---

## Installation

```bash
git clone https://github.com/votre-organisation/agrilink-symfony.git
cd agrilink-symfony
composer install
```

Configurer `.env.local` pour la base de données :

```dotenv
DATABASE_URL="mysql://root:@127.0.0.1:3306/agrilink_db"
```

Lancer le serveur Symfony :

```bash
symfony server:start
```

---

## Configuration

* Doctrine ORM
* JWT Authentication pour APIs
* Clés API IA pour modèles

---

## Fonctionnalités Métiers & APIs

* Tri, recherche dynamique, multicritères (Ajax)
* Archivage automatique
* Réponse automatique aux utilisateurs
* SMS (services 326/327)
* Statistiques avancées & graphiques
* Exportation en PDF personnalisé (iText)
* Voice-to-text pour publication de contenu
* Traduction automatique (multilingue)
* Filtrage de "bad words" (cryptage)
* Caméra : détection d'émotions + voix
* Affichage : offres d'emploi, vidéos TikTok, articles agricoles
* Partage social (Facebook, LinkedIn, WhatsApp)
* Badges "positive", "negative", "neutral" (analyse RoBERTa)
* Gestion avancée des commentaires (filtrage)

Fonctionnalités additionnelles :

* Calendrier des événements et demandes
* Notifications temps réel (Websockets)
* Système de badge
* 2FA authentification, reset password, 3 time password
* Remember Me
* Google Meet / Mail intégration
* Suggestion d'utilisateurs
* Crop Recommendation (récoltes adaptées)
* Map : OpenStreetMap ou Google Maps

---

## Modules de gestion

1. **Utilisateurs** : inscription, vérification, connexion, authentification à deux facteurs
2. **Ressources** : matériel agricole, gestion de stock
3. **Points de recyclage** : création, gestion, localisation sur carte
4. **Evénements** : planification, gestion calendrier, expiration automatique
5. **Posts** : publications, modération, réactions
6. **Réclamations** : soumission, classement positif/négatif, suivi des états

---

## IA & Modèles

* Modèle de détection de maladies des plantes
* Modèle détection maladies animales
* Modèle d'émotions utilisateur
* Modèle prédiction de rendement des terres
* Modèle de contrôle d'image agricole
* Modèle de détection du type de sol
* Modèle d'état de fruits (Healthy/Rotten)
* Modèle estimation des calories des fruits (pour 100g)
* GPT2 chatbot pour conseils agricoles
* Analyse de sentiment des posts (positive/neutral/negative)

---

## Utilisation

* Accéder via `http://127.0.0.1:8000`
* APIs REST disponibles sous `/api/`
* Utilisation Postman ou Swagger pour explorer les endpoints

---

## Contribution

1. Fork
2. Nouvelle branche :

```bash
git checkout -b feature/ma-fonctionnalite
```

3. Commit :

```bash
git commit -m "Ajout de fonctionnalité"
```

4. Push et Pull Request

---

## Licence

MIT - Voir fichier [LICENSE](LICENSE)
