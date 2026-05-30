<?php
// File : app/controllers/ReferentielController.php
// Gestion des filières, niveaux, centres et années académiques
// Accessible uniquement par le rôle DE

class ReferentielController {

    public function index(): void {
        Auth::requireRole('de');

        // Chargement de toutes les données pour les 4 panels
        $filiere         = new Filiere();
        $niveau          = new Niveau();
        $centre          = new Centre();
        $anneeAcademique = new AnneeAcademique();

        $filieres = $filiere->getAll();
        $niveaux  = $niveau->getAll();
        $centres  = $centre->getAll();
        $annees   = $anneeAcademique->getAll();

        require_once ROOT_PATH . '/app/views/referentiel/index.php';
    }

    // ── FILIÈRES ────────────────────────────────────────────────

    public function creerFiliere(): void {
        Auth::requireRole('de');

        $nom    = trim($_POST['nom_filiere'] ?? '');
        $filiere = new Filiere();

        if (empty($nom)) {
            Session::flash('error', 'Le nom de la filière est obligatoire.');
        } elseif ($filiere->existsByNom($nom)) {
            Session::flash('error', 'Cette filière existe déjà.');
        } else {
            $filiere->create($nom);
            Session::flash('success', 'Filière "' . htmlspecialchars($nom) . '" créée avec succès.');
        }

        header('Location: ' . BASE_URL . '/de/referentiel');
        exit;
    }

    public function supprimerFiliere(int $id): void {
        Auth::requireRole('de');

        $filiere = new Filiere();

        if ($filiere->isUsed($id)) {
            Session::flash('error', 'Impossible de supprimer cette filière — elle est utilisée dans des inscriptions ou des mémoires.');
        } else {
            $filiere->delete($id);
            Session::flash('success', 'Filière supprimée avec succès.');
        }

        header('Location: ' . BASE_URL . '/de/referentiel');
        exit;
    }

    // ── NIVEAUX ─────────────────────────────────────────────────

    public function creerNiveau(): void {
        Auth::requireRole('de');

        $libelle = trim($_POST['libelle'] ?? '');
        $niveau  = new Niveau();

        if (empty($libelle)) {
            Session::flash('error', 'Le libellé du niveau est obligatoire.');
        } elseif ($niveau->existsByLibelle($libelle)) {
            Session::flash('error', 'Ce niveau existe déjà.');
        } else {
            $niveau->create($libelle);
            Session::flash('success', 'Niveau "' . htmlspecialchars($libelle) . '" créé avec succès.');
        }

        header('Location: ' . BASE_URL . '/de/referentiel');
        exit;
    }

    public function supprimerNiveau(int $id): void {
        Auth::requireRole('de');

        $niveau = new Niveau();

        if ($niveau->isUsed($id)) {
            Session::flash('error', 'Impossible de supprimer ce niveau — il est utilisé dans des inscriptions ou des mémoires.');
        } else {
            $niveau->delete($id);
            Session::flash('success', 'Niveau supprimé avec succès.');
        }

        header('Location: ' . BASE_URL . '/de/referentiel');
        exit;
    }

    // ── CENTRES ─────────────────────────────────────────────────

    public function creerCentre(): void {
        Auth::requireRole('de');

        $nom    = trim($_POST['nom_centre'] ?? '');
        $centre = new Centre();

        if (empty($nom)) {
            Session::flash('error', 'Le nom du centre est obligatoire.');
        } elseif ($centre->existsByNom($nom)) {
            Session::flash('error', 'Ce centre existe déjà.');
        } else {
            $centre->create($nom);
            Session::flash('success', 'Centre "' . htmlspecialchars($nom) . '" créé avec succès.');
        }

        header('Location: ' . BASE_URL . '/de/referentiel');
        exit;
    }

    public function supprimerCentre(int $id): void {
        Auth::requireRole('de');

        $centre = new Centre();

        if ($centre->isUsed($id)) {
            Session::flash('error', 'Impossible de supprimer ce centre — il est utilisé dans des inscriptions ou des mémoires.');
        } else {
            $centre->delete($id);
            Session::flash('success', 'Centre supprimé avec succès.');
        }

        header('Location: ' . BASE_URL . '/de/referentiel');
        exit;
    }

    // ── ANNÉES ACADÉMIQUES ──────────────────────────────────────

    public function creerAnnee(): void {
        Auth::requireRole('de');

        $libelle         = trim($_POST['libelle'] ?? '');
        $anneeAcademique = new AnneeAcademique();

        if (empty($libelle)) {
            Session::flash('error', 'Le libellé de l\'année académique est obligatoire.');
        } elseif ($anneeAcademique->existsByLibelle($libelle)) {
            Session::flash('error', 'Cette année académique existe déjà.');
        } else {
            $anneeAcademique->create($libelle);
            Session::flash('success', 'Année académique "' . htmlspecialchars($libelle) . '" créée avec succès.');
        }

        header('Location: ' . BASE_URL . '/de/referentiel');
        exit;
    }

    public function supprimerAnnee(int $id): void {
        Auth::requireRole('de');

        $anneeAcademique = new AnneeAcademique();

        if ($anneeAcademique->isUsed($id)) {
            Session::flash('error', 'Impossible de supprimer cette année académique — elle est utilisée dans des inscriptions ou des mémoires.');
        } else {
            $anneeAcademique->delete($id);
            Session::flash('success', 'Année académique supprimée avec succès.');
        }

        header('Location: ' . BASE_URL . '/de/referentiel');
        exit;
    }
}