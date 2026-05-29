# 📋 EVOLUTION DU PROJET — Plateforme Mémoires

---

## STRATÉGIE DE COMMITS

- **1 commit feat = 1 fonctionnalité complète livrée**
- Chaque dev travaille sur sa branche et Eliel merge sur main
- Format : `feat(scope): description`
- Les fix ne comptent pas : `fix(scope): description`
- **Objectif : 18 commits feat minimum**

---

## 📋 PHASES DU PROJET

### **PHASE 1 : Archivage et Consultation (Priorité)**
Rendre accessible le catalogue des anciens mémoires archivés par le DE.

1. ✅ **feat: initialisation** — Infrastructure MVC + BD
2. ✅ **feat: authentification** — Login + layouts + dashboard admin
3. ✅ **feat(admin): stats & utilisateurs** — Admin dashboard + search
4. ✅ **feat(archivage): upload sécurisé des anciens mémoires** — DE archivage
5. ➜ **feat(catalogue): liste & détail mémoires publiés + recherche avancée**
6. ➜ **feat(gestion-etudiants): création, inscription, passage diplômé**
7. ➜ **feat(interactions): likes & commentaires AJAX**
8. ➜ **feat(pdf-securise): lecteur PDF.js + proxy sécurisé**

### **PHASE 2 : Soumission Étudiante (Après Phase 1)**
Permettre aux étudiants diplômés de soumettre leurs mémoires.

9. ⏳ **feat(soumission-etudiant): formulaire + stockage + attente validation**
10. ⏳ **feat(validation-professeur): dashboard prof + validation/refus**
11. ⏳ **feat(tests): PHPUnit tests complets**

---

## 🧑‍💻 ELIEL — 5 commits feat

### ✅ feat: initialisation du projet avec architecture MVC, routing, sécurité, base de données et répartition des tâches
**Hash** : `d05c3f28336265b60620ec8bc1d3e610c70df2b3`
**Date** : 26 Mai 2026
**Statut** : ✅ LIVRÉ

### ✅ feat: mise en place de l'authentification, du layout principal et du dashboard admin
**Hash** : `ae76fc7f0469f4adb5d74efb93da067ea86ea7a5`
**Date** : 27 Mai 2026
**Statut** : ✅ LIVRÉ

### ✅ feat(archivage): archivage des anciens mémoires par le DE + upload PDF sécurisé
**Hash** : `da1c9b44dbdb8df6a8f0ca6f6e6c878c4a262947`
**Date** : 27 Mai 2026
**Statut** : ✅ LIVRÉ

### ⏳ feat(catalogue): liste mémoires publiés + recherche avancée + filtres + détail
**Priorité** : 🔴 IMMÉDIATE (Phase 1)
**Statut** : À faire

#### Dépend de
- Archivage ✅ (mémoires en BD)
- Models : Filiere ✅, Niveau ✅, Centre ✅, AnneeAcademique ✅, Professeur ✅

#### Comprend
- Backend : `CatalogueController.php` + `Memoire::getAllPublished()`, `search()`
- Frontend : `catalogue/index.php` + `catalogue/detail.php`

---

### ⏳ feat(interactions): likes + commentaires AJAX
**Priorité** : 🟡 Phase 1 (après catalogue & gestion étudiants)
**Statut** : À faire

#### Dépend de
- Catalogue ✅ (user voit les mémoires)
- Gestion étudiants ✅ (users existent)

#### Comprend
- Backend : `LikeController.php` + `CommentaireController.php`
- Models : `Like.php` ✅, `Commentaire.php` ✅
- Frontend : AJAX dans `main.js`

---

### ⏳ feat(pdf-securise): lecteur PDF.js + proxy PHP sécurisé
**Priorité** : 🟡 Phase 1 (après interactions)
**Statut** : À faire

#### Dépend de
- Interactions ✅ (on peut commenter en lisant)

#### Comprend
- Backend : `PdfController::servir()`
- Frontend : `layouts/pdf.php` + intégration PDF.js

---

## 🎨 FADEL — Front-end (3 commits feat)

## 👩‍💻 HIDAYATH — 4 commits feat

### ⏳ feat(referentiel): gestion filières, niveaux, centres, années académiques
**Priorité** : 🟢 En cours (partiellement fait)
**Statut** : À finir

#### Actuellement fait
- `ReferentielController` : `index()`, `creerFiliere()`, `supprimerFiliere()`, `creerNiveau()`
- Models : `Filiere` ✅, `Niveau` ✅, `Centre` ✅, `AnneeAcademique` ✅

#### Manque
- `ReferentielController` : `creerCentre()`, `supprimerCentre()`, `creerAnnee()`, `supprimerAnnee()`

---

### ⏳ feat(gestion-etudiants): création compte + inscription + passage diplômé + liste
**Priorité** : 🔴 IMMÉDIATE (Phase 1, après catalogue)
**Statut** : À faire

#### Dépend de
- Archivage ✅ (BD prête)
- Référentiel partiellement ✅

