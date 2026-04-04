## Code propre et bonnes pratiques

### 3 - Les bonnes habitudes

#### 1. Le nommage
Nous avons traduit tout le code en anglais (ex: `$friendsList`, `accept`, `reject`). Pour le design, nous avons changé le nom des classes CSS pour éviter les bugs d'affichage.
Nous avons adapté la camalCase pour les variables et fonctions el la PascalCase pour les classes.

#### 2. Commentaires et Documentation
Nous avons enlevé les commentaires inutiles. Nous avons ajouté des textes explicatifs au-dessus des fonctions pour dire ce qu'elles font et ce qu'elles renvoient. Le mode d'emploi du projet est ici : https://cbulle.github.io/trips-road/.

#### 3. La programmation défensive
Nous avons ajouté des vérifications de sécurité. Par exemple, l'ID de l'utilisateur est récupéré de façon cachée et sécurisée, et non plus via le formulaire. L'affichage vérifie toujours que les données existent avant de les montrer.

#### 4. Gestion des Erreurs
Les appels vers l'extérieur (comme la carte Nominatim) sont protégés. S'il y a un problème, le site ne plante pas, il enregistre juste l'erreur de son côté. Les pages d'erreur 400 et 500 ont été refaites pour être plus claires.

#### 5. Débogage
Nous avons effacé tous les bouts de code qui servaient juste à faire des tests de notre côté (`dd()`, `var_dump()`). Le code final est propre.

---

### 4 - Les bonnes pratiques

#### 1. L'architecture
Les fichiers principaux (contrôleurs) ont été simplifiés. Le code compliqué a été rangé dans d'autres petits fichiers ou fonctions à part.

#### 2. Concepts de programmation
Nous utilisons les outils de CakePHP pour lier les données entre elles, au lieu de faire des recherches compliquées à la main. Nous utilisons aussi ses outils pour gérer les dates facilement.

#### 3. Tests unitaires
Le code est bien découpé, ce qui le rend prêt à être testé automatiquement. La base de données vérifie aussi que les informations reçues sont correctes.

#### 4. Intégration continue
Le code est sauvegardé et géré sur GitHub. La documentation est générée et mise en ligne automatiquement avec GitHub Pages.

---

## Utilisation de l'Intelligence Artificielle

Les outils Gemini et Claude nous ont aidés à chercher des informations, corriger des bugs et ranger notre code.

### 1. Messagerie
* **Action :** Séparation du code, correction du design des boutons, et utilisation des liens CakePHP.
* **Essais :** 2 à 3 fois.
* **Temps gagné :** 2 à 3 heures.

### 2. Pages d'erreur (400 / 500)
* **Action :** Nettoyage des fichiers, correction du style et des icônes.
* **Essais :** 4 fois.
* **Temps gagné :** 1 heure.

### 3. Rédaction de la documentation
* **Action :** Résumer les changements techniques pour le texte de présentation.
* **Essais :** 3 fois.
* **Temps gagné :** 2 heures.

### Bilan
L'IA nous a fait gagner environ 5 à 7 heures de travail, surtout pour corriger le design et écrire la documentation.