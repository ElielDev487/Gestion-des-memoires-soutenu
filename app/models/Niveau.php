<?php
// File : app/models/Niveau.php
// Gestion des niveaux en base de données
// CRUD complet : getAll, getById, create, update, delete

class Niveau {

    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Retourne tous les niveaux triés par libellé
     * Utilisé dans : selects des formulaires, liste référentiel
     */
    public function getAll(): array {
        $stmt = $this->db->query(
            'SELECT id_niveau, libelle
             FROM niveau
             ORDER BY libelle ASC'
        );
        return $stmt->fetchAll();
    }

    /**
     * Retourne un niveau par son id
     * Utilisé dans : vérification avant update/delete
     */
    public function getById(int $id): ?array {
        $stmt = $this->db->prepare(
            'SELECT id_niveau, libelle
             FROM niveau
             WHERE id_niveau = ?'
        );
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Crée un nouveau niveau
     * Retourne l'id inséré
     */
    public function create(string $libelle): int {
        $stmt = $this->db->prepare(
            'INSERT INTO niveau (libelle)
             VALUES (?)'
        );
        $stmt->execute([trim($libelle)]);
        return (int) $this->db->lastInsertId();
    }

    /**
     * Met à jour le libellé d'un niveau
     * Retourne true si succès, false si échec
     */
    public function update(int $id, string $libelle): bool {
        $stmt = $this->db->prepare(
            'UPDATE niveau
             SET libelle = ?
             WHERE id_niveau = ?'
        );
        return $stmt->execute([trim($libelle), $id]);
    }

    /**
     * Supprime un niveau par son id
     *  Vérifier avant avec isUsed() qu'aucune inscription ou mémoire n'y est lié
     * Retourne true si succès, false si échec
     */
    public function delete(int $id): bool {
        $stmt = $this->db->prepare(
            'DELETE FROM niveau
             WHERE id_niveau = ?'
        );
        return $stmt->execute([$id]);
    }

    /**
     * Vérifie si un niveau avec ce libellé existe déjà
     * Utilisé avant create pour éviter les doublons
     */
    public function existsByLibelle(string $libelle): bool {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) as total
             FROM niveau
             WHERE libelle = ?'
        );
        $stmt->execute([trim($libelle)]);
        return $stmt->fetch()['total'] > 0;
    }

    /**
     * Vérifie si un niveau est utilisé dans une inscription ou un mémoire
     * Utilisé avant delete pour éviter les erreurs de contrainte FK
     */
    public function isUsed(int $id): bool {
        // Vérification dans les inscriptions (niveau d'inscription)
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) as total
             FROM inscription
             WHERE id_niveau = ?'
        );
        $stmt->execute([$id]);
        if ($stmt->fetch()['total'] > 0) return true;

        // Vérification dans les inscriptions (niveau du diplôme)
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) as total
             FROM inscription
             WHERE niveau_diplome = ?'
        );
        $stmt->execute([$id]);
        if ($stmt->fetch()['total'] > 0) return true;

        // Vérification dans les mémoires archivés
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) as total
             FROM memoire
             WHERE id_niveau_archive = ?'
        );
        $stmt->execute([$id]);
        return $stmt->fetch()['total'] > 0;
    }
}
