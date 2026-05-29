# 📦 LIVRABLE — Qui doit faire quoi maintenant

## 🎯 Priorité absolue pour débloquer le reste

---

## 🧑‍💻 ELIEL — 2 commits restants

### 1️⃣ `feat(interactions): likes + commentaires AJAX`

**Dépend de :** ✅ Catalogue backend livré

**À livrer :**
- `app/controllers/LikeController.php`
  - `toggle(id)` — POST endpoint, retourne JSON
  - `hasLiked(id, user)` — vérifie si user a liké
- `app/controllers/CommentaireController.php`
  - `ajouter(id)` — POST endpoint, retourne JSON
  - Valide le contenu
  - Crée un commentaire en BD

**Modèles déjà fait ✅** :
- `Like.php` ✅
- `Commentaire.php` ✅

**Routes à ajouter** (si pas déjà là):
```php
$router->add('POST', 'like/{id}',         'LikeController',        'toggle');
$router->add('POST', 'commentaire/{id}',  'CommentaireController', 'ajouter');
```

**Message de commit :**
```
feat(interactions): likes et commentaires AJAX + controllers + JSON endpoints

- LikeController::toggle() — like/unlike avec retour JSON
- CommentaireController::ajouter() — création commentaire avec validation
- Validation des permissions utilisateur
- Endpoints JSON pour les vues
```

**Blockers ?** NON — Fadel peut faire les vues en parallèle

---

### 2️⃣ `feat(pdf-securise): lecteur PDF.js + proxy PHP sécurisé`

**Dépend de :** ✅ Interactions livrées

**À livrer :**
- `app/controllers/PdfController.php`
  - `servir(id)` — récupère le PDF depuis storage/, vérifie permission
  - Retourne le fichier avec les bons headers

**Routes** :
```php
$router->add('GET', 'pdf/{id}', 'PdfController', 'servir');
```

**Message de commit :**
```
feat(pdf-securise): proxy PDF.js sécurisé + lecteur intégré

- PdfController::servir() — proxy sécurisé pour accès PDF
- Vérification session utilisateur avant serving
- Headers corrects pour affichage dans PDF.js
- Stockage PDF en dehors du public/
```

**Blockers ?** NON — Steeve peut faire le layout PDF en parallèle

---

## 👩‍💻 HIDAYATH — 4 commits restants

### 1️⃣ `feat(referentiel): finir centres + années + suppression`

**Dépend de :** ✅ Models déjà complets

**À livrer :**
- `app/controllers/ReferentielController.php` — finir les 4 méthodes manquantes :
  - `creerCentre()` — POST, crée un centre
  - `supprimerCentre(id)` — POST, supprime avec check FK
  - `creerAnnee()` — POST, crée année académique
  - `supprimerAnnee(id)` — POST, supprime avec check FK

**Routes** (déjà là ?) :
```php
$router->add('POST', 'de/centres/creer',          'ReferentielController', 'creerCentre');
$router->add('POST', 'de/centres/supprimer/{id}', 'ReferentielController', 'supprimerCentre');
$router->add('POST', 'de/annees/creer',           'ReferentielController', 'creerAnnee');
$router->add('POST', 'de/annees/supprimer/{id}',  'ReferentielController', 'supprimerAnnee');
```

**Message de commit :**
```
feat(referentiel): compléter gestion centres + années académiques

- ReferentielController::creerCentre() et supprimerCentre()
- ReferentielController::creerAnnee() et supprimerAnnee()
- Validation existence + check contraintes FK
- Vérifications avant suppression
```

---

### 2️⃣ `feat(gestion-etudiants): création, inscription, passage diplômé`

**Dépend de :** ✅ Référentiel complet

**À livrer :**
- `app/models/Etudiant.php` — CRUD complet
  - `create(data)` — crée un étudiant
  - `getAll()` — tous les étudiants
  - `getById(id)` — un étudiant
  - `search(query)` — recherche par nom/prenom/matricule
  - `update(id, data)` — update si besoin

- `app/models/Inscription.php` — parcours étudiant
  - `create(data)` — crée l'inscription
  - `getByEtudiant(id)` — inscription d'un étudiant
  - `diplomer(id, niveau_diplome)` — change type à 'diplome' + niveau

