## 🧹 Qualité du Code & Bonnes Pratiques

### 3 - Les bonnes habitudes

#### 1. Le nommage
* **Ce qu'on a fait :** Standardisation et internationalisation du code, et prévention des conflits CSS.
* **Comment on l'a fait :** Nous avons traduit l'intégralité du code (variables, méthodes, classes) en anglais pour respecter les standards et assurer une lisibilité du code pour tous. Par exemple, nous utilisons des variables explicites comme `$friendsList` et des méthodes claires comme `accept` et `reject` au lieu de leurs équivalents français. Côté front-end, nous avons renommé les classes génériques pour éviter qu'elles n'héritent des styles globaux de CakePHP.

#### 2. Commentaires / Docs
* **Ce qu'on a fait :** Nettoyage du code et mise en place d'une documentation complète accessible ici : [https://cbulle.github.io/trips-road/](https://cbulle.github.io/trips-road/) (contenant les explications des fonctions et le guide d'installation).
* **Comment on l'a fait :** Nous avons supprimé les commentaires redondants qui décrivaient des parties de codes explicites par elles-mêmes. À la place, nous avons formaté les en-têtes de méthodes (PHPDoc) pour indiquer précisément les types de paramètres, les retours attendus et les exceptions. La méthode protégée `_getCoordinates($cityName, $table)` en est un parfait exemple, documentée avec ses balises `@param` et `@return` pour permettre à l'IDE de comprendre instantanément son fonctionnement.

#### 3. La programmation défensive
* **Ce qu'on a fait :** Anticipation des comportements non autorisés et prévention des crashs d'interface.
* **Comment on l'a fait :** Mise en place de clauses de garde au début des fonctions et forçage des identifiants sensibles côté serveur pour bloquer les actions interdites. Par exemple, au lieu de faire confiance aux données postées pour récupérer l'ID utilisateur, nous le forçons de manière sécurisée via la session d'authentification : `$comment->user_id = $this->request->getAttribute('identity')->getIdentifier();`. Côté vue, l'existence des variables est systématiquement vérifiée avant affichage.

#### 4. Gestion des Erreurs
* **Ce qu'on a fait :** Tolérance aux pannes (Fail-safe) et amélioration de l'UX lors des crashs.
* **Comment on l'a fait :** Les requêtes externes ou risquées sont encapsulées dans des blocs `try/catch`. L'appel à l'API externe Nominatim, par exemple, est sécurisé : si l'API ne répond pas, l'application écrit discrètement dans les logs (`\Cake\Log\Log::error`) et renvoie `null` au lieu de déclencher une fatale Erreur 500. De plus, les pages d'erreurs globales 400 et 500 ont été repensées et épurées afin de rendre l'erreur plus compréhensible et lisible pour l'utilisateur final.

#### 5. Débogage
* **Ce qu'on a fait :** Nettoyage des traces de développement pour la production.
* **Comment on l'a fait :** Suppression systématique de toutes les fonctions de debug (`dd()`, `var_dump()`, `console.log()`) utilisées pendant la phase de création. Le code poussé sur la branche de production est propre, garantissant un environnement stable et performant, sans fuite de données via des logs résiduels.

---

### 4 - Les bonnes pratiques

#### 1. L'architecture
* **Ce qu'on a fait :** Respect du pattern MVC ("Fat Model, Skinny Controller") et isolation des composants.
* **Comment on l'a fait :** Les contrôleurs ont été allégés au maximum. La logique complexe (comme le formatage, la cryptographie ou la manipulation de données brutes) est systématiquement extraite dans des méthodes privées dédiées ou déléguée aux modèles et View Cells (comme la cellule de messagerie). Cela permet de garder des contrôleurs extrêmement clairs et centrés sur le flux de la requête.

#### 2. Concepts de programmation
* **Ce qu'on a fait :** Exploitation avancée de l'ORM et application du principe DRY (Don't Repeat Yourself).
* **Comment on l'a fait :** Nous naviguons intelligemment à travers les relations d'objets (associations de CakePHP) plutôt que d'écrire des requêtes manuelles répétitives. L'utilisation experte des utilitaires natifs (comme `FrozenTime` pour les dates) et la mutualisation des composants d'interface nous assurent un code qui ne se répète pas et s'appuie sur la robustesse du framework.

#### 3. Tests unitaires
* **Ce qu'on a fait :** Conception d'un code hautement testable.
* **Comment on l'a fait :** Bien que la couverture de tests automatisés ne soit pas totale sur ce projet, notre choix de découpler fortement la logique métier (Fat Model) des contrôleurs rend l'application structurellement prête pour l'intégration de tests unitaires (PHPUnit). La validation stricte des entités au niveau de la base de données agit déjà comme une première barrière de test des données entrantes.

#### 4. Intégration continue
* **Ce qu'on a fait :** Versioning strict et documentation automatisée.
* **Comment on l'a fait :** Utilisation rigoureuse de Git et GitHub pour le travail collaboratif. Le dépôt intègre des environnements propres avec une mise en ligne de la documentation via GitHub Pages. Le projet est configuré de façon standard, ce qui le rend compatible avec la mise en place future d'un pipeline CI/CD (GitHub Actions) complet.

---

## 🤖 Utilisation de l'Intelligence Artificielle

Dans le cadre de l'amélioration de la qualité du code et du refactoring de l'application, l'Intelligence Artificielle a été utilisée comme assistant de développement.

### Quelle IA et quelle configuration ?
* **Modèle utilisé :** Gemini, Claude.
* **Configuration / Méthodologie :** Nous avons utilisé l'IA pour différentes tâches dans notre projet. Elle est intervenue comme un véritable assistant pour accélérer nos recherches (notamment pour trouver l'API OpenStreetMap Nominatim), faire du débogage complexe, et refactoriser des fichiers. Nous lui avons imposé un contexte strict pour que le code fourni reste systématiquement propre, cohérent, et aligné avec l'architecture CakePHP.

#### 1. Refactoring de la Messagerie (Logique CakePHP & CSS)
* **Démarche :** Séparation du JS et de la vue, correction des conflits Flexbox sur la zone de saisie, et implémentation de l'ORM CakePHP (remplacement des `loadModel` obsolètes par l'arbre d'association).
* **Succès :** Total. L'IA a permis d'identifier rapidement pourquoi les boutons CSS prenaient toute la largeur et a fourni une solution élégante avec les associations CakePHP.
* **Nombre d'essais :** 2 à 3 itérations (ajustement des proportions CSS et correction d'une erreur 500 liée aux versions récentes de CakePHP).
* **Gain de temps estimé :** ~2 à 3 heures.

