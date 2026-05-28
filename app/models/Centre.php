<?php
// File : app/models/Centre.php
// Gestion des centres en base de données
// CRUD complet : getAll, getById, create, update, delete
class Centre {
    private PDO $db;
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    /**
     * Retourne tous les centres triés par nom
     * Utilisé dans : selects des formulaires, liste référentiel
     */
    public function getAll(): array {
        $stmt = $this->db->query(
            'SELECT id_centre, nom_centre
             FROM centre
             ORDER BY nom_centre ASC'
        );
        return $stmt->fetchAll();
    }
    /**
     * Retourne un centre par son id
     * Utilisé dans : vérification avant update/delete
     */
    public function getById(int $id): ?array {
        $stmt = $this->db->prepare(
            'SELECT id_centre, nom_centre
             FROM centre
             WHERE id_centre = ?'
        );
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }
    /**
     * Crée un nouveau centre
     * Retourne l'id inséré ou false si échec
     */
    public function create(string $nom_centre): int|false {
        $stmt = $this->db->prepare(
            'INSERT INTO centre (nom_centre)
             VALUES (?)'
        );
        $stmt->execute([trim($nom_centre)]);
        return (int) $this->db->lastInsertId();
    }
    /**
     * Met à jour le nom d'un centre
     * Retourne true si succès, false si échec
     */
    public function update(int $id, string $nom_centre): bool {
        $stmt = $this->db->prepare(
            'UPDATE centre
             SET nom_centre = ?
             WHERE id_centre = ?'
        );
        return $stmt->execute([trim($nom_centre), $id]);
    }
    /**
     * Supprime un centre par son id
     *  Vérifier avant qu'aucune inscription ou mémoire n'y est lié
     * Retourne true si succès, false si échec
     */
    public function delete(int $id): bool {
        $stmt = $this->db->prepare(
            'DELETE FROM centre
             WHERE id_centre = ?'
        );
        return $stmt->execute([$id]);
    }
    /**
     * Vérifie si un centre avec ce nom existe déjà
     * Utilisé avant create pour éviter les doublons
     */
    public function existsByNom(string $nom_centre): bool {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) as total
             FROM centre
             WHERE nom_centre = ?'
        );
        $stmt->execute([trim($nom_centre)]);
        return $stmt->fetch()['total'] > 0;
    }
    /**
     * Vérifie si un centre est utilisé dans une inscription ou un mémoire
     * Utilisé avant delete pour éviter les erreurs de contrainte FK
     */
    public function isUsed(int $id): bool {
        // Vérification dans les inscriptions
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) as total
             FROM inscription
             WHERE id_centre = ?'
        );
        $stmt->execute([$id]);
        if ($stmt->fetch()['total'] > 0) return true;
        // Vérification dans les mémoires archivés
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) as total
             FROM memoire
             WHERE id_centre_archive = ?'
        );
        $stmt->execute([$id]);
        return $stmt->fetch()['total'] > 0;
    }
}