#### Comprend
- Backend : `EtudiantController.php` (liste, creer, diplomer)
- Models : `Etudiant.php`, `Inscription.php`
- Frontend : `etudiants/liste.php`, `etudiants/modal_diplome.php`

---

### ⏳ feat(professeur-liste-validation): création + liste + dashboard + validation/refus
**Priorité** : 🟡 Phase 2 (après Phase 1)
**Statut** : À faire

#### Actuellement fait
- `ProfesseurController` : `liste()`, `creer()`

#### Manque
- `ProfesseurController` : `dashboard()`, `listeMemoiresProf()`, `valider()`, `refuser()`
- Vues : `professeurs/liste.php`, `professeurs/dashboard.php`

---

### ⏳ feat(tests): PHPUnit — tests complets
**Priorité** : 🟢 Fin Phase 1
**Statut** : À faire

---

## 🎨 FADEL — Front-end lead (3 commits feat)

> Vues du catalogue et des interactions — complexité élevée

### ⏳ feat(ui-catalogue): vues catalogue + détail mémoire + chips filtres
**Priorité** : 🔴 IMMÉDIATE (après CatalogueController)
**Statut** : À faire

#### Dépend de
- `CatalogueController.php` ✅

#### Comprend
- `app/views/catalogue/index.php`
  - Grille 3 colonnes de cards mémoires
  - Barre recherche (simple + avancée)
  - Chips filtres rapides (filière / niveau / centre / année / professeur)
  - Pagination
  - Affiche : titre, auteur, filière, niveau, centre, année, prof, date, stats
- `app/views/catalogue/detail.php`
  - Infos complètes du mémoire
  - Bouton like (AJAX + compteur)
  - Section commentaires (AJAX + liste dynamique)
  - Bouton "Lire le mémoire" → link vers `/pdf/{id}`
  - Sidebar avec infos académiques

---

### ⏳ feat(ui-interactions): AJAX likes + commentaires + animations
**Priorité** : 🟡 Phase 1 (après interactions backend)
**Statut** : À faire

#### Dépend de
- `LikeController.php`, `CommentaireController.php`

#### Comprend
- Mise à jour `public/assets/js/main.js`
  - Toggle like (fetch POST → `/like/{id}`, update compteur)
  - Ajouter commentaire (fetch POST → `/commentaire/{id}`, add dynamique)
  - Validation formulaire commentaire

---

### ⏳ feat(ui-memoire-archivage): vues archivage DE + liste mémoires
**Priorité** : 🟢 Peut être fait en parallèle
**Statut** : À faire

#### Comprend
- `app/views/memoires/archiver.php` (complète)
  - Tous les selects dynamiques (filière, niveau, centre, année, professeur)
  - Zone upload PDF (glisser-déposer)
  - Colonne gauche infos / colonne droite résumé
- `app/views/memoires/liste_de.php`
  - Tableau avec statuts (archivés)
  - Actions rapides

---

## 👩‍🎨 PRINCESSE — Front-end (3 commits feat)

> Vues DE, étudiants, professeurs

### ⏳ feat(ui-gestion-etudiants): liste + création + modale diplôme
**Priorité** : 🔴 IMMÉDIATE (après EtudiantController)
**Statut** : À faire

#### Dépend de
- `EtudiantController.php`

#### Comprend
- `app/views/etudiants/liste.php`
  - Tableau tous les étudiants (matricule, nom, prenom, filière, niveau, centre, années, type)
  - Barre recherche + filter
  - Bouton "Passer diplômé" sur chaque ligne (ouvre modale)
  - Formulaire création
- `app/views/etudiants/modal_diplome.php`
  - Overlay sombre
  - Radio buttons : L3 / M1 / M2
  - Champ année diplôme
  - Avertissement orange

---

### ⏳ feat(ui-dashboards-de): dashboard DE + stats + actions rapides
**Priorité** : 🟡 Phase 1 (après modèles métier)
**Statut** : À faire

#### Comprend
- `app/views/de/dashboard.php`
  - 4 cartes stats
  - Actions rapides
  - Tableau derniers inscrits/mémoires

---

### ⏳ feat(ui-professeur): liste + dashboard + validation
**Priorité** : 🟡 Phase 2 (après Phase 1)
**Statut** : À faire

#### Comprend
- `app/views/professeurs/liste.php`
  - Tableau + formulaire création
- `app/views/professeurs/dashboard.php`
  - Stats + onglets (en attente / validés / refusés)
  - Tableau avec actions

---

## 📊 RÉSUMÉ ACTUEL (fin mai 2026)

| Dev | Commits feat | Livrés | Restants | État |
|-----|-------------|--------|----------|------|
| Eliel | 5 | 4 | 1 | Catalogue backend ✅ |
| Hidayath | 4 | 0 | 4 | À faire |
| Fadel | 3 | 0 | 3 | À faire |
| Princesse | 3 | 0 | 3 | À faire |
| **Total** | **15** | **4** | **11** | **Phase 1 en cours** |

