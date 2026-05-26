# 👥 RÉPARTITION DES TÂCHES — Plateforme Mémoires
## Équipe de 5 · 5 jours

---

## 🧑‍💻 ELIEL — Chef de projet · Back-end critique

> Tâches critiques, architecture, sécurité, intégration finale

### J1 — Infrastructure
- [ ] Lancer `setup.bat` + vérifier l'arborescence
- [ ] Importer `schema.sql` + créer `seeds.sql`
- [ ] Coder `core/Database.php` (singleton PDO)
- [ ] Coder `core/Router.php`
- [ ] Coder `core/Session.php` + `core/Auth.php`
- [ ] Coder `config/config.php` + `config/routes.php`
- [ ] Coder `public/index.php` + `public/.htaccess`
- [ ] Coder `AuthController.php` + `app/models/Utilisateur.php`
- [ ] Coder `app/views/layouts/auth.php` + `app/views/auth/login.php`

### J2 — Archivage mémoires ← PRIORITÉ ABSOLUE
- [ ] Coder `app/models/Memoire.php`
- [ ] Coder `MemoireController.php` → archivage DE
  - Upload PDF sécurisé dans `storage/memoires/`
  - Enregistrement en DB avec `statut = publie` directement
- [ ] Coder `PdfController.php` (proxy sécurisé — vérifie session avant de servir)
- [ ] Coder `app/views/layouts/pdf.php`
- [ ] Intégrer `PDF.js` dans `public/assets/js/pdfviewer.js`

### J3 — Catalogue + Interactions
- [ ] Coder `CatalogueController.php`
  - Liste mémoires publiés
  - Recherche avancée (filière, niveau, centre, professeur, année)
  - Page détail mémoire
- [ ] Coder `LikeController.php` (AJAX + JSON)
- [ ] Coder `CommentaireController.php` (AJAX + JSON)
- [ ] Coder `app/models/Like.php` + `app/models/Commentaire.php`

### J4 — Dashboard professeur + étudiant
- [ ] Coder `ProfesseurController.php` → validation / refus mémoire
- [ ] Coder dashboard étudiant (statut mémoire soumis)
- [ ] Aider Hidayath sur les bugs remontés

### J5 — Tests + Intégration finale
- [ ] Configurer `tests/phpunit.xml` + `tests/bootstrap.php`
- [ ] Écrire `tests/Unit/AuthTest.php`
- [ ] Écrire `tests/Feature/MemoireArchivageTest.php`
- [ ] Écrire `tests/Feature/CatalogueTest.php`
- [ ] Revue de code globale + corrections
- [ ] Recette finale + démo

---

## 👩‍💻 HIDAYATH — Back-end · Seconde du chef

> Gestion des données métier (étudiants, professeurs, référentiel)

### J1 — Models métier
- [ ] Coder `app/models/Etudiant.php`
- [ ] Coder `app/models/Professeur.php`
- [ ] Coder `app/models/Inscription.php`
  - Logique passage diplômé (`niveau_diplome` + `peut_soumettre = TRUE`)
- [ ] Coder `app/models/Filiere.php` + `Niveau.php` + `Centre.php` + `AnneeAcademique.php`

### J2 — Controllers DE
- [ ] Coder `EtudiantController.php`
  - Liste étudiants
  - Création étudiant + inscription (formulaire unique)
  - Passage diplômé (POST → choisir L3/M1/M2)
- [ ] Coder `ProfesseurController.php`
  - Liste professeurs
  - Création professeur

### J3 — Référentiel + Dashboards
- [ ] Coder `ReferentielController.php`
  - Filières, niveaux, centres, années : créer / supprimer
- [ ] Coder `DEController.php` (dashboard avec stats)
- [ ] Coder `AdminController.php` (dashboard + liste utilisateurs)

### J4 — Tests + Corrections
- [ ] Écrire `tests/Feature/EtudiantTest.php`
  - Création étudiant + inscription liée
  - Passage diplômé → `peut_soumettre = TRUE`
- [ ] Écrire `tests/Feature/AuthLoginTest.php`
- [ ] Écrire `tests/Unit/ValidatorTest.php`
- [ ] Corriger les bugs remontés par les fronts

### J5 — Support + Intégration
- [ ] Support à Eliel pour la recette finale
- [ ] Vérifier que toutes les routes sont bien reliées aux vues

---

## 🎨 FADEL — Front-end lead

> Le plus solide des 3 front — vues les plus complexes

### J1 — CSS global
- [ ] Coder `public/assets/css/main.css`
  - Variables CSS (navy `#1A2E5A`, rouge `#B71C1C`, blanc)
  - Sidebar desktop + hamburger mobile
  - Topbar
  - Cards mémoires (grille 3 colonnes)
  - Badges statuts
  - Responsive mobile-first
- [ ] Coder `app/views/layouts/main.php`
  - Inclusion sidebar + topbar + zone contenu
  - Variable `$role` pour adapter le menu

### J2 — Vues archivage ← PRIORITÉ
- [ ] Coder `app/views/memoires/archiver.php`
  - Tous les selects dynamiques (filière, niveau, centre, année, professeur)
  - Zone upload PDF (glisser-déposer)
  - Colonne gauche infos / colonne droite résumé
- [ ] Coder `app/views/memoires/liste_de.php`

