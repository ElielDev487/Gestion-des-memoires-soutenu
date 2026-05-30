<?php
// File : app/controllers/EtudiantController.php
// Contrôleur principal pour la gestion des étudiants
// Fonctionnalités : création compte, inscription, liste, passage diplômé
require_once __DIR__ . '/../models/Etudiant.php';
require_once __DIR__ . '/../models/Inscription.php';
class EtudiantController {
    private Etudiant $etudiant;
    private Inscription $inscription;
    private PDO $db;
    public function __construct() {
        $this->db          = Database::getInstance()->getConnection();
        $this->etudiant    = new Etudiant();
        $this->inscription = new Inscription();
    }
    // =========================================================
    //  CREATION DE COMPTE ETUDIANT
    // =========================================================
    /**
     * Crée le compte utilisateur + profil étudiant + inscription en une transaction
     * Le mot de passe est hashé avec PASSWORD_DEFAULT (bcrypt)
     * Retourne ['succes' => bool, 'message'|'erreurs' => ...]
     */
    public function creerCompte(array $post): array {
        $erreurs = $this->validerCreationCompte($post);
        if (!empty($erreurs)) return ['succes' => false, 'erreurs' => $erreurs];
        // Vérification doublon matricule
        if ($this->etudiant->existsByMatricule($post['matricule'])) {
            return ['succes' => false, 'erreurs' => ['Ce matricule est déjà utilisé.']];
        }
        // Vérification doublon email
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) as total FROM utilisateur WHERE email = ?'
        );
        $stmt->execute([trim($post['email'])]);
        if ($stmt->fetch()['total'] > 0) {
            return ['succes' => false, 'erreurs' => ['Cet email est déjà utilisé.']];
        }
        try {
            $this->db->beginTransaction();
            // 1. Créer le compte utilisateur avec mot de passe hashé
            $mot_de_passe_hash = password_hash($post['mot_de_passe'], PASSWORD_DEFAULT);
            $stmt = $this->db->prepare(
                "INSERT INTO utilisateur (email, mot_de_passe, statut, role)
                 VALUES (?, ?, 'actif', 'etudiant')"
            );
            $stmt->execute([trim($post['email']), $mot_de_passe_hash]);
            $id_utilisateur = (int) $this->db->lastInsertId();
            // 2. Créer le profil étudiant
            $id_etudiant = $this->etudiant->create(
                $post['matricule'],
                $post['nom'],
                $post['prenom'],
                $post['telephone'] ?? '',
                $id_utilisateur
            );
            // 3. Créer l'inscription
            $this->inscription->create(
                $id_etudiant,
                (int) $post['id_filiere'],
                (int) $post['id_niveau'],
                (int) $post['id_centre'],
                (int) $post['id_annee_academique'],
                $post['type_etudiant'],
                $post['niveau_diplome'],
                false // peut_soumettre = false par défaut, activé par l'admin
            );
            $this->db->commit();
            return ['succes' => true, 'message' => 'Compte créé avec succès.',
                    'id_etudiant' => $id_etudiant];
        } catch (Exception $e) {
            $this->db->rollBack();
            return ['succes' => false, 'erreurs' => ['Erreur lors de la création du compte.']];
        }
    }
    // =========================================================
    //  LISTE DES ETUDIANTS
    // =========================================================
    /**
     * Retourne la liste complète des étudiants avec leurs infos d'inscription
     * Utilisé dans : tableau de bord admin, direction des études
     */
    public function lister(): array {
        return $this->etudiant->getAll();
    }
    /**
     * Retourne le détail complet d'un étudiant avec son inscription
     * Utilisé dans : fiche étudiant
     */
    public function detail(int $id): ?array {
        return $this->etudiant->getById($id);
    }
    // =========================================================
    //  MODIFICATION DE COMPTE
    // =========================================================
    /**
     * Met à jour les informations personnelles d'un étudiant
     * Retourne ['succes' => bool, 'message'|'erreurs' => ...]
     */
    public function modifierCompte(int $id, array $post): array {
        $erreurs = $this->validerModification($post);
        if (!empty($erreurs)) return ['succes' => false, 'erreurs' => $erreurs];
        if ($this->etudiant->update($id, $post['nom'], $post['prenom'], $post['telephone'] ?? '')) {
            return ['succes' => true, 'message' => 'Informations mises à jour.'];
        }
        return ['succes' => false, 'erreurs' => ['Erreur lors de la mise à jour.']];
    }
    // =========================================================
    //  INSCRIPTION
    // =========================================================
    /**
     * Met à jour l'inscription d'un étudiant (filière, niveau, centre, année)
     * Retourne ['succes' => bool, 'message'|'erreurs' => ...]
     */
    public function modifierInscription(int $id_inscription, array $post): array {
        $erreurs = $this->validerInscription($post);
        if (!empty($erreurs)) return ['succes' => false, 'erreurs' => $erreurs];
        if ($this->inscription->update(
            $id_inscription,
            (int) $post['id_filiere'],
            (int) $post['id_niveau'],
            (int) $post['id_centre'],
            (int) $post['id_annee_academique'],
            $post['type_etudiant'],
            $post['niveau_diplome'],
            isset($post['peut_soumettre'])
        )) {
            return ['succes' => true, 'message' => 'Inscription mise à jour.'];
        }
        return ['succes' => false, 'erreurs' => ['Erreur lors de la mise à jour.']];
    }
    /**
     * Active ou désactive le droit de soumission de mémoire d'un étudiant
     * Utilisé dans : gestion des droits par l'administration
     */
    public function togglePeutSoumettre(int $id_etudiant, bool $valeur): array {
        if ($this->inscription->setPeutSoumettre($id_etudiant, $valeur)) {
            $msg = $valeur ? 'Soumission de mémoire activée.' : 'Soumission de mémoire désactivée.';
            return ['succes' => true, 'message' => $msg];
        }
        return ['succes' => false, 'erreurs' => ['Erreur lors de la mise à jour.']];
    }
    // =========================================================
    //  PASSAGE DIPLOME
    // =========================================================
    /**
     * Marque un étudiant comme diplômé
     * Met son statut à "diplome" et désactive peut_soumettre
     * Retourne ['succes' => bool, 'message'|'erreurs' => ...]
     */
    public function passerDiplome(int $id): array {
        // Vérifier que l'étudiant existe
        $etudiant = $this->etudiant->getById($id);
        if (!$etudiant) {
            return ['succes' => false, 'erreurs' => ['Étudiant introuvable.']];
        }
        // Vérifier qu'il n'est pas déjà diplômé
        if ($etudiant['statut'] === 'diplome') {
            return ['succes' => false, 'erreurs' => ['Cet étudiant est déjà diplômé.']];
        }
        if ($this->etudiant->passerDiplome($id)) {
            return ['succes' => true, 'message' => 'Étudiant marqué comme diplômé.'];
        }
        return ['succes' => false, 'erreurs' => ['Erreur lors du passage diplômé.']];
    }
    // =========================================================
    //  SUPPRESSION
    // =========================================================
    /**
     * Supprime un étudiant si aucun mémoire n'y est lié
     * Retourne ['succes' => bool, 'message'|'erreurs' => ...]
     */
    public function supprimer(int $id): array {
        if ($this->etudiant->isUsed($id)) {
            return ['succes' => false, 'erreurs' => ['Cet étudiant a des mémoires liés, suppression impossible.']];
        }
        if ($this->etudiant->delete($id)) {
            return ['succes' => true, 'message' => 'Étudiant supprimé.'];
        }
        return ['succes' => false, 'erreurs' => ['Erreur lors de la suppression.']];
    }
    // =========================================================
    //  VALIDATIONS PRIVEES
    // =========================================================
    /**
     * Valide les données de création de compte
     * Retourne un tableau d'erreurs (vide si tout est valide)
     */
    private function validerCreationCompte(array $post): array {
        $e = [];
        if (empty(trim($post['matricule']   ?? ''))) $e[] = 'Le matricule est obligatoire.';
        if (empty(trim($post['nom']         ?? ''))) $e[] = 'Le nom est obligatoire.';
        if (empty(trim($post['prenom']      ?? ''))) $e[] = 'Le prénom est obligatoire.';
        if (empty(trim($post['email']       ?? ''))) $e[] = "L'email est obligatoire.";
        if (empty(trim($post['mot_de_passe']?? ''))) $e[] = 'Le mot de passe est obligatoire.';
        if (!empty($post['email']) && !filter_var($post['email'], FILTER_VALIDATE_EMAIL)) {
            $e[] = "L'adresse email est invalide.";
        }
        if (!empty($post['mot_de_passe']) && strlen($post['mot_de_passe']) < 8) {
            $e[] = 'Le mot de passe doit contenir au moins 8 caractères.';
        }
        if (empty($post['id_filiere']))          $e[] = 'La filière est obligatoire.';
        if (empty($post['id_niveau']))           $e[] = 'Le niveau est obligatoire.';
        if (empty($post['id_centre']))           $e[] = 'Le centre est obligatoire.';
        if (empty($post['id_annee_academique'])) $e[] = "L'année académique est obligatoire.";
        if (empty(trim($post['type_etudiant']  ?? ''))) $e[] = 'Le type étudiant est obligatoire.';
        if (empty(trim($post['niveau_diplome'] ?? ''))) $e[] = 'Le niveau diplôme est obligatoire.';
        return $e;
    }
    /**
     * Valide les données de modification du profil
     * Retourne un tableau d'erreurs (vide si tout est valide)
     */
    private function validerModification(array $post): array {
        $e = [];
        if (empty(trim($post['nom']    ?? ''))) $e[] = 'Le nom est obligatoire.';
        if (empty(trim($post['prenom'] ?? ''))) $e[] = 'Le prénom est obligatoire.';
        return $e;
    }
    /**
     * Valide les données de modification d'inscription
     * Retourne un tableau d'erreurs (vide si tout est valide)
     */
    private function validerInscription(array $post): array {
        $e = [];
        if (empty($post['id_filiere']))          $e[] = 'La filière est obligatoire.';
        if (empty($post['id_niveau']))           $e[] = 'Le niveau est obligatoire.';
        if (empty($post['id_centre']))           $e[] = 'Le centre est obligatoire.';
        if (empty($post['id_annee_academique'])) $e[] = "L'année académique est obligatoire.";
        if (empty(trim($post['type_etudiant']  ?? ''))) $e[] = 'Le type étudiant est obligatoire.';
        if (empty(trim($post['niveau_diplome'] ?? ''))) $e[] = 'Le niveau diplôme est obligatoire.';
        return $e;
    }
}