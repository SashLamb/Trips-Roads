## Qualité du Code & Bonnes Pratiques

#### Le nommage
* **Ce qu'on a fait :** Standardisation et internationalisation du code, et prévention des conflits CSS.
* **Comment on l'a fait :** Nous avons traduit l'intégralité du code (variables, méthodes, classes) en anglais pour respecter les standards et assurer une lisibilité du code pour tous. Côté front-end, nous avons renommé les classes génériques pour éviter qu'elles n'héritent des styles globaux de CakePHP.
* **Où:** Dans `FriendshipsController.php`, nous utilisons des variables explicites comme `$friendsList` et des méthodes claires comme `accept` et `reject` au lieu de leurs équivalents français. **Cette convention de nommage strict s'applique à l'ensemble du projet.**

#### Commentaires / Docs
* **Ce qu'on a fait :** Nettoyage du code et mise en place de blocs de documentation. Mise en place d'une documentation complète que vous pouvez retrouver ici : https://cbulle.github.io/trips-road/ (elle contient les explications de toutes les fonctions ainsi que le guide d'installation).
* **Comment on l'a fait :** Nous avons supprimé les commentaires redondants qui decrivaient des parties de codes qui n'en avait pas besoin. À la place, nous avons formaté les en-têtes de méthodes (PHPDoc) pour indiquer précisément les types de paramètres, les retours attendus et les exceptions.
* **Où:** Dans `RoadtripsController.php`, la méthode protégée `_getCoordinates($cityName, $table)` est rigoureusement documentée avec ses balises `@param` et `@return`, permettant à l'IDE de comprendre instantanément son fonctionnement. **Ce standard de documentation est exigé sur toutes les fonctions de notre base de code.**

#### La programmation défensive
* **Ce qu'on a fait :** Anticipation des comportements non autorisés et prévention des crashs d'interface.
* **Comment on l'a fait :** Mise en place de clauses de garde au début des fonctions et forçage des identifiants sensibles côté serveur pour bloquer les actions interdites ou qui pourrait générer des erreurs. Côté vue, vérification systématique de l'existence des variables avant l'affichage.
* **Où (Exemple concret) :** Dans `CommentsController.php` (méthode `add`), au lieu de récupérer l'ID de l'utilisateur depuis les données postées, nous le forçons de manière sécurisée via la session d'authentification : `$comment->user_id = $this->request->getAttribute('identity')->getIdentifier();`. **Cette approche de sécurité *zéro confiance* s'applique pour toutes les requêtes.**

#### Gestion des Erreurs
* **Ce qu'on a fait :** Tolérance aux pannes (Fail-safe) et amélioration de l'UX lors des crashs.
* **Comment on l'a fait :** Encapsulation des requêtes externes ou risquées dans des blocs `try/catch` pour capturer l'erreur sans crasher l'application. Les pages d'erreurs 400 et 500 ont également été repensées et épurées afin de rendre l'erreur plsu conpréhensible et lisible pour tout le monde.
* **Où (Exemple concret) :** Dans `RoadtripsController.php`, l'appel à l'API externe Nominatim est placé dans un `try/catch`. Si l'API ne répond pas, l'application écrit discrètement dans les logs (`\Cake\Log\Log::error`) et renvoie `null` au lieu de déclencher une fatale Erreur 500. **Cette gestion robuste des exceptions est déployée sur toutes les actions critiques.**

##  Utilisation de l'Intelligence Artificielle

Dans le cadre de l'amélioration de la qualité du code et du refactoring de l'application, l'Intelligence Artificielle a été utilisée comme assistant de développement.

### Quelle IA et quelle configuration ?
* **Modèle utilisé :** Gemini, Claude.
* **Configuration / Méthodologie :** Nous avons utilisé l'IA pour différente chose dans notre projets. Mais nous l'avons utiliser comme un assistant pour faire des recherche rapidement (notamment pour trouver des API OpenStreetMap plus rapidement). Ou encore faire du debuggage et de la refactorisation de fichier en les lui donnant, tout en faisant en sorte que le code fourni reste cohérent et propre.

#### 1. Refactoring de la Messagerie (Logique CakePHP & CSS)
* **Démarche :** Séparation du JS et de la vue, correction des conflits Flexbox sur la zone de saisie, et implémentation de l'ORM CakePHP (remplacement des `loadModel` obsolètes par l'arbre d'association `$this->Messages->Senders`).
* **Succès :** Total. L'IA a permis d'identifier rapidement pourquoi les boutons CSS prenaient toute la largeur (conflit Flexbox) et a fourni une solution élégante avec les associations CakePHP.
* **Nombre d'essais :** 2 à 3 itérations (ajustement des proportions CSS et correction d'une erreur 500 liée à la dépréciation de `loadModel` dans CakePHP 4/5).
* **Gain de temps estimé :** ~2 à 3 heures. Le débogage de Flexbox et la recherche dans la documentation de CakePHP pour la syntaxe exacte des View Cells auraient été chronophages.

#### 2. Refonte des pages d'erreur globales (400 / 500)
* **Démarche :** Nettoyage des fichiers `error400.php` et `error500.php`, suppression des styles en ligne (`style="..."`), et résolution du bug d'affichage des polices Material Icons.
* **Succès :** Total, mais a nécessité un guidage précis. L'IA a d'abord proposé un design trop éloigné du site d'origine.
* **Nombre d'essais :** 4 itérations. Il a fallu recadrer l'IA pour qu'elle abandonne sa première proposition, reprenne le design centré d'origine, et supprime finalement les icônes Material Design qui causaient des bugs d'affichage.
* **Gain de temps estimé :** ~1 heure. Permet de générer le CSS de mise en page très rapidement, bien que la direction artistique ait nécessité des allers-retours.

#### 3. Rédaction de la documentation et mappage Clean Code
* **Démarche :** Synthétiser l'ensemble des modifications techniques (nommage, architecture, sécurité) pour les faire correspondre parfaitement au plan d'évaluation académique (habitudes de code, programmation défensive, etc.).
* **Succès :** Total. L'IA a excellé dans la vulgarisation et l'organisation des concepts techniques.
* **Nombre d'essais :** 3 itérations. La première proposition était trop générique ; il a fallu demander l'intégration d'exemples précis tirés de nos différents contrôleurs (`FriendshipsController`, `RoadtripsController`, etc.).
* **Gain de temps estimé :** ~2 heures. L'effort de rédaction, de formatage Markdown et de recherche des meilleurs exemples dans la base de code a été drastiquement réduit.

### Évaluation globale du gain de temps
Par rapport à une résolution 100% manuelle (recherche sur StackOverflow, lecture de la documentation officielle de CakePHP 4/5, débogage CSS empirique), l'utilisation de l'IA a permis d'économiser environ **5 à 7 heures de travail** sur cette phase de refactoring. Son plus grand atout n'a pas été d'écrire le code à notre place, mais de **débloquer instantanément des situations techniques** (comme les erreurs de syntaxe ORM ou les conflits CSS) tout en nous forçant à respecter les conventions de nommage et d'architecture.