### J3 — Catalogue
- [ ] Coder `app/views/catalogue/index.php`
  - Grille 3 colonnes de cards
  - Chips filtres rapides
  - Barre de recherche
  - Pagination
- [ ] Coder `app/views/catalogue/detail.php`
  - Infos mémoire
  - Bouton like (AJAX)
  - Section commentaires (AJAX)
  - Bouton "Lire le mémoire"

### J4 — JS interactif
- [ ] Coder `public/assets/js/main.js`
  - Toggle sidebar mobile (hamburger)
  - AJAX likes (fetch + mise à jour compteur)
  - AJAX commentaires (fetch + ajout dynamique)
  - Ouverture modale "Passer diplômé"

### J5 — Corrections + responsive
- [ ] Tests visuels sur mobile et desktop
- [ ] Corrections CSS remontées par l'équipe
- [ ] Coder `app/views/shared/flash.php`

---

## 👩‍🎨 PRINCESSE — Front-end

> Vues DE, étudiants, professeurs

### J1 — Login + Shared
- [ ] Coder `app/views/auth/login.php`
  - Carte centrée
  - Badges rôles
  - Responsive
- [ ] Coder `app/views/shared/sidebar.php` (menu dynamique selon rôle)
- [ ] Coder `app/views/shared/topbar.php`
- [ ] Coder `app/views/shared/403.php` + `404.php`

### J2 — Dashboards
- [ ] Coder `app/views/de/dashboard.php`
  - 4 cartes stats
  - Actions rapides
  - Tableau derniers inscrits
- [ ] Coder `app/views/admin/dashboard.php`
- [ ] Coder `app/views/admin/utilisateurs.php`

### J3 — Gestion étudiants
- [ ] Coder `app/views/etudiants/liste.php`
  - Barre recherche
  - Tableau avec bouton "Passer diplômé" sur chaque ligne
  - Formulaire création en bas (infos perso + inscription)
- [ ] Coder `app/views/etudiants/modal_diplome.php`
  - Overlay sombre
  - Radio buttons L3 / M1 / M2
  - Champ année diplôme
  - Avertissement orange

### J4 — Gestion professeurs
- [ ] Coder `app/views/professeurs/liste.php`
  - Tableau + formulaire création
- [ ] Coder `app/views/professeurs/dashboard.php`
  - 4 cartes stats
  - Onglets (en attente / validés / refusés / tous)
  - Tableau avec boutons Lire / Valider / Refuser

### J5 — Corrections
- [ ] Corrections + responsive sur toutes ses vues
- [ ] Support [Sans Nom] si besoin

---

## 👨‍🎨 [SANS NOM] — Front-end

> Vues lecteur PDF, référentiel, étudiant

### J1 — Layouts
- [ ] Coder `app/views/layouts/pdf.php`
  - Fond sombre (`#111827`)
  - Sidebar sombre réduite
  - Zone canvas PDF.js (gauche)
  - Panel infos + likes + commentaires (droite)
- [ ] Coder `app/views/layouts/auth.php`

### J2 — Référentiel
- [ ] Coder `app/views/referentiel/index.php`
  - 3 panels côte à côte (filières / niveaux / centres)
  - Champ ajout + liste avec bouton supprimer sur chaque item
  - Panel années académiques en bas

### J3 — Validation professeur + étudiant
- [ ] Coder `app/views/professeurs/validation.php`
  - Header mémoire + badge statut
  - Visionneuse PDF (canvas PDF.js)
  - Panel décision (commentaire + boutons Valider / Refuser)
- [ ] Coder `app/views/etudiant/dashboard.php`
  - 4 cartes stats
  - Bloc statut mémoire
  - Récemment consultés

### J4 — Vues restantes
- [ ] Coder `app/views/etudiant/mon_memoire.php`
- [ ] Coder `app/views/memoires/liste_prof.php`
  - Onglets en attente / validés / refusés
  - Tableau avec actions
- [ ] Coder `app/views/memoires/soumettre.php` ← PHASE 2
- [ ] Intégrer `pdfviewer.js` dans le layout PDF (avec Eliel)

### J5 — Corrections
- [ ] Tests visuels lecteur PDF sur mobile
- [ ] Corrections remontées

---

## 🔗 DÉPENDANCES CRITIQUES

| Dev | Attend | De qui | Quand |
|-----|--------|--------|-------|
| Tout le monde | `core/` + `config/` + routes | Eliel | Fin J1 |
| Tout le monde | `app/views/layouts/main.php` | Fadel | Fin J1 |
| Fadel | `MemoireController` (selects dynamiques) | Eliel | Fin J2 |
| Fadel | `CatalogueController` | Eliel | Fin J3 |
| [Sans Nom] | `PdfController` fonctionnel | Eliel | Fin J2 |
| Princesse | `EtudiantController` | Hidayath | Fin J2 |
| Hidayath | `core/Database.php` + `core/Auth.php` | Eliel | Fin J1 |

---

## ⚠️ RÈGLES D'ÉQUIPE

- **Zéro SQL dans les vues** — tout passe par les models
- **Zéro `mysqli`** — PDO uniquement
- **Zéro `md5` / `sha1`** — `password_hash()` / `password_verify()` uniquement
- **Zéro PDF en accès direct** — tout passe par `PdfController`
- Eliel valide chaque intégration dans le code commun
