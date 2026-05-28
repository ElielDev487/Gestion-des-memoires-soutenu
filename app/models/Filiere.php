<?php
// File : app/models/Filiere.php
// Gestion des filières en base de données
// CRUD complet : getAll, getById, create, update, delete

class Filiere {

    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Retourne toutes les filières triées par nom
     * Utilisé dans : selects des formulaires, liste référentiel
     */
    public function getAll(): array {
        $stmt = $this->db->query(
            'SELECT id_filiere, nom_filiere
             FROM filiere
             ORDER BY nom_filiere ASC'
        );
        return $stmt->fetchAll();
    }

    /**
     * Retourne une filière par son id
     * Utilisé dans : vérification avant update/delete
     */
    public function getById(int $id): ?array {
        $stmt = $this->db->prepare(
            'SELECT id_filiere, nom_filiere
             FROM filiere
             WHERE id_filiere = ?'
        );
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Crée une nouvelle filière
     * Retourne l'id inséré ou false si échec
     */
    public function create(string $nom): int|false {
        $stmt = $this->db->prepare(
            'INSERT INTO filiere (nom_filiere)
             VALUES (?)'
        );
        $stmt->execute([trim($nom)]);
        return (int) $this->db->lastInsertId();
    }

    /**
     * Met à jour le nom d'une filière
     * Retourne true si succès, false si échec
     */
    public function update(int $id, string $nom): bool {
        $stmt = $this->db->prepare(
            'UPDATE filiere
             SET nom_filiere = ?
             WHERE id_filiere = ?'
        );
        return $stmt->execute([trim($nom), $id]);
    }

    /**
     * Supprime une filière par son id
     * Vérifier avant qu'aucune inscription ou mémoire n'y est lié
     * Retourne true si succès, false si échec
     */
    public function delete(int $id): bool {
        $stmt = $this->db->prepare(
            'DELETE FROM filiere
             WHERE id_filiere = ?'
        );
        return $stmt->execute([$id]);
    }

    /**
     * Vérifie si une filière avec ce nom existe déjà
     * Utilisé avant create pour éviter les doublons
     */
    public function existsByNom(string $nom): bool {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) as total
             FROM filiere
             WHERE nom_filiere = ?'
        );
        $stmt->execute([trim($nom)]);
        return $stmt->fetch()['total'] > 0;
    }

    /**
     * Vérifie si une filière est utilisée dans une inscription ou un mémoire
     * Utilisé avant delete pour éviter les erreurs de contrainte FK
     */
    public function isUsed(int $id): bool {
        // Vérification dans les inscriptions
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) as total
             FROM inscription
             WHERE id_filiere = ?'
        );
        $stmt->execute([$id]);
        if ($stmt->fetch()['total'] > 0) return true;

        // Vérification dans les mémoires archivés
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) as total
             FROM memoire
             WHERE id_filiere_archive = ?'
        );
        $stmt->execute([$id]);
        return $stmt->fetch()['total'] > 0;
    }
}