<?php
// File : app/models/AnneeAcademique.php
// Gestion des années académiques en base de données
// CRUD complet : getAll, getById, create, update, delete

class AnneeAcademique {

    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Retourne toutes les années académiques, la plus récente en premier
     * Utilisé dans : selects des formulaires, liste référentiel
     */
    public function getAll(): array {
        $stmt = $this->db->query(
            'SELECT id_annee_academique, libelle
             FROM annee_academique
             ORDER BY libelle DESC'
        );
        return $stmt->fetchAll();
    }

    /**
     * Retourne une année académique par son id
     * Utilisé dans : vérification avant update/delete
     */
    public function getById(int $id): ?array {
        $stmt = $this->db->prepare(
            'SELECT id_annee_academique, libelle
             FROM annee_academique
             WHERE id_annee_academique = ?'
        );
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Crée une nouvelle année académique
     * Retourne l'id inséré
     */
    public function create(string $libelle): int {
        $stmt = $this->db->prepare(
            'INSERT INTO annee_academique (libelle)
             VALUES (?)'
        );
        $stmt->execute([trim($libelle)]);
        return (int) $this->db->lastInsertId();
    }

    /**
     * Met à jour le libellé d'une année académique
     * Retourne true si succès, false si échec
     */
    public function update(int $id, string $libelle): bool {
        $stmt = $this->db->prepare(
            'UPDATE annee_academique
             SET libelle = ?
             WHERE id_annee_academique = ?'
        );
        return $stmt->execute([trim($libelle), $id]);
    }

    /**
     * Supprime une année académique par son id
     * Vérifier avant avec isUsed() qu'aucune inscription ou mémoire n'y est lié
     * Retourne true si succès, false si échec
     */
    public function delete(int $id): bool {
        $stmt = $this->db->prepare(
            'DELETE FROM annee_academique
             WHERE id_annee_academique = ?'
        );
        return $stmt->execute([$id]);
    }

    /**
     * Vérifie si un libellé existe déjà
     * Utilisé avant create pour éviter les doublons
     */
    public function existsByLibelle(string $libelle): bool {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) as total
             FROM annee_academique
             WHERE libelle = ?'
        );
        $stmt->execute([trim($libelle)]);
        return $stmt->fetch()['total'] > 0;
    }

    /**
     * Vérifie si une année est utilisée dans une inscription ou un mémoire
     * Utilisé avant delete pour éviter les erreurs de contrainte FK
     */
    public function isUsed(int $id): bool {
        // Vérification dans les inscriptions
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) as total
             FROM inscription
             WHERE id_annee_academique = ?'
        );
        $stmt->execute([$id]);
        if ($stmt->fetch()['total'] > 0) return true;

        // Vérification dans les mémoires archivés
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) as total
             FROM memoire
             WHERE id_annee_archive = ?'
        );
        $stmt->execute([$id]);
        return $stmt->fetch()['total'] > 0;
    }
}