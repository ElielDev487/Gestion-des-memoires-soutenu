<?php
// File : app/controllers/MemoireController.php
// Gestion des mémoires : archivage DE, soumission étudiant, dashboard

class MemoireController {

    public function listeDe(): void {
        Auth::requireRole('de');

        $memoire  = new Memoire();
        $memoires = $memoire->getAllForDE();

        require_once ROOT_PATH . '/app/views/memoires/liste_de.php';
    }

    public function showArchiver(): void {
        Auth::requireRole('de');

        // Chargement des données pour les selects du formulaire
        $filiere         = new Filiere();
        $niveau          = new Niveau();
        $centre          = new Centre();
        $anneeAcademique = new AnneeAcademique();
        $professeur      = new Professeur();

        $filieres    = $filiere->getAll();
        $niveaux     = $niveau->getAll();
        $centres     = $centre->getAll();
        $annees      = $anneeAcademique->getAll();
        $professeurs = $professeur->getAll();

        require_once ROOT_PATH . '/app/views/memoires/archiver.php';
    }

    public function archiver(): void {
        Auth::requireRole('de');

        $errors = [];

        // Validation des champs obligatoires
        $titre        = trim($_POST['titre']        ?? '');
        $theme        = trim($_POST['theme']        ?? '');
        $resume       = trim($_POST['resume']       ?? '');
        $auteur_nom   = trim($_POST['auteur_nom']   ?? '');
        $auteur_prenom = trim($_POST['auteur_prenom'] ?? '');
        $id_filiere   = intval($_POST['id_filiere']   ?? 0);
        $id_niveau    = intval($_POST['id_niveau']    ?? 0);
        $id_centre    = intval($_POST['id_centre']    ?? 0);
        $id_annee     = intval($_POST['id_annee']     ?? 0);
		// Professeur — optionnel
		// Si un id est sélectionné via autocomplete → on utilise l'id
		// Si un nom libre est saisi → on le stocke dans directeur_nom
		$id_professeur = intval($_POST['id_professeur'] ?? 0);
		$directeur_nom = trim($_POST['directeur_nom_libre'] ?? '');

		// Si prof sélectionné dans la liste, récupérer son nom complet
		if ($id_professeur > 0) {
			$prof          = new Professeur();
			$profData      = $prof->getById($id_professeur);
			$directeur_nom = $profData
				? $profData['prenom'] . ' ' . $profData['nom']
				: $directeur_nom;
			$id_professeur = $id_professeur;
		} else {
			// Pas de prof sélectionné — on garde le nom libre saisi
			$id_professeur = null;
		}

        if (empty($titre))         $errors[] = 'Le titre est obligatoire.';
        if (empty($theme))         $errors[] = 'Le thème est obligatoire.';
        if (empty($auteur_nom))    $errors[] = 'Le nom de l\'auteur est obligatoire.';
        if (empty($auteur_prenom)) $errors[] = 'Le prénom de l\'auteur est obligatoire.';
        if ($id_filiere === 0)     $errors[] = 'La filière est obligatoire.';
        if ($id_niveau === 0)      $errors[] = 'Le niveau est obligatoire.';
        if ($id_centre === 0)      $errors[] = 'Le centre est obligatoire.';
        if ($id_annee === 0)       $errors[] = 'L\'année académique est obligatoire.';
        $id_professeur = $id_professeur === 0 ? null : $id_professeur;

        // Validation et upload du PDF
        $fichier_pdf = '';

        if (empty($_FILES['fichier_pdf']['name'])) {
            $errors[] = 'Le fichier PDF est obligatoire.';
        } else {
            $file     = $_FILES['fichier_pdf'];
            $mime     = mime_content_type($file['tmp_name']);
            $size     = $file['size'];
            $ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

            if ($mime !== 'application/pdf' || $ext !== 'pdf') {
                $errors[] = 'Le fichier doit être un PDF.';
            } elseif ($size > MAX_UPLOAD_SIZE) {
                $errors[] = 'Le fichier ne doit pas dépasser 10 Mo.';
            } else {
                // Nom unique pour éviter les collisions
                $fichier_pdf = uniqid('memoire_', true) . '.pdf';
                $destination = STORAGE_PATH . $fichier_pdf;

                if (!move_uploaded_file($file['tmp_name'], $destination)) {
                    $errors[] = 'Erreur lors de l\'upload du fichier.';
                    $fichier_pdf = '';
                }
            }
        }

        // Si erreurs : recharger le formulaire 
        if (!empty($errors)) {
            // Recharger les données des selects
            $filiere         = new Filiere();
            $niveau          = new Niveau();
            $centre          = new Centre();
            $anneeAcademique = new AnneeAcademique();
            $professeur      = new Professeur();

            $filieres    = $filiere->getAll();
            $niveaux     = $niveau->getAll();
            $centres     = $centre->getAll();
            $annees      = $anneeAcademique->getAll();
            $professeurs = $professeur->getAll();

            require_once ROOT_PATH . '/app/views/memoires/archiver.php';
            return;
        }

        // Enregistrement en base
        $memoire = new Memoire();
        $memoire->archiver([
            'titre'         => $titre,
            'theme'         => $theme,
            'resume'        => $resume,
            'fichier_pdf'   => $fichier_pdf,
            'auteur_nom'    => $auteur_nom,
            'auteur_prenom' => $auteur_prenom,
            'id_filiere'    => $id_filiere,
            'id_niveau'     => $id_niveau,
            'id_centre'     => $id_centre,
            'id_annee'      => $id_annee,
            'id_professeur' => $id_professeur,
    		'directeur_nom' => $directeur_nom,
        ]);

        Session::flash('success', 'Mémoire archivé et publié avec succès.');
        header('Location: ' . BASE_URL . '/de/memoires');
        exit;
    }

    public function dashboardEtudiant(): void {
        Auth::requireRole('etudiant');
        require_once ROOT_PATH . '/app/views/etudiant/dashboard.php';
    }

    public function monMemoire(): void {
        Auth::requireRole('etudiant');
        require_once ROOT_PATH . '/app/views/etudiant/mon_memoire.php';
    }

    public function showSoumettre(): void {
        Auth::requireRole('etudiant');
        require_once ROOT_PATH . '/app/views/memoires/soumettre.php';
    }

    public function soumettre(): void {
        Auth::requireRole('etudiant');
        // Phase 2 — à implémenter
    }
}