# 👥 RÉPARTITION DES TÂCHES — Plateforme Mémoires

## ⚠️ IMPORTANT : Nouveau plan basé sur PHASE 1

**PHASE 1 priorité** : Rendre accessible le catalogue des anciens mémoires
- Catalogue (liste + recherche + détail)
- Gestion des étudiants (qui va consulter le catalogue)
- Interactions (likes + commentaires)
- Lecteur PDF sécurisé

**PHASE 2 ultérieure** : Soumission étudiant + validation professeur

---

## 🧑‍💻 ELIEL — Back-end (Catalogue + Interactions + PDF)

> Chef de projet · Tâches critiques et intégration

### Priorité 🔴 IMMÉDIATE : Catalogue
- [ ] Coder `CatalogueController.php`
  - `index()` → liste mémoires publiés (paginée)
  - `recherche()` → recherche avancée
  - `detail(id)` → détail d'un mémoire
- [ ] Ajouter méthodes à `Memoire.php`
  - `getAllPublished()` → tous les mémoires avec statut='publie'
  - `getById(id)` → récupérer un mémoire complet
  - `search(filters)` → recherche par filière/niveau/centre/professeur/année
  - `getCommentaires(id)` → commentaires d'un mémoire
  - `getLikeCount(id)` → nombre de likes
  - `getCommentCount(id)` → nombre de commentaires

### Priorité 🟡 Interactions (après catalogue + gestion étudiants)
- [ ] Coder `LikeController.php`
  - `toggle(id)` → AJAX : like/unlike un mémoire
- [ ] Coder `CommentaireController.php`
  - `ajouter(id)` → AJAX : ajouter commentaire
- [ ] Coder `Like.php` model
  - `toggle(id_memoire, id_utilisateur)`
  - `countByMemoire(id_memoire)`
  - `hasLiked(id_memoire, id_utilisateur)`
- [ ] Coder `Commentaire.php` model
  - `ajouter(data)`
  - `getByMemoire(id_memoire)`

### Priorité 🟡 PDF sécurisé (après interactions)
- [ ] Coder `PdfController.php`
  - `servir(id)` → proxy sécurisé (vérifie session + route vers storage/)

---

## 👩‍💻 HIDAYATH — Back-end (Données métier)

> Gestion des modèles et controllers DE

### Priorité 🟡 Finir Référentiel (partiellement fait)
- [ ] Finir `ReferentielController.php`
  - `creerCentre()`, `supprimerCentre()`
  - `creerAnnee()`, `supprimerAnnee()` (pour `id_annee_academique`)

### Priorité 🔴 IMMÉDIATE : Gestion des étudiants (après catalogue)
- [ ] Coder `EtudiantController.php`
  - `liste()` → tous les étudiants avec search
  - `creer()` → crée user + etudiant + inscription (POST)
  - `diplomer(id)` → POST, change `type_etudiant` à 'diplome' + choisit `niveau_diplome`
- [ ] Coder `Etudiant.php` model
  - `getAll()`
  - `getById(id)`
  - `create(data)` → crée l'étudiant
  - `search(query)` → recherche par nom/prenom/matricule
- [ ] Coder `Inscription.php` model
  - `create(data)` → crée inscription unique par étudiant
  - `getByEtudiant(id_etudiant)`
  - `diplomer(id_inscription, niveau_diplome)` → change type + niveau

### Priorité 🟡 Phase 2 (validation professeur)
- [ ] Finir `ProfesseurController.php`
  - `dashboard()` → stats + onglets en attente/validés/refusés
  - `listeMemoiresProf()` → tous les mémoires pour validation
  - `valider(id)` → POST, change statut à 'publie'
  - `refuser(id)` → POST, change statut à 'refuse'

---

## 🎨 FADEL — Front-end (Catalogue + Interactions + PDF)

> Lead front · Vues complexes

### Priorité 🔴 IMMÉDIATE : Catalogue
- [ ] Coder `app/views/catalogue/index.php`
  - Grille 3 colonnes de cards mémoires
  - Barre recherche (simple + avancée)
  - Chips filtres rapides (filière / niveau / centre / année / professeur)
  - Pagination
  - Affiche : titre, auteur, filière, niveau, centre, année, prof, date, stats (likes/commentaires)
- [ ] Coder `app/views/catalogue/detail.php`
  - Infos complètes du mémoire
  - Bouton like (AJAX + compteur)
  - Section commentaires (AJAX + liste dynamique)
  - Bouton "Lire le mémoire" → link vers `/pdf/{id}`
  - Sidebar avec infos académiques