- `app/controllers/EtudiantController.php`
  - `liste()` — GET, affiche tous les étudiants (pagine ?)
  - `creer()` — POST, crée user + etudiant + inscription
  - `diplomer(id)` — POST, passe au statut diplômé

**Routes** (déjà là ?) :
```php
$router->add('GET',  'de/etudiants',               'EtudiantController', 'liste');
$router->add('POST', 'de/etudiants/creer',         'EtudiantController', 'creer');
$router->add('POST', 'de/etudiants/diplomer/{id}', 'EtudiantController', 'diplomer');
```

**Message de commit :**
```
feat(gestion-etudiants): création, inscription, passage diplômé

- Etudiant.php : CRUD complet (create, getAll, getById, search)
- Inscription.php : gestion parcours + méthode diplomer()
- EtudiantController : liste, créer, diplômer
- Auto-génération matricule unique
- Création compte utilisateur automatique (mot de passe par défaut)
- Validation email unique + données obligatoires
```

**Blockers ?** NON — Princesse peut faire les vues en parallèle

---

### 3️⃣ `feat(professeur-validation): dashboard prof + validation/refus`

**Dépend de :** ✅ Interactions + PDF backend

**À livrer :**
- `app/controllers/ProfesseurController.php` — finir les 4 méthodes :
  - `dashboard()` — GET, stats + onglets
  - `listeMemoiresProf()` — GET, tous les mémoires pour ce prof
  - `valider(id)` — POST, change statut à 'publie'
  - `refuser(id)` — POST, change statut à 'refuse'

**Message de commit :**
```
feat(validation-professeur): dashboard + validation/refus mémoires

- ProfesseurController::dashboard() — stats + onglets statuts
- ProfesseurController::listeMemoiresProf() — mémoires en attente
- ProfesseurController::valider() et refuser() — changement de statut
- Vérification permissions (professeur vérifie que c'est ses mémoires)
- Mise à jour date_publication et statut
```

**Blockers ?** OUI si on peut pas accéder aux vues — **Steeve doit faire la UI PDF d'abord**

---

### 4️⃣ `feat(tests): PHPUnit tests complets`

**Dépend de :** ✅ Tout le reste de Phase 1

**À livrer :**
- `tests/Feature/EtudiantTest.php`
- `tests/Feature/CatalogueTest.php`
- `tests/Unit/AuthTest.php` ou amélioration

**Message de commit :**
```
feat(tests): tests PHPUnit phase 1 complète

- Tests création/inscription étudiant + passage diplômé
- Tests catalogue (pagination, filtres, search)
- Tests authentification
- Coverage minimum 70%
```

---

## 🎨 FADEL — Front-end (2 commits restants)

### 1️⃣ `feat(ui-catalogue): vues catalogue + détail + filtres`

**Dépend de :** ✅ `CatalogueController.php` livré

**À livrer :**
- `app/views/catalogue/index.php`
  - Affiche `$memoires` (array pagioné)
  - Grille 3 colonnes de cards
  - Champs filtres : filière, niveau, centre, année, professeur (selects)
  - Barre recherche (input `q`)
  - Pagination : liens vers page suivante/précédente
  - Affiche stats : nb likes, nb commentaires

- `app/views/catalogue/detail.php`
  - Affiche `$memo` (un mémoire complet)
  - Infos : titre, auteur, filière, niveau, centre, année, professeur, résumé, date
  - Bouton like : POST vers `/like/{id}` + AJAX
  - Section commentaires : liste + formulaire ajout
  - Bouton "Lire le PDF" → link `/pdf/{id}`

**Variables de la vue (du controller) :**
- `$memoires` (array)
- `$page`, `$totalPages` (pagination)
- `$filieres`, `$niveaux`, `$centres`, `$annees`, `$professeurs` (pour selects)
- `$q`, `$id_filiere`, etc. (valeurs actuelles des filtres)
- `$memo` (pour detail.php)
- `$commentaires` (pour detail.php)
- `$hasLiked` (boolean — user a-t-il déjà liké ?)

**Message de commit :**
```
feat(ui-catalogue): vues catalogue + détail mémoire + filtres + pagination

- catalogue/index.php : grille 3 colonnes + filtres dynamiques
- catalogue/detail.php : affichage complet + likes + commentaires
- Formulaire recherche et filtres
- Pagination fonctionnelle
- Responsive mobile-first
```

