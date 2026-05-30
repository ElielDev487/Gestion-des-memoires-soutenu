<?php
// File : app/models/Etudiant.php
// Gestion des étudiants en base de données
// CRUD complet : getAll, getById, create, update, delete + passage diplômé
class Etudiant {
    private PDO $db;
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    /**
     * Retourne tous les étudiants avec leur inscription courante
     * Utilisé dans : liste des étudiants (admin, direction)
     */
    public function getAll(): array {
        $stmt = $this->db->query(
            'SELECT e.id_etudiant, e.matricule, e.nom, e.prenom, e.telephone,
                    u.email, u.statut,
                    i.type_etudiant, i.niveau_diplome, i.peut_soumettre,
                    f.nom_filiere, n.libelle AS niveau,
                    c.nom_centre, a.libelle AS annee_academique
             FROM etudiant e
             INNER JOIN utilisateur u         ON u.id_utilisateur    = e.id_utilisateur
             LEFT JOIN  inscription i         ON i.id_etudiant       = e.id_etudiant
             LEFT JOIN  filiere f             ON f.id_filiere        = i.id_filiere
             LEFT JOIN  niveau n              ON n.id_niveau         = i.id_niveau
             LEFT JOIN  centre c              ON c.id_centre         = i.id_centre
             LEFT JOIN  annee_academique a    ON a.id_annee_academique = i.id_annee_academique
             ORDER BY e.nom ASC'
        );
        return $stmt->fetchAll();
    }
    /**
     * Retourne un étudiant par son id avec toutes ses informations
     * Utilisé dans : fiche détail, vérification avant update/delete
     */
    public function getById(int $id): ?array {
        $stmt = $this->db->prepare(
            'SELECT e.id_etudiant, e.matricule, e.nom, e.prenom, e.telephone,
                    u.email, u.statut,
                    i.id_inscription, i.id_filiere, i.id_niveau, i.id_centre,
                    i.id_annee_academique, i.type_etudiant, i.niveau_diplome, i.peut_soumettre
             FROM etudiant e
             INNER JOIN utilisateur u      ON u.id_utilisateur    = e.id_utilisateur
             LEFT JOIN  inscription i      ON i.id_etudiant       = e.id_etudiant
             WHERE e.id_etudiant = ?'
        );
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }
    /**
     * Retourne un étudiant par son id_utilisateur
     * Utilisé dans : après login pour récupérer le profil étudiant
     */
    public function getByUtilisateur(int $id_utilisateur): ?array {
        $stmt = $this->db->prepare(
            'SELECT e.id_etudiant, e.matricule, e.nom, e.prenom, e.telephone,
                    u.email, u.statut
             FROM etudiant e
             INNER JOIN utilisateur u ON u.id_utilisateur = e.id_utilisateur
             WHERE e.id_utilisateur = ?'
        );
        $stmt->execute([$id_utilisateur]);
        $result = $stmt->fetch();
        return $result ?: null;
    }
    /**
     * Crée le profil étudiant (après création du compte utilisateur)
     * Retourne l'id inséré ou false si échec
     */
    public function create(string $matricule, string $nom, string $prenom,
                           string $telephone, int $id_utilisateur): int|false {
        $stmt = $this->db->prepare(
            'INSERT INTO etudiant (matricule, nom, prenom, telephone, id_utilisateur)
             VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            trim($matricule),
            trim($nom),
            trim($prenom),
            trim($telephone),
            $id_utilisateur
        ]);
        return (int) $this->db->lastInsertId();
    }
    /**
     * Met à jour les informations personnelles d'un étudiant
     * Retourne true si succès, false si échec
     */
    public function update(int $id, string $nom, string $prenom,
                           string $telephone): bool {
        $stmt = $this->db->prepare(
            'UPDATE etudiant
             SET nom = ?, prenom = ?, telephone = ?
             WHERE id_etudiant = ?'
        );
        return $stmt->execute([trim($nom), trim($prenom), trim($telephone), $id]);
    }
    /**
     * Supprime un étudiant par son id
     * ⚠️ Vérifier avant qu'aucun mémoire ou inscription n'y est lié
     * Retourne true si succès, false si échec
     */
    public function delete(int $id): bool {
        $stmt = $this->db->prepare(
            'DELETE FROM etudiant
             WHERE id_etudiant = ?'
        );
        return $stmt->execute([$id]);
    }
    /**
     * Marque un étudiant comme diplômé (statut utilisateur = diplome)
     * Désactive aussi peut_soumettre dans son inscription
     * Retourne true si succès, false si échec
     */
    public function passerDiplome(int $id): bool {
        // Mise à jour du statut dans utilisateur
        $stmt = $this->db->prepare(
            "UPDATE utilisateur u
             INNER JOIN etudiant e ON e.id_utilisateur = u.id_utilisateur
             SET u.statut = 'diplome'
             WHERE e.id_etudiant = ?"
        );
        if (!$stmt->execute([$id])) return false;
        // Désactivation de la soumission de mémoire
        $stmt = $this->db->prepare(
            'UPDATE inscription
             SET peut_soumettre = 0
             WHERE id_etudiant = ?'
        );
        return $stmt->execute([$id]);
    }
    /**
     * Vérifie si un matricule existe déjà
     * Utilisé avant create pour éviter les doublons
     */
    public function existsByMatricule(string $matricule): bool {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) as total
             FROM etudiant
             WHERE matricule = ?'
        );
        $stmt->execute([trim($matricule)]);
        return $stmt->fetch()['total'] > 0;
    }
    /**
     * Vérifie si un étudiant est lié à un mémoire
     * Utilisé avant delete pour éviter les erreurs de contrainte FK
     */
    public function isUsed(int $id): bool {
        // Vérification dans les mémoires
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) as total
             FROM memoire
             WHERE id_etudiant = ?'
        );
        $stmt->execute([$id]);
        return $stmt->fetch()['total'] > 0;
    }
}