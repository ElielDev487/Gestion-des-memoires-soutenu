<?php
// File : app/controllers/ProfesseurController.php
// Gestion des professeurs : création, liste, validation mémoires
// Accessible selon les actions par le rôle DE ou professeur

class ProfesseurController {

    /**
     * Liste des professeurs — accessible par le DE
     */
    public function liste(): void {
        Auth::requireRole('de');

        $professeur  = new Professeur();
        $professeurs = $professeur->getAllWithEmail();

        require_once ROOT_PATH . '/app/views/professeurs/liste.php';
    }

    /**
     * Création d'un compte professeur par le DE
     * Crée d'abord un utilisateur puis le profil professeur lié
     */
    public function creer(): void {
        Auth::requireRole('de');

        $errors = [];

        $nom       = trim($_POST['nom']       ?? '');
        $prenom    = trim($_POST['prenom']    ?? '');
        $email     = trim($_POST['email']     ?? '');
        $telephone = trim($_POST['telephone'] ?? '');
        $specialite = trim($_POST['specialite'] ?? '');

        // Validation
        if (empty($nom))    $errors[] = 'Le nom est obligatoire.';
        if (empty($prenom)) $errors[] = 'Le prénom est obligatoire.';
        if (empty($email))  $errors[] = 'L\'email est obligatoire.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'L\'email n\'est pas valide.';
        }

        if (empty($errors)) {
            // Vérifier si l'email existe déjà
            $utilisateur = new Utilisateur();
            if ($utilisateur->findByEmail($email)) {
                $errors[] = 'Cet email est déjà utilisé.';
            }
        }

        if (!empty($errors)) {
            Session::flash('error', implode(' ', $errors));
            header('Location: ' . BASE_URL . '/de/professeurs');
            exit;
        }

        // Créer le compte utilisateur
        $utilisateur    = new Utilisateur();
        $motDePasse     = password_hash('Memoire2025!', PASSWORD_DEFAULT);
        $id_utilisateur = $utilisateur->create([
            'email'       => $email,
            'mot_de_passe'=> $motDePasse,
            'role'        => 'professeur',
        ]);

        // Créer le profil professeur
        $professeur = new Professeur();
        $professeur->create([
            'nom'           => $nom,
            'prenom'        => $prenom,
            'telephone'     => $telephone,
            'specialite'    => $specialite,
            'id_utilisateur'=> $id_utilisateur,
        ]);

        Session::flash('success', 'Compte professeur créé. Mot de passe par défaut : Memoire2025!');
        header('Location: ' . BASE_URL . '/de/professeurs');
        exit;
    }

    /**
     * Dashboard du professeur — liste ses mémoires à valider
     */
    public function dashboard(): void {
        Auth::requireRole('professeur');
        require_once ROOT_PATH . '/app/views/professeurs/dashboard.php';
    }

    /**
     * Liste des mémoires du professeur connecté
     */
    public function listeMemoiresProf(): void {
        Auth::requireRole('professeur');
        require_once ROOT_PATH . '/app/views/memoires/liste_prof.php';
    }

    /**
     * Valide un mémoire et le publie
     */
    public function valider(int $id): void {
        Auth::requireRole('professeur');
        // Phase 2 — à implémenter
        header('Location: ' . BASE_URL . '/professeur/memoires');
        exit;
    }

    /**
     * Refuse un mémoire
     */
    public function refuser(int $id): void {
        Auth::requireRole('professeur');
        // Phase 2 — à implémenter
        header('Location: ' . BASE_URL . '/professeur/memoires');
        exit;
    }

    /**
     * Recherche AJAX de professeurs par nom/prénom
     * Retourne un JSON — utilisé par l'autocomplete du formulaire d'archivage
     * Route : GET /api/professeurs?q=...
     */
    public function search(): void {
        Auth::requireLogin();

        $q          = trim($_GET['q'] ?? '');
        $professeur = new Professeur();
        $data       = empty($q) ? [] : $professeur->search($q);

        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}