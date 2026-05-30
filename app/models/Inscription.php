<?php
// File : app/models/Inscription.php
// Gestion des inscriptions des étudiants en base de données
// CRUD complet : getAll, getById, create, update, delete
class Inscription {
    private PDO $db;
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    /**
     * Retourne toutes les inscriptions avec les infos associées
     * Utilisé dans : liste des inscriptions (admin, direction)
     */
    public function getAll(): array {
        $stmt = $this->db->query(
            'SELECT i.id_inscription, i.type_etudiant, i.niveau_diplome, i.peut_soumettre,
                    e.nom, e.prenom, e.matricule,
                    f.nom_filiere, n.libelle AS niveau,
                    c.nom_centre, a.libelle AS annee_academique
             FROM inscription i
             INNER JOIN etudiant          e ON e.id_etudiant         = i.id_etudiant
             INNER JOIN filiere           f ON f.id_filiere          = i.id_filiere
             INNER JOIN niveau            n ON n.id_niveau           = i.id_niveau
             INNER JOIN centre            c ON c.id_centre           = i.id_centre
             INNER JOIN annee_academique  a ON a.id_annee_academique = i.id_annee_academique
             ORDER BY e.nom ASC'
        );
        return $stmt->fetchAll();
    }
    /**
     * Retourne une inscription par son id
     * Utilisé dans : vérification avant update/delete
     */
    public function getById(int $id): ?array {
        $stmt = $this->db->prepare(
            'SELECT i.id_inscription, i.id_etudiant, i.id_filiere, i.id_niveau,
                    i.id_centre, i.id_annee_academique,
                    i.type_etudiant, i.niveau_diplome, i.peut_soumettre,
                    e.nom, e.prenom, e.matricule
             FROM inscription i
             INNER JOIN etudiant e ON e.id_etudiant = i.id_etudiant
             WHERE i.id_inscription = ?'
        );
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }
    /**
     * Retourne l'inscription active d'un étudiant
     * Utilisé dans : vérification soumission mémoire, profil étudiant
     */
    public function getByEtudiant(int $id_etudiant): ?array {
        $stmt = $this->db->prepare(
            'SELECT i.*, f.nom_filiere, n.libelle AS niveau,
                    c.nom_centre, a.libelle AS annee_academique
             FROM inscription i
             INNER JOIN filiere          f ON f.id_filiere          = i.id_filiere
             INNER JOIN niveau           n ON n.id_niveau           = i.id_niveau
             INNER JOIN centre           c ON c.id_centre           = i.id_centre
             INNER JOIN annee_academique a ON a.id_annee_academique = i.id_annee_academique
             WHERE i.id_etudiant = ?
             ORDER BY i.id_inscription DESC
             LIMIT 1'
        );
        $stmt->execute([$id_etudiant]);
        $result = $stmt->fetch();
        return $result ?: null;
    }
    /**
     * Crée une nouvelle inscription pour un étudiant
     * Retourne l'id inséré ou false si échec
     */
    public function create(int $id_etudiant, int $id_filiere, int $id_niveau,
                           int $id_centre, int $id_annee_academique,
                           string $type_etudiant, string $niveau_diplome,
                           bool $peut_soumettre = false): int|false {
        $stmt = $this->db->prepare(
            'INSERT INTO inscription
                 (id_etudiant, id_filiere, id_niveau, id_centre,
                  id_annee_academique, type_etudiant, niveau_diplome, peut_soumettre)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $id_etudiant,
            $id_filiere,
            $id_niveau,
            $id_centre,
            $id_annee_academique,
            trim($type_etudiant),
            trim($niveau_diplome),
            $peut_soumettre ? 1 : 0
        ]);
        return (int) $this->db->lastInsertId();
    }
    /**
     * Met à jour une inscription existante
     * Retourne true si succès, false si échec
     */
    public function update(int $id, int $id_filiere, int $id_niveau,
                           int $id_centre, int $id_annee_academique,
                           string $type_etudiant, string $niveau_diplome,
                           bool $peut_soumettre): bool {
        $stmt = $this->db->prepare(
            'UPDATE inscription
             SET id_filiere          = ?,
                 id_niveau           = ?,
                 id_centre           = ?,
                 id_annee_academique = ?,
                 type_etudiant       = ?,
                 niveau_diplome      = ?,
                 peut_soumettre      = ?
             WHERE id_inscription = ?'
        );
        return $stmt->execute([
            $id_filiere,
            $id_niveau,
            $id_centre,
            $id_annee_academique,
            trim($type_etudiant),
            trim($niveau_diplome),
            $peut_soumettre ? 1 : 0,
            $id
        ]);
    }
    /**
     * Active ou désactive la soumission de mémoire pour un étudiant
     * Utilisé dans : gestion des droits par l'administration
     */
    public function setPeutSoumettre(int $id_etudiant, bool $valeur): bool {
        $stmt = $this->db->prepare(
            'UPDATE inscription
             SET peut_soumettre = ?
             WHERE id_etudiant = ?'
        );
        return $stmt->execute([$valeur ? 1 : 0, $id_etudiant]);
    }
    /**
     * Supprime une inscription par son id
     * ⚠️ Vérifier avant qu'aucun mémoire n'y est lié
     * Retourne true si succès, false si échec
     */
    public function delete(int $id): bool {
        $stmt = $this->db->prepare(
            'DELETE FROM inscription
             WHERE id_inscription = ?'
        );
        return $stmt->execute([$id]);
    }
    /**
     * Vérifie si une inscription est utilisée dans un mémoire
     * Utilisé avant delete pour éviter les erreurs de contrainte FK
     */
    public function isUsed(int $id): bool {
        // Vérification dans les mémoires
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) as total
             FROM memoire
             WHERE id_inscription = ?'
        );
        $stmt->execute([$id]);
        return $stmt->fetch()['total'] > 0;
    }
}