**Blockers ?** NON — Prêt dès maintenant

---

### 2️⃣ `feat(ui-interactions): AJAX likes + commentaires`

**Dépend de :** ✅ `LikeController` + `CommentaireController` livrés

**À livrer :**
- Mise à jour `public/assets/js/main.js`
  - Fonction like toggle (fetch POST → `/like/{id}`)
  - Mise à jour compteur likes
  - Fonction ajouter commentaire (fetch POST → `/commentaire/{id}`)
  - Ajout dynamique du commentaire à la liste
  - Validation simple du formulaire

**Message de commit :**
```
feat(ui-interactions): AJAX likes et commentaires

- Toggle like : fetch POST + mise à jour compteur
- Ajouter commentaire : fetch POST + ajout dynamique
- Validation formulaire commentaire (non-vide, length max)
- Animations smooth
- Gestion erreurs réseau
```

**Blockers ?** NON — Attendre les controllers mais peut coder en parallèle

---

## 👩‍🎨 PRINCESSE — Front-end (3 commits)

### 1️⃣ `feat(ui-gestion-etudiants): liste + création + modale diplôme`

**Dépend de :** ✅ `EtudiantController.php` livré

**À livrer :**
- `app/views/etudiants/liste.php`
  - Tableau : matricule, nom, prenom, filière, niveau, centre, année, type (simple/diplome)
  - Barre recherche + filtres
  - Bouton "Passer diplômé" sur chaque ligne (ouvre modale)
  - Formulaire création en bas (ou bouton qui montre formulaire)
    - Inputs : nom, prenom, matricule, telephone, filière, niveau, centre, année

- `app/views/etudiants/modal_diplome.php`
  - Overlay sombre
  - Radio buttons L3 / M1 / M2
  - Champ année diplôme
  - Avertissement orange
  - Boutons Annuler / Valider

**Variables :**
- `$etudiants` (array)
- `$filieres`, `$niveaux`, `$centres`, `$annees` (pour selects)

**Message de commit :**
```
feat(ui-gestion-etudiants): liste + création + modale passage diplômé

- etudiants/liste.php : tableau avec recherche et filtres
- etudiants/modal_diplome.php : modale pour passage diplômé
- Formulaire création étudiant
- Validation client-side
- Responsive mobile
```

---

### 2️⃣ `feat(ui-dashboards-de): dashboard DE + stats`

**Dépend de :** Avoir les données backend (peut être fait avant si hardcodé)

**À livrer :**
- `app/views/de/dashboard.php`
  - 4 cartes stats (nb étudiants, nb mémoires, nb en attente, etc.)
  - Actions rapides (créer étudiant, archiver mémoire, etc.)
  - Tableau derniers étudiants inscrits

**Message de commit :**
```
feat(ui-dashboards-de): dashboard DE avec stats et actions

- 4 cartes de statistiques
- Actions rapides
- Tableau derniers inscrits
- Responsive
```

---

### 3️⃣ `feat(ui-professeur): liste + dashboard + validation`

**Dépend de :** ✅ `ProfesseurController` livré

**À livrer :**
- `app/views/professeurs/liste.php`
  - Tableau professeurs
  - Formulaire création

- `app/views/professeurs/dashboard.php`
  - Stats (en attente, validés, refusés)
  - Onglets pour filtrer
  - Tableau mémoires avec boutons Lire/Valider/Refuser

**Message de commit :**
```
feat(ui-professeur): liste + dashboard + onglets validation

- professeurs/liste.php : tableau + création
- professeurs/dashboard.php : stats + onglets + actions
- Boutons valider/refuser connectés aux endpoints
- Responsive
```

---

## 👨‍🎨 STEEVE — Front-end (3 commits)

### 1️⃣ `feat(ui-pdf): layout lecteur PDF.js`

**Dépend de :** ✅ `PdfController.php` livré

**À livrer :**
- `app/views/layouts/pdf.php`
  - Fond sombre (`#111827`)
  - Layout 2 colonnes :
    - **Gauche (80%)** : canvas PDF.js
    - **Droite (20%)** : panel infos + likes + commentaires
  - Header avec titre du mémoire
  - Sidebar réduite (menu foncé)