#### 2. Refonte des pages d'erreur globales (400 / 500)
* **Démarche :** Nettoyage des fichiers natifs, suppression des styles en ligne (`style="..."`), et résolution du bug d'affichage des polices Material Icons.
* **Succès :** Total, mais a nécessité un guidage précis.
* **Nombre d'essais :** 4 itérations. Il a fallu recadrer l'IA pour qu'elle abandonne sa première proposition, reprenne le design centré d'origine, et supprime les icônes problématiques.
* **Gain de temps estimé :** ~1 heure.

#### 3. Rédaction de la documentation et mappage Clean Code
* **Démarche :** Synthétiser l'ensemble des modifications techniques pour les faire correspondre parfaitement au plan d'évaluation académique.
* **Succès :** Total. L'IA a excellé dans la vulgarisation et l'organisation des concepts techniques.
* **Nombre d'essais :** 3 itérations pour obtenir le bon format et intégrer des exemples réels de notre code.
* **Gain de temps estimé :** ~2 heures. L'effort de rédaction et de formatage a été drastiquement réduit.

### Évaluation globale du gain de temps
Par rapport à une résolution 100% manuelle (recherche sur StackOverflow, lecture de la documentation officielle, débogage CSS empirique), l'utilisation de l'IA a permis d'économiser environ **5 à 7 heures de travail** sur cette phase. Son plus grand atout n'a pas été d'écrire le code à notre place, mais de **débloquer instantanément des situations techniques** tout en nous forçant à respecter nos propres conventions de nommage et d'architecture.