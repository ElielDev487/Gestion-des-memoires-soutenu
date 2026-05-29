<?php
// File : app/controllers/CatalogueController.php
// Catalogue public des mémoires — accessible à tous les utilisateurs connectés

class CatalogueController {

    /**
     * Page principale du catalogue
     * Gère aussi la recherche avancée via les paramètres GET
     * index() et recherche() fusionnés — même logique, filtres optionnels
     */
    public function index(): void {
        Auth::requireLogin();

        // ── Paramètres de recherche et filtres ──────────────────
        $q             = trim($_GET['q']             ?? '');
        $id_filiere    = intval($_GET['id_filiere']    ?? 0);
        $id_niveau     = intval($_GET['id_niveau']     ?? 0);
        $id_centre     = intval($_GET['id_centre']     ?? 0);
        $id_annee      = intval($_GET['id_annee']      ?? 0);
        $id_professeur = intval($_GET['id_professeur'] ?? 0);
        $page          = max(1, intval($_GET['page']   ?? 1));
        $perPage       = 12;

        // ── Construction du tableau de filtres ──────────────────
        $filtres = [];
        if (!empty($q))         $filtres['q']             = $q;
        if ($id_filiere > 0)    $filtres['id_filiere']    = $id_filiere;
        if ($id_niveau > 0)     $filtres['id_niveau']     = $id_niveau;
        if ($id_centre > 0)     $filtres['id_centre']     = $id_centre;
        if ($id_annee > 0)      $filtres['id_annee']      = $id_annee;
        if ($id_professeur > 0) $filtres['id_professeur'] = $id_professeur;

        // ── Données pour les selects de filtres ─────────────────
        $filieres    = (new Filiere())->getAll();
        $niveaux     = (new Niveau())->getAll();
        $centres     = (new Centre())->getAll();
        $annees      = (new AnneeAcademique())->getAll();
        $professeurs = (new Professeur())->getAll();

        // ── Pagination SQL (performant) ──────────────────────────
        $memoire       = new Memoire();
        $totalMemoires = $memoire->countPublies($filtres);
        $totalPages    = max(1, (int) ceil($totalMemoires / $perPage));

        // Assure que la page demandée ne dépasse pas le total
        $page   = min($page, $totalPages);
        $offset = ($page - 1) * $perPage;

        $memoires = $memoire->getAllPublies($filtres, $perPage, $offset);

        // ── Indique si on est en mode recherche (pour la vue) ───
        $isRecherche = !empty($filtres);

        require_once ROOT_PATH . '/app/views/catalogue/index.php';
    }

    /**
     * Page détail d'un mémoire
     * Affiche toutes les infos, les likes et les commentaires
     * Accessible uniquement si le mémoire est publié
     */
    public function detail(int $id): void {
        Auth::requireLogin();

        $memoire = new Memoire();
        $memo    = $memoire->getById($id);

        // Vérifier que le mémoire existe et est publié
        if (!$memo || $memo['statut'] !== 'publie') {
            http_response_code(404);
            require_once ROOT_PATH . '/app/views/shared/404.php';
            return;
        }

        // Récupérer les commentaires du mémoire
        $commentaireModel = new Commentaire();
        $commentaires     = $commentaireModel->getByMemoire($id);

        // Vérifier si l'utilisateur connecté a déjà liké ce mémoire
        $likeModel     = new Like();
        $currentUserId = Session::get('id_utilisateur');
        $hasLiked      = $likeModel->hasLiked($id, $currentUserId);

        require_once ROOT_PATH . '/app/views/catalogue/detail.php';
    }
}