- `public/assets/js/pdfviewer.js` OU ajouter à `main.js`
  - Intégration PDF.js
  - Chargement du PDF depuis `$memo['fichier_pdf']`
  - Pagination PDF

**Variables :**
- `$memo` (mémoire complet)
- `$commentaires` (liste)
- `$hasLiked` (boolean)

**Message de commit :**
```
feat(ui-pdf): lecteur PDF.js intégré + layout sombre

- layouts/pdf.php : layout 2 colonnes
- Canvas PDF.js pour affichage sécurisé
- Panel infos + likes + commentaires côté droit
- Fond sombre et ergonomie optimisée
- Pagination PDF
```

---

### 2️⃣ `feat(ui-referentiel): vues filières + niveaux + centres + années`

**Dépend de :** ✅ `ReferentielController.php` complet

**À livrer :**
- `app/views/referentiel/index.php`
  - 4 panels côte à côte
  - Chaque panel :
    - Input créer nouvel item
    - Liste avec boutons supprimer
  - Panel années académiques en bas

**Message de commit :**
```
feat(ui-referentiel): gestion filières + niveaux + centres + années

- referentiel/index.php : 4 panels de gestion
- Input création + liste avec supprimer
- Responsive multi-écrans
```

---

### 3️⃣ `feat(ui-etudiant): dashboard étudiant + mon mémoire`

**Dépend de :** ✅ Phase 2 backend (peut être fait après)

**À livrer :**
- `app/views/etudiant/dashboard.php`
  - 4 cartes stats
  - Bloc statut mémoire (si soumis)
  - Récemment consultés

- `app/views/etudiant/mon_memoire.php`
  - Affichage de son mémoire (si soumis)
  - Statut de validation

- `app/views/memoires/soumettre.php` ← **PHASE 2**
  - Formulaire soumission

**Message de commit :**
```
feat(ui-etudiant): dashboard + mon mémoire + soumission

- etudiant/dashboard.php : stats + statut mémoire
- etudiant/mon_memoire.php : consultation du mémoire
- memoires/soumettre.php : formulaire soumission PHASE 2
- Responsive
```

---

## 📋 ORDRE D'EXÉCUTION RECOMMANDÉ

### **Week 1 — PHASE 1 Catalogue**

1. ✅ **Eliel** : `feat(interactions)` — LikeController + CommentaireController
2. ✅ **Hidayath** : `feat(referentiel)` — finir centres + années
3. ✅ **Fadel** : `feat(ui-catalogue)` — vues catalogue + détail
4. ✅ **Princesse** : `feat(ui-gestion-etudiants)` — liste + création + modale
5. ✅ **Hidayath** : `feat(gestion-etudiants)` — models + controller
6. ✅ **Fadel** : `feat(ui-interactions)` — AJAX likes + commentaires

### **Week 2 — PHASE 1 PDF + Tests**

7. ✅ **Eliel** : `feat(pdf-securise)` — PdfController
8. ✅ **Steeve** : `feat(ui-pdf)` — layout PDF.js
9. ✅ **Steeve** : `feat(ui-referentiel)` — vues référentiel
10. ✅ **Princesse** : `feat(ui-dashboards-de)` — dashboard DE
11. ✅ **Hidayath** : `feat(validation-professeur)` — validation/refus
12. ✅ **Princesse** : `feat(ui-professeur)` — liste + dashboard
13. ✅ **Hidayath** : `feat(tests)` — PHPUnit

### **Week 3+ — PHASE 2**

14. ✅ **Eliel** : soumission étudiant
15. ✅ **Steeve** : `feat(ui-etudiant)` — dashboard + soumission
16. ✅ Tests phase 2
17. ✅ Déploiement

---

## 🚀 **QUI PEUT COMMENCER MAINTENANT ?**

**Vert ✅ (Prêt) :**
- **Fadel** → `feat(ui-catalogue)` (backend ready)
- **Steeve** → `feat(ui-referentiel)` (backend ready)
- **Eliel** → `feat(interactions)` (déjà planifié)

**Orange ⏳ (Attend un truc rapide) :**
- **Hidayath** → `feat(referentiel)` finir (15 min de code)
- **Princesse** → `feat(ui-gestion-etudiants)` (attend referentiel d'Hidayath)

---

**Et voilà, tout le monde sait quoi faire ! 🎯**