### Priorité 🟡 Interactions (après catalogue + gestion étudiants)
- [ ] Ajouter AJAX à `public/assets/js/main.js`
  - Toggle like (fetch POST → `/like/{id}`, update compteur)
  - Ajouter commentaire (fetch POST → `/commentaire/{id}`, add dynamique)
  - Validation formulaire commentaire

### Priorité 🟡 PDF (après interactions)
- [ ] Intégrer PDF.js dans layout
  - Coder `app/views/layouts/pdf.php`
  - Canvas PDF.js (gauche) + panel infos/likes/commentaires (droite)
  - Styles sombre (`#111827` fond)

---

## 👩‍🎨 PRINCESSE — Front-end (Gestion DE)

> Vues pour la Direction d'Études

### Priorité 🔴 IMMÉDIATE : Gestion des étudiants (après Fadel catalogue)
- [ ] Coder `app/views/etudiants/liste.php`
  - Tableau tous les étudiants (matricule, nom, prenom, filière, niveau, centre, années, type)
  - Barre recherche (search + filter par type : simple/diplome)
  - Bouton "Passer diplômé" sur chaque ligne (ouvre modale)
  - Formulaire création en bas (ou modale)
    - Inputs : nom, prenom, matricule, telephone, filière, niveau, centre, année
    - Génère user + etudiant + inscription automatiquement
- [ ] Coder `app/views/etudiants/modal_diplome.php`
  - Overlay sombre
  - Titre "Passer l'étudiant au statut diplômé"
  - Radio buttons : L3 / M1 / M2 (choisir le niveau diplôme)
  - Champ année diplôme
  - Avertissement orange : "cet étudiant pourra soumettre son mémoire"
  - Boutons Annuler / Valider

### Priorité 🟡 Référentiel (optionnel, peut être partiellement fait)
- [ ] Vérifier que `app/views/referentiel/index.php` existe et fonctionne
  - 4 panels : Filières / Niveaux / Centres / Années
  - Chaque panel : input création + liste avec boutons supprimer

### Priorité 🟡 Phase 2 (validation professeur)
- [ ] Coder `app/views/professeurs/liste.php` (après gestion étudiants)
  - Tableau tous les professeurs
  - Bouton "Ajouter professeur"
- [ ] Coder `app/views/professeurs/dashboard.php`
  - Stats (en attente / validés / refusés)
  - Onglets pour filtrer par statut
  - Tableau avec boutons Lire / Valider / Refuser

---

## 👨‍🎨 STEEVE — Front-end (PDF + Référentiel + Étudiant)

> Remaining vues

### Priorité 🟡 Référentiel (peut commencer maintenant)
- [ ] Vérifier/finir `app/views/referentiel/index.php`
  - 4 panels côte à côte
  - Champ ajout + liste avec bouton supprimer sur chaque item
  - Panel années académiques en bas

### Priorité 🟡 PDF sécurisé (après Fadel interactions)
- [ ] Intégrer PDF.js avec Eliel
  - Ajouter `pdfviewer.js` dans `public/assets/js/`
  - Tester lecteur sur layout pdf.php

### Priorité 🟡 Étudiant dashboard (Phase 2)
- [ ] Coder `app/views/etudiant/dashboard.php`
  - 4 cartes stats
  - Bloc statut du mémoire (si soumis : en_attente/publie/refuse)
  - Récemment consultés
- [ ] Coder `app/views/etudiant/mon_memoire.php`
- [ ] Coder `app/views/memoires/soumettre.php` ← PHASE 2
- [ ] Coder `app/views/memoires/liste_prof.php`

---

## 🔗 DÉPENDANCES ET BLOCKERS

| Qui | Attend | De qui | Quoi |
|-----|--------|--------|------|
| Fadel | Backend Catalogue | Eliel | `CatalogueController` + `Memoire.php` methods |
| Princesse | Backend Étudiants | Hidayath | `EtudiantController` + `Inscription.php` |
| Fadel | Backend Interactions | Eliel | `LikeController` + `CommentaireController` |
| Steeve | Backend PDF | Eliel | `PdfController` |
| Tout | Référentiel fini | Hidayath | Finir `ReferentielController` |

---

## ⚠️ RÈGLES D'ÉQUIPE

- **Zéro SQL dans les vues** — tout passe par les models
- **Zéro `mysqli`** — PDO uniquement
- **Zéro `md5` / `sha1`** — `password_hash()` / `password_verify()` uniquement
- **Zéro PDF en accès direct** — tout passe par `PdfController`
- **Zéro pagination SQL** → gérer avec `LIMIT` + `OFFSET`
- **AJAX responses** → toujours JSON (`Content-Type: application/json`)
- Eliel valide chaque intégration + routes